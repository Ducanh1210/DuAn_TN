@extends('admin.layouts.app')

@section('title', 'Quản lý Địa điểm')

@section('actions')
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">ID</th>
                        <th>Tên Địa điểm</th>
                        <th>Danh mục</th>
                        <th>Tọa độ (Lat, Lng)</th>
                        <th class="text-center">Trạng thái</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $item)
                        <tr>
                            <td class="text-center">{{ $item->id }}</td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td><small class="text-muted">{{ $item->lat }}, {{ $item->lng }}</small></td>
                            <td class="text-center">
                                @if($item->status == 'published')
                                    <span class="badge bg-success">Công khai</span>
                                @elseif($item->status == 'draft')
                                    <span class="badge bg-warning text-dark">Bản nháp</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.locations.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.locations.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Chưa có địa điểm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($locations->hasPages())
    <div class="card-footer bg-white">
        {{ $locations->links() }}
    </div>
    @endif
</div>
@endsection
