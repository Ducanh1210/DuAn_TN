<?php $__env->startSection('title', 'Địa Điểm Yêu Thích'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-uppercase mb-0" style="color: #2c3e50;">Địa Điểm Yêu Thích Của Tôi</h2>
    </div>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $loc = $fav->location;
            ?>
            <div class="col-md-4 col-sm-6 fav-card-wrapper" id="fav-<?php echo e($fav->id); ?>">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden fav-card">
                    <a href="<?php echo e(route('client.locations.360', $loc->slug)); ?>" class="text-decoration-none">
                        <div class="position-relative" style="aspect-ratio: 4/3;">
                            <img src="<?php echo e($loc->thumbnail_url ? (str_starts_with($loc->thumbnail_url, 'http') ? $loc->thumbnail_url : asset('storage/' . ltrim($loc->thumbnail_url, '/'))) : 'https://via.placeholder.com/400x300?text=No+Image'); ?>" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="<?php echo e($loc->name); ?>">
                            <?php if($loc->category): ?>
                                <span class="position-absolute top-0 start-0 m-2 badge" style="background-color: <?php echo e($loc->category->icon_color ?? '#primary'); ?>"><?php echo e($loc->category->name); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark text-truncate"><?php echo e($loc->name); ?></h5>
                            <p class="card-text text-muted small mb-0 text-truncate"><i class="fa-solid fa-location-dot me-1"></i> <?php echo e($loc->address); ?></p>
                        </div>
                    </a>
                    
                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle btn-remove-fav" data-id="<?php echo e($loc->id); ?>" title="Xóa khỏi yêu thích" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                        <i class="fa-solid fa-heart-crack"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="fa-regular fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                <h4 class="text-muted">Bạn chưa lưu địa điểm nào.</h4>
                <a href="<?php echo e(url('/')); ?>" class="btn btn-primary mt-3">Khám phá ngay</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4 custom-pagination">
        <?php echo e($favorites->links('pagination::bootstrap-5')); ?>

    </div>
</div>

<style>
    .fav-card { transition: transform 0.2s, box-shadow 0.2s; }
    .fav-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .btn-remove-fav { opacity: 0; transition: opacity 0.2s, background 0.2s; }
    .fav-card:hover .btn-remove-fav { opacity: 1; }
    .btn-remove-fav:hover { background: #c0392b !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const removeBtns = document.querySelectorAll('.btn-remove-fav');
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!confirm('Bạn có chắc chắn muốn xóa địa điểm này khỏi danh sách yêu thích?')) return;
            
            const locId = this.dataset.id;
            const cardWrapper = this.closest('.fav-card-wrapper');
            
            fetch(`/locations/${locId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'removed') {
                    cardWrapper.style.transition = 'all 0.3s';
                    cardWrapper.style.opacity = '0';
                    cardWrapper.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        cardWrapper.remove();
                        // check if empty
                        if (document.querySelectorAll('.fav-card-wrapper').length === 0) {
                            location.reload(); // reload to show empty state
                        }
                    }, 300);
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/favorites/index.blade.php ENDPATH**/ ?>