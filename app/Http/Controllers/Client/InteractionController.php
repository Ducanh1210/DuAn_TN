<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Services\PointService;

class InteractionController extends Controller
{
    public function toggleFavorite(Request $request, Location $location)
    {
        $user = Auth::user();
        $favorite = $user->favoriteLocations()->where('location_id', $location->id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed', 'message' => 'Đã xóa khỏi danh sách yêu thích.']);
        } else {
            $user->favoriteLocations()->create(['location_id' => $location->id]);
            PointService::awardPoints($user, PointService::POINTS_FAVORITE, 'favorite', 'Yêu thích địa điểm ' . $location->name);
            \App\Services\MissionService::trackProgress($user, 'favorite_location', 1, false, $location->id);
            return response()->json([
                'status' => 'added',
                'message' => 'Đã thêm vào danh sách yêu thích (+' . PointService::POINTS_FAVORITE . ' điểm).',
                'points' => $user->fresh()->points,
            ]);
        }
    }

    public function storeComment(Request $request, Location $location)
    {
        $user = Auth::user();

        // Check if user already commented on this location (1 review per user per location)
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

        PointService::awardPoints(Auth::user(), PointService::POINTS_COMMENT, 'comment', 'Bình luận địa điểm ' . $location->name);
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

    public function deleteComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }

        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa bình luận.']);
    }

    public function myFavorites()
    {
        $favorites = Auth::user()->favoriteLocations()->with('location.category', 'location.images')->paginate(12);
        return view('client.favorites.index', compact('favorites'));
    }

    public function report(Request $request)
    {
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $modelClass = '\\App\\Models\\' . $request->reportable_type;
        if (!class_exists($modelClass)) {
            return response()->json(['success' => false, 'message' => 'Loại báo cáo không hợp lệ.'], 400);
        }

        $reportable = $modelClass::find($request->reportable_id);
        if (!$reportable) {
            return response()->json(['success' => false, 'message' => 'Nội dung không tồn tại.'], 404);
        }

        // Check if user already reported this recently
        $existingReport = \App\Models\Report::where('reporter_id', Auth::id())
            ->where('reportable_id', $request->reportable_id)
            ->where('reportable_type', $modelClass)
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

    public function myContributions()
    {
        return redirect()->route('home');
    }

    public function suggestLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'category_suggest' => 'nullable|string|max:80',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120' // 5MB max
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('location_suggestions', 'public');
                $imagePaths[] = 'storage/' . $path;
            }
        }

        $suggestion = \App\Models\LocationSuggestion::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'category_suggest' => $request->category_suggest,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'images' => $imagePaths,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Cảm ơn bạn đã đóng góp! Đề xuất của bạn đã được ghi nhận và đang chờ duyệt.',
            'data' => $suggestion
        ]);
    }

    public function submitFeedback(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:wrong_info,duplicate_location,image_error,wrong_position,location_closed,system_suggestion,other',
            'target_type' => 'nullable|in:location,news,event,comment,system',
            'target_id' => 'nullable|integer',
            'content' => 'required|string|max:1000',
        ]);

        $feedback = \App\Models\FeedbackReport::create([
            'user_id' => Auth::check() ? Auth::id() : null,
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
