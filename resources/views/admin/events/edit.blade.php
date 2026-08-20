@extends('admin.layouts.app')
@section('title', 'Chỉnh sửa Sự Kiện')

@section('actions')
<a href="{{ route('admin.events.index') }}" class="btn-minimal">Quay lại</a>
@endsection

@section('content')
<form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-minimal p-4 mb-4">
            <div class="fw-medium text-dark mb-3 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Thông tin sự kiện</div>
            
            <div class="mb-3">
                <label for="name" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên sự kiện <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $event->name) }}" required style="border-color: #e2e8f0;">
                @error('name')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Mô tả sự kiện</label>
                <textarea class="form-control form-control-sm" id="description" name="description" rows="3" style="border-color: #e2e8f0;">{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="program" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Chương trình / Nội dung</label>
                <textarea class="form-control form-control-sm" id="program" name="program" rows="6">{{ old('program', $event->program) }}</textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Bắt đầu <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control form-control-sm @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required style="border-color: #e2e8f0;">
                    @error('start_time')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Kết thúc <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control form-control-sm @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $event->end_time->format('Y-m-d\TH:i')) }}" required style="border-color: #e2e8f0;">
                    @error('end_time')<div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        {{-- Info --}}
        <div class="card-minimal p-3 mb-3">
            <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.775rem;">
                <div>Tác giả: <strong class="text-dark">{{ $event->creator->display_name ?? $event->creator->username ?? '—' }}</strong></div>
                <div>•</div>
                <div>Tạo lúc: {{ $event->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        {{-- Image --}}
        <div class="card-minimal p-3 mb-3">
            <div class="fw-medium text-dark mb-2 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Ảnh sự kiện</div>
            <div class="img-preview-box mb-2 border rounded p-3 text-center bg-light cursor-pointer" onclick="document.getElementById('featured_image').click()" title="Click chọn hoặc dán ảnh từ Clipboard" style="border-style: dashed !important; border-color: #cbd5e1 !important; min-height: 140px; cursor: pointer;">
                @if($event->featured_image)
                    <img id="imgPrev" src="{{ asset('storage/' . $event->featured_image) }}" class="rounded w-100" style="max-height: 160px; object-fit: cover;">
                    <div class="ph text-muted py-3" id="imgPh" style="display:none; font-size: 0.775rem;">
                        <div>Tải ảnh lên hoặc Ctrl+V dán ảnh</div>
                    </div>
                @else
                    <img id="imgPrev" src="" class="rounded w-100" style="display:none; max-height: 160px; object-fit: cover;">
                    <div class="ph text-muted py-3" id="imgPh" style="font-size: 0.775rem;">
                        <div>Tải ảnh lên hoặc Ctrl+V dán ảnh</div>
                    </div>
                @endif
            </div>
            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*" onchange="prevImg(event)">
        </div>

        {{-- Settings --}}
        <div class="card-minimal p-3">
            <div class="fw-medium text-dark mb-2 pb-2 border-bottom" style="font-size: 0.85rem; border-color: var(--border-light) !important;">Cài đặt</div>
            <div class="mb-3">
                <label for="location_text" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Địa điểm tổ chức</label>
                <input type="text" class="form-control form-control-sm" id="location_text" name="location_text" value="{{ old('location_text', $event->location_text) }}" style="border-color: #e2e8f0;">
            </div>
            <div class="mb-3">
                <label for="location_id" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Liên kết địa điểm</label>
                <select class="form-select form-select-sm" id="location_id" name="location_id" style="border-color: #e2e8f0;">
                    <option value="">— Không liên kết —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id',$event->location_id)==$loc->id?'selected':'' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                <select class="form-select form-select-sm" id="status" name="status" style="border-color: #e2e8f0;">
                    <option value="active" {{ old('status',$event->status)=='active'?'selected':'' }}>Đang diễn ra</option>
                    <option value="hidden" {{ old('status',$event->status)=='hidden'?'selected':'' }}>Ẩn</option>
                    <option value="cancelled" {{ old('status',$event->status)=='cancelled'?'selected':'' }}>Đã hủy</option>
                    <option value="expired" {{ old('status',$event->status)=='expired'?'selected':'' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured',$event->is_featured)?'checked':'' }}>
                    <label class="form-check-label fw-medium text-dark" for="is_featured" style="font-size: 0.8rem;">Sự kiện nổi bật</label>
                </div>
            </div>
            <div class="d-flex gap-2 pt-2 border-top" style="border-color: var(--border-light) !important;">
                <a href="{{ route('admin.events.index') }}" class="btn-minimal flex-fill text-center text-decoration-none">Hủy</a>
                <button type="submit" class="btn-minimal btn-minimal-primary flex-fill">Cập nhật</button>
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

// Paste image support
document.addEventListener('paste', function(e) {
    var ae = document.activeElement;
    if (ae && (ae.tagName === 'INPUT' || ae.tagName === 'TEXTAREA' || ae.isContentEditable || ae.tagName === 'IFRAME')) {
        return; 
    }
    if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
        var file = e.clipboardData.files[0];
        if (file.type.indexOf('image/') !== -1) {
            e.preventDefault();
            var fi = document.getElementById('featured_image');
            var dt = new DataTransfer();
            dt.items.add(file);
            fi.files = dt.files;
            var evt = new Event('change');
            fi.dispatchEvent(evt);
        }
    }
});

$(document).ready(function() {
    tinymce.init({
        selector: '#program',
        plugins: 'image link media table code lists fullscreen preview',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | table | fullscreen preview code',
        height: 450,
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
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        }
    });
});
</script>
@endpush
@include('admin.partials.featured-image-compress')
