@extends('admin.layouts.app')
@section('title', 'Thêm Sự Kiện Mới')
@push('styles')
<style>
.form-card{border:none;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden}
.form-card .card-header{background:linear-gradient(135deg,#f8f9fc,#eef1f8);border-bottom:1px solid #e2e8f0;padding:16px 24px}
.form-card .card-header h6{font-weight:700;color:#334155;margin:0}
.form-card .card-body{padding:24px}
.form-label{font-size:.88rem;font-weight:600;color:#475569}
.form-control:focus,.form-select:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,.15)}
.img-preview-box{width:100%;height:200px;border:2px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f8fafc;overflow:hidden;cursor:pointer;transition:.3s}
.img-preview-box:hover{border-color:#8b5cf6;background:#f5f3ff}
.img-preview-box img{width:100%;height:100%;object-fit:cover}
.img-preview-box .ph{text-align:center;color:#94a3b8}
.img-preview-box .ph i{font-size:2rem}
.btn-submit{background:linear-gradient(135deg,#8b5cf6,#6d28d9);border:none;padding:10px 28px;font-weight:600;border-radius:8px;transition:.3s}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(139,92,246,.4)}
.form-check-input:checked{background-color:#f59e0b;border-color:#f59e0b}
</style>
@endpush

@section('content')
<form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
<div class="col-lg-8">
    <div class="form-card card">
        <div class="card-header"><h6><i class="fas fa-calendar-plus me-2"></i>Thông tin sự kiện</h6></div>
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả sự kiện</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="program" class="form-label">Chương trình / Nội dung</label>
                <textarea class="form-control" id="program" name="program" rows="6" placeholder="Nội dung chi tiết chương trình sự kiện...">{{ old('program') }}</textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Bắt đầu <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">Kết thúc <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    {{-- Image --}}
    <div class="form-card card mb-4">
        <div class="card-header"><h6><i class="fas fa-image me-2"></i>Ảnh sự kiện</h6></div>
        <div class="card-body">
            <div class="img-preview-box mb-2" onclick="document.getElementById('featured_image').click()">
                <img id="imgPrev" src="" style="display:none">
                <div class="ph" id="imgPh"><i class="fas fa-cloud-upload-alt d-block mb-2"></i><span>Click để chọn ảnh</span></div>
            </div>
            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*" onchange="prevImg(event)">
        </div>
    </div>
    {{-- Location & Settings --}}
    <div class="form-card card">
        <div class="card-header"><h6><i class="fas fa-cog me-2"></i>Cài đặt</h6></div>
        <div class="card-body">
            <div class="mb-3">
                <label for="location_text" class="form-label">Địa điểm tổ chức</label>
                <input type="text" class="form-control" id="location_text" name="location_text" value="{{ old('location_text') }}" placeholder="VD: Chùa Bà Đanh, Kim Bảng">
            </div>
            <div class="mb-3">
                <label for="location_id" class="form-label">Liên kết địa điểm</label>
                <select class="form-select" id="location_id" name="location_id">
                    <option value="">— Không liên kết —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id')==$loc->id?'selected':'' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Liên kết đến địa điểm trên bản đồ (tùy chọn)</small>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="active" {{ old('status','active')=='active'?'selected':'' }}>✅ Đang diễn ra</option>
                    <option value="hidden" {{ old('status')=='hidden'?'selected':'' }}>👁️‍🗨️ Ẩn</option>
                    <option value="cancelled" {{ old('status')=='cancelled'?'selected':'' }}>❌ Đã hủy</option>
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured')?'checked':'' }}>
                    <label class="form-check-label fw-semibold" for="is_featured"><i class="fas fa-star text-warning me-1"></i>Sự kiện nổi bật</label>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary flex-grow-1"><i class="fas fa-arrow-left me-1"></i>Hủy</a>
                <button type="submit" class="btn btn-submit text-white flex-grow-1"><i class="fas fa-save me-1"></i>Lưu</button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
@endsection

@push('scripts')
<script>
function prevImg(e){var f=e.target.files[0];if(f){var r=new FileReader();r.onload=function(ev){document.getElementById('imgPrev').src=ev.target.result;document.getElementById('imgPrev').style.display='block';document.getElementById('imgPh').style.display='none'};r.readAsDataURL(f)}}
</script>
@endpush
