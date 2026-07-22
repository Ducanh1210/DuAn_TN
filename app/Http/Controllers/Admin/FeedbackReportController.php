<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackReportController extends Controller
{
    public function index(Request $request)
    {
        $query = FeedbackReport::with('user', 'resolver')->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== '') {
            if ($request->type === 'system') {
                $query->where('report_type', 'system_suggestion');
            } else {
                $query->where('report_type', '!=', 'system_suggestion');
            }
        }

        $feedbacks = $query->paginate(15);
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function show($id)
    {
        $feedback = FeedbackReport::with('user', 'resolver')->findOrFail($id);
        
        // Load target model dynamically if possible
        $targetName = 'Không xác định';
        $targetLink = '#';
        if ($feedback->target_type && $feedback->target_id) {
            $targetModel = $feedback->target()->first();
            if ($targetModel) {
                if ($feedback->target_type === 'location') {
                    $targetName = $targetModel->name;
                    $targetLink = route('admin.locations.edit', $targetModel->id);
                } elseif ($feedback->target_type === 'news') {
                    $targetName = $targetModel->title;
                    $targetLink = route('admin.news.edit', $targetModel->id);
                }
            }
        }

        return view('admin.feedbacks.show', compact('feedback', 'targetName', 'targetLink'));
    }

    public function update(Request $request, $id)
    {
        $feedback = FeedbackReport::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,resolved,rejected',
            'admin_response' => 'nullable|string',
        ]);

        $feedback->status = $request->status;
        $feedback->admin_response = $request->admin_response;

        if (in_array($request->status, ['resolved', 'rejected'])) {
            $feedback->resolved_by = auth()->id();
            $feedback->resolved_at = now();
        }

        $feedback->save();

        // Cộng điểm nếu resolved và là báo cáo đúng (ví dụ: +10 điểm)
        if ($feedback->status === 'resolved' && $feedback->user_id && $feedback->report_type !== 'system_suggestion') {
            $user = \App\Models\User::find($feedback->user_id);
            if ($user) {
                \App\Services\PointService::awardPoints($user, 10, 'feedback_resolved', 'Đóng góp nội dung bản đồ được xác nhận.');
            }
        }

        return redirect()->route('admin.feedbacks.show', $feedback->id)
                         ->with('success', 'Đã cập nhật trạng thái phản hồi/báo cáo thành công.');
    }
}
