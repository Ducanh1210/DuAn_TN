@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn-minimal">Quay lại</a>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card-minimal p-4 text-center mb-3">
            <div class="d-flex justify-content-center mb-2">
                <x-user-avatar :user="$user" size="72" />
            </div>
            <h5 class="fw-semibold text-dark mb-0" style="font-size: 1.05rem;">{{ $user->display_name }}</h5>
            <div class="text-muted small mb-2">{{ '@' . $user->username }}</div>
            
            <div>
                @if($user->role == 'admin')
                    <span class="badge-minimal" style="background: #f5f3ff; color: #5b21b6; border: 1px solid #ede9fe;">Admin</span>
                @elseif($user->role == 'moderator')
                    <span class="badge-minimal" style="background: #fffbeb; color: #b45309; border: 1px solid #fef3c7;">Moderator</span>
                @else
                    <span class="badge-minimal">User</span>
                @endif
            </div>
        </div>

        <div class="card-minimal p-3">
            <div class="fw-medium text-dark mb-3 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Thay đổi điểm số</div>
            <form action="{{ route('admin.users.adjust_points', $user->id) }}" method="POST" novalidate>
                @csrf
                @method('PATCH')
                <div class="mb-2">
                    <label class="form-label text-muted" style="font-size: 0.775rem;">Số điểm (dùng số âm để trừ)</label>
                    <input type="number" name="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror" placeholder="Ví dụ: 50 hoặc -20" value="{{ old('amount') }}" style="border-color: #e2e8f0;">
                    @error('amount')
                        <div class="invalid-feedback d-block mt-1" style="font-size: 0.725rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted" style="font-size: 0.775rem;">Lý do thay đổi</label>
                    <input type="text" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Lý do thay đổi điểm" value="{{ old('description') }}" style="border-color: #e2e8f0;">
                    @error('description')
                        <div class="invalid-feedback d-block mt-1" style="font-size: 0.725rem;">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn-minimal btn-minimal-primary w-100">Cập nhật điểm</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card-minimal p-3 mb-3">
            <div class="fw-medium text-dark mb-2 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Thông tin chi tiết</div>
            <table class="table table-borderless table-sm mb-0 align-middle">
                <tbody>
                    <tr>
                        <th width="30%" class="text-muted fw-normal" style="font-size: 0.8rem;">ID Tài khoản</th>
                        <td class="fw-medium text-dark" style="font-size: 0.8rem;">#{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Tên hiển thị</th>
                        <td class="fw-medium text-dark" style="font-size: 0.8rem;">{{ $user->display_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Username</th>
                        <td class="text-secondary" style="font-size: 0.8rem;">{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Email</th>
                        <td class="text-secondary" style="font-size: 0.8rem;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Điểm tích lũy</th>
                        <td class="fw-medium text-primary" style="font-size: 0.85rem;">{{ $user->points }} điểm</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Trạng thái</th>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge-minimal badge-minimal-success">Hoạt động</span>
                            @elseif($user->status == 'inactive')
                                <span class="badge-minimal">Chưa kích hoạt</span>
                            @else
                                <span class="badge-minimal" style="background: #fef2f2; color: #991b1b;">Bị khóa</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Ngày tạo</th>
                        <td class="text-muted" style="font-size: 0.8rem;">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal" style="font-size: 0.8rem;">Cập nhật lần cuối</th>
                        <td class="text-muted" style="font-size: 0.8rem;">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-minimal p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color:var(--border-light)!important;">
                <div class="fw-medium text-dark" style="font-size:0.85rem;">Lịch sử giao dịch điểm</div>
                <span class="text-muted" style="font-size:0.72rem;">
                    {{ $pointHistory->total() }} mục
                    @if(($historyData['raw_total'] ?? 0) > $pointHistory->total())
                        · gộp từ {{ $historyData['raw_total'] }} bản ghi
                    @endif
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-minimal align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                            <th>Số điểm</th>
                            <th>Nội dung chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointHistory as $tx)
                            <tr>
                                <td class="text-muted" style="font-size:0.775rem;">{{ $tx['created_at']->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge-minimal">
                                        {{ \App\Services\PointService::actionLabel($tx['action']) }}
                                    </span>
                                    @if(!empty($tx['aggregated']))
                                        <span class="text-muted" style="font-size:0.68rem;"> · gộp</span>
                                    @endif
                                </td>
                                <td class="fw-medium {{ $tx['amount'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.8rem;">
                                    {{ $tx['amount'] >= 0 ? '+' : '' }}{{ $tx['amount'] }}
                                </td>
                                <td class="text-muted" style="font-size:0.775rem;max-width:280px;white-space:normal;">{{ $tx['description'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Chưa có giao dịch điểm nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pointHistory->hasPages())
            <div class="p-2 border-top">{{ $pointHistory->links('pagination::bootstrap-5') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
