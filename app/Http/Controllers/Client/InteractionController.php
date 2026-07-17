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
            // Award points for adding a favorite
            PointService::awardPoints($user, 2, 'favorite', 'Yêu thích địa điểm ' . $location->name);
            return response()->json(['status' => 'added', 'message' => 'Đã thêm vào danh sách yêu thích.']);
        }
    }

    public function storeComment(Request $request, Location $location)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment = $location->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'status' => 'visible',
        ]);

        // Award points for comment
        PointService::awardPoints(Auth::user(), 5, 'comment', 'Bình luận địa điểm ' . $location->name);

        $comment->load('user');

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
                    'avatar_url' => $comment->user->avatar_url ? (str_starts_with($comment->user->avatar_url, 'http') ? $comment->user->avatar_url : asset('storage/' . $comment->user->avatar_url)) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->display_name ?? $comment->user->username) . '&background=0072FF&color=fff',
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
}
