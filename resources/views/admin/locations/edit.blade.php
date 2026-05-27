@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Địa điểm: ' . $location->name)

@section('content')
<ul class="nav nav-tabs mb-4" id="locationTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Thông tin cơ bản</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-success" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab"><i class="fas fa-images"></i> Quản lý Hình ảnh</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-primary" id="pano-tab" data-bs-toggle="tab" data-bs-target="#pano" type="button" role="tab"><i class="fas fa-vr-cardboard"></i> Dữ liệu 360°</button>
    </li>
</ul>

<div class="mb-3 d-flex justify-content-end">
    <a href="{{ route('admin.locations.360_editor', $location->id) }}" class="btn btn-warning fw-bold text-dark shadow-sm">
        <i class="fas fa-external-link-alt"></i> Mở Trình chỉnh sửa Tour 360° nâng cao
    </a>
</div>

<div class="tab-content" id="locationTabsContent">
    <!-- INFO TAB -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.locations.update', $location->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Tên địa điểm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $location->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="2">{{ old('short_description', $location->short_description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-bold">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $location->address) }}">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $location->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="lat" class="form-label fw-bold">Vĩ độ (Lat) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lat') is-invalid @enderror" id="lat" name="lat" value="{{ old('lat', $location->lat) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="lng" class="form-label fw-bold">Kinh độ (Lng) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lng') is-invalid @enderror" id="lng" name="lng" value="{{ old('lng', $location->lng) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="published" {{ old('status', $location->status) == 'published' ? 'selected' : '' }}>Công khai</option>
                                    <option value="draft" {{ old('status', $location->status) == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                    <option value="hidden" {{ old('status', $location->status) == 'hidden' ? 'selected' : '' }}>Ẩn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Cập nhật Thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- IMAGES TAB -->
    <div class="tab-pane fade" id="images" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Upload Hình ảnh mới</h5>
                <input type="file" id="imageUploadInput" class="d-none" multiple accept="image/*">
                <button type="button" class="btn btn-sm btn-success" onclick="document.getElementById('imageUploadInput').click()">
                    <i class="fas fa-upload"></i> Chọn ảnh tải lên
                </button>
            </div>
            <div class="card-body bg-light" id="imagesList">
                <div class="row g-3">
                    @foreach($images as $img)
                    <div class="col-md-3 col-sm-4 image-card" id="img-{{ $img->id }}">
                        <div class="card h-100 position-relative">
                            <img src="{{ Storage::url($img->image_url) }}" class="card-img-top object-fit-cover" height="150" alt="">
                            <div class="position-absolute top-0 end-0 p-1">
                                <button class="btn btn-sm btn-danger btn-delete-image" data-id="{{ $img->id }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- PANORAMA TAB -->
    <div class="tab-pane fade" id="pano" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4 text-center p-5 bg-light">
            <h4 class="mb-3">Bạn đã kích hoạt Trình chỉnh sửa Tour 360° nâng cao</h4>
            <p class="text-muted">Chức năng upload 360 cơ bản đã được vô hiệu hóa. Vui lòng nhấn vào nút bên dưới để mở công cụ chỉnh sửa trực quan (thêm hotspot, liên kết ảnh, v.v.).</p>
            <a href="{{ route('admin.locations.360_editor', $location->id) }}" class="btn btn-lg btn-warning fw-bold text-dark mx-auto mt-2 shadow">
                <i class="fas fa-vr-cardboard"></i> Mở 360 Tour Editor
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // CSRF Token Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    // Upload Image
    $('#imageUploadInput').change(function() {
        let files = this.files;
        if(files.length === 0) return;
        
        for(let i=0; i<files.length; i++) {
            let formData = new FormData();
            formData.append('file', files[i]);
            
            $.ajax({
                url: '{{ route('admin.locations.upload_image', $location->id) }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.success) {
                        let html = `
                            <div class="col-md-3 col-sm-4 image-card" id="img-${response.image.id}">
                                <div class="card h-100 position-relative">
                                    <img src="${response.url}" class="card-img-top object-fit-cover" height="150" alt="">
                                    <div class="position-absolute top-0 end-0 p-1">
                                        <button class="btn btn-sm btn-danger btn-delete-image" data-id="${response.image.id}"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#imagesList .row').append(html);
                    }
                }
            });
        }
    });

    // Delete Image
    $(document).on('click', '.btn-delete-image', function() {
        if(!confirm('Xóa ảnh này?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: '/admin/locations/image/' + id,
            type: 'DELETE',
            success: function(res) {
                if(res.success) $('#img-' + id).remove();
            }
        });
    });

</script>
@endpush
