<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="header">
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Tổng quan hệ thống Hà Nam Travel Hub</p>
    </div>
</div>

<!-- Grid Statistics -->
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .stat-card {
        background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(79, 70, 229, 0.12), 0 4px 6px -2px rgba(79, 70, 229, 0.04);
        border-color: rgba(79, 70, 229, 0.3);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        top: -30px;
        right: -30px;
        transition: transform 0.5s ease;
    }
    .stat-card:hover::after {
        transform: scale(1.2);
    }
    .stat-icon {
        background: var(--primary-light);
        color: var(--primary);
        padding: 16px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.05);
    }
    .stat-icon.success {
        background: var(--success-light);
        color: var(--success);
    }
    .stat-icon.warning {
        background: var(--warning-light);
        color: var(--warning);
    }
    .stat-icon span {
        font-size: 32px;
    }
    .stat-info h3 {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-info p {
        font-size: 30px;
        font-weight: 700;
        color: var(--text-main);
    }
    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 32px;
    }
    
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, rgba(224, 231, 255, 0.7) 0%, rgba(238, 242, 255, 0.7) 100%);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px -10px rgba(79, 70, 229, 0.15), 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(129, 140, 248, 0.25) 0%, transparent 70%);
        top: -150px;
        right: -80px;
        border-radius: 50%;
    }
    .welcome-text {
        max-width: 70%;
        z-index: 2;
    }
    .welcome-text h1 {
        font-size: 24px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 8px;
    }
    .welcome-text p {
        font-size: 14px;
        line-height: 1.6;
        color: #4338ca;
    }
</style>

<div class="welcome-banner">
    <div class="welcome-text">
        <h1>Chào mừng quay trở lại, Admin! 👋</h1>
        <p>Hệ thống số hóa thông tin du lịch và di sản Hà Nam đã sẵn sàng. Hãy khám phá và quản lý các địa điểm, hình ảnh và thiết lập bản đồ 360° VR Tour ấn tượng.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-rounded">pin_drop</span>
        </div>
        <div class="stat-info">
            <h3>Địa điểm du lịch & dịch vụ</h3>
            <p><?php echo e($locationsCount); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <span class="material-symbols-rounded">category</span>
        </div>
        <div class="stat-info">
            <h3>Danh mục nhóm</h3>
            <p><?php echo e($categoriesCount); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <span class="material-symbols-rounded">360</span>
        </div>
        <div class="stat-info">
            <h3>Cảnh Panorama 360°</h3>
            <p><?php echo e($panoramasCount); ?></p>
        </div>
    </div>
</div>

<div class="card-title" style="margin-bottom: 16px;">
    <h2>Lối tắt nhanh</h2>
</div>
<div class="quick-actions">
    <a href="<?php echo e(route('admin.locations.create')); ?>" class="btn btn-primary">
        <span class="material-symbols-rounded">add</span> Thêm địa điểm mới
    </a>
    <a href="<?php echo e(route('admin.categories.index')); ?>" class="btn btn-secondary">
        <span class="material-symbols-rounded">list</span> Quản lý danh mục
    </a>
    <a href="<?php echo e(route('home')); ?>" target="_blank" class="btn btn-secondary">
        <span class="material-symbols-rounded">map</span> Xem bản đồ thực tế
    </a>
</div>

<div class="card">
    <div class="card-title">
        <span>Địa điểm vừa được thêm gần đây</span>
        <a href="<?php echo e(route('admin.locations.index')); ?>" class="btn btn-secondary btn-sm">Xem tất cả</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Địa điểm</th>
                    <th>Danh mục</th>
                    <th>Tỉnh/Thành phố</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $latestLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <?php if($location->thumbnail_url): ?>
                                    <img src="<?php echo e(asset($location->thumbnail_url)); ?>" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;" alt="">
                                <?php else: ?>
                                    <div style="width: 44px; height: 44px; border-radius: 8px; background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                        <span class="material-symbols-rounded" style="color: var(--text-muted);">image</span>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main);"><?php echo e($location->name); ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted);"><?php echo e(Str::limit($location->address, 40)); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo e($location->category->name); ?></span>
                        </td>
                        <td><?php echo e($location->province); ?></td>
                        <td>
                            <?php if($location->status === 'published'): ?>
                                <span class="badge badge-success">Đang hiển thị</span>
                            <?php elseif($location->status === 'hidden'): ?>
                                <span class="badge badge-danger">Đã ẩn</span>
                            <?php elseif($location->status === 'draft'): ?>
                                <span class="badge badge-warning">Bản nháp</span>
                            <?php else: ?>
                                <span class="badge"><?php echo e($location->status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($location->created_at->format('d/m/Y H:i')); ?></td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="<?php echo e(route('admin.locations.edit', $location->id)); ?>" class="btn btn-secondary btn-sm" title="Sửa">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                                </a>
                                <a href="<?php echo e(route('admin.locations.gallery', $location->id)); ?>" class="btn btn-secondary btn-sm" title="Ảnh gallery">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">image</span>
                                </a>
                                <a href="<?php echo e(route('admin.locations.panorama', $location->id)); ?>" class="btn btn-secondary btn-sm" title="Ảnh 360°">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">360</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Chưa có địa điểm nào được nhập.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>