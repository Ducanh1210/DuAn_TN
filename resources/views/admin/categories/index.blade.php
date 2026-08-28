@extends('admin.layouts.app')

@section('title', 'Quản lý Danh mục')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn-minimal btn-minimal-primary">Thêm danh mục</a>
@endsection

@section('content')
<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th width="60" class="text-center">ID</th>
                    <th width="60" class="text-center">Icon</th>
                    <th>Tên Danh mục</th>
                    <th>Slug</th>
                    <th width="80" class="text-center">Thứ tự</th>
                    <th width="110" class="text-center">Trạng thái</th>
                    <th width="140" class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $item)
                    <tr>
                        <td class="text-center text-muted" style="font-size: 0.775rem;">{{ $item->id }}</td>
                        <td class="text-center">
                            @if($item->icon)
                                <div class="d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <img src="{{ asset($item->icon) }}" alt="Icon" style="height: 22px; width: 22px; object-fit: contain;">
                                </div>
                            @else
                                <span class="text-muted" style="font-size: 0.75rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $item->name }}</div>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.775rem;">{{ $item->slug }}</span>
                        </td>
                        <td class="text-center text-muted" style="font-size: 0.8rem;">{{ $item->display_order }}</td>
                        <td class="text-center">
                            @if($item->status == 'active')
                                <span class="badge-minimal badge-minimal-success">Hiển thị</span>
                            @else
                                <span class="badge-minimal">Đang ẩn</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.categories.edit', $item->id) }}" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">Sửa</a>
                            @if(($item->locations_count ?? 0) > 0)
                                <button type="button" class="btn-minimal py-1 px-2 text-muted" style="font-size: 0.75rem; cursor: not-allowed; opacity: 0.5;" title="Danh mục này đang chứa {{ $item->locations_count }} địa điểm du lịch, không thể xóa" disabled>Xóa</button>
                            @else
                                <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="d-inline" 
                                      data-confirm-title="Xóa danh mục" 
                                      data-confirm-text="Bạn có chắc chắn muốn xóa danh mục <strong>&quot;{{ $item->name }}&quot;</strong> không? Thao tác này không thể hoàn tác." 
                                      data-confirm-btn="Xóa danh mục" 
                                      data-confirm-type="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="p-3 border-top" style="border-color: var(--border-light) !important;">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
