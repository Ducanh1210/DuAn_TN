@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục Mới')

@section('content')
<div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-9">
                    <label for="icon" class="form-label fw-bold">Ảnh Icon (Map Marker)</label>
                    <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*">
                    <small class="text-muted">Nên dùng ảnh PNG có nền trong suốt, kích thước khoảng 64x64px.</small>
                    @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="icon_color" class="form-label fw-bold">Màu ghim bản đồ</label>
                    <input type="color" class="form-control form-control-color w-100 @error('icon_color') is-invalid @enderror" id="icon_color" name="icon_color" value="{{ old('icon_color', '#ef4444') }}" title="Chọn màu cho ghim">
                    @error('icon_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="display_order" class="form-label fw-bold">Thứ tự hiển thị</label>
                    <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order') }}">
                    <small class="text-muted">Để trống để tự động xếp cuối</small>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label fw-bold">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">Lưu Danh Mục</button>
            </div>
        </form>
    </div>
</div>
@endsection
