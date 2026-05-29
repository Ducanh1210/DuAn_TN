<?php $__env->startSection('title', 'Chỉnh sửa Địa điểm: ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
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
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-warning" id="audio-tab" data-bs-toggle="tab" data-bs-target="#audio" type="button" role="tab"><i class="fas fa-microphone"></i> Audio thuyết minh</button>
    </li>
</ul>

<div class="mb-3 d-flex justify-content-end">
    <a href="<?php echo e(route('admin.locations.360_editor', $location->id)); ?>" class="btn btn-warning fw-bold text-dark shadow-sm">
        <i class="fas fa-external-link-alt"></i> Mở Trình chỉnh sửa Tour 360° nâng cao
    </a>
</div>

<div class="tab-content" id="locationTabsContent">
    <!-- INFO TAB -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('admin.locations.update', $location->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Tên địa điểm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $location->name)); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="short_description" class="form-label fw-bold">Mô tả ngắn</label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="2"><?php echo e(old('short_description', $location->short_description)); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-bold">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo e(old('address', $location->address)); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="category_id" name="category_id" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $location->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="lat" class="form-label fw-bold">Vĩ độ (Lat) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['lat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="lat" name="lat" value="<?php echo e(old('lat', $location->lat)); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="lng" class="form-label fw-bold">Kinh độ (Lng) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['lng'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="lng" name="lng" value="<?php echo e(old('lng', $location->lng)); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="published" <?php echo e(old('status', $location->status) == 'published' ? 'selected' : ''); ?>>Công khai</option>
                                    <option value="draft" <?php echo e(old('status', $location->status) == 'draft' ? 'selected' : ''); ?>>Bản nháp</option>
                                    <option value="hidden" <?php echo e(old('status', $location->status) == 'hidden' ? 'selected' : ''); ?>>Ẩn</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?php echo e(route('admin.locations.index')); ?>" class="btn btn-secondary">Quay lại</a>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 col-sm-4 image-card" id="img-<?php echo e($img->id); ?>">
                        <div class="card h-100 position-relative">
                            <img src="<?php echo e(Storage::url($img->image_url)); ?>" class="card-img-top object-fit-cover" height="150" alt="">
                            <div class="position-absolute top-0 end-0 p-1">
                                <button class="btn btn-sm btn-danger btn-delete-image" data-id="<?php echo e($img->id); ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- PANORAMA TAB -->
    <div class="tab-pane fade" id="pano" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4 text-center p-5 bg-light">
            <h4 class="mb-3">Bạn đã kích hoạt Trình chỉnh sửa Tour 360° nâng cao</h4>
            <p class="text-muted">Chức năng upload 360 cơ bản đã được vô hiệu hóa. Vui lòng nhấn vào nút bên dưới để mở công cụ chỉnh sửa trực quan (thêm hotspot, liên kết ảnh, v.v.).</p>
            <a href="<?php echo e(route('admin.locations.360_editor', $location->id)); ?>" class="btn btn-lg btn-warning fw-bold text-dark mx-auto mt-2 shadow">
                <i class="fas fa-vr-cardboard"></i> Mở 360 Tour Editor
            </a>
        </div>
    </div>

    <!-- AUDIO TAB -->
    <div class="tab-pane fade" id="audio" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-microphone text-warning me-2"></i>Audio thuyết minh địa điểm</h5>
                <small class="text-muted">Upload file âm thanh thuyết minh giới thiệu về địa điểm. Audio sẽ phát khi người dùng xem chế độ 360°.</small>
            </div>
            <div class="card-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($location->audio_url): ?>
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded border">
                        <i class="fas fa-volume-up text-success fs-4"></i>
                        <div class="flex-grow-1">
                            <audio controls class="w-100" style="height: 40px;">
                                <source src="<?php echo e(asset('storage/' . $location->audio_url)); ?>" type="audio/mpeg">
                            </audio>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" id="btnDeleteAudio">
                            <i class="fas fa-trash-alt me-1"></i> Xóa audio
                        </button>
                    </div>
                    <div class="alert alert-success py-2 mb-3">
                        <i class="fas fa-check-circle me-1"></i> Đã có audio thuyết minh cho địa điểm này.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-info-circle me-1"></i> Chưa có audio thuyết minh. Upload file để người dùng có thể nghe khi xem 360°.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="d-flex align-items-center gap-3">
                    <input type="file" id="audioUploadInput" class="d-none" accept="audio/*">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('audioUploadInput').click()">
                        <i class="fas fa-upload me-1"></i> <?php echo e($location->audio_url ? 'Đổi file audio' : 'Upload audio thuyết minh'); ?>

                    </button>
                    <small class="text-muted">Hỗ trợ: MP3, WAV, OGG, M4A (tối đa 20MB)</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // CSRF Token Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    });

    // Upload Image
    $('#imageUploadInput').change(function() {
        let files = this.files;
        if(files.length === 0) return;
        
        for(let i=0; i<files.length; i++) {
            let formData = new FormData();
            formData.append('file', files[i]);
            
            $.ajax({
                url: '<?php echo e(route('admin.locations.upload_image', $location->id)); ?>',
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

    // Upload Audio
    $('#audioUploadInput').change(function() {
        let file = this.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('audio', file);

        $.ajax({
            url: '<?php echo e(route("admin.locations.upload_audio", $location->id)); ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    alert('Upload audio thuyết minh thành công!');
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Lỗi upload audio: ' + (xhr.responseJSON?.message || xhr.responseText));
            }
        });
    });

    // Delete Audio
    $(document).on('click', '#btnDeleteAudio', function() {
        if (!confirm('Xóa audio thuyết minh của địa điểm này?')) return;
        $.ajax({
            url: '<?php echo e(route("admin.locations.delete_audio", $location->id)); ?>',
            type: 'DELETE',
            success: function(res) {
                if (res.success) {
                    alert('Đã xóa audio thuyết minh!');
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Lỗi xóa audio: ' + xhr.responseText);
            }
        });
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\datnv2\DuAn_TN\resources\views/admin/locations/edit.blade.php ENDPATH**/ ?>