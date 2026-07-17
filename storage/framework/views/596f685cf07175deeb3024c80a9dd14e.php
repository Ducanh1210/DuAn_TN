<?php $__env->startSection('title', 'Quản lý Bình luận'); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Bình luận</h6>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form action="<?php echo e(route('admin.comments.index')); ?>" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo nội dung, tên user..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="visible" <?php echo e(request('status') == 'visible' ? 'selected' : ''); ?>>Hiển thị</option>
                        <option value="hidden" <?php echo e(request('status') == 'hidden' ? 'selected' : ''); ?>>Đang ẩn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo e(route('admin.comments.index')); ?>" class="btn btn-secondary w-100">Xóa lọc</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Người dùng</th>
                        <th>Địa điểm</th>
                        <th>Nội dung</th>
                        <th width="120">Thời gian</th>
                        <th width="100">Trạng thái</th>
                        <th width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($comment->id); ?></td>
                            <td>
                                <strong><?php echo e($comment->user->display_name ?? ($comment->user->username ?? 'Unknown User')); ?></strong><br>
                                <small class="text-muted"><?php echo e($comment->user->email ?? ''); ?></small>
                            </td>
                            <td>
                                <?php if($comment->location): ?>
                                    <a href="<?php echo e(route('client.locations.360', $comment->location->slug)); ?>" target="_blank"><?php echo e($comment->location->name); ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Địa điểm đã xóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e(Str::limit($comment->content, 100)); ?>

                            </td>
                            <td>
                                <small><?php echo e($comment->created_at->format('d/m/Y H:i')); ?></small>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-status" type="checkbox" data-id="<?php echo e($comment->id); ?>" <?php echo e($comment->status === 'visible' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" id="status-label-<?php echo e($comment->id); ?>">
                                        <?php echo $comment->status === 'visible' ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Đang ẩn</span>'; ?>

                                    </label>
                                </div>
                            </td>
                            <td>
                                <form action="<?php echo e(route('admin.comments.destroy', $comment->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này? Hành động này không thể hoàn tác.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có bình luận nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            <?php echo e($comments->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtns = document.querySelectorAll('.toggle-status');
        
        toggleBtns.forEach(btn => {
            btn.addEventListener('change', function() {
                const commentId = this.dataset.id;
                const isChecked = this.checked;
                const label = document.getElementById('status-label-' + commentId);
                
                fetch(`/admin/comments/${commentId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'visible') {
                            label.innerHTML = '<span class="badge bg-success">Hiển thị</span>';
                        } else {
                            label.innerHTML = '<span class="badge bg-secondary">Đang ẩn</span>';
                        }
                    } else {
                        alert('Có lỗi xảy ra.');
                        this.checked = !isChecked; // revert
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối.');
                    this.checked = !isChecked; // revert
                });
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/comments/index.blade.php ENDPATH**/ ?>