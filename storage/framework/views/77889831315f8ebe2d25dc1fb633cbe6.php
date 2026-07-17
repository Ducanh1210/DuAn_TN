<?php $__env->startSection('title', $event->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('client.events.index')); ?>">Sự kiện</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e(Str::limit($event->title, 50)); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-4">
                <span class="badge bg-danger mb-3 px-3 py-2 fs-6"><i class="fa-solid fa-fire me-1"></i> Sự kiện</span>
                
                <h1 class="fw-bold mb-4 text-danger"><?php echo e($event->title); ?></h1>
                
                <div class="text-muted small mb-4">
                    <i class="fa-regular fa-calendar me-1"></i> Xuất bản: <?php echo e($event->published_at ? $event->published_at->format('d/m/Y H:i') : $event->created_at->format('d/m/Y H:i')); ?>

                    <span class="mx-2">|</span>
                    <i class="fa-solid fa-eye me-1"></i> <?php echo e(number_format($event->view_count)); ?> lượt xem
                </div>
                
                <?php if($event->featured_image): ?>
                    <img src="<?php echo e(str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . ltrim($event->featured_image, '/'))); ?>" class="img-fluid rounded-3 mb-4 w-100" alt="<?php echo e($event->title); ?>" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>

                <?php if($event->summary): ?>
                    <div class="alert alert-light border shadow-sm mb-4" style="font-size: 1.1rem; font-style: italic;">
                        <?php echo e($event->summary); ?>

                    </div>
                <?php endif; ?>

                <?php if($event->content): ?>
                    <h4 class="fw-bold mb-3 border-start border-4 border-danger ps-3">Thông tin chi tiết</h4>
                    <div class="content-body mb-4" style="line-height: 1.8; font-size: 16px;">
                        <?php echo $event->content; ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="bg-white p-4 rounded-4 shadow-sm position-sticky" style="top: 100px;">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Sự kiện khác</h4>
                <div class="d-flex flex-column gap-3">
                    <?php $__empty_1 = true; $__currentLoopData = $relatedEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('client.events.show', $item->slug)); ?>" class="text-decoration-none">
                            <div class="d-flex gap-3 align-items-center group-hover">
                                <img src="<?php echo e($item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150'); ?>" alt="<?php echo e($item->title); ?>" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="text-dark fw-bold mb-1 text-hover-danger" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo e($item->title); ?></h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?php echo e($item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y')); ?></small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Không có sự kiện nào khác.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-hover-danger { transition: color 0.2s; }
    .group-hover:hover .text-hover-danger { color: #dc3545 !important; }
    .content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/events/show.blade.php ENDPATH**/ ?>