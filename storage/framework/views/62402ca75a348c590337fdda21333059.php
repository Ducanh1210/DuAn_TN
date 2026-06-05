<?php $__env->startSection('title', 'Tin Tức & Cẩm Nang Du Lịch Hà Nam'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Breadcrumb or simple Title -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-uppercase" style="color: #2c3e50;">TIN TỨC - SỰ KIỆN</h2>
    </div>

    <div class="row">
        <!-- Left Column: Main News List -->
        <div class="col-lg-8 pe-lg-4">
            <div class="d-flex flex-column gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $newsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="news-list-item pb-4 border-bottom">
                        <a href="<?php echo e(route('client.news.show', $item->slug)); ?>" class="text-decoration-none">
                            <div class="row g-3 align-items-start">
                                <!-- Thumbnail -->
                                <div class="col-sm-4 col-md-5">
                                    <div class="img-wrapper rounded overflow-hidden shadow-sm">
                                        <img src="<?php echo e($item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/600x400?text=No+Image'); ?>" alt="<?php echo e($item->title); ?>" class="img-fluid w-100" style="aspect-ratio: 4/3; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>
                                </div>
                                <!-- Content -->
                                <div class="col-sm-8 col-md-7">
                                    <h4 class="news-title fw-bold mb-2" style="color: #1a1a1a; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: color 0.2s;">
                                        <?php echo e($item->title); ?>

                                    </h4>
                                    <p class="news-excerpt text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.5;">
                                        <?php echo e(strip_tags($item->summary ?? $item->content)); ?>

                                    </p>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">Chưa có bài viết nào.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            
            <div class="mt-4 custom-pagination">
                <?php echo e($newsList->links('pagination::bootstrap-5')); ?>

            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="sidebar position-sticky" style="top: 100px;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-danger border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #c0392b;">TIN NỔI BẬT</h5>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $popularNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="sidebar-item">
                            <a href="<?php echo e(route('client.news.show', $item->slug)); ?>" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="<?php echo e($item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image'); ?>" alt="<?php echo e($item->title); ?>" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        <?php echo e($item->title); ?>

                                    </h6>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted small">Chưa có tin nổi bật.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- EVENTS BLOCK IN SIDEBAR -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-success border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #27ae60;">SỰ KIỆN SẮP TỚI</h5>
                    <a href="<?php echo e(route('client.events.index')); ?>" class="small text-success text-decoration-none fw-semibold">Xem thêm <i class="fa-solid fa-angle-right"></i></a>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $upcomingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="sidebar-item">
                            <a href="<?php echo e(route('client.events.show', $item->slug)); ?>" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="<?php echo e($item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image'); ?>" alt="<?php echo e($item->name); ?>" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        <?php echo e($item->name); ?>

                                    </h6>
                                    <small class="text-success fw-semibold"><i class="fa-regular fa-calendar me-1"></i> <?php echo e($item->start_time ? $item->start_time->format('d/m/Y') : ''); ?></small>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted small">Không có sự kiện nào sắp tới.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .news-list-item:hover .news-title { color: #27ae60 !important; }
    .news-list-item:hover .img-wrapper img { transform: scale(1.05); }
    
    .sidebar-item:hover .sidebar-title { color: #2980b9 !important; }

    /* Pagination */
    .custom-pagination .page-link {
        color: #2c3e50;
        border: none;
        padding: 8px 12px;
        margin: 0 2px;
        font-weight: 600;
        background: transparent;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #2c3e50;
        color: white;
        border-radius: 4px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/news/index.blade.php ENDPATH**/ ?>