@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')
<!-- Header & Greeting -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title">Tổng quan</h1>
        <p class="text-muted mb-0" style="font-size: 0.8rem; font-weight: 400;">Cập nhật hệ thống thông tin địa điểm Ninh Bình Travel Hub</p>
    </div>
    <div class="text-muted" style="font-size: 0.775rem;">
        Hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>
</div>

<!-- Continuous Horizontal Metric Strip -->
<div class="metric-strip mb-4">
    <div class="row g-0 align-items-center">
        <div class="col-6 col-md-3 metric-item">
            <div class="metric-label">Địa điểm du lịch</div>
            <div class="metric-value">{{ \App\Models\Location::count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item">
            <div class="metric-label">Tin tức & Sự kiện</div>
            <div class="metric-value">{{ \App\Models\News::count() + \App\Models\Event::count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
            <div class="metric-label">Người dùng</div>
            <div class="metric-value">{{ \App\Models\User::count() }}</div>
        </div>
        <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
            <div class="metric-label">Bình luận (7 ngày)</div>
            <div class="metric-value">{{ \App\Models\Comment::where('created_at', '>=', now()->subDays(7))->count() }}</div>
        </div>
    </div>
</div>

<!-- 2-Column Minimal Layout Grid -->
<div class="row g-3">
    <!-- Left Column: Recent Locations -->
    <div class="col-12 col-lg-7">
        <div class="card-minimal">
            <div class="card-header-minimal d-flex justify-content-between align-items-center">
                <span>Địa điểm vừa thêm gần đây</span>
                <a href="{{ route('admin.locations.index') }}" class="btn-minimal">Quản lý</a>
            </div>
            <div class="table-responsive">
                <table class="table table-minimal">
                    <thead>
                        <tr>
                            <th>Tên địa điểm</th>
                            <th>Danh mục</th>
                            <th class="text-end">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Location::with('category')->orderBy('created_at', 'desc')->take(5)->get() as $loc)
                        <tr>
                            <td>
                                <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $loc->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $loc->address ?? 'Ninh Bình' }}</div>
                            </td>
                            <td>
                                <span class="badge-minimal">
                                    {{ $loc->category->name ?? 'Mặc định' }}
                                </span>
                            </td>
                            <td class="text-end text-muted" style="font-size: 0.775rem;">
                                {{ $loc->created_at ? $loc->created_at->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu địa điểm.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Users -->
    <div class="col-12 col-lg-5">
        <div class="card-minimal">
            <div class="card-header-minimal d-flex justify-content-between align-items-center">
                <span>Tài khoản mới</span>
                <a href="{{ route('admin.users.index') }}" class="btn-minimal">Xem thêm</a>
            </div>
            <ul class="list-group list-group-flush border-0">
                @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $user)
                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2.5 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <x-user-avatar :user="$user" size="26" />
                        <div class="text-truncate">
                            <div class="text-dark" style="font-size: 0.8rem; font-weight: 500;">{{ $user->display_name ?? $user->username }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.725rem;">{{ $user->email }}</div>
                        </div>
                    </div>
                    <span class="text-muted flex-shrink-0 ms-2" style="font-size: 0.7rem;">
                        {{ $user->created_at->diffForHumans(null, true, true) }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@endsection
