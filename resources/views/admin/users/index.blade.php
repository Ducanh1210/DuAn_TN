@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Người dùng</a>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th width="80">Avatar</th>
                        <th>Tên hiển thị</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                @if($user->avatar_url)
                                    <img src="{{ str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : asset('storage/' . $user->avatar_url) }}" alt="Avatar" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($user->display_name ?? $user->username, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $user->display_name }}</strong></td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->role == 'moderator')
                                    <span class="badge bg-warning text-dark">Moderator</span>
                                @else
                                    <span class="badge bg-info">User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @elseif($user->status == 'inactive')
                                    <span class="badge bg-secondary">Chưa kích hoạt</span>
                                @else
                                    <span class="badge bg-dark">Bị khóa</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info text-white" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                @if(auth()->id() != $user->id)
                                    @if($user->status == 'banned')
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Mở khóa"><i class="fas fa-unlock"></i></button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Khóa"><i class="fas fa-lock"></i></button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Chưa có người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
