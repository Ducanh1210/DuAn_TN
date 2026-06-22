<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

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
                    'avatar_url' => $comment->user->avatar_url ? (str_starts_with($comment->user->avatar_url, 'http') ? $comment->user->avatar_url : asset('storage/' . $comment->user->avatar_url)) : 'https://ui-avatars.com/api/?name='.urlencode($comment->user->display_name ?? $comment->user->username).'&background=0072FF&color=fff',
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
}
