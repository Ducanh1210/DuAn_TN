<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\ModerationService;
use Illuminate\Http\Request;

/**
 * Controller quản trị bình luận: liệt kê (tìm kiếm, lọc theo trạng thái/cờ AI), quét kiểm duyệt
 * bằng AI, xóa và ẩn/hiện bình luận.
 */
class CommentController extends Controller
{
    /** Danh sách bình luận có tìm kiếm, lọc theo trạng thái và cờ kiểm duyệt AI, kèm số liệu tổng quan. */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'location'])->orderBy('created_at', 'desc');

        // Lọc theo địa điểm
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Tìm kiếm theo nội dung hoặc tên/hiển thị/email của người viết
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
        
        // Lọc theo trạng thái hiển thị
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo cờ kiểm duyệt AI (unchecked = chưa quét)
        if ($request->filled('ai_flag')) {
            if ($request->ai_flag === 'unchecked') {
                $query->whereNull('ai_checked_at');
            } else {
                $query->where('ai_flag', $request->ai_flag);
            }
        }

        // Sắp xếp rủi ro cao lên đầu khi rà soát theo cờ AI
        if ($request->boolean('sort_risk')) {
            $query->reorder()->orderByRaw('ai_score IS NULL, ai_score DESC')->orderBy('created_at', 'desc');
        }

        $comments = $query->paginate(20)->withQueryString();

        // Số liệu tổng quan cho banner kiểm duyệt AI
        $aiStats = [
            'violation' => Comment::where('ai_flag', 'violation')->count(),
            'suspect' => Comment::where('ai_flag', 'suspect')->count(),
            'unchecked' => Comment::whereNull('ai_checked_at')->count(),
        ];

        $aiConfigured = (new ModerationService())->isConfigured();

        return view('admin.comments.index', compact('comments', 'aiStats', 'aiConfigured'));
    }

    /** Quét bình luận bằng AI để gắn cờ nội dung nghi ngờ/vi phạm (giới hạn số lượng mỗi lần chạy). */
    public function scanAi(Request $request, ModerationService $moderation)
    {
        if (!$moderation->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa cấu hình API Key AI (GEMINI_API_KEY).',
            ], 400);
        }

        $scope = $request->input('scope', 'unchecked');

        $query = Comment::query();
        if ($scope === 'unchecked') {
            $query->whereNull('ai_checked_at');
        }
        // scope === 'all' => quét lại toàn bộ

        // Giới hạn số lượng mỗi lần chạy để kiểm soát độ trễ và chi phí
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

    /** Xóa vĩnh viễn một bình luận. */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')
            ->with('success', 'Đã xóa bình luận thành công.');
    }

    /** Bật/tắt hiển thị bình luận (visible <-> hidden). */
    public function toggleStatus(Comment $comment)
    {
        // Chuyển đổi giữa hiển thị và ẩn
        $newStatus = $comment->status === 'visible' ? 'hidden' : 'visible';
        
        $comment->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'status' => $newStatus
        ]);
    }
}
