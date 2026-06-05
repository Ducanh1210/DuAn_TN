<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?> - Hà Nam Travel Hub</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0..1,0" rel="stylesheet" />

    <!-- Style -->
    <style>
        :root {
            --bg-dark: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: #ffffff;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: rgba(79, 70, 229, 0.08);
            --success: #10b981;
            --success-light: rgba(16, 185, 129, 0.1);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.1);
            --warning: #f59e0b;
            --warning-light: rgba(245, 158, 11, 0.1);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --sidebar-width: 280px;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(245, 158, 11, 0.05) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Floating Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 20px;
            bottom: 20px;
            left: 20px;
            border-radius: 28px;
            z-index: 100;
            padding: 28px 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 40px;
            text-decoration: none;
        }

        .brand span.icon {
            background: linear-gradient(135deg, var(--primary), #818cf8);
            color: #fff;
            padding: 8px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 24px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-item a:hover {
            color: var(--primary);
            background-color: var(--primary-light);
            transform: translateX(4px);
        }

        .nav-item.active a {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 600;
        }

        .nav-item a span.material-symbols-rounded {
            font-size: 22px;
            transition: transform 0.2s ease;
        }
        
        .nav-item a:hover span.material-symbols-rounded {
            transform: scale(1.1);
        }

        /* Modern scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: calc(var(--sidebar-width) + 40px);
            flex-grow: 1;
            padding: 32px 40px;
            min-width: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .page-title p {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Cards & Containers */
        .card {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 28px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-main);
        }

        /* Forms styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-control, select, textarea {
            width: 100%;
            padding: 12px 16px;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 8px;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            padding: 16px;
            background-color: rgba(248, 250, 252, 0.4);
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:hover td {
            background-color: rgba(248, 250, 252, 0.6);
        }

        /* Status badges */
        .badge {
            display: inline-flex;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: var(--success-light);
            color: #047857;
        }

        .badge-danger {
            background-color: var(--danger-light);
            color: #b91c1c;
        }

        .badge-warning {
            background-color: var(--warning-light);
            color: #b45309;
        }

        .badge-info {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        /* Notifications */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        /* Layout Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .tab-item {
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab-item:hover {
            color: var(--primary);
        }

        .tab-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand">
            <span class="material-symbols-rounded icon">map</span>
            <span>Ha Nam Admin</span>
        </a>

        <div class="menu-label">Menu Quản trị</div>
        <ul class="nav-list">
            <li class="nav-item <?php echo e(Route::is('admin.dashboard') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.dashboard')); ?>">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?php echo e(Route::is('admin.categories.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.categories.index')); ?>">
                    <span class="material-symbols-rounded">category</span>
                    <span>Danh mục địa điểm</span>
                </a>
            </li>
            <li class="nav-item <?php echo e(Route::is('admin.locations.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.locations.index')); ?>">
                    <span class="material-symbols-rounded">pin_drop</span>
                    <span>Quản lý địa điểm</span>
                </a>
            </li>
        </ul>

        <div class="menu-label" style="margin-top: auto;">Hệ thống</div>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="<?php echo e(route('home')); ?>" target="_blank">
                    <span class="material-symbols-rounded">open_in_new</span>
                    <span>Xem Bản đồ</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Toast / Alerts -->
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <span class="material-symbols-rounded">check_circle</span>
                <div><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-error">
                <span class="material-symbols-rounded">error</span>
                <div><?php echo e(session('error')); ?></div>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="alert alert-warning">
                <span class="material-symbols-rounded">warning</span>
                <div><?php echo e(session('warning')); ?></div>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/layout.blade.php ENDPATH**/ ?>