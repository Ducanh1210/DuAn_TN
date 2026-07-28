@extends('admin.layouts.app')

@section('title', 'Thêm Địa điểm mới')

@section('content')
<div class="card-minimal p-4">
    <form action="{{ route('admin.locations.store', request()->query()) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên địa điểm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" style="border-color: #e2e8f0;">
                    @error('name') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                </div>
                
                <div class="mb-3">
                    <label for="short_description" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Mô tả ngắn</label>
                    <textarea class="form-control form-control-sm" id="short_description" name="short_description" rows="3" style="border-color: #e2e8f0;">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Địa chỉ</label>
                    <input type="text" class="form-control form-control-sm" id="address" name="address" value="{{ old('address') }}" style="border-color: #e2e8f0;">
                </div>
            </div>
            
            <div class="col-md-4 border-start" style="border-color: var(--border-light) !important;">
                <div class="mb-3">
                    <label for="category_id" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm @error('category_id') is-invalid @enderror" id="category_id" name="category_id" style="border-color: #e2e8f0;">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label for="lat" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Vĩ độ (Lat) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('lat') is-invalid @enderror" id="lat" name="lat" value="{{ old('lat') }}" style="border-color: #e2e8f0;">
                        @error('lat') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label for="lng" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Kinh độ (Lng) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('lng') is-invalid @enderror" id="lng" name="lng" value="{{ old('lng') }}" style="border-color: #e2e8f0;">
                        @error('lng') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                    <select class="form-select form-select-sm" id="status" name="status" style="border-color: #e2e8f0;">
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Công khai</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Ảnh đại diện (Thumbnail)</label>
                    <input type="file" class="form-control form-control-sm @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*" style="border-color: #e2e8f0;">
                    @error('thumbnail') <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-light) !important;">
            <a href="{{ route('admin.locations.index', request()->query()) }}" class="btn-minimal text-decoration-none">Hủy bỏ</a>
            <button type="submit" class="btn-minimal btn-minimal-primary">Lưu và Tiếp tục thêm Ảnh</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Tự động tách tọa độ khi dán định dạng "lat, lng" vào ô Lat
    document.getElementById('lat').addEventListener('input', function() {
        let val = this.value;
        if (val.includes(',')) {
            let parts = val.split(',');
            if (parts.length >= 2) {
                this.value = parts[0].trim();
                document.getElementById('lng').value = parts[1].trim();
            }
        }
    });
</script>
@endpush
