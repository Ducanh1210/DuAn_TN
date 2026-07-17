<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'reportable', 'handler'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,rejected'
        ]);

        $report->update([
            'status' => $request->status,
            'handled_by' => auth()->id()
        ]);

        return back()->with('success', 'Cập nhật trạng thái báo cáo thành công.');
    }

    public function deleteReportedContent(Report $report)
    {
        $reportable = $report->reportable;
        if ($reportable) {
            $reportable->delete();
            $report->update([
                'status' => 'resolved',
                'handled_by' => auth()->id()
            ]);
            return back()->with('success', 'Đã xóa nội dung vi phạm và đánh dấu báo cáo là Đã xử lý.');
        }

        return back()->with('error', 'Nội dung này không tồn tại hoặc đã bị xóa.');
    }
}
