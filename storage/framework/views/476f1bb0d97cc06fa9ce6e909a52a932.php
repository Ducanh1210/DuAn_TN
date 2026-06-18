<?php $__env->startSection('title', 'Quản lý Địa điểm'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.locations.create', request()->query())); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm mới</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Form Lọc & Tìm kiếm -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="<?php echo e(route('admin.locations.index')); ?>" method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên địa điểm (nhấn Enter)..." value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80" class="text-center">
                            <a href="<?php echo e(request()->fullUrlWithQuery(['sort_dir' => $sortDir === 'desc' ? 'asc' : 'desc', 'page' => null])); ?>" class="text-dark text-decoration-none d-inline-flex align-items-center justify-content-center">
                                ID 
                                <?php if($sortDir === 'asc'): ?>
                                    <i class="fas fa-sort-up ms-1 text-muted opacity-50" style="font-size: 0.8em;"></i>
                                <?php else: ?>
                                    <i class="fas fa-sort-down ms-1 text-muted opacity-50" style="font-size: 0.8em;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th width="100">Ảnh</th>
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
                            <td>
                                <?php if($item->thumbnail_url): ?>
                                    <img src="<?php echo e(asset('storage/' . $item->thumbnail_url)); ?>" class="rounded shadow-sm border" style="width: 65px; height: 40px; object-fit: cover;" alt="<?php echo e($item->name); ?>">
                                <?php else: ?>
                                    <img src="https://placehold.co/400x250/e2e8f0/475569?text=No+Image" class="rounded shadow-sm border" style="width: 65px; height: 40px; object-fit: cover;" alt="No Image">
                                <?php endif; ?>
                            </td>
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
                                <a href="<?php echo e(route('admin.locations.edit', [$item->id] + request()->query())); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.locations.destroy', $item->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa điểm này?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">Chưa có địa điểm nào.</td>
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

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\datnv2\DuAn_TN\resources\views/admin/locations/index.blade.php ENDPATH**/ ?>