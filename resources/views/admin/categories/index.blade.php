@extends('admin.layouts.app')

@section('title', 'Quản lý Danh mục')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                        <th width="50" class="text-center">ID</th>
                        <th width="60" class="text-center">Icon</th>
                        <th>Tên Danh mục</th>
                        <th>Slug</th>
                        <th class="text-center">Thứ tự</th>
                        <th class="text-center">Trạng thái</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $item)
                            <td class="text-center">{{ $item->id }}</td>
                            <td class="text-center">
                                @if($item->icon)
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <img src="{{ asset($item->icon) }}" alt="Icon" style="height: 32px; width: 32px; object-fit: contain;">
                                        <div style="width: 20px; height: 8px; background-color: {{ $item->icon_color ?? '#ef4444' }}; border-radius: 4px; border: 1px solid #ddd;" title="Màu ghim: {{ $item->icon_color ?? '#ef4444' }}"></div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                        <div style="width: 20px; height: 8px; background-color: {{ $item->icon_color ?? '#ef4444' }}; border-radius: 4px; border: 1px solid #ddd;" title="Màu ghim: {{ $item->icon_color ?? '#ef4444' }}"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>{{ $item->slug }}</td>
                            <td class="text-center">{{ $item->display_order }}</td>
                            <td class="text-center">
                                @if($item->status == 'active')
                                    <span class="badge bg-success">Hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Đang ẩn</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.categories.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Chưa có danh mục nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
