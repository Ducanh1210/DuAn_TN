<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\ModerationService;
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

        // Filter by AI moderation flag
        if ($request->filled('ai_flag')) {
            if ($request->ai_flag === 'unchecked') {
                $query->whereNull('ai_checked_at');
            } else {
                $query->where('ai_flag', $request->ai_flag);
            }
        }

        // Sort riskiest first when reviewing AI flags
        if ($request->boolean('sort_risk')) {
            $query->reorder()->orderByRaw('ai_score IS NULL, ai_score DESC')->orderBy('created_at', 'desc');
        }

        $comments = $query->paginate(20)->withQueryString();

        // Summary counters for the AI moderation banner
        $aiStats = [
            'violation' => Comment::where('ai_flag', 'violation')->count(),
            'suspect' => Comment::where('ai_flag', 'suspect')->count(),
            'unchecked' => Comment::whereNull('ai_checked_at')->count(),
        ];

        $aiConfigured = (new ModerationService())->isConfigured();

        return view('admin.comments.index', compact('comments', 'aiStats', 'aiConfigured'));
    }

    /**
     * Scan comments with AI to flag suspicious / violating content.
     */
    public function scanAi(Request $request, ModerationService $moderation)
    {
        if (!$moderation->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa cấu hình API Key AI (OPENROUTER_API_KEY).',
            ], 400);
        }

        $scope = $request->input('scope', 'unchecked');

        $query = Comment::query();
        if ($scope === 'unchecked') {
            $query->whereNull('ai_checked_at');
        }
        // scope === 'all' => rescan everything

        // Cap per run to keep latency & cost under control
        $comments = $query->orderBy('created_at', 'desc')->limit(60)->get(['id', 'content']);

        if ($comments->isEmpty()) {
            return response()->json([
                'success' => true,
                'scanned' => 0,
                'flagged' => 0,
                'message' => $scope === 'unchecked'
                    ? 'Không còn bình luận mới nào cần quét.'
                    : 'Không có bình luận nào để quét.',
            ]);
        }

        $items = $comments->map(fn ($c) => ['id' => $c->id, 'content' => $c->content])->all();
        $results = $moderation->moderateBatch($items);

        if (empty($results)) {
            return response()->json([
                'success' => false,
                'message' => 'AI không trả về kết quả. Vui lòng thử lại sau.',
            ], 500);
        }

        $flagged = 0;
        $now = now();
        foreach ($comments as $comment) {
            $res = $results[$comment->id] ?? null;
            if (!$res) {
                // Không có kết quả cho bình luận này -> đánh dấu đã quét, coi như an toàn
                $comment->update([
                    'ai_flag' => 'safe',
                    'ai_score' => 0,
                    'ai_reason' => null,
                    'ai_checked_at' => $now,
                ]);
                continue;
            }

            if (in_array($res['flag'], ['violation', 'suspect'], true)) {
                $flagged++;
            }

            $comment->update([
                'ai_flag' => $res['flag'],
                'ai_score' => $res['score'],
                'ai_reason' => $res['reason'] !== '' ? $res['reason'] : null,
                'ai_checked_at' => $now,
            ]);
        }

        return response()->json([
            'success' => true,
            'scanned' => $comments->count(),
            'flagged' => $flagged,
            'message' => "Đã quét {$comments->count()} bình luận, phát hiện {$flagged} nghi ngờ/vi phạm.",
        ]);
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
