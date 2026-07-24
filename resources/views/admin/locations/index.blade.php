@extends('admin.layouts.app')

@section('title', 'Quản lý Địa điểm')

@section('actions')
    <a href="{{ route('admin.locations.create', request()->query()) }}" class="btn-minimal btn-minimal-primary">Thêm địa điểm</a>
@endsection

@section('content')
<!-- Form Lọc & Tìm kiếm Minimalist -->
<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.locations.index') }}" method="GET" class="row g-2 align-items-center">
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
                    <th>Tọa độ</th>
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
                        <td>
                            <div class="fw-medium text-dark" style="font-size: 0.825rem;">{{ $item->name }}</div>
                            <div class="text-muted text-truncate" style="max-width: 260px; font-size: 0.725rem;">{{ $item->address ?? 'Ninh Bình' }}</div>
                        </td>
                        <td>
                            <span class="badge-minimal">
                                {{ $item->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $item->lat }}, {{ $item->lng }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->status == 'published')
                                <span class="badge-minimal badge-minimal-success">Công khai</span>
                            @elseif($item->status == 'draft')
                                <span class="badge-minimal">Bản nháp</span>
                            @else
                                <span class="badge-minimal">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.locations.edit', [$item->id] + request()->query()) }}" class="btn-minimal py-1 px-2 text-decoration-none me-1" style="font-size: 0.75rem;">Sửa</a>
                            <form action="{{ route('admin.locations.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size: 0.75rem;">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có địa điểm nào.</td>
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
@endsection
