<?php $__env->startSection('title', 'Sự Kiện Lễ Hội Tại Hà Nam'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Breadcrumb or simple Title -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-uppercase" style="color: #2c3e50;">SỰ KIỆN - LỄ HỘI</h2>
    </div>

    <div class="row">
        <!-- Left Column: Main Events List -->
        <div class="col-lg-8 pe-lg-4">
            <div class="d-flex flex-column gap-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $now = \Carbon\Carbon::now();
                        $isHappening = $event->start_time <= $now && $event->end_time >= $now;
                        $isUpcoming = $event->start_time > $now;
                        $isPast = $event->end_time < $now;
                    ?>
                    <article class="news-list-item pb-4 border-bottom">
                        <a href="<?php echo e(route('client.events.show', $event->slug)); ?>" class="text-decoration-none">
                            <div class="row g-3 align-items-start">
                                <!-- Thumbnail -->
                                <div class="col-sm-4 col-md-5 position-relative">
                                    <div class="img-wrapper rounded overflow-hidden shadow-sm">
                                        <img src="<?php echo e($event->featured_image ? (str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . ltrim($event->featured_image, '/'))) : 'https://via.placeholder.com/600x400?text=No+Image'); ?>" alt="<?php echo e($event->name); ?>" class="img-fluid w-100" style="aspect-ratio: 4/3; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isHappening): ?>
                                        <span class="position-absolute top-0 start-0 m-2 badge bg-danger shadow-sm">Đang diễn ra</span>
                                    <?php elseif($isUpcoming): ?>
                                        <span class="position-absolute top-0 start-0 m-2 badge bg-success shadow-sm">Sắp tới</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <!-- Content -->
                                <div class="col-sm-8 col-md-7">
                                    <h4 class="news-title fw-bold mb-2" style="color: #1a1a1a; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: color 0.2s;">
                                        <?php echo e($event->name); ?>

                                    </h4>
                                    <p class="small fw-semibold text-danger mb-2"><i class="fa-regular fa-calendar me-1"></i> <?php echo e($event->start_time ? $event->start_time->format('d/m/Y H:i') : ''); ?> - <?php echo e($event->end_time ? $event->end_time->format('d/m/Y H:i') : ''); ?></p>
                                    <p class="small text-muted mb-2"><i class="fa-solid fa-location-dot me-1"></i> <?php echo e($event->location_text); ?></p>
                                    <p class="news-excerpt text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.5;">
                                        <?php echo e(strip_tags($event->description)); ?>

                                    </p>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">Chưa có sự kiện nào sắp tới.</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            
            <div class="mt-4 custom-pagination">
                <?php echo e($events->links('pagination::bootstrap-5')); ?>

            </div>
        </div>

        <!-- Right Column: Banner / Or something else (optional) -->
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="sidebar position-sticky" style="top: 100px;">
                <div class="p-4 bg-light rounded-3 text-center border">
                    <h5 class="fw-bold text-danger mb-3">Tải Ứng Dụng</h5>
                    <p class="small text-muted mb-3">Khám phá Hà Nam dễ dàng hơn trên điện thoại của bạn.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-dark"><i class="fa-brands fa-apple me-1"></i> iOS</button>
                        <button class="btn btn-success"><i class="fa-brands fa-android me-1"></i> Android</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .news-list-item:hover .news-title { color: #27ae60 !important; }
    .news-list-item:hover .img-wrapper img { transform: scale(1.05); }

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

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/events/index.blade.php ENDPATH**/ ?>