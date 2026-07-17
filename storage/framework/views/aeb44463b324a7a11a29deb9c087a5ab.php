<?php $__env->startSection('title', $location->name . ' - Chi tiết địa điểm'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="#"><?php echo e($location->category->name ?? 'Danh mục'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($location->name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <!-- Title & Favorite Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2 fw-bold text-dark mb-0"><?php echo e($location->name); ?></h1>
                <?php if(auth()->guard()->check()): ?>
                <button id="favoriteBtn" class="btn <?php echo e($isFavorited ? 'btn-danger' : 'btn-outline-danger'); ?> rounded-circle p-2" onclick="toggleFavorite()" style="width: 44px; height: 44px;">
                    <i class="<?php echo e($isFavorited ? 'fa-solid' : 'fa-regular'); ?> fa-heart fs-5"></i>
                </button>
                <?php else: ?>
                <button class="btn btn-outline-danger rounded-circle p-2" onclick="alert('Vui lòng đăng nhập để lưu địa điểm này.')" style="width: 44px; height: 44px;">
                    <i class="fa-regular fa-heart fs-5"></i>
                </button>
                <?php endif; ?>
            </div>
            
            <p class="text-muted mb-4"><i class="fa-solid fa-map-marker-alt me-2 text-danger"></i> <?php echo e($location->address); ?></p>

            <!-- Actions (360) -->
            <div class="mb-4">
                <a href="<?php echo e(route('client.locations.360', $location->slug)); ?>" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm">
                    <i class="fa-solid fa-vr-cardboard me-2"></i> Khám phá 360°
                </a>
            </div>

            <!-- Images Carousel -->
            <?php if($location->images->count() > 0): ?>
            <div id="locationImagesCarousel" class="carousel slide mb-4 rounded overflow-hidden shadow-sm" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $__currentLoopData = $location->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-item <?php echo e($index === 0 ? 'active' : ''); ?>">
                        <img src="<?php echo e(str_starts_with($image->image_url, 'http') ? $image->image_url : asset('storage/' . ltrim($image->image_url, '/'))); ?>" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Image">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($location->images->count() > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#locationImagesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#locationImagesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Description -->
            <div class="bg-white p-4 rounded shadow-sm mb-4 content-body">
                <h4 class="fw-bold mb-3 border-bottom pb-2">Giới thiệu</h4>
                <div class="text-muted lh-lg">
                    <?php echo $location->description; ?>

                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white p-4 rounded shadow-sm">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Bình luận & Đánh giá (<?php echo e($comments->count()); ?>)</h4>
                
                <!-- Comment Form -->
                <?php if(auth()->guard()->check()): ?>
                <form action="<?php echo e(route('client.comments.store', $location->id)); ?>" method="POST" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <textarea name="content" rows="3" class="form-control" placeholder="Chia sẻ trải nghiệm của bạn về địa điểm này..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="rating-stars text-warning fs-5">
                            <!-- Simple rating input for UI, can be enhanced later -->
                            <select name="rating" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="5">5 Sao (Tuyệt vời)</option>
                                <option value="4">4 Sao (Rất tốt)</option>
                                <option value="3">3 Sao (Bình thường)</option>
                                <option value="2">2 Sao (Kém)</option>
                                <option value="1">1 Sao (Tệ)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold rounded-pill">Gửi bình luận</button>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-secondary text-center mb-4">
                    Vui lòng <a href="<?php echo e(route('login')); ?>" class="text-primary fw-bold text-decoration-none">đăng nhập</a> để để lại bình luận.
                </div>
                <?php endif; ?>

                <!-- Comments List -->
                <div class="comments-list">
                    <?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                        <div class="flex-shrink-0">
                            <?php if($comment->user->avatar_url): ?>
                                <img src="<?php echo e(str_starts_with($comment->user->avatar_url, 'http') ? $comment->user->avatar_url : asset($comment->user->avatar_url)); ?>" alt="Avatar" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold" style="width: 48px; height: 48px; font-size: 20px;">
                                    <?php echo e(strtoupper(substr($comment->user->display_name ?? $comment->user->username, 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0 text-dark"><?php echo e($comment->user->display_name ?? $comment->user->username); ?></h6>
                                <small class="text-muted"><?php echo e($comment->created_at->diffForHumans()); ?></small>
                            </div>
                            <?php if($comment->rating): ?>
                            <div class="text-warning mb-2" style="font-size: 14px;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="fa-<?php echo e($i <= $comment->rating ? 'solid' : 'regular'); ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <?php endif; ?>
                            <p class="text-secondary mb-2" style="line-height: 1.5;"><?php echo e($comment->content); ?></p>
                            
                            <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->id() === $comment->user_id || auth()->user()->role === 'admin'): ?>
                                <form action="<?php echo e(route('client.comments.destroy', $comment->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none" style="font-size: 13px;"><i class="fa-solid fa-trash me-1"></i>Xóa</button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted py-4">
                        Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nhận!
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded shadow-sm sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Thông tin thêm</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3 text-center" style="width: 40px;">
                            <i class="fa-solid fa-layer-group text-primary"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Danh mục</small>
                            <span class="fw-semibold text-dark"><?php echo e($location->category->name ?? 'N/A'); ?></span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3 text-center" style="width: 40px;">
                            <i class="fa-solid fa-heart text-danger"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Lượt lưu</small>
                            <span class="fw-semibold text-dark"><span id="favCount"><?php echo e($favoriteCount); ?></span> người đã lưu</span>
                        </div>
                    </li>
                    <?php if($location->audio_url): ?>
                    <li class="d-flex align-items-center">
                        <div class="bg-light rounded p-2 me-3 text-center" style="width: 40px;">
                            <i class="fa-solid fa-volume-high text-success"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Thuyết minh</small>
                            <span class="fw-semibold text-dark">Có sẵn trong Khám phá 360</span>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if(auth()->guard()->check()): ?>
<script>
    function toggleFavorite() {
        fetch(`<?php echo e(route('client.favorites.toggle', $location->id)); ?>`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const btn = document.getElementById('favoriteBtn');
            const icon = btn.querySelector('i');
            const favCountSpan = document.getElementById('favCount');
            let currentCount = parseInt(favCountSpan.innerText);

            if (data.status === 'added') {
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                favCountSpan.innerText = currentCount + 1;
            } else {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular');
                favCountSpan.innerText = currentCount - 1;
            }
        })
        .catch(error => console.error('Error toggling favorite:', error));
    }
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/locations/show.blade.php ENDPATH**/ ?>