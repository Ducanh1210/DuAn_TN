@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                @if($user->avatar_url)
                    <img src="{{ str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : asset($user->avatar_url) }}" alt="Avatar" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 64px;">
                        {{ strtoupper(substr($user->display_name ?? $user->username, 0, 1)) }}
                    </div>
                @endif
                <h4 class="font-weight-bold">{{ $user->display_name }}</h4>
                <p class="text-muted">{{ '@' . $user->username }}</p>
                
                <div class="mt-3">
                    @if($user->role == 'admin')
                        <span class="badge bg-danger fs-6 px-3 py-2">Admin</span>
                    @elseif($user->role == 'moderator')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Moderator</span>
                    @else
                        <span class="badge bg-info fs-6 px-3 py-2">User</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="30%">ID Tài khoản</th>
                            <td>#{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên hiển thị</th>
                            <td>{{ $user->display_name }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($user->status == 'inactive')
                                    <span class="badge bg-secondary">Chưa kích hoạt</span>
                                @else
                                    <span class="badge bg-dark">Bị khóa</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối</th>
                            <td>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Đăng nhập lần cuối</th>
                            <td>{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d/m/Y H:i:s') : 'Chưa từng đăng nhập' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
