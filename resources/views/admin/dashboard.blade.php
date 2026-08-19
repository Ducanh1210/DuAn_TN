@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')
@php
    $pendingBiz = \App\Models\BusinessProfile::where('status', 'pending')->count();
    $pendingSuggestions = \App\Models\LocationSuggestion::where('status', 'pending')->count();
    $pendingReportLocations = \App\Models\Report::whereIn('reportable_type', \App\Models\Report::morphTypes(\App\Models\Location::class))->where('status', 'pending')->count();
    $pendingReportComments = \App\Models\Report::whereIn('reportable_type', \App\Models\Report::morphTypes(\App\Models\Comment::class))->where('status', 'pending')->count();
    $pendingFeedbacks = \App\Models\FeedbackReport::where('status', 'pending')->count();
    $pendingTasks = [
        ['label' => 'Duyệt doanh nghiệp', 'count' => $pendingBiz, 'url' => route('admin.business-profiles.index', ['status' => 'pending'])],
        ['label' => 'Đề xuất địa điểm', 'count' => $pendingSuggestions, 'url' => route('admin.contributions.index')],
        ['label' => 'Báo cáo địa điểm', 'count' => $pendingReportLocations, 'url' => route('admin.reports.index', ['tab' => 'locations'])],
        ['label' => 'Báo cáo bình luận', 'count' => $pendingReportComments, 'url' => route('admin.reports.index', ['tab' => 'comments'])],
        ['label' => 'Góp ý / báo lỗi', 'count' => $pendingFeedbacks, 'url' => route('admin.reports.index', ['tab' => 'feedbacks'])],
    ];
    $totalPending = collect($pendingTasks)->sum('count');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-header-title">Tổng quan</h1>
        <p class="text-muted mb-0" style="font-size:0.8rem;font-weight:400;">Cập nhật hệ thống thông tin địa điểm Ninh Bình Travel Hub</p>
    </div>
    <div class="text-muted" style="font-size:0.775rem;">
        Hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>
</div>

<div class="card-minimal mb-4">
    <div class="card-header-minimal d-flex justify-content-between align-items-center">
        <span>Việc cần xử lý</span>
        @if($totalPending > 0)
            <span class="badge-count">{{ $totalPending }}</span>
        @endif
    </div>
    <div class="p-3">
        <div class="row g-2">
            @foreach($pendingTasks as $task)
            <div class="col-md-6 col-lg-4">
                <a href="{{ $task['url'] }}" class="pending-task-link {{ $task['count'] > 0 ? 'has-pending' : '' }}">
                    <span>{{ $task['label'] }}</span>
                    @if($task['count'] > 0)
                        <span class="badge-count">{{ $task['count'] }}</span>
                    @else
                        <span class="text-muted" style="font-size:0.75rem;">0</span>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

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

<div class="row g-3">
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
                                <div class="fw-medium text-dark" style="font-size:0.825rem;">{{ $loc->name }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $loc->address ?? 'Ninh Bình' }}</div>
                            </td>
                            <td>
                                <span class="badge-minimal">{{ $loc->category->name ?? 'Mặc định' }}</span>
                            </td>
                            <td class="text-end text-muted" style="font-size:0.775rem;">
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

    <div class="col-12 col-lg-5">
        <div class="card-minimal">
            <div class="card-header-minimal d-flex justify-content-between align-items-center">
                <span>Tài khoản mới</span>
                <a href="{{ route('admin.users.index') }}" class="btn-minimal">Xem thêm</a>
            </div>
            <ul class="list-group list-group-flush border-0">
                @foreach(\App\Models\User::orderBy('created_at', 'desc')->take(5)->get() as $user)
                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2.5 border-bottom" style="border-color:#f1f5f9!important;">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <x-user-avatar :user="$user" size="26" />
                        <div class="text-truncate">
                            <div class="text-dark" style="font-size:0.8rem;font-weight:500;">{{ $user->display_name ?? $user->username }}</div>
                            <div class="text-muted text-truncate" style="font-size:0.725rem;">{{ $user->email }}</div>
                        </div>
                    </div>
                    <span class="text-muted flex-shrink-0 ms-2" style="font-size:0.7rem;">
                        {{ $user->created_at->diffForHumans(null, true, true) }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
