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
                <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=0072FF&color=fff';" alt="Avatar" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
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

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thay đổi điểm số</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.adjust_points', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Số điểm (sử dụng số âm để trừ)</label>
                        <input type="number" name="amount" class="form-control form-control-sm" placeholder="Ví dụ: 50 hoặc -20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Lý do thay đổi</label>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Lý do thay đổi điểm" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Cập nhật điểm</button>
                </form>
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
                            <th>Điểm tích lũy</th>
                            <td><strong class="text-primary fs-5">{{ $user->points }}</strong> điểm</td>
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

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lịch sử giao dịch điểm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                                <th>Số điểm</th>
                                <th>Nội dung chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->pointTransactions as $tx)
                                <tr>
                                    <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($tx->action === 'daily_login')
                                            <span class="badge bg-success bg-opacity-10 text-success">Điểm danh</span>
                                        @elseif($tx->action === 'comment')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Bình luận</span>
                                        @elseif($tx->action === 'favorite')
                                            <span class="badge bg-info bg-opacity-10 text-info">Yêu thích</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Khác</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->amount >= 0 ? '+' : '' }}{{ $tx->amount }}
                                    </td>
                                    <td>{{ $tx->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Chưa có giao dịch điểm nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
