<?php $__env->startSection('title', 'Chỉnh sửa Bài Viết'); ?>
<?php $__env->startPush('styles'); ?>
<style>
.form-card{border:none;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden}
.form-card .card-header{background:linear-gradient(135deg,#f8f9fc,#eef1f8);border-bottom:1px solid #e2e8f0;padding:16px 24px}
.form-card .card-header h6{font-weight:700;color:#334155;margin:0}
.form-card .card-body{padding:24px}
.form-label{font-size:.88rem;font-weight:600;color:#475569}
.form-control:focus,.form-select:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.img-preview-box{width:100%;height:200px;border:2px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#f8fafc;overflow:hidden;cursor:pointer;transition:.3s}
.img-preview-box:hover{border-color:#3b82f6;background:#eff6ff}
.img-preview-box img{width:100%;height:100%;object-fit:cover}
.img-preview-box .ph{text-align:center;color:#94a3b8}
.img-preview-box .ph i{font-size:2rem}
.btn-submit{background:linear-gradient(135deg,#3b82f6,#1d4ed8);border:none;padding:10px 28px;font-weight:600;border-radius:8px;transition:.3s}
.btn-submit:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(59,130,246,.4)}
.info-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;font-size:.82rem;font-weight:500}
/* TinyMCE customizations */
.tox-tinymce { border-radius: 8px !important; border-color: #cbd5e1 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('admin.news.update', $news->id)); ?>" method="POST" enctype="multipart/form-data">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="row g-4">
<div class="col-lg-8">
    <div class="form-card card">
        <div class="card-header"><h6><i class="fas fa-pen-fancy me-2"></i>Nội dung bài viết</h6></div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="title" name="title" value="<?php echo e(old('title', $news->title)); ?>" required>
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-3">
                <label for="summary" class="form-label">Tóm tắt</label>
                <textarea class="form-control" id="summary" name="summary" rows="3"><?php echo e(old('summary', $news->summary)); ?></textarea>
            </div>
            <div class="mb-0">
                <label for="content" class="form-label">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="content" name="content" rows="14" required><?php echo e(old('content', $news->content)); ?></textarea>
                <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    
    <div class="form-card card mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="info-badge bg-light"><i class="fas fa-eye text-primary"></i> <?php echo e(number_format($news->view_count)); ?> lượt xem</span>
                <span class="info-badge bg-light"><i class="fas fa-user text-success"></i> <?php echo e($news->author->display_name ?? $news->author->username ?? '—'); ?></span>
                <span class="info-badge bg-light"><i class="fas fa-clock text-warning"></i> <?php echo e($news->created_at->format('d/m/Y H:i')); ?></span>
            </div>
        </div>
    </div>

    
    <div class="form-card card mb-4">
        <div class="card-header"><h6><i class="fas fa-image me-2"></i>Ảnh đại diện</h6></div>
        <div class="card-body">
            <div class="img-preview-box mb-2" onclick="document.getElementById('featured_image').click()">
                <?php if($news->featured_image): ?>
                    <img id="imgPrev" src="<?php echo e(asset('storage/' . $news->featured_image)); ?>">
                    <div class="ph" id="imgPh" style="display:none"><i class="fas fa-cloud-upload-alt d-block mb-2"></i><span>Click để đổi ảnh</span></div>
                <?php else: ?>
                    <img id="imgPrev" src="" style="display:none">
                    <div class="ph" id="imgPh"><i class="fas fa-cloud-upload-alt d-block mb-2"></i><span>Click để chọn ảnh</span></div>
                <?php endif; ?>
            </div>
            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*" onchange="prevImg(event)">
            <?php $__errorArgs = ['featured_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    
    <div class="form-card card">
        <div class="card-header"><h6><i class="fas fa-cog me-2"></i>Cài đặt xuất bản</h6></div>
        <div class="card-body">
            <div class="mb-3">
                <label for="type" class="form-label">Loại bài viết</label>
                <select class="form-select" id="type" name="type">
                    <option value="news" <?php echo e(old('type',$news->type)=='news'?'selected':''); ?>>📰 Tin tức</option>
                    <option value="event" <?php echo e(old('type',$news->type)=='event'?'selected':''); ?>>📅 Sự kiện</option>
                    <option value="guide" <?php echo e(old('type',$news->type)=='guide'?'selected':''); ?>>📖 Cẩm nang</option>
                    <option value="announcement" <?php echo e(old('type',$news->type)=='announcement'?'selected':''); ?>>📢 Thông báo</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="published" <?php echo e(old('status',$news->status)=='published'?'selected':''); ?>>✅ Xuất bản</option>
                    <option value="draft" <?php echo e(old('status',$news->status)=='draft'?'selected':''); ?>>📝 Bản nháp</option>
                    <option value="hidden" <?php echo e(old('status',$news->status)=='hidden'?'selected':''); ?>>👁️‍🗨️ Ẩn</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="published_at" class="form-label">Ngày xuất bản</label>
                <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?php echo e(old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '')); ?>">
            </div>
            <hr>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.news.index')); ?>" class="btn btn-secondary flex-grow-1"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
                <button type="submit" class="btn btn-submit btn-primary flex-grow-1"><i class="fas fa-save me-1"></i>Cập nhật</button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
        promotion: false,
        branding: false,
        statusbar: false,
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '<?php echo e(route('admin.news.upload_image')); ?>');
                xhr.setRequestHeader("X-CSRF-TOKEN", "<?php echo e(csrf_token()); ?>");

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/news/edit.blade.php ENDPATH**/ ?>