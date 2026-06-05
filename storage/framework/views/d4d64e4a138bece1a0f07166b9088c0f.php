<?php $__env->startSection('title', $news->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('client.news.index')); ?>">Tin tức</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e(Str::limit($news->title, 50)); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                <span class="badge bg-primary mb-3"><?php echo e($news->type_label); ?></span>
                <h1 class="fw-bold mb-3"><?php echo e($news->title); ?></h1>
                <div class="d-flex align-items-center text-muted mb-4 pb-4 border-bottom">
                    <span class="me-4"><i class="fa-regular fa-calendar me-2"></i> <?php echo e($news->published_at ? $news->published_at->format('d/m/Y') : $news->created_at->format('d/m/Y')); ?></span>
                    <span><i class="fa-regular fa-eye me-2"></i> <?php echo e($news->view_count); ?> lượt xem</span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($news->featured_image): ?>
                    <img src="<?php echo e(str_starts_with($news->featured_image, 'http') ? $news->featured_image : asset('storage/' . ltrim($news->featured_image, '/'))); ?>" class="img-fluid rounded-3 mb-4 w-100" alt="<?php echo e($news->title); ?>" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($news->summary): ?>
                    <div class="lead fw-semibold mb-4 text-dark" style="line-height: 1.8;">
                        <?php echo e($news->summary); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="content-body" style="line-height: 1.8; font-size: 16px;">
                    <?php echo $news->content; ?>

                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="bg-white p-4 rounded-4 shadow-sm position-sticky" style="top: 100px;">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Tin tức liên quan</h4>
                <div class="d-flex flex-column gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $relatedNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('client.news.show', $item->slug)); ?>" class="text-decoration-none">
                            <div class="d-flex gap-3 align-items-center group-hover">
                                <img src="<?php echo e($item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150'); ?>" alt="<?php echo e($item->title); ?>" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="text-dark fw-bold mb-1 text-hover-primary" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo e($item->title); ?></h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?php echo e($item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y')); ?></small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted">Không có bài viết nào khác.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-hover-primary { transition: color 0.2s; }
    .group-hover:hover .text-hover-primary { color: #0072FF !important; }
    .content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/news/show.blade.php ENDPATH**/ ?>