@extends('admin.layouts.app')

@section('title', match($tab ?? 'locations') {
    'comments' => 'Báo cáo bình luận',
    'feedbacks' => 'Góp ý / báo lỗi',
    default => 'Báo cáo địa điểm',
})

@section('content')
<div class="card-minimal">
    <div class="card-header-minimal">
        {{ match($tab ?? 'locations') {
            'comments' => 'Báo cáo bình luận',
            'feedbacks' => 'Góp ý / báo lỗi bản đồ',
            default => 'Báo cáo địa điểm',
        } }}
    </div>

    @if(($tab ?? 'locations') === 'feedbacks')
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Người gửi</th>
                    <th>Loại</th>
                    <th>Nội dung</th>
                    <th>Ngày</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Xem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $item)
                <tr>
                    <td class="text-muted" style="font-size:0.75rem;">{{ $item->id }}</td>
                    <td style="font-size:0.825rem;">{{ $item->user->display_name ?? ($item->user->username ?? 'Khách') }}</td>
                    <td><span class="badge-minimal">{{ $item->report_type }}</span></td>
                    <td style="white-space:normal;max-width:280px;font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($item->content, 80) }}</td>
                    <td class="text-muted" style="font-size:0.75rem;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($item->status === 'pending')
                            <span class="badge-minimal-warning">Chưa xem</span>
                        @elseif($item->status === 'resolved')
                            <span class="badge-minimal-success">Đã ghi nhận</span>
                        @elseif($item->status === 'rejected')
                            <span class="badge-minimal-danger">Bỏ qua</span>
                        @else
                            <span class="badge-minimal">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                            <a href="{{ route('admin.reports.feedbacks.show', $item->id) }}" class="btn-minimal py-1 px-2" style="font-size:0.75rem;">Chi tiết</a>
                            <form action="{{ route('admin.reports.feedbacks.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa góp ý này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size:0.75rem;">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Chưa có góp ý nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($feedbacks->hasPages())
    <div class="p-3 border-top">{{ $feedbacks->links('pagination::bootstrap-5') }}</div>
    @endif

    @else
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width:50px;">ID</th>
                    <th>Người báo cáo</th>
                    @if(($tab ?? 'locations') === 'comments')
                        <th>Nội dung bình luận</th>
                        <th>Tại địa điểm</th>
                    @else
                        <th>Địa điểm bị báo cáo</th>
                    @endif
                    <th>Lý do</th>
                    <th>Chi tiết</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Xem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="text-center text-muted" style="font-size:0.775rem;">{{ $report->id }}</td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size:0.825rem;">
                                {{ $report->reporter->display_name ?? ($report->reporter->username ?? 'Unknown') }}
                            </div>
                        </td>
                        @if(($tab ?? 'locations') === 'comments')
                            <td style="font-size:0.8rem;max-width:220px;white-space:normal;">
                                @if($report->reportable)
                                    {{ Str::limit($report->reportable->content, 80) }}
                                @else
                                    <span class="text-danger" style="font-size:0.75rem;">Đã bị xóa</span>
                                @endif
                            </td>
                            <td style="font-size:0.8rem;">
                                @if($report->reportable?->location)
                                    {{ $report->reportable->location->name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @else
                            <td style="font-size:0.825rem;">
                                @if($report->reportable)
                                    <span class="fw-medium" style="color:#0f2442;">{{ $report->reportable->name }}</span>
                                @else
                                    <span class="text-danger" style="font-size:0.75rem;">Đã bị xóa</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <div class="fw-medium text-secondary" style="font-size:0.8rem;">{{ $report->reason }}</div>
                        </td>
                        <td>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $report->description ?? 'Không có' }}</div>
                        </td>
                        <td class="text-muted" style="font-size:0.75rem;">
                            {{ $report->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <form action="{{ route('admin.reports.update_status', $report->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; min-width: 120px;">
                                    <option value="pending" @selected($report->status === 'pending')>Chờ xử lý</option>
                                    <option value="resolved" @selected($report->status === 'resolved')>Đã xử lý</option>
                                    <option value="rejected" @selected($report->status === 'rejected')>Từ chối</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                                @if(($tab ?? 'locations') === 'comments')
                                    @if($report->reportable?->location_id)
                                        <a href="{{ route('admin.locations.edit', $report->reportable->location_id) }}" class="btn-minimal py-1 px-2" style="font-size:0.75rem;">Chi tiết</a>
                                    @endif
                                @elseif($report->reportable)
                                    <a href="{{ route('admin.locations.edit', $report->reportable_id) }}" class="btn-minimal py-1 px-2" style="font-size:0.75rem;">Chi tiết</a>
                                @endif
                                <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa báo cáo này khỏi danh sách?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size:0.75rem;">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($tab ?? 'locations') === 'comments' ? 9 : 8 }}" class="text-center text-muted py-4">
                            Chưa có báo cáo {{ ($tab ?? 'locations') === 'comments' ? 'bình luận' : 'địa điểm' }} nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reports->hasPages())
    <div class="p-3 border-top" style="border-color:var(--border-light)!important;">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
    @endif
    @endif
</div>
@endsection
