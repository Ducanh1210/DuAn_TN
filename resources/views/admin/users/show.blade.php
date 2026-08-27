@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn-minimal">Quay lại</a>
@endsection

@section('content')
<div class="card-minimal">
    <div class="p-4">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: var(--border-light) !important;">
            <x-user-avatar :user="$user" size="56" />
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="fw-semibold text-dark" style="font-size: 1.05rem;">{{ $user->display_name }}</span>
                    @if($user->role == 'admin')
                        <span class="badge-minimal" style="background:#f5f3ff;color:#5b21b6;border:1px solid #ede9fe;">Admin</span>
                    @elseif($user->role == 'moderator')
                        <span class="badge-minimal" style="background:#fffbeb;color:#b45309;border:1px solid #fef3c7;">Moderator</span>
                    @else
                        <span class="badge-minimal">User</span>
                    @endif
                    @if($user->status == 'active')
                        <span class="badge-minimal badge-minimal-success">Hoạt động</span>
                    @elseif($user->status == 'inactive')
                        <span class="badge-minimal">Chưa kích hoạt</span>
                    @else
                        <span class="badge-minimal" style="background:#fef2f2;color:#991b1b;">Bị khóa</span>
                    @endif
                </div>
                <div class="text-muted" style="font-size: 0.82rem;">{{ '@' . $user->username }}</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">ID tài khoản</div>
                <div class="text-dark" style="font-size: 0.88rem;">#{{ $user->id }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Tên hiển thị</div>
                <div class="text-dark" style="font-size: 0.88rem;">{{ $user->display_name }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Username</div>
                <div class="text-secondary" style="font-size: 0.88rem;">{{ $user->username }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Email</div>
                <div class="text-secondary text-break" style="font-size: 0.88rem;">{{ $user->email }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Điểm</div>
                <div class="text-secondary" style="font-size: 0.88rem;">{{ $user->points }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Trạng thái</div>
                <div>
                    @if($user->status == 'active')
                        <span class="badge-minimal badge-minimal-success">Hoạt động</span>
                    @elseif($user->status == 'inactive')
                        <span class="badge-minimal">Chưa kích hoạt</span>
                    @else
                        <span class="badge-minimal" style="background:#fef2f2;color:#991b1b;">Bị khóa</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Ngày tạo</div>
                <div class="text-secondary" style="font-size: 0.88rem;">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="text-muted mb-1" style="font-size: 0.72rem;">Cập nhật lần cuối</div>
                <div class="text-secondary" style="font-size: 0.88rem;">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
