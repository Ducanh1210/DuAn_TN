@extends('admin.layouts.app')

@section('title', 'Quản lý Bình luận')

@section('content')
<!-- Search & Filter Form Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo nội dung, tên user..." value="{{ request('search') }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="visible" {{ request('status') == 'visible' ? 'selected' : '' }}>Hiển thị</option>
                <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn-minimal btn-minimal-primary flex-fill">Tìm kiếm</button>
            <a href="{{ route('admin.comments.index') }}" class="btn-minimal flex-fill text-center text-decoration-none">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">ID</th>
                    <th>Người dùng</th>
                    <th>Địa điểm</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $comment)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $comment->id }}</td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $comment->user->display_name ?? ($comment->user->username ?? 'Unknown') }}</div>
                            <div class="text-muted" style="font-size: 0.725rem;">{{ $comment->user->email ?? '' }}</div>
                        </td>
                        <td>
                            @if($comment->location)
                                <a href="{{ route('client.locations.360', $comment->location->slug) }}" target="_blank" class="text-decoration-none text-primary" style="font-size: 0.8rem;">
                                    {{ $comment->location->name }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size: 0.75rem;">Địa điểm đã xóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-secondary" style="font-size: 0.8rem; max-width: 320px;">
                                {{ Str::limit($comment->content, 90) }}
                            </div>
                        </td>
                        <td class="text-muted" style="font-size: 0.75rem;">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $comment->id }}" {{ $comment->status === 'visible' ? 'checked' : '' }} style="cursor: pointer;">
                                <div id="status-label-{{ $comment->id }}" class="mt-1">
                                    {!! $comment->status === 'visible' ? '<span class="badge-minimal badge-minimal-success">Hiển thị</span>' : '<span class="badge-minimal">Đang ẩn</span>' !!}
                                </div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có bình luận nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($comments->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $comments->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtns = document.querySelectorAll('.toggle-status');
        
        toggleBtns.forEach(btn => {
            btn.addEventListener('change', function() {
                const commentId = this.dataset.id;
                const label = document.getElementById('status-label-' + commentId);
                
                fetch(`/admin/comments/${commentId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'visible') {
                            label.innerHTML = '<span class="badge-minimal badge-minimal-success">Hiển thị</span>';
                        } else {
                            label.innerHTML = '<span class="badge-minimal">Đang ẩn</span>';
                        }
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
@endpush
@endsection
