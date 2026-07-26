@extends('admin.layouts.app')

@section('title', 'Thêm Danh Mục Mới')

@section('content')
<div class="card-minimal mx-auto p-4" style="max-width: 720px;">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div class="mb-3">
            <label for="name" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên danh mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" style="border-color: #e2e8f0;">
            @error('name') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Mô tả</label>
            <textarea class="form-control form-control-sm" id="description" name="description" rows="3" style="border-color: #e2e8f0;">{{ old('description') }}</textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-9">
                <label for="icon" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Ảnh Icon (Map Marker)</label>
                <input type="file" class="form-control form-control-sm @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*" style="border-color: #e2e8f0;">
                <div class="text-muted mt-1" style="font-size: 0.725rem;">Nên dùng ảnh PNG có nền trong suốt, kích thước khoảng 64x64px.</div>
                @error('icon') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label for="icon_color" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Màu ghim bản đồ</label>
                <input type="color" class="form-control form-control-color w-100 @error('icon_color') is-invalid @enderror" id="icon_color" name="icon_color" value="{{ old('icon_color', '#ef4444') }}" title="Chọn màu cho ghim" style="border-color: #e2e8f0; height: 31px;">
                @error('icon_color') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="display_order" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Thứ tự hiển thị</label>
                <input type="number" min="0" class="form-control form-control-sm @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order') }}" style="border-color: #e2e8f0;">
                <div class="text-muted mt-1" style="font-size: 0.725rem;">Để trống để tự động xếp cuối (không nhập số âm)</div>
                @error('display_order') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                <select class="form-select form-select-sm" id="status" name="status" style="border-color: #e2e8f0;">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top" style="border-color: var(--border-light) !important;">
            <a href="{{ route('admin.categories.index') }}" class="btn-minimal text-decoration-none">Hủy bỏ</a>
            <button type="submit" class="btn-minimal btn-minimal-primary">Lưu Danh Mục</button>
        </div>
    </form>
</div>
@endsection
