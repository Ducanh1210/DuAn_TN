<?php $__env->startSection('title', 'Quản lý Danh mục'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                        <th width="50" class="text-center">ID</th>
                        <th width="60" class="text-center">Icon</th>
                        <th>Tên Danh mục</th>
                        <th>Slug</th>
                        <th class="text-center">Thứ tự</th>
                        <th class="text-center">Trạng thái</th>
                        <th width="150" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <td class="text-center"><?php echo e($item->id); ?></td>
                            <td class="text-center">
                                <?php if($item->icon): ?>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <img src="<?php echo e(asset($item->icon)); ?>" alt="Icon" style="height: 32px; width: 32px; object-fit: contain;">
                                        <div style="width: 20px; height: 8px; background-color: <?php echo e($item->icon_color ?? '#ef4444'); ?>; border-radius: 4px; border: 1px solid #ddd;" title="Màu ghim: <?php echo e($item->icon_color ?? '#ef4444'); ?>"></div>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                        <div style="width: 20px; height: 8px; background-color: <?php echo e($item->icon_color ?? '#ef4444'); ?>; border-radius: 4px; border: 1px solid #ddd;" title="Màu ghim: <?php echo e($item->icon_color ?? '#ef4444'); ?>"></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?php echo e($item->name); ?></td>
                            <td><?php echo e($item->slug); ?></td>
                            <td class="text-center"><?php echo e($item->display_order); ?></td>
                            <td class="text-center">
                                <?php if($item->status == 'active'): ?>
                                    <span class="badge bg-success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Đang ẩn</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo e(route('admin.categories.edit', $item->id)); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.categories.destroy', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Chưa có danh mục nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($categories->hasPages()): ?>
    <div class="card-footer bg-white">
        <?php echo e($categories->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\datnv2\DuAn_TN\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>