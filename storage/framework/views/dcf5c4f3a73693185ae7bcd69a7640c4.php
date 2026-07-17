<?php $__env->startSection('title', 'Quản lý Người dùng'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Người dùng</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th width="80">Avatar</th>
                        <th>Tên hiển thị</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Điểm</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($user->id); ?></td>
                            <td>
                                <?php if($user->avatar_url): ?>
                                    <img src="<?php echo e(str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : asset('storage/' . $user->avatar_url)); ?>" alt="Avatar" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <?php echo e(strtoupper(substr($user->display_name ?? $user->username, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo e($user->display_name); ?></strong></td>
                            <td><?php echo e($user->username); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><strong class="text-primary"><?php echo e($user->points); ?></strong></td>
                            <td>
                                <?php if($user->role == 'admin'): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php elseif($user->role == 'moderator'): ?>
                                    <span class="badge bg-warning text-dark">Moderator</span>
                                <?php else: ?>
                                    <span class="badge bg-info">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user->status == 'active'): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php elseif($user->status == 'inactive'): ?>
                                    <span class="badge bg-secondary">Chưa kích hoạt</span>
                                <?php else: ?>
                                    <span class="badge bg-dark">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-sm btn-info text-white" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                <?php if(auth()->id() != $user->id): ?>
                                    <?php if($user->status == 'banned'): ?>
                                        <form action="<?php echo e(route('admin.users.toggle_status', $user->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-sm btn-success" title="Mở khóa"><i class="fas fa-unlock"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('admin.users.toggle_status', $user->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản này?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Khóa"><i class="fas fa-lock"></i></button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Chưa có người dùng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            <?php echo e($users->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/users/index.blade.php ENDPATH**/ ?>