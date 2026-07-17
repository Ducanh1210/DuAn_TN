<?php $__env->startSection('title', 'Chi tiết Người dùng'); ?>

<?php $__env->startSection('actions'); ?>
    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <?php if($user->avatar_url): ?>
                    <img src="<?php echo e(str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : asset($user->avatar_url)); ?>" alt="Avatar" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 64px;">
                        <?php echo e(strtoupper(substr($user->display_name ?? $user->username, 0, 1))); ?>

                    </div>
                <?php endif; ?>
                <h4 class="font-weight-bold"><?php echo e($user->display_name); ?></h4>
                <p class="text-muted"><?php echo e('@' . $user->username); ?></p>
                
                <div class="mt-3">
                    <?php if($user->role == 'admin'): ?>
                        <span class="badge bg-danger fs-6 px-3 py-2">Admin</span>
                    <?php elseif($user->role == 'moderator'): ?>
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Moderator</span>
                    <?php else: ?>
                        <span class="badge bg-info fs-6 px-3 py-2">User</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thay đổi điểm số</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.users.adjust_points', $user->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Số điểm (sử dụng số âm để trừ)</label>
                        <input type="number" name="amount" class="form-control form-control-sm" placeholder="Ví dụ: 50 hoặc -20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Lý do thay đổi</label>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Lý do thay đổi điểm" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Cập nhật điểm</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th width="30%">ID Tài khoản</th>
                            <td>#<?php echo e($user->id); ?></td>
                        </tr>
                        <tr>
                            <th>Tên hiển thị</th>
                            <td><?php echo e($user->display_name); ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?php echo e($user->username); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo e($user->email); ?></td>
                        </tr>
                        <tr>
                            <th>Điểm tích lũy</th>
                            <td><strong class="text-primary fs-5"><?php echo e($user->points); ?></strong> điểm</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                <?php if($user->status == 'active'): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php elseif($user->status == 'inactive'): ?>
                                    <span class="badge bg-secondary">Chưa kích hoạt</span>
                                <?php else: ?>
                                    <span class="badge bg-dark">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td><?php echo e($user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối</th>
                            <td><?php echo e($user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <th>Đăng nhập lần cuối</th>
                            <td><?php echo e($user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('d/m/Y H:i:s') : 'Chưa từng đăng nhập'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lịch sử giao dịch điểm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Hành động</th>
                                <th>Số điểm</th>
                                <th>Nội dung chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $user->pointTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($tx->created_at->format('d/m/Y H:i')); ?></td>
                                    <td>
                                        <?php if($tx->action === 'daily_login'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">Điểm danh</span>
                                        <?php elseif($tx->action === 'comment'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Bình luận</span>
                                        <?php elseif($tx->action === 'favorite'): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info">Yêu thích</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Khác</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold <?php echo e($tx->amount >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e($tx->amount >= 0 ? '+' : ''); ?><?php echo e($tx->amount); ?>

                                    </td>
                                    <td><?php echo e($tx->description); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Chưa có giao dịch điểm nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/users/show.blade.php ENDPATH**/ ?>