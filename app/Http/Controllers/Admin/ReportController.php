<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\FeedbackReport;
use App\Models\Location;
use App\Models\Report;
use Illuminate\Http\Request;

/**
 * Controller quản trị báo cáo & góp ý: chia tab theo báo cáo địa điểm, báo cáo bình luận
 * và góp ý/báo lỗi (FeedbackReport); cho phép cập nhật trạng thái xử lý.
 */
class ReportController extends Controller
{
    /** Danh sách theo tab: locations / comments / feedbacks, kèm số lượng đang chờ xử lý. */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'locations');
        $locationType = Location::class;
        $commentType = Comment::class;

        $locationTypes = Report::morphTypes($locationType);
        $commentTypes = Report::morphTypes($commentType);

        if ($tab === 'comments') {
            $reports = Report::with(['reporter', 'handler', 'reportable.location'])
                ->whereIn('reportable_type', $commentTypes)
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
                ->whereIn('reportable_type', $locationTypes)
                ->orderByDesc('created_at')
                ->paginate(15)
                ->withQueryString();
            $feedbacks = null;
        }

        $pendingLocations = Report::whereIn('reportable_type', $locationTypes)
            ->where('status', 'pending')
            ->count();
        $pendingComments = Report::whereIn('reportable_type', $commentTypes)
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

    /** Cập nhật trạng thái xử lý một báo cáo vi phạm. */
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

    /** Xóa một báo cáo vi phạm (địa điểm / bình luận). */
    public function destroy(Report $report)
    {
        $report->delete();

        return back()->with('success', 'Đã xóa báo cáo.');
    }

    /** Xóa một góp ý / báo lỗi. */
    public function destroyFeedback($id)
    {
        $feedback = FeedbackReport::findOrFail($id);
        $feedback->delete();

        return redirect()
            ->route('admin.reports.index', ['tab' => 'feedbacks'])
            ->with('success', 'Đã xóa góp ý / báo lỗi.');
    }

    /** Chi tiết một góp ý/báo lỗi kèm liên kết tới đối tượng liên quan (địa điểm/tin tức). */
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

    /** Cập nhật trạng thái và phản hồi của quản trị cho một góp ý/báo lỗi. */
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
