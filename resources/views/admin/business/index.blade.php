@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu doanh nghiệp')

@section('content')
<!-- Metric & Filter Tabs -->
<div class="metric-strip mb-3 d-flex flex-wrap align-items-center justify-content-between p-0 overflow-hidden">
    <a href="{{ route('admin.business-profiles.index', ['status' => 'all', 'search' => $search]) }}" 
       class="metric-item flex-fill text-decoration-none py-3 px-4 transition-all {{ $status === 'all' ? 'bg-light border-bottom border-2' : '' }}" style="{{ $status === 'all' ? 'border-bottom-color:#1e3a5f!important;' : '' }}">
        <div class="metric-label">Tất cả yêu cầu</div>
        <div class="metric-value text-dark" style="font-size: 1.2rem;">{{ $counts['all'] }}</div>
    </a>
    <a href="{{ route('admin.business-profiles.index', ['status' => 'pending', 'search' => $search]) }}" 
       class="metric-item flex-fill text-decoration-none py-3 px-4 transition-all {{ $status === 'pending' ? 'bg-light border-bottom border-2' : '' }}" style="{{ $status === 'pending' ? 'border-bottom-color:#1e3a5f!important;' : '' }}">
        <div class="metric-label {{ $status === 'pending' ? 'text-dark fw-medium' : '' }}">Chờ phê duyệt</div>
        <div class="metric-value {{ $status === 'pending' ? 'text-dark' : '' }}" style="font-size:1.2rem;">{{ $counts['pending'] }}</div>
    </a>
    <a href="{{ route('admin.business-profiles.index', ['status' => 'approved', 'search' => $search]) }}" 
       class="metric-item flex-fill text-decoration-none py-3 px-4 transition-all {{ $status === 'approved' ? 'bg-light border-bottom border-2' : '' }}" style="{{ $status === 'approved' ? 'border-bottom-color:#1e3a5f!important;' : '' }}">
        <div class="metric-label {{ $status === 'approved' ? 'text-dark fw-medium' : '' }}">Đã duyệt</div>
        <div class="metric-value {{ $status === 'approved' ? 'text-dark' : '' }}" style="font-size:1.2rem;">{{ $counts['approved'] }}</div>
    </a>
    <a href="{{ route('admin.business-profiles.index', ['status' => 'rejected', 'search' => $search]) }}" 
       class="metric-item flex-fill text-decoration-none py-3 px-4 transition-all {{ $status === 'rejected' ? 'bg-light border-bottom border-2' : '' }}" style="{{ $status === 'rejected' ? 'border-bottom-color:#1e3a5f!important;' : '' }}">
        <div class="metric-label {{ $status === 'rejected' ? 'text-dark fw-medium' : '' }}">Đã từ chối</div>
        <div class="metric-value {{ $status === 'rejected' ? 'text-dark' : '' }}" style="font-size:1.2rem;">{{ $counts['rejected'] }}</div>
    </a>
</div>

<!-- Form Lọc & Tìm kiếm Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.business-profiles.index') }}" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm tên doanh nghiệp, SĐT, tài khoản..." value="{{ $search }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-minimal btn-minimal-primary w-100 py-1" style="font-size: 0.8rem;">Tìm kiếm</button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Tên Doanh Nghiệp</th>
                    <th>Chủ Tài Khoản</th>
                    <th>Danh Mục</th>
                    <th>SĐT / Địa Chỉ</th>
                    <th class="text-center">Trạng Thái</th>
                    <th>Ngày Gửi</th>
                    <th class="text-end pe-4" style="width: 100px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($businessProfiles as $index => $item)
                <tr>
                    <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $businessProfiles->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('admin.business-profiles.show', $item->id) }}" class="fw-medium text-dark text-decoration-none" style="font-size: 0.825rem;">
                            {{ $item->business_name }}
                        </a>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-user-avatar :user="$item->user" size="24" />
                            <div>
                                <div class="fw-medium text-dark" style="font-size: 0.8rem;">{{ $item->user->display_name ?? $item->user->username }}</div>
                                <div class="text-muted" style="font-size: 0.725rem;">{{ $item->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-minimal">
                            {{ $item->category ? $item->category->name : 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size: 0.8rem;">{{ $item->phone }}</div>
                        <div class="text-muted text-truncate" style="max-width: 220px; font-size: 0.725rem;" title="{{ $item->address_street }}, {{ $item->address_city }}">
                            {{ $item->address_street }}, {{ $item->address_city }}
                        </div>
                    </td>
                    <td class="text-center">
                        @if($item->status === 'pending')
                            <span class="badge-minimal badge-minimal-warning">Chờ duyệt</span>
                        @elseif($item->status === 'approved')
                            <span class="badge-minimal badge-minimal-success">Đã duyệt</span>
                        @elseif($item->status === 'rejected')
                            <span class="badge-minimal badge-minimal-danger" title="Lý do: {{ $item->reject_reason }}">Bị từ chối</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.business-profiles.show', $item->id) }}" class="btn-minimal py-1 px-2 text-decoration-none" style="font-size: 0.75rem;">
                            Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Không tìm thấy yêu cầu doanh nghiệp nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($businessProfiles->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $businessProfiles->links() }}
    </div>
    @endif
</div>
@endsection
