@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title">Overview</h1>
    <div class="text-muted" style="font-size: 0.875rem;">
        <i class="far fa-calendar-alt me-1"></i> Today is {{ \Carbon\Carbon::now()->format('F j, Y') }}
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stat Card 1 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.8125rem;">Tổng Địa Điểm</h6>
                        <h3 class="fw-bold mb-0 text-dark tracking-tight">{{ \App\Models\Location::count() }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-blue-50 text-blue-600">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-medium" style="font-size: 0.75rem;">
                        <i class="fas fa-arrow-up me-1"></i> 12%
                    </span>
                    <span class="text-muted ms-2" style="font-size: 0.75rem;">vs tháng trước</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.8125rem;">Tin Tức & Sự Kiện</h6>
                        <h3 class="fw-bold mb-0 text-dark tracking-tight">{{ \App\Models\News::count() + \App\Models\Event::count() }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-emerald-50 text-emerald-600">
                        <i class="fas fa-newspaper"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-medium" style="font-size: 0.75rem;">
                        <i class="fas fa-arrow-up me-1"></i> 4%
                    </span>
                    <span class="text-muted ms-2" style="font-size: 0.75rem;">vs tháng trước</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.8125rem;">Người Dùng</h6>
                        <h3 class="fw-bold mb-0 text-dark tracking-tight">{{ \App\Models\User::count() }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-amber-50 text-amber-600">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-medium" style="font-size: 0.75rem;">
                        <i class="fas fa-arrow-up me-1"></i> 22%
                    </span>
                    <span class="text-muted ms-2" style="font-size: 0.75rem;">vs tháng trước</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.8125rem;">Bình Luận (7 ngày)</h6>
                        <h3 class="fw-bold mb-0 text-dark tracking-tight">{{ \App\Models\Comment::where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-rose-50 text-rose-600">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 fw-medium" style="font-size: 0.75rem;">
                        <i class="fas fa-arrow-down me-1"></i> 5%
                    </span>
                    <span class="text-muted ms-2" style="font-size: 0.75rem;">vs tuần trước</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart / Main Area -->
    <div class="col-12 col-lg-8">
        <div class="card panel-card h-100">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">Hoạt động gần đây</h6>
                <button class="btn btn-sm btn-light text-muted border-0 rounded" style="background-color: #f1f5f9;">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-center w-100 h-100 rounded-3" style="min-height: 320px; background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                    <div class="text-center">
                        <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background-color: #f1f5f9; color: #94a3b8;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h6 class="fw-semibold text-dark mb-1">Chưa có dữ liệu biểu đồ</h6>
                        <p class="text-muted small mb-0">Dữ liệu thống kê sẽ sớm được cập nhật.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Area -->
    <div class="col-12 col-lg-4">
        <div class="card panel-card h-100">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">Người dùng mới</h6>
                <a href="{{ route('admin.users.index') }}" class="text-primary text-decoration-none small fw-semibold">Xem tất cả</a>
            </div>
            <div class="card-body px-0 pt-2 pb-0">
                <ul class="list-group list-group-flush">
                    @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $user)
                    <li class="list-group-item px-4 py-3 border-bottom-0 user-item d-flex align-items-center transition-all">
                        <div class="avatar me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                             style="width: 40px; height: 40px; font-size: 0.95rem; background-color: #e2e8f0; color: #475569;">
                            {{ strtoupper(substr($user->display_name ?? $user->username ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 fw-semibold text-dark text-truncate" style="font-size: 0.875rem;">{{ $user->display_name ?? $user->username }}</h6>
                            <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ $user->email }}</small>
                        </div>
                        <div class="ms-3 text-end">
                            <span class="text-muted small" style="font-size: 0.7rem;">
                                {{ $user->created_at->diffForHumans(null, true, true) }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .tracking-tight { letter-spacing: -0.025em; }
    
    .card {
        border-radius: 0.75rem;
        background-color: #ffffff;
    }
    
    .stat-card {
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
    }
    
    .stat-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .panel-card {
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    .stat-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    /* Tailwind color approximations */
    .bg-blue-50 { background-color: #eff6ff; }
    .text-blue-600 { color: #2563eb; }
    .bg-emerald-50 { background-color: #ecfdf5; }
    .text-emerald-600 { color: #059669; }
    .bg-amber-50 { background-color: #fffbeb; }
    .text-amber-600 { color: #d97706; }
    .bg-rose-50 { background-color: #fff1f2; }
    .text-rose-600 { color: #e11d48; }
    
    .user-item:hover {
        background-color: #f8fafc;
    }
    
    .transition-all {
        transition: all 0.15s ease-in-out;
    }
</style>
@endpush
