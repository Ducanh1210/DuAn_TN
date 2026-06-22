<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'location'])->orderBy('created_at', 'desc');

        // Filter by location id
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Search by user name, display name or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('username', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $comments = $query->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')
            ->with('success', 'Đã xóa bình luận thành công.');
    }

    /**
     * Toggle the status of the comment.
     */
    public function toggleStatus(Comment $comment)
    {
        // Toggle between visible and hidden
        $newStatus = $comment->status === 'visible' ? 'hidden' : 'visible';
        
        $comment->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'status' => $newStatus
        ]);
    }
}
