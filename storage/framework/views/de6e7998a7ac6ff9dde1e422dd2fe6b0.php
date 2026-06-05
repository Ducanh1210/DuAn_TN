<?php $__env->startSection('title', 'Thêm Bài Viết Mới'); ?>
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
/* TinyMCE customizations */
.tox-tinymce { border-radius: 8px !important; border-color: #cbd5e1 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('admin.news.store')); ?>" method="POST" enctype="multipart/form-data">
<?php echo csrf_field(); ?>
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
unset($__errorArgs, $__bag); ?>" id="title" name="title" value="<?php echo e(old('title')); ?>" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="summary" class="form-label">Tóm tắt</label>
                <textarea class="form-control" id="summary" name="summary" rows="3"><?php echo e(old('summary')); ?></textarea>
                <small class="text-muted">Tối đa 500 ký tự</small>
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
unset($__errorArgs, $__bag); ?>" id="content" name="content" rows="14" required><?php echo e(old('content')); ?></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="form-card card mb-4">
        <div class="card-header"><h6><i class="fas fa-image me-2"></i>Ảnh đại diện</h6></div>
        <div class="card-body">
            <div class="img-preview-box mb-2" onclick="document.getElementById('featured_image').click()">
                <img id="imgPrev" src="" style="display:none">
                <div class="ph" id="imgPh"><i class="fas fa-cloud-upload-alt d-block mb-2"></i><span class="d-block">Click để chọn ảnh</span><small>PNG, JPG, WEBP — tối đa 5MB</small></div>
            </div>
            <input type="file" class="d-none" id="featured_image" name="featured_image" accept="image/*" onchange="prevImg(event)">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['featured_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <div class="form-card card">
        <div class="card-header"><h6><i class="fas fa-cog me-2"></i>Cài đặt xuất bản</h6></div>
        <div class="card-body">
            <div class="mb-3">
                <label for="type" class="form-label">Loại bài viết</label>
                <select class="form-select" id="type" name="type">
                    <option value="news" <?php echo e(old('type')=='news'?'selected':''); ?>>📰 Tin tức</option>
                    <option value="guide" <?php echo e(old('type')=='guide'?'selected':''); ?>>📖 Cẩm nang</option>
                    <option value="announcement" <?php echo e(old('type')=='announcement'?'selected':''); ?>>📢 Thông báo</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="published" <?php echo e(old('status','published')=='published'?'selected':''); ?>>✅ Xuất bản ngay</option>
                    <option value="draft" <?php echo e(old('status')=='draft'?'selected':''); ?>>📝 Bản nháp</option>
                    <option value="hidden" <?php echo e(old('status')=='hidden'?'selected':''); ?>>👁️‍🗨️ Ẩn</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="published_at" class="form-label">Ngày xuất bản</label>
                <input type="datetime-local" class="form-control" id="published_at" name="published_at" value="<?php echo e(old('published_at')); ?>">
                <small class="text-muted">Để trống = thời điểm hiện tại</small>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.news.index')); ?>" class="btn btn-secondary flex-grow-1"><i class="fas fa-arrow-left me-1"></i>Hủy</a>
                <button type="submit" class="btn btn-submit btn-primary flex-grow-1"><i class="fas fa-save me-1"></i>Lưu</button>
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

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/news/create.blade.php ENDPATH**/ ?>