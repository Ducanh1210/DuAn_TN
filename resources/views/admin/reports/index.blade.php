@extends('admin.layouts.app')

@section('title', 'Quản lý Báo cáo vi phạm')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Báo cáo vi phạm</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Người báo cáo</th>
                        <th>Đối tượng bị báo cáo</th>
                        <th>Lý do</th>
                        <th>Chi tiết</th>
                        <th width="120">Ngày gửi</th>
                        <th width="150">Trạng thái</th>
                        <th width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>
                                <strong>{{ $report->reporter->display_name ?? ($report->reporter->username ?? 'Unknown') }}</strong>
                            </td>
                            <td>
                                @if($report->reportable)
                                    @if(class_basename($report->reportable_type) === 'Comment')
                                        <span class="badge bg-info">Bình luận</span><br>
                                        <small>{{ Str::limit($report->reportable->content, 50) }}</small>
                                    @elseif(class_basename($report->reportable_type) === 'Location')
                                        <span class="badge bg-primary">Địa điểm</span><br>
                                        <small>{{ $report->reportable->name }}</small>
                                    @elseif(class_basename($report->reportable_type) === 'User')
                                        <span class="badge bg-warning text-dark">Người dùng</span><br>
                                        <small>{{ $report->reportable->display_name }}</small>
                                    @else
                                        <span class="badge bg-secondary">{{ class_basename($report->reportable_type) }}</span>
                                    @endif
                                @else
                                    <span class="text-danger">Đã bị xóa</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $report->reason }}</strong>
                            </td>
                            <td>
                                {{ $report->description ?? 'Không có' }}
                            </td>
                            <td>
                                <small>{{ $report->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <form action="{{ route('admin.reports.update_status', $report->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                                        <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                    </select>
                                </form>
                                @if($report->handled_by)
                                    <small class="text-muted d-block mt-1">Bởi: {{ $report->handler->display_name ?? 'Admin' }}</small>
                                @endif
                            </td>
                            <td>
                                @if($report->reportable && $report->status === 'pending')
                                    <form action="{{ route('admin.reports.delete_content', $report->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA nội dung vi phạm này? (Sẽ đánh dấu báo cáo là Đã xử lý)');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-100" title="Xóa nội dung vi phạm">
                                            <i class="fas fa-trash"></i> Xóa vi phạm
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary w-100" disabled>Không thể xóa</button>
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
        
        <div class="d-flex justify-content-center mt-3">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
