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


<div class="tab-content" id="locationTabsContent">
    <!-- INFO TAB -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('admin.locations.update', [$location->id] + request()->query())); ?>" method="POST" enctype="multipart/form-data">
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
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $location->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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

                            <div class="mb-3">
                                <label for="thumbnail" class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                                <input type="file" class="form-control <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="thumbnail" name="thumbnail" accept="image/*">
                                <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if($location->thumbnail_url): ?>
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Ảnh đại diện hiện tại:</small>
                                        <img src="<?php echo e(asset('storage/' . $location->thumbnail_url)); ?>" class="img-thumbnail" style="max-height: 120px;" alt="Thumbnail">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?php echo e(route('admin.locations.index', request()->query())); ?>" class="btn btn-secondary">Quay lại</a>
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
                    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 col-sm-4 image-card" id="img-<?php echo e($img->id); ?>">
                        <div class="card h-100 position-relative">
                            <img src="<?php echo e(Storage::url($img->image_url)); ?>" class="card-img-top object-fit-cover" height="150" alt="">
                            <div class="position-absolute top-0 end-0 p-1">
                                <button class="btn btn-sm btn-danger btn-delete-image" data-id="<?php echo e($img->id); ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <style>
            .transition-transform {
                transition: transform 0.2s ease-in-out;
            }
            .rotate-180 {
                transform: rotate(180deg);
            }
            /* Custom Range Sliders track & thumb visual fixes */
            .form-range {
                height: 1.5rem;
                padding: 0;
                background: transparent;
                margin-top: 5px;
            }
            .form-range::-webkit-slider-runnable-track {
                width: 100%;
                height: 6px;
                cursor: pointer;
                background: #cbd5e1; /* visible slate-300 track */
                border-radius: 3px;
                border: none;
            }
            .form-range::-webkit-slider-thumb {
                height: 18px;
                width: 18px;
                border-radius: 50%;
                background: #ffc107; /* warning yellow */
                cursor: pointer;
                -webkit-appearance: none;
                margin-top: -6px; /* (6px - 18px)/2 */
                box-shadow: 0 1px 3px rgba(0,0,0,0.3);
                transition: transform 0.1s ease, background-color 0.1s ease;
            }
            .form-range::-webkit-slider-thumb:hover {
                transform: scale(1.15);
                background: #e0a800; /* slightly darker yellow */
            }
            /* Firefox styles */
            .form-range::-moz-range-track {
                width: 100%;
                height: 6px;
                cursor: pointer;
                background: #cbd5e1;
                border-radius: 3px;
                border: none;
            }
            .form-range::-moz-range-thumb {
                height: 18px;
                width: 18px;
                border-radius: 50%;
                background: #ffc107;
                cursor: pointer;
                border: none;
                box-shadow: 0 1px 3px rgba(0,0,0,0.3);
                transition: transform 0.1s ease, background-color 0.1s ease;
            }
            .form-range::-moz-range-thumb:hover {
                transform: scale(1.15);
                background: #e0a800;
            }
        </style>
        <?php if($location->audio_url): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-volume-up text-success me-2"></i>Trình phát audio thuyết minh hiện tại</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border <?php echo e(isset($location->attributes['tts_text']) ? 'mb-3' : ''); ?>">
                        <i class="fas fa-play-circle text-success fs-3"></i>
                        <div class="flex-grow-1">
                            <audio controls class="w-100" style="height: 40px;" id="currentAudioPlayer">
                                <source src="<?php echo e(asset('storage/' . $location->audio_url)); ?>" type="audio/mpeg">
                                Trình duyệt không hỗ trợ thẻ audio.
                            </audio>
                        </div>
                        <button class="btn btn-outline-danger" id="btnDeleteAudio">
                            <i class="fas fa-trash-alt me-1"></i> Xóa audio
                        </button>
                    </div>

                    <?php if(isset($location->attributes['tts_text'])): ?>
                        <div class="p-3 bg-light rounded border border-start-0 border-end-0 border-bottom-0">
                            <h6 class="fw-bold small text-secondary mb-2"><i class="fas fa-file-alt text-primary me-2"></i>Nội dung văn bản thuyết minh AI:</h6>
                            <div class="text-dark bg-white p-3 rounded border" style="font-size: 13px; line-height: 1.6; border-left: 4px solid #ffc107 !important; font-style: italic; white-space: pre-wrap;">"<?php echo e($location->attributes['tts_text']); ?>"</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info shadow-sm border-0 mb-4 py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fs-4 me-3 text-info"></i>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Chưa có audio thuyết minh</h6>
                        <p class="mb-0 text-muted small">Hãy chọn một trong hai phương thức bên dưới để thêm âm thanh thuyết minh cho địa điểm này khi xem ở chế độ 360°.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Upload thủ công -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-upload text-primary me-2"></i>Cách 1: Upload file thủ công</h6>
                        <small class="text-muted">Tải lên file âm thanh thuyết minh có sẵn trên thiết bị của bạn.</small>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center py-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-file-audio text-muted display-4 mb-2"></i>
                            <p class="text-muted small">Hỗ trợ các định dạng: MP3, WAV, OGG, M4A<br>Dung lượng tối đa: 20MB</p>
                        </div>
                        <input type="file" id="audioUploadInput" class="d-none" accept="audio/*">
                        <button type="button" class="btn btn-outline-primary fw-bold w-100 py-2" onclick="document.getElementById('audioUploadInput').click()">
                            <i class="fas fa-cloud-upload-alt me-1"></i> <?php echo e($location->audio_url ? 'Đổi file audio khác' : 'Chọn file từ thiết bị'); ?>

                        </button>
                    </div>
                </div>
            </div>

            <!-- Tạo bằng AI -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-robot text-warning me-2"></i>Cách 2: Tạo bằng AI Text-to-Speech (VieNeu-TTS)</h6>
                            <small class="text-muted">Chuyển đổi văn bản tiếng Việt thành giọng nói tự động sử dụng AI.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="ttsConnectionBadge" class="badge bg-secondary py-2 px-3" style="font-size: 12px; border-radius: 20px;">
                                <i class="fas fa-circle-notch fa-spin me-1"></i> Đang kiểm tra...
                            </span>
                            <button type="button" id="btnRetryTtsConnection" class="btn btn-sm btn-outline-secondary p-1 border-0" title="Kiểm tra lại kết nối" style="line-height: 1; border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="ttsVoiceSelect" class="form-label fw-bold small">Chọn giọng đọc (AI Voice) <span class="text-danger">*</span></label>
                            <select id="ttsVoiceSelect" class="form-select">
                                <option value="">Đang tải danh sách giọng đọc...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ttsTextInput" class="form-label fw-bold small">Văn bản thuyết minh <span class="text-danger">*</span></label>
                            <textarea id="ttsTextInput" class="form-control" rows="4" placeholder="Nhập đoạn thuyết minh giới thiệu về địa danh tại đây (Tối đa 5000 ký tự)..."><?php echo e(old('tts_text', $location->attributes['tts_text'] ?? $location->short_description)); ?></textarea>
                            <div class="form-text d-flex justify-content-end">
                                <span id="ttsCharCount">0/5000</span>
                            </div>
                        </div>

                        <!-- Cài đặt nâng cao -->
                        <div class="mb-3">
                            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 border-0 px-2 py-1 bg-light text-muted w-100 justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#ttsAdvancedSettings" aria-expanded="false" aria-controls="ttsAdvancedSettings" style="border-radius: 8px;">
                                <span class="fw-bold small"><i class="fas fa-sliders-h me-1 text-warning"></i> Cài đặt nâng cao (AI Settings)</span>
                                <i class="fas fa-chevron-down small transition-transform" id="advancedChevron"></i>
                            </button>
                            <div class="collapse mt-2" id="ttsAdvancedSettings">
                                <div class="card card-body bg-light border-0 py-3 px-3 shadow-none mb-0" style="border-radius: 8px;">
                                    <div class="row g-3">
                                        <!-- Cảm xúc -->
                                        <div class="col-md-6">
                                            <label for="ttsEmotionSelect" class="form-label fw-bold mb-1 text-secondary" style="font-size: 11px;">CẢM XÚC (EMOTION)</label>
                                            <select id="ttsEmotionSelect" class="form-select form-select-sm" style="border-radius: 6px;">
                                                <option value="natural" selected>Tự nhiên (Natural)</option>
                                                <option value="happy">Vui vẻ (Happy)</option>
                                                <option value="sad">Buồn bã (Sad)</option>
                                                <option value="angry">Tức giận (Angry)</option>
                                                <option value="fearful">Sợ hãi (Fearful)</option>
                                                <option value="excited">Phấn khích (Excited)</option>
                                            </select>
                                        </div>
                                        <!-- Độ ngẫu nhiên -->
                                        <div class="col-md-6">
                                            <label for="ttsTemperatureInput" class="form-label fw-bold mb-1 text-secondary" style="font-size: 11px;">ĐỘ NGẪU NHIÊN (TEMP: <span id="ttsTemperatureVal" class="text-dark">0.8</span>)</label>
                                            <input type="range" id="ttsTemperatureInput" class="form-range" min="0.1" max="1.5" step="0.05" value="0.8">
                                            <small class="text-muted d-block mt-1" style="font-size: 10px; line-height: 1.2;">Thấp: giọng đều, ít lỗi. Cao: truyền cảm, tự nhiên hơn.</small>
                                        </div>
                                        <!-- Top-K -->
                                        <div class="col-md-4">
                                            <label for="ttsTopKInput" class="form-label fw-bold mb-1 text-secondary" style="font-size: 11px;">GIỚI HẠN TỪ (TOP-K: <span id="ttsTopKVal" class="text-dark">25</span>)</label>
                                            <input type="range" id="ttsTopKInput" class="form-range" min="1" max="100" step="1" value="25">
                                            <small class="text-muted d-block mt-1" style="font-size: 10px; line-height: 1.2;">Lọc số lượng từ AI được chọn. Giúp tránh phát âm từ lạ.</small>
                                        </div>
                                        <!-- Top-P -->
                                        <div class="col-md-4">
                                            <label for="ttsTopPInput" class="form-label fw-bold mb-1 text-secondary" style="font-size: 11px;">TỶ LỆ LỌC (TOP-P: <span id="ttsTopPVal" class="text-dark">0.95</span>)</label>
                                            <input type="range" id="ttsTopPInput" class="form-range" min="0.1" max="1.0" step="0.05" value="0.95">
                                            <small class="text-muted d-block mt-1" style="font-size: 10px; line-height: 1.2;">Cao: đọc trôi chảy, tự nhiên. Thấp: đọc đều và khuôn mẫu.</small>
                                        </div>
                                        <!-- Phạt lặp từ -->
                                        <div class="col-md-4">
                                            <label for="ttsPenaltyInput" class="form-label fw-bold mb-1 text-secondary" style="font-size: 11px;">PHẠT LẶP TỪ (PENALTY: <span id="ttsPenaltyVal" class="text-dark">1.2</span>)</label>
                                            <input type="range" id="ttsPenaltyInput" class="form-range" min="0.5" max="2.0" step="0.05" value="1.2">
                                            <small class="text-muted d-block mt-1" style="font-size: 10px; line-height: 1.2;">Ngăn AI lặp từ hoặc phát tiếng ồn thừa. Nên giữ 1.0 - 1.2.</small>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <!-- Tiến trình xử lý AI (Progress Bar) -->
                        <div id="ttsProgressContainer" class="mb-3 d-none p-3 bg-light rounded border border-warning shadow-sm animate__animated animate__fadeIn">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span id="ttsProgressStatus" class="fw-bold text-primary"><i class="fas fa-spinner fa-spin me-1"></i>Đang kết nối tới máy chủ AI...</span>
                                <span id="ttsProgressPercent" class="fw-bold text-muted">0%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div id="ttsProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="mt-1 text-muted" style="font-size: 11px;">
                                <span id="ttsProgressTimer">Đã chạy: 0.0s</span>
                            </div>
                        </div>

                        <button type="button" id="btnGenerateTts" class="btn btn-warning fw-bold text-dark w-100 py-2">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status" id="ttsSpinner" aria-hidden="true"></span>
                            <i class="fas fa-robot me-1" id="ttsRobotIcon"></i>
                            Tạo âm thanh thuyết minh AI
                        </button>
                    </div>
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

    // Fetch voices list for VieNeu-TTS
    function loadTtsVoices() {
        let voiceSelect = $('#ttsVoiceSelect');
        let badge = $('#ttsConnectionBadge');
        
        badge.removeClass('bg-success bg-danger').addClass('bg-secondary')
             .html('<i class="fas fa-circle-notch fa-spin me-1"></i> Đang kết nối...')
             .attr('title', 'Đang kiểm tra kết nối tới máy chủ VieNeu-TTS...');
             
        $.ajax({
            url: '<?php echo e(route("admin.locations.tts_voices")); ?>',
            type: 'GET',
            success: function(res) {
                voiceSelect.empty();
                
                // Check if connection failed or error
                if (res.length === 0 || (res.length === 1 && res[0].id === 'error')) {
                    voiceSelect.append('<option value="">⚠️ Không có giọng đọc (Server Offline)</option>');
                    $('#btnGenerateTts').prop('disabled', true);
                    
                    let errorMsg = 'Không thể kết nối tới máy chủ VieNeu-TTS.';
                    if (res.length === 1 && res[0].name) {
                        errorMsg = res[0].name.replace('⚠️ ', '');
                    }
                    
                    badge.removeClass('bg-secondary bg-success').addClass('bg-danger')
                         .html('<i class="fas fa-exclamation-circle me-1"></i> Lỗi kết nối')
                         .attr('title', errorMsg);
                    return;
                }
                
                $('#btnGenerateTts').prop('disabled', false);
                
                badge.removeClass('bg-secondary bg-danger').addClass('bg-success')
                     .html('<i class="fas fa-check-circle me-1"></i> Kết nối tốt')
                     .attr('title', 'Đã kết nối thành công tới máy chủ VieNeu-TTS.');
                
                let savedVoice = localStorage.getItem('vieneu_tts_voice');
                res.forEach(function(voice) {
                    let selected = '';
                    if (savedVoice) {
                        selected = (voice.id === savedVoice) ? 'selected' : '';
                    } else {
                        // Fallback default selected for ngochuyen / ngoc-huyen
                        selected = (voice.id.toLowerCase() === 'ngochuyen' || voice.id.toLowerCase() === 'ngoc-huyen') ? 'selected' : '';
                    }
                    voiceSelect.append(`<option value="${voice.id}" ${selected}>${voice.name}</option>`);
                });
                
                updateTtsCharCount();
            },
            error: function(xhr) {
                voiceSelect.empty().append('<option value="">⚠️ Lỗi kết nối tới máy chủ TTS</option>');
                $('#btnGenerateTts').prop('disabled', true);
                
                badge.removeClass('bg-secondary bg-success').addClass('bg-danger')
                     .html('<i class="fas fa-times-circle me-1"></i> Mất kết nối')
                     .attr('title', 'Không thể kết nối tới máy chủ VieNeu-TTS (Cổng 8001). Vui lòng kiểm tra xem dịch vụ đã được khởi chạy chưa.');
            }
        });
    }

    // Retry TTS Connection
    $(document).on('click', '#btnRetryTtsConnection', function() {
        let btn = $(this);
        btn.prop('disabled', true).find('i').addClass('fa-spin');
        
        loadTtsVoices();
        
        setTimeout(function() {
            btn.prop('disabled', false).find('i').removeClass('fa-spin');
        }, 1000);
    });

    // Restore active tab from localStorage on load
    let activeTabId = localStorage.getItem('active_location_tab');
    if (activeTabId) {
        let activeTab = $('#' + activeTabId);
        if (activeTab.length) {
            let tabTrigger = new bootstrap.Tab(activeTab[0]);
            tabTrigger.show();
        }
    }

    // Save active tab to localStorage on tab switch
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('active_location_tab', $(e.target).attr('id'));
    });

    // Load voices on tab show
    $('#audio-tab').on('shown.bs.tab', function () {
        loadTtsVoices();
    });

    // Trigger loading voices immediately if audio tab is active on load
    if ($('#audio-tab').hasClass('active')) {
        loadTtsVoices();
    }

    // Count chars in TTS text input
    function updateTtsCharCount() {
        let text = $('#ttsTextInput').val() || '';
        let len = text.length;
        $('#ttsCharCount').text(len + '/5000');
        if (len > 5000) {
            $('#ttsCharCount').addClass('text-danger');
            $('#btnGenerateTts').prop('disabled', true);
        } else {
            $('#ttsCharCount').removeClass('text-danger');
            // Check if voices are loaded before enabling
            let val = $('#ttsVoiceSelect').val();
            if (val && val !== 'error' && val !== '') {
                $('#btnGenerateTts').prop('disabled', false);
            }
        }
    }
    
    $('#ttsTextInput').on('input', updateTtsCharCount);

    // Update advanced settings slider values and save to localStorage
    $('#ttsTemperatureInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTemperatureVal').text(val);
        localStorage.setItem('vieneu_tts_temp', val);
    });
    $('#ttsTopKInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTopKVal').text(val);
        localStorage.setItem('vieneu_tts_top_k', val);
    });
    $('#ttsTopPInput').on('input', function() {
        let val = $(this).val();
        $('#ttsTopPVal').text(val);
        localStorage.setItem('vieneu_tts_top_p', val);
    });
    $('#ttsPenaltyInput').on('input', function() {
        let val = $(this).val();
        $('#ttsPenaltyVal').text(val);
        localStorage.setItem('vieneu_tts_penalty', val);
    });

    // Save dropdown changes to localStorage
    $(document).on('change', '#ttsVoiceSelect', function() {
        localStorage.setItem('vieneu_tts_voice', $(this).val());
    });
    $('#ttsEmotionSelect').change(function() {
        localStorage.setItem('vieneu_tts_emotion', $(this).val());
    });

    // Restore settings from localStorage on load
    $(document).ready(function() {
        let savedEmotion = localStorage.getItem('vieneu_tts_emotion');
        if (savedEmotion) {
            $('#ttsEmotionSelect').val(savedEmotion);
        }

        let savedTemp = localStorage.getItem('vieneu_tts_temp');
        if (savedTemp) {
            $('#ttsTemperatureInput').val(savedTemp);
            $('#ttsTemperatureVal').text(savedTemp);
        }

        let savedTopK = localStorage.getItem('vieneu_tts_top_k');
        if (savedTopK) {
            $('#ttsTopKInput').val(savedTopK);
            $('#ttsTopKVal').text(savedTopK);
        }

        let savedTopP = localStorage.getItem('vieneu_tts_top_p');
        if (savedTopP) {
            $('#ttsTopPInput').val(savedTopP);
            $('#ttsTopPVal').text(savedTopP);
        }

        let savedPenalty = localStorage.getItem('vieneu_tts_penalty');
        if (savedPenalty) {
            $('#ttsPenaltyInput').val(savedPenalty);
            $('#ttsPenaltyVal').text(savedPenalty);
        }
    });

    // Toggle chevron rotation
    $('#ttsAdvancedSettings').on('show.bs.collapse', function () {
        $('#advancedChevron').addClass('rotate-180');
    }).on('hide.bs.collapse', function () {
        $('#advancedChevron').removeClass('rotate-180');
    });

    // Generate TTS Audio
    $('#btnGenerateTts').click(function() {
        let text = $('#ttsTextInput').val().trim();
        let voiceId = $('#ttsVoiceSelect').val();
        let emotion = $('#ttsEmotionSelect').val();
        let temperature = $('#ttsTemperatureInput').val();
        let topK = $('#ttsTopKInput').val();
        let topP = $('#ttsTopPInput').val();
        let repetitionPenalty = $('#ttsPenaltyInput').val();

        if (!text) {
            alert('Vui lòng nhập văn bản thuyết minh.');
            return;
        }

        let btn = $(this);
        let spinner = $('#ttsSpinner');
        let robotIcon = $('#ttsRobotIcon');

        // Show loading state
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        robotIcon.addClass('d-none');
        btn.contents().last()[0].textContent = ' Đang xử lý AI TTS...';

        // Calculate expected time based on character length
        // V3 Turbo CPU averages around 40-50 chars per second of generation time.
        let charLen = text.length;
        let expectedSec = Math.max(3, Math.ceil(charLen / 45)) + 3; 
        
        $('#ttsProgressContainer').removeClass('d-none');
        $('#ttsProgressPercent').text('0%');
        $('#ttsProgressBar').css('width', '0%').attr('aria-valuenow', 0);
        $('#ttsProgressTimer').text('Đã chạy: 0.0s');
        
        let start = Date.now();
        let progressInterval = setInterval(function() {
            let elapsed = (Date.now() - start) / 1000;
            $('#ttsProgressTimer').text('Đã chạy: ' + elapsed.toFixed(1) + 's');
            
            // Calculate percentage (cap at 95% until done)
            let percent = Math.min(95, Math.round((elapsed / expectedSec) * 100));
            $('#ttsProgressPercent').text(percent + '%');
            $('#ttsProgressBar').css('width', percent + '%').attr('aria-valuenow', percent);
            
            // Dynamic statuses based on elapsed time
            if (elapsed < 1.5) {
                $('#ttsProgressStatus').html('<i class="fas fa-cog fa-spin me-1 text-warning"></i> Đang khởi tạo mô hình AI...');
            } else if (elapsed < 3) {
                $('#ttsProgressStatus').html('<i class="fas fa-brain fa-spin me-1 text-warning"></i> Đang phân tích cú pháp văn bản...');
            } else if (elapsed < expectedSec * 0.8) {
                $('#ttsProgressStatus').html('<i class="fas fa-volume-up fa-spin me-1 text-warning"></i> Đang tổng hợp giọng nói AI...');
            } else {
                $('#ttsProgressStatus').html('<i class="fas fa-file-audio fa-spin me-1 text-warning"></i> Đang hoàn thiện tệp WAV...');
            }
        }, 100);

        $.ajax({
            url: '<?php echo e(route("admin.locations.generate_tts", $location->id)); ?>',
            type: 'POST',
            data: {
                text: text,
                voice_id: voiceId,
                emotion: emotion,
                temperature: temperature,
                top_k: topK,
                top_p: topP,
                repetition_penalty: repetitionPenalty
            },
            success: function(res) {
                clearInterval(progressInterval);
                $('#ttsProgressPercent').text('100%');
                $('#ttsProgressBar').css('width', '100%').attr('aria-valuenow', 100);
                $('#ttsProgressStatus').html('<i class="fas fa-check-circle text-success me-1"></i> Hoàn thành!');

                if (res.success) {
                    alert('Tạo âm thanh thuyết minh AI thành công!');
                    location.reload();
                } else {
                    alert('Lỗi tạo âm thanh: ' + res.message);
                    resetTtsBtn();
                }
            },
            error: function(xhr) {
                clearInterval(progressInterval);
                $('#ttsProgressContainer').addClass('d-none');
                let msg = xhr.responseJSON?.message || xhr.responseText || 'Lỗi không xác định';
                alert('Lỗi khi gửi yêu cầu chuyển đổi TTS: ' + msg);
                resetTtsBtn();
            }
        });

        function resetTtsBtn() {
            btn.prop('disabled', false);
            spinner.addClass('d-none');
            robotIcon.removeClass('d-none');
            btn.contents().last()[0].textContent = ' Tạo âm thanh thuyết minh AI';
        }
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/locations/edit.blade.php ENDPATH**/ ?>