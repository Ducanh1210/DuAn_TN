<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\FeedbackReport;
use App\Models\Location;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'locations');
        $locationType = Location::class;
        $commentType = Comment::class;

        if ($tab === 'comments') {
            $reports = Report::with(['reporter', 'handler', 'reportable.location'])
                ->where('reportable_type', $commentType)
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();
            $feedbacks = null;
        } elseif ($tab === 'feedbacks') {
            $feedbacks = FeedbackReport::with('user')
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();
            $reports = null;
        } else {
            $tab = 'locations';
            $reports = Report::with(['reporter', 'handler', 'reportable'])
                ->where('reportable_type', $locationType)
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();
            $feedbacks = null;
        }

        $pendingLocations = Report::where('reportable_type', $locationType)
            ->where('status', 'pending')
            ->count();
        $pendingComments = Report::where('reportable_type', $commentType)
            ->where('status', 'pending')
            ->count();
        $pendingFeedbacks = FeedbackReport::where('status', 'pending')->count();

        return view('admin.reports.index', compact(
            'tab',
            'reports',
            'feedbacks',
            'pendingLocations',
            'pendingComments',
            'pendingFeedbacks'
        ));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,rejected',
        ]);

        $report->update([
            'status' => $request->status,
            'handled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Cập nhật trạng thái báo cáo thành công.');
    }

    public function showFeedback($id)
    {
        $feedback = FeedbackReport::with('user', 'resolver')->findOrFail($id);

        $targetName = '—';
        $targetLink = null;

        if ($feedback->target_type === 'location' && $feedback->target_id) {
            $loc = Location::find($feedback->target_id);
            if ($loc) {
                $targetName = $loc->name;
                $targetLink = route('admin.locations.edit', $loc->id);
            }
        } elseif ($feedback->target_type === 'news' && $feedback->target_id) {
            $news = \App\Models\News::find($feedback->target_id);
            if ($news) {
                $targetName = $news->title;
                $targetLink = route('admin.news.edit', $news->id);
            }
        }

        return view('admin.reports.show_feedback', compact('feedback', 'targetName', 'targetLink'));
    }

    public function updateFeedback(Request $request, $id)
    {
        $feedback = FeedbackReport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,resolved,rejected',
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $feedback->status = $request->status;
        $feedback->admin_response = $request->admin_response;

        if (in_array($request->status, ['resolved', 'rejected'], true)) {
            $feedback->resolved_by = auth()->id();
            $feedback->resolved_at = now();
        }

        $feedback->save();

        return redirect()
            ->route('admin.reports.feedbacks.show', $feedback->id)
            ->with('success', 'Đã cập nhật góp ý / báo lỗi.');
    }
}
