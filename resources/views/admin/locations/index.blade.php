@extends('admin.layouts.app')

@section('title', 'Quản lý Địa điểm')

@section('actions')
    @unless($trash)
        <a href="{{ route('admin.locations.create', request()->query()) }}" class="btn-minimal btn-minimal-primary">Thêm địa điểm</a>
    @endunless
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between gap-2 mb-3">
    <a href="{{ route('admin.locations.index', request()->except(['trash', 'page'])) }}"
       class="btn-minimal py-1 px-3 text-decoration-none {{ !$trash ? 'btn-minimal-primary' : '' }}"
       style="font-size:0.78rem;{{ !$trash ? 'color:#fff;' : '' }}">
        Đang hoạt động ({{ $activeCount }})
    </a>
    <a href="{{ route('admin.locations.index', array_merge(request()->except(['page']), ['trash' => 1])) }}"
       class="btn-minimal py-1 px-3 text-decoration-none {{ $trash ? 'btn-minimal-primary' : '' }}"
       style="font-size:0.78rem;{{ $trash ? 'color:#fff;' : '' }}">
        Thùng rác ({{ $trashedCount }})
    </a>
</div>

<!-- Form Lọc & Tìm kiếm Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.locations.index') }}" method="GET" class="row g-2 align-items-center">
        @if($trash)
            <input type="hidden" name="trash" value="1">
        @endif
        <div class="col-md-7">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nhập tên địa điểm cần tìm..." value="{{ request('search') }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-5">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()" style="border-color: #e2e8f0;">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_dir' => $sortDir === 'desc' ? 'asc' : 'desc', 'page' => null]) }}" class="text-muted text-decoration-none">
                            ID {{ $sortDir === 'asc' ? '↑' : '↓' }}
                        </a>
                    </th>
                    <th style="width: 60px;">Ảnh</th>
                    <th>Tên Địa điểm</th>
                    <th>Danh mục</th>
                    <th>{{ $trash ? 'Ngày gỡ' : 'Tọa độ' }}</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $item)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $item->id }}</td>
                        <td>
                            @if($item->thumbnail_url)
                                <img src="{{ asset('storage/' . $item->thumbnail_url) }}" class="rounded" style="width: 48px; height: 32px; object-fit: cover;" alt="{{ $item->name }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 32px; font-size: 0.65rem;">No Img</div>
                            @endif
                        </td>
                        @php $isBizLoc = $item->created_by && in_array($item->created_by, $businessOwnerIds); @endphp
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">
                                {{ $item->name }}
                                @if($isBizLoc)
                                    <span class="badge-minimal" style="background:#eef2ff;color:#4338ca;font-weight:500;font-size:0.65rem;">Doanh nghiệp</span>
                                @endif
                            </div>
                            <div class="text-muted text-truncate" style="max-width: 260px; font-size: 0.725rem;">{{ $item->address ?? 'Ninh Bình' }}</div>
                        </td>
                        <td>
                            <span class="badge-minimal">
                                {{ $item->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($trash)
                                <span class="text-muted" style="font-size: 0.75rem;">{{ optional($item->deleted_at)->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $item->lat }}, {{ $item->lng }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($trash)
                                <span class="badge-minimal" style="background:#fef2f2;color:#b91c1c;">Đã gỡ tạm</span>
                            @elseif($item->status == 'published')
                                <span class="badge-minimal badge-minimal-success">Công khai</span>
                            @elseif($item->status == 'draft')
                                <span class="badge-minimal">Bản nháp</span>
                            @else
                                <span class="badge-minimal">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($trash)
                                <form action="{{ route('admin.locations.restore', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem; color:#15803d;">Khôi phục</button>
                                </form>
                                @if($isBizLoc)
                                    <button type="button" class="btn-minimal py-1 px-2 text-danger btn-force-delete-biz" style="font-size: 0.75rem;"
                                        data-action="{{ route('admin.locations.force_destroy', $item->id) }}"
                                        data-name="{{ $item->name }}">Xóa vĩnh viễn</button>
                                @else
                                    <form action="{{ route('admin.locations.force_destroy', $item->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Xóa VĨNH VIỄN địa điểm này? Không thể khôi phục. File ảnh/360 cũng sẽ bị xóa.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa vĩnh viễn</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('admin.locations.edit', [$item->id] + request()->query()) }}" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">Sửa</a>
                                @if($isBizLoc)
                                    <button type="button" class="btn-minimal py-1 px-2 text-danger btn-delete-biz" style="font-size: 0.75rem;"
                                        data-action="{{ route('admin.locations.destroy', $item->id) }}"
                                        data-name="{{ $item->name }}">Xóa</button>
                                @else
                                    <form action="{{ route('admin.locations.destroy', $item->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ $trash ? 'Thùng rác trống.' : 'Chưa có địa điểm nào.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locations->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $locations->links() }}
    </div>
    @endif
</div>

<!-- Modal: Xóa tạm địa điểm doanh nghiệp (bắt buộc nhập lý do) -->
<div class="modal fade" id="deleteBizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteBizForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size: 1rem;">Xóa địa điểm doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary mb-2" style="font-size: 0.85rem;">
                        Bạn có chắc chắn muốn xóa địa điểm <strong id="deleteBizName"></strong>?
                    </p>
                    <p class="text-secondary mb-3" style="font-size: 0.82rem;">
                        Vui lòng nhập <strong>lý do xóa</strong>. Lý do sẽ được gửi thông báo đến tài khoản doanh nghiệp.
                    </p>
                    <label class="form-label" style="font-size: 0.82rem;">Lý do xóa <span class="text-danger">*</span></label>
                    <textarea name="delete_reason" id="deleteBizReason" class="form-control form-control-sm" rows="4" maxlength="1000" required placeholder="Ví dụ: Địa điểm không còn hoạt động / thông tin sai lệch / vi phạm quy định..."></textarea>
                    <div class="invalid-feedback" id="deleteBizReasonError">Vui lòng nhập lý do.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal text-danger" style="border-color:#fecaca;">Xóa & Thông báo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Xóa vĩnh viễn địa điểm doanh nghiệp trong thùng rác -->
<div class="modal fade" id="forceDeleteBizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="forceDeleteBizForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size: 1rem;">Xóa vĩnh viễn địa điểm doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary mb-2" style="font-size: 0.85rem;">
                        Bạn sắp <strong class="text-danger">xóa vĩnh viễn</strong> địa điểm <strong id="forceDeleteBizName"></strong>.
                        Không thể khôi phục; ảnh và tour 360° cũng sẽ bị xóa.
                    </p>
                    <label class="form-label" style="font-size: 0.82rem;">Lý do xóa vĩnh viễn <span class="text-danger">*</span></label>
                    <textarea name="delete_reason" id="forceDeleteBizReason" class="form-control form-control-sm" rows="4" maxlength="1000" required placeholder="Nhập lý do..."></textarea>
                    <div class="invalid-feedback">Vui lòng nhập lý do.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal text-danger" style="border-color:#fecaca;">Xóa vĩnh viễn</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function bindReasonModal(modalId, formId, nameId, reasonId, btnSelector) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById(formId);
            const nameEl = document.getElementById(nameId);
            const reasonEl = document.getElementById(reasonId);

            document.querySelectorAll(btnSelector).forEach(btn => {
                btn.addEventListener('click', function() {
                    form.action = this.dataset.action;
                    nameEl.textContent = this.dataset.name || '';
                    reasonEl.value = '';
                    reasonEl.classList.remove('is-invalid');
                    modal.show();
                });
            });

            form.addEventListener('submit', function(e) {
                if (!reasonEl.value.trim()) {
                    e.preventDefault();
                    reasonEl.classList.add('is-invalid');
                }
            });
        }

        bindReasonModal('deleteBizModal', 'deleteBizForm', 'deleteBizName', 'deleteBizReason', '.btn-delete-biz');
        bindReasonModal('forceDeleteBizModal', 'forceDeleteBizForm', 'forceDeleteBizName', 'forceDeleteBizReason', '.btn-force-delete-biz');
    });
</script>
@endpush
