@extends('admin.layouts.app')

@section('title', 'Quản lý Báo cáo vi phạm')

@section('content')
<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">ID</th>
                    <th>Người báo cáo</th>
                    <th>Đối tượng</th>
                    <th>Lý do</th>
                    <th>Chi tiết</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $report->id }}</td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $report->reporter->display_name ?? ($report->reporter->username ?? 'Unknown') }}</div>
                        </td>
                        <td>
                            @if($report->reportable)
                                @if(class_basename($report->reportable_type) === 'Comment')
                                    <span class="badge-minimal me-1">Bình luận</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ Str::limit($report->reportable->content, 35) }}</span>
                                @elseif(class_basename($report->reportable_type) === 'Location')
                                    <span class="badge-minimal me-1" style="background: #eff6ff; color: #1d4ed8;">Địa điểm</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $report->reportable->name }}</span>
                                @elseif(class_basename($report->reportable_type) === 'User')
                                    <span class="badge-minimal me-1" style="background: #fef3c7; color: #92400e;">User</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $report->reportable->display_name }}</span>
                                @else
                                    <span class="badge-minimal">{{ class_basename($report->reportable_type) }}</span>
                                @endif
                            @else
                                <span class="text-danger" style="font-size: 0.75rem;">Đã bị xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium text-secondary" style="font-size: 0.8rem;">{{ $report->reason }}</div>
                        </td>
                        <td>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $report->description ?? 'Không có' }}</div>
                        </td>
                        <td class="text-muted" style="font-size: 0.75rem;">
                            {{ $report->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <form action="{{ route('admin.reports.update_status', $report->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; border-color: #e2e8f0;">
                                    <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                                    <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            @if($report->reportable && $report->status === 'pending')
                                <form action="{{ route('admin.reports.delete_content', $report->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA nội dung vi phạm này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">
                                        Xóa vi phạm
                                    </button>
                                </form>
                            @else
                                <span class="text-muted" style="font-size: 0.75rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Chưa có báo cáo nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reports->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
