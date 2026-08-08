@extends('admin.layouts.app')

@section('title', 'Quản lý Bình luận')

@section('content')
<!-- AI Moderation Banner -->
<div class="card-minimal mb-3 p-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="fw-semibold text-dark d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                <i class="fas fa-robot"></i> Kiểm duyệt bằng AI
            </div>
            <div class="text-muted mt-1" style="font-size: 0.775rem;">
                AI quét bình luận và gắn cờ nội dung nghi ngờ / vi phạm để bạn xem xét nhanh.
                <span class="text-danger fw-medium">{{ $aiStats['violation'] }} vi phạm</span> ·
                <span class="fw-medium" style="color:#b45309;">{{ $aiStats['suspect'] }} nghi ngờ</span> ·
                <span class="text-secondary">{{ $aiStats['unchecked'] }} chưa quét</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($aiConfigured)
                <button type="button" id="btnScanAi" class="btn-minimal btn-minimal-primary" data-scope="unchecked">
                    <i class="fas fa-magic me-1"></i><span class="scan-label">Quét bình luận mới</span>
                </button>
                <button type="button" id="btnScanAll" class="btn-minimal" data-scope="all" title="Quét lại toàn bộ bình luận">
                    Quét lại tất cả
                </button>
            @else
                <span class="badge-minimal text-muted">Chưa cấu hình API Key AI</span>
            @endif
        </div>
    </div>
    <div id="scanProgress" class="text-muted mt-2 d-none" style="font-size: 0.775rem;">
        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
        Đang quét bằng AI, vui lòng đợi...
    </div>
</div>

<!-- Search & Filter Form Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo nội dung, tên user..." value="{{ request('search') }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                <option value="">-- Trạng thái --</option>
                <option value="visible" {{ request('status') == 'visible' ? 'selected' : '' }}>Hiển thị</option>
                <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đang ẩn</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="ai_flag" class="form-select form-select-sm" style="border-color: #e2e8f0;">
                <option value="">-- AI: Tất cả --</option>
                <option value="violation" {{ request('ai_flag') == 'violation' ? 'selected' : '' }}>Vi phạm</option>
                <option value="suspect" {{ request('ai_flag') == 'suspect' ? 'selected' : '' }}>Nghi ngờ</option>
                <option value="safe" {{ request('ai_flag') == 'safe' ? 'selected' : '' }}>An toàn</option>
                <option value="unchecked" {{ request('ai_flag') == 'unchecked' ? 'selected' : '' }}>Chưa quét</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn-minimal btn-minimal-primary flex-fill">Tìm kiếm</button>
            <a href="{{ route('admin.comments.index', ['sort_risk' => 1]) }}" class="btn-minimal flex-fill text-center text-decoration-none" title="Sắp xếp rủi ro cao lên đầu">Rủi ro cao</a>
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
                    <th class="text-center">AI</th>
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
                        <td class="text-center" style="min-width: 120px;">
                            @if($comment->ai_checked_at)
                                @php
                                    $flag = $comment->ai_flag;
                                    $badgeStyle = 'background:#f1f5f9;color:#64748b;';
                                    $label = 'An toàn';
                                    if ($flag === 'violation') { $badgeStyle = 'background:#fee2e2;color:#b91c1c;'; $label = 'Vi phạm'; }
                                    elseif ($flag === 'suspect') { $badgeStyle = 'background:#fef3c7;color:#b45309;'; $label = 'Nghi ngờ'; }
                                @endphp
                                <span class="badge-minimal" style="{{ $badgeStyle }} font-weight:500;">
                                    {{ $label }}@if(!is_null($comment->ai_score)) · {{ $comment->ai_score }}@endif
                                </span>
                                @if($comment->ai_reason)
                                    <div class="text-muted mt-1" style="font-size: 0.7rem; max-width: 200px; margin:0 auto;" title="{{ $comment->ai_reason }}">
                                        {{ Str::limit($comment->ai_reason, 60) }}
                                    </div>
                                @endif
                            @else
                                <span class="text-muted" style="font-size: 0.72rem;">Chưa quét</span>
                            @endif
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
                        <td colspan="8" class="text-center text-muted py-4">Chưa có bình luận nào.</td>
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

        // AI moderation scan
        const scanProgress = document.getElementById('scanProgress');
        const scanButtons = [document.getElementById('btnScanAi'), document.getElementById('btnScanAll')].filter(Boolean);

        function runScan(scope, triggerBtn) {
            if (!scanProgress) return;
            scanButtons.forEach(b => b.disabled = true);
            scanProgress.classList.remove('d-none');

            fetch('{{ route('admin.comments.scan_ai') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ scope: scope })
            })
            .then(res => res.json())
            .then(data => {
                scanProgress.classList.add('d-none');
                scanButtons.forEach(b => b.disabled = false);
                if (data.success) {
                    alert(data.message || 'Đã quét xong.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Không thể quét lúc này.');
                }
            })
            .catch(err => {
                console.error(err);
                scanProgress.classList.add('d-none');
                scanButtons.forEach(b => b.disabled = false);
                alert('Có lỗi khi quét AI. Vui lòng thử lại.');
            });
        }

        const btnScanAi = document.getElementById('btnScanAi');
        if (btnScanAi) {
            btnScanAi.addEventListener('click', () => runScan('unchecked', btnScanAi));
        }
        const btnScanAll = document.getElementById('btnScanAll');
        if (btnScanAll) {
            btnScanAll.addEventListener('click', () => {
                if (confirm('Quét lại TẤT CẢ bình luận (tối đa 60 mỗi lần)? Thao tác này sẽ ghi đè kết quả AI cũ.')) {
                    runScan('all', btnScanAll);
                }
            });
        }
    });
</script>
@endpush
@endsection
