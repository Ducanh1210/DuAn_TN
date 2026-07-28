@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu doanh nghiệp')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <p class="text-muted small mb-0">Xem và phê duyệt các yêu cầu đăng ký tài khoản doanh nghiệp từ người dùng</p>
    </div>
</div>

<!-- Metric & Filter Tabs -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.business-profiles.index', ['status' => 'all', 'search' => $search]) }}" 
           class="card-minimal text-decoration-none p-3 d-block transition-all {{ $status === 'all' ? 'border-primary shadow-sm bg-light' : '' }}">
            <div class="metric-label">Tất cả yêu cầu</div>
            <div class="metric-value text-dark">{{ $counts['all'] }}</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.business-profiles.index', ['status' => 'pending', 'search' => $search]) }}" 
           class="card-minimal text-decoration-none p-3 d-block transition-all {{ $status === 'pending' ? 'border-warning shadow-sm bg-warning bg-opacity-10' : '' }}">
            <div class="metric-label text-warning font-weight-bold">Chờ phê duyệt</div>
            <div class="metric-value text-warning fw-bold">{{ $counts['pending'] }}</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.business-profiles.index', ['status' => 'approved', 'search' => $search]) }}" 
           class="card-minimal text-decoration-none p-3 d-block transition-all {{ $status === 'approved' ? 'border-success shadow-sm bg-success bg-opacity-10' : '' }}">
            <div class="metric-label text-success">Đã duyệt</div>
            <div class="metric-value text-success">{{ $counts['approved'] }}</div>
        </a>
    </div>
    <div class="col-md-3 col-6">
        <a href="{{ route('admin.business-profiles.index', ['status' => 'rejected', 'search' => $search]) }}" 
           class="card-minimal text-decoration-none p-3 d-block transition-all {{ $status === 'rejected' ? 'border-danger shadow-sm bg-danger bg-opacity-10' : '' }}">
            <div class="metric-label text-danger">Đã từ chối</div>
            <div class="metric-value text-danger">{{ $counts['rejected'] }}</div>
        </a>
    </div>
</div>

<!-- Search & Table Card -->
<div class="card-minimal">
    <div class="card-header-minimal d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="fw-semibold">Danh sách doanh nghiệp ({{ $businessProfiles->total() }})</div>
        
        <form action="{{ route('admin.business-profiles.index') }}" method="GET" class="d-flex gap-2 m-0" style="max-width: 320px;">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên, SĐT, tài khoản..." value="{{ $search }}">
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Tên doanh nghiệp</th>
                    <th>Chủ tài khoản</th>
                    <th>Danh mục</th>
                    <th>Số điện thoại / Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th style="width: 140px; text-align: right;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($businessProfiles as $index => $item)
                <tr>
                    <td>{{ $businessProfiles->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('admin.business-profiles.show', $item->id) }}" class="fw-semibold text-primary text-decoration-none">
                            {{ $item->business_name }}
                        </a>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-user-avatar :user="$item->user" size="24" />
                            <div>
                                <div class="fw-semibold small">{{ $item->user->display_name ?? $item->user->username }}</div>
                                <div class="text-muted" style="font-size: 0.725rem;">{{ $item->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $item->category ? $item->category->name : 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="small fw-semibold"><i class="fas fa-phone me-1 text-muted"></i>{{ $item->phone }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ $item->address_street }}, {{ $item->address_city }}">
                            {{ $item->address_street }}, {{ $item->address_city }}
                        </div>
                    </td>
                    <td>
                        @if($item->status === 'pending')
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock me-1"></i>Chờ duyệt</span>
                        @elseif($item->status === 'approved')
                            <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Đã duyệt</span>
                        @elseif($item->status === 'rejected')
                            <span class="badge bg-danger px-2 py-1" title="Lý do: {{ $item->reject_reason }}"><i class="fas fa-times-circle me-1"></i>Bị từ chối</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted small">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.business-profiles.show', $item->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Xem chi tiết & duyệt">
                            <i class="fas fa-eye me-1"></i>Chi tiết
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-building fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0 small">Không tìm thấy yêu cầu doanh nghiệp nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($businessProfiles->hasPages())
    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
        {{ $businessProfiles->links() }}
    </div>
    @endif
</div>
@endsection
