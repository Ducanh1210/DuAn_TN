@extends('admin.layouts.app')
@section('title', 'Thêm Bài Viết Mới')

@section('actions')
<a href="{{ route('admin.news.index') }}" class="btn-minimal">Quay lại</a>
@endsection

@section('content')
<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-minimal p-4 mb-4">
            <div class="fw-medium text-dark mb-3 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Nội dung bài viết</div>
            
            <div class="mb-3">
                <label for="title" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required style="border-color: #e2e8f0;">
                @error('title')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="summary" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tóm tắt</label>
                <textarea class="form-control form-control-sm" id="summary" name="summary" rows="3" style="border-color: #e2e8f0;">{{ old('summary') }}</textarea>
                <div class="text-muted mt-1" style="font-size: 0.725rem;">Tối đa 500 ký tự</div>
            </div>
            <div class="mb-0">
                <label for="content" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-sm @error('content') is-invalid @enderror" id="content" name="content" rows="14" style="border-color: #e2e8f0;">{{ old('content') }}</textarea>
                <div id="contentClientError" class="text-danger small mt-1 {{ $errors->has('content') ? '' : 'd-none' }}" style="font-size: 0.75rem;">
                    {{ $errors->first('content') ?: 'Vui lòng nhập nội dung chi tiết.' }}
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card-minimal p-3 mb-3">
            <div class="fw-medium text-dark mb-2 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Ảnh đại diện</div>
            <div class="img-preview-box mb-2 border rounded p-3 text-center bg-light cursor-pointer" onclick="document.getElementById('featured_image').click()" title="Click chọn ảnh" style="border-style: dashed !important; border-color: #cbd5e1 !important; min-height: 140px; cursor: pointer;">
                <img id="imgPrev" src="" class="rounded w-100" style="display:none; max-height: 160px; object-fit: cover;">
                <div class="ph text-muted py-3" id="imgPh" style="font-size: 0.775rem;">
                    <div>Click chọn ảnh đại diện</div>
                    <div style="font-size: 0.7rem;" class="mt-1">Ảnh nặng sẽ được tự động nén</div>
                </div>
            </div>
            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*" onchange="prevImg(event)">
            @error('featured_image')<div class="text-danger small mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
        </div>

        <div class="card-minimal p-3">
            <div class="fw-medium text-dark mb-2 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Cài đặt xuất bản</div>
            <div class="mb-3">
                <label for="type" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Loại bài viết</label>
                <select class="form-select form-select-sm" id="type" name="type" style="border-color: #e2e8f0;">
                    <option value="news" {{ old('type')=='news'?'selected':'' }}>Tin tức</option>
                    <option value="event" {{ old('type')=='event'?'selected':'' }}>Sự kiện</option>
                    <option value="guide" {{ old('type')=='guide'?'selected':'' }}>Cẩm nang</option>
                    <option value="announcement" {{ old('type')=='announcement'?'selected':'' }}>Thông báo</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                <select class="form-select form-select-sm" id="status" name="status" style="border-color: #e2e8f0;">
                    <option value="published" {{ old('status','published')=='published'?'selected':'' }}>Xuất bản ngay</option>
                    <option value="draft" {{ old('status')=='draft'?'selected':'' }}>Bản nháp</option>
                    <option value="hidden" {{ old('status')=='hidden'?'selected':'' }}>Ẩn</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="published_at" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Ngày xuất bản</label>
                <input type="datetime-local" class="form-control form-control-sm" id="published_at" name="published_at" value="{{ old('published_at') }}" style="border-color: #e2e8f0;">
                <div class="text-muted mt-1" style="font-size: 0.725rem;">Để trống = thời điểm hiện tại</div>
            </div>
            <div class="d-flex gap-2 pt-2 border-top" style="border-color: var(--border-light) !important;">
                <a href="{{ route('admin.news.index') }}" class="btn-minimal flex-fill text-center text-decoration-none">Hủy</a>
                <button type="submit" class="btn-minimal btn-minimal-primary flex-fill">Lưu bài viết</button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
function prevImg(e){var f=e.target.files[0];if(f){var r=new FileReader();r.onload=function(ev){document.getElementById('imgPrev').src=ev.target.result;document.getElementById('imgPrev').style.display='block';document.getElementById('imgPh').style.display='none'};r.readAsDataURL(f)}}

$(document).ready(function() {
    tinymce.init({
        selector: '#content',
        plugins: 'image link media table code lists fullscreen preview',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | table | fullscreen preview code',
        height: 500,
        image_title: true,
        automatic_uploads: true,
        relative_urls: false,
        remove_script_host: true,
        convert_urls: true,
        document_base_url: '{{ rtrim(url('/'), '/') }}/',
        promotion: false,
        branding: false,
        statusbar: false,
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '{{ route('admin.news.upload_image') }}');
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");

                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = function() {
                    if (xhr.status === 403) {
                        reject('HTTP Error: ' + xhr.status, { remove: true });
                        return;
                    }
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    var json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.url != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    resolve(json.url);
                };

                xhr.onerror = function () {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };

                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            });
        },
        setup: function (editor) {
            editor.on('change input keyup', function () {
                tinymce.triggerSave();
                if (typeof window.clearNewsContentError === 'function') {
                    window.clearNewsContentError();
                }
            });
        }
    });
});
</script>
@include('admin.partials.tinymce-content-guard')
@endpush
@include('admin.partials.featured-image-compress')
