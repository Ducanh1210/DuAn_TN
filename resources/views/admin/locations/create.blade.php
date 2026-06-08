@extends('admin.layouts.app')

@section('title', 'Thêm Địa điểm mới')

@section('content')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('admin.locations.store', request()->query()) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Tên địa điểm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lat" class="form-label fw-bold">Vĩ độ (Lat) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lat') is-invalid @enderror" id="lat" name="lat" value="{{ old('lat') }}" required>
                        @error('lat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lng" class="form-label fw-bold">Kinh độ (Lng) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('lng') is-invalid @enderror" id="lng" name="lng" value="{{ old('lng') }}" required>
                        @error('lng') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Công khai</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                        <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*">
                        @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.locations.index', request()->query()) }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">Lưu và Tiếp tục thêm Ảnh</button>
            </div>
        </form>
    </div>
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
