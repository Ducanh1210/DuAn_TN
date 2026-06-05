<?php $__env->startSection('title', 'Quản lý Địa điểm'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.locations.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">ID</th>
                        <th>Tên Địa điểm</th>
                        <th>Danh mục</th>
                        <th>Tọa độ (Lat, Lng)</th>
                        <th class="text-center">Trạng thái</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="text-center"><?php echo e($item->id); ?></td>
                            <td class="fw-bold"><?php echo e($item->name); ?></td>
                            <td><?php echo e($item->category->name ?? 'N/A'); ?></td>
                            <td><small class="text-muted"><?php echo e($item->lat); ?>, <?php echo e($item->lng); ?></small></td>
                            <td class="text-center">
                                <?php if($item->status == 'published'): ?>
                                    <span class="badge bg-success">Công khai</span>
                                <?php elseif($item->status == 'draft'): ?>
                                    <span class="badge bg-warning text-dark">Bản nháp</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(ucfirst($item->status)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo e(route('admin.locations.edit', $item->id)); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.locations.destroy', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Chưa có địa điểm nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($locations->hasPages()): ?>
    <div class="card-footer bg-white">
        <?php echo e($locations->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/locations/index.blade.php ENDPATH**/ ?>