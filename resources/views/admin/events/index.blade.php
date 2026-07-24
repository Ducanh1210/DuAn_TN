@extends('admin.layouts.app')
@section('title', 'Quản lý Sự kiện')
@section('actions')
<a href="{{ route('admin.events.create') }}" class="btn-minimal btn-minimal-primary">Thêm sự kiện</a>
@endsection

@section('content')
<!-- Horizontal Metric Strip -->
<div class="metric-strip mb-3">
    <div class="row g-0 align-items-center">
        <div class="col-6 col-md-3 metric-item">
            <div class="metric-label">Tổng sự kiện</div>
            <div class="metric-value">{{ \App\Models\Event::count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item">
            <div class="metric-label">Đang diễn ra</div>
            <div class="metric-value">{{ \App\Models\Event::where('status','active')->count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
            <div class="metric-label">Nổi bật</div>
            <div class="metric-value">{{ \App\Models\Event::where('is_featured',true)->count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
            <div class="metric-label">Sắp diễn ra</div>
            <div class="metric-value">{{ \App\Models\Event::where('start_time','>=',now())->count() }}</div>
        </div>
    </div>
</div>

<!-- Filters Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tên sự kiện, mô tả..." value="{{ request('search') }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Đang diễn ra</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Đã hủy</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Đã kết thúc</option>
                <option value="hidden" {{ request('status')=='hidden'?'selected':'' }}>Đã ẩn</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn-minimal btn-minimal-primary flex-fill">Lọc</button>
            <a href="{{ route('admin.events.index') }}" class="btn-minimal text-decoration-none px-3 text-center">Làm mới</a>
        </div>
    </form>
</div>

<!-- Table Minimalist -->
<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">ID</th>
                    <th style="width: 60px;">Ảnh</th>
                    <th>Tên sự kiện</th>
                    <th>Thời gian</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center">Nổi bật</th>
                    <th>Người tạo</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $ev)
                <tr>
                    <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $ev->id }}</td>
                    <td>
                        @if($ev->featured_image)
                            <img src="{{ asset('storage/' . $ev->featured_image) }}" class="rounded" style="width: 48px; height: 32px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 32px; font-size: 0.65rem;">No Img</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-medium text-dark" style="font-size: 0.825rem; max-width: 260px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $ev->name }}</div>
                        <div class="text-muted" style="font-size: 0.725rem; max-width: 260px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $ev->location_text ?? ($ev->location->name ?? '—') }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.75rem;" class="text-muted">{{ $ev->start_time->format('d/m/Y H:i') }}</div>
                        <div style="font-size: 0.75rem;" class="text-muted">{{ $ev->end_time->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="text-center">
                        @if($ev->status == 'active')
                            <span class="badge-minimal badge-minimal-success">Đang diễn ra</span>
                        @elseif($ev->status == 'expired')
                            <span class="badge-minimal">Kết thúc</span>
                        @elseif($ev->status == 'cancelled')
                            <span class="badge-minimal" style="background: #fef2f2; color: #991b1b;">Đã hủy</span>
                        @else
                            <span class="badge-minimal">{{ $ev->status_label }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($ev->is_featured)
                            <span class="badge-minimal" style="background: #fffbeb; color: #b45309;">Có</span>
                        @else
                            <span class="text-muted" style="font-size: 0.75rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted" style="font-size: 0.775rem;">{{ $ev->creator->display_name ?? $ev->creator->username ?? '—' }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.events.edit', $ev->id) }}" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">Sửa</a>
                        <form action="{{ route('admin.events.toggle', $ev->id) }}" method="POST" class="d-inline me-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-minimal py-1 px-2" style="font-size: 0.75rem;">
                                {{ $ev->status === 'active' ? 'Ẩn' : 'Hiện' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.events.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa sự kiện này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Chưa có sự kiện nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($events->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection
