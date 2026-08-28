<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Client\Location\SuggestLocationRequest;
use App\Models\Location;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Services\PointService;

/**
 * Controller xử lý các tương tác của người dùng với địa điểm: yêu thích, bình luận/đánh giá,
 * báo cáo vi phạm, đề xuất địa điểm mới và gửi góp ý. Nhiều hành động cộng điểm và cập nhật nhiệm vụ.
 */
class InteractionController extends Controller
{
    /** Thêm/bỏ yêu thích một địa điểm (toggle); lần thêm mới sẽ cộng điểm và tính nhiệm vụ. */
    public function toggleFavorite(Request $request, Location $location)
    {
        $user = Auth::user();
        $favorite = $user->favoriteLocations()->where('location_id', $location->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed', 'message' => 'Đã xóa khỏi danh sách yêu thích.']);
        } else {
            $user->favoriteLocations()->create(['location_id' => $location->id]);
            \App\Services\MissionService::trackProgress($user, 'favorite_location', 1, false, $location->id);
            return response()->json([
                'status' => 'added',
                'message' => 'Đã thêm vào danh sách yêu thích.',
            ]);
        }
    }

    /** Gửi đánh giá/bình luận cho địa điểm (mỗi người chỉ được đánh giá 1 lần), cộng điểm và tính nhiệm vụ. */
    public function storeComment(Request $request, Location $location)
    {
        $user = Auth::user();

        // Mỗi tài khoản chỉ được đánh giá một địa điểm một lần
        $existingComment = $location->comments()->where('user_id', $user->id)->first();
        if ($existingComment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá địa điểm này rồi! Mỗi tài khoản chỉ được gửi đánh giá 1 lần.'
            ], 422);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment = $location->comments()->create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'status' => 'visible',
        ]);

        $this->refreshLocationRating($location);

        \App\Services\MissionService::trackProgress(Auth::user(), 'write_comment', 1);

        $comment->load('user.equippedFrame');
        $frame = $comment->user->equippedFrame;

        return response()->json([
            'success' => true,
            'message' => 'Bình luận của bạn đã được gửi.',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'rating' => $comment->rating,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'display_name' => $comment->user->display_name ?? $comment->user->username,
                    'avatar_url' => $comment->user->avatar_formatted_url,
                    'frame_css' => $frame->css_style ?? '',
                    'frame_image_url' => !empty($frame?->image_url) ? asset($frame->image_url) : '',
                    'comments_count' => Comment::where('user_id', $comment->user_id)->where('status', 'visible')->count(),
                ]
            ]
        ]);
    }

    /** Cập nhật nội dung/điểm đánh giá của bình luận (chỉ chủ sở hữu mới được sửa). */
    public function updateComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền chỉnh sửa.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment->update([
            'content' => $request->input('content'),
            'rating' => $request->input('rating', $comment->rating),
        ]);

        $this->refreshLocationRating($comment->location);

        $comment->load('user.equippedFrame');
        $frame = $comment->user->equippedFrame;

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bài đánh giá thành công.',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'rating' => $comment->rating,
                'created_at' => $comment->updated_at->diffForHumans(),
                'user' => [
                    'display_name' => $comment->user->display_name ?? $comment->user->username,
                    'avatar_url' => $comment->user->avatar_formatted_url,
                    'frame_css' => $frame->css_style ?? '',
                    'frame_image_url' => !empty($frame?->image_url) ? asset($frame->image_url) : '',
                    'comments_count' => Comment::where('user_id', $comment->user_id)->where('status', 'visible')->count(),
                ]
            ]
        ]);
    }

    /** Xóa bình luận (chỉ chủ sở hữu mới được xóa). */
    public function deleteComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }

        $location = $comment->location;
        $comment->delete();
        $this->refreshLocationRating($location);
        return response()->json(['success' => true, 'message' => 'Đã xóa bình luận.']);
    }

    private function refreshLocationRating(?Location $location): void
    {
        if (!$location) {
            return;
        }

        $averageRating = Comment::where('location_id', $location->id)
            ->whereNull('parent_id')
            ->where('status', 'visible')
            ->whereNotNull('rating')
            ->avg('rating');

        $location->update([
            'average_rating' => $averageRating !== null ? round((float) $averageRating, 2) : 0,
        ]);
    }

    /** Trang danh sách địa điểm yêu thích của người dùng. */
    public function myFavorites()
    {
        $favorites = Auth::user()->favoriteLocations()
            ->whereHas('location', fn ($q) => $q->where('status', 'published'))
            ->with('location.category', 'location.images')
            ->paginate(12);

        return view('client.favorites.index', compact('favorites'));
    }

    /** Báo cáo vi phạm một nội dung (dùng quan hệ đa hình), chặn báo cáo trùng đang chờ xử lý. */
    public function report(Request $request)
    {
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $typeName = preg_replace('/[^A-Za-z]/', '', (string) $request->reportable_type);
        if (!in_array($typeName, ['Location', 'Comment'], true)) {
            return response()->json(['success' => false, 'message' => 'Loại báo cáo không hợp lệ.'], 400);
        }

        $modelClass = 'App\\Models\\' . $typeName;
        if (!class_exists($modelClass)) {
            return response()->json(['success' => false, 'message' => 'Loại báo cáo không hợp lệ.'], 400);
        }

        $reportable = $modelClass::find($request->reportable_id);
        if (!$reportable) {
            return response()->json(['success' => false, 'message' => 'Nội dung không tồn tại.'], 404);
        }

        // Chặn nếu người dùng đã báo cáo nội dung này và đang chờ xử lý
        $existingReport = \App\Models\Report::where('reporter_id', Auth::id())
            ->where('reportable_id', $request->reportable_id)
            ->whereIn('reportable_type', [$modelClass, '\\' . $modelClass])
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'Bạn đã báo cáo nội dung này rồi và đang chờ xử lý.']);
        }

        \App\Models\Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $modelClass,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét sớm nhất có thể.']);
    }

    /** (Cũ) Trang đóng góp của người dùng — hiện chỉ chuyển về trang chủ. */
    public function myContributions()
    {
        return redirect()->route('home');
    }

    /** Gửi đề xuất địa điểm mới kèm ảnh; lưu ở trạng thái chờ admin duyệt, không tự lên bản đồ. */
    public function suggestLocation(SuggestLocationRequest $request)
    {
        $validated = $request->validated();

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('location_suggestions', 'public');
                $imagePaths[] = 'storage/' . $path;
            }
        }

        $suggestion = \App\Models\LocationSuggestion::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'category_suggest' => $validated['category_suggest'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
            'images' => $imagePaths,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Cảm ơn bạn đã đóng góp! Đề xuất đã được ghi nhận để Ban quản trị tham khảo (không tự đăng lên bản đồ).',
            'data' => $suggestion
        ]);
    }

    /** Gửi góp ý / báo lỗi tới ban quản trị (cho phép cả khách chưa đăng nhập). */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:wrong_info,duplicate_location,image_error,wrong_position,location_closed,system_suggestion,other',
            'target_type' => 'nullable|in:location,news,event,comment,system',
            'target_id' => 'nullable|integer',
            'content' => 'required|string|max:1000',
        ]);

        $feedback = \App\Models\FeedbackReport::create([
            'user_id' => Auth::id(),
            'report_type' => $request->report_type,
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'content' => $request->content,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Cảm ơn bạn! Đóng góp/báo cáo của bạn đã được gửi cho Ban quản trị.'
        ]);
    }
}
