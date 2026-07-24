@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn-minimal btn-minimal-primary">Thêm tài khoản</a>
@endsection

@section('content')
<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">ID</th>
                    <th style="width: 50px;">Avatar</th>
                    <th>Tên hiển thị</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Xu</th>
                    <th>Vai trò</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $user->id }}</td>
                        <td>
                            <x-user-avatar :user="$user" size="32" />
                        </td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $user->display_name }}</div>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.775rem;">{{ $user->username }}</span>
                        </td>
                        <td>
                            <span class="text-secondary" style="font-size: 0.775rem;">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span class="fw-medium text-primary" style="font-size: 0.8rem;">{{ $user->points }}</span>
                        </td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge-minimal" style="background: #f5f3ff; color: #5b21b6; border: 1px solid #ede9fe;">Admin</span>
                            @elseif($user->role == 'moderator')
                                <span class="badge-minimal" style="background: #fffbeb; color: #b45309; border: 1px solid #fef3c7;">Mod</span>
                            @else
                                <span class="badge-minimal">User</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->status == 'active')
                                <span class="badge-minimal badge-minimal-success">Hoạt động</span>
                            @elseif($user->status == 'inactive')
                                <span class="badge-minimal">Chưa kích hoạt</span>
                            @else
                                <span class="badge-minimal" style="background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2;">Bị khóa</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">Xem</a>
                            @if(auth()->id() != $user->id)
                                @if($user->status == 'banned')
                                    <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-minimal py-1 px-2 text-success" style="font-size: 0.75rem;">Mở</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Khóa</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Chưa có người dùng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
