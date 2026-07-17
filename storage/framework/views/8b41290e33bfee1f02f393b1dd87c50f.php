<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?> - Quản Trị Hệ Thống</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-bg: #0f172a;      /* Slate 900 */
            --sidebar-hover: #1e293b;   /* Slate 800 */
            --sidebar-active: #2563eb;  /* Blue 600 */
            --sidebar-text: #94a3b8;    /* Slate 400 */
            --sidebar-text-active: #ffffff;
            --main-bg: #f8fafc;         /* Slate 50 */
            --card-bg: #ffffff;
            --accent-color: #2563eb;    /* Blue 600 */
            --text-primary: #0f172a;    /* Slate 900 */
            --text-secondary: #475569;  /* Slate 600 */
            --border-color: #e2e8f0;    /* Slate 200 */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #fff;
            padding-top: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 280px;
            z-index: 1040;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #fff;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }
        
        .sidebar-brand-icon {
            width: 32px;
            height: 32px;
            background-color: var(--accent-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 1rem;
        }

        .sidebar-menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
        }

        .sidebar nav {
            padding: 0 1rem;
        }

        .sidebar a {
            color: var(--sidebar-text);
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            transition: all 0.15s ease;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.8;
            transition: all 0.15s ease;
        }

        .sidebar a:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-text-active);
        }

        .sidebar a:hover i {
            opacity: 1;
        }

        .sidebar a.active {
            background-color: var(--sidebar-active);
            color: var(--sidebar-text-active);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -1px rgba(37, 99, 235, 0.1);
        }

        .sidebar a.active i {
            opacity: 1;
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            z-index: 1030;
            position: sticky;
            top: 0;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        .btn-logout {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            color: #64748b;
            background: transparent;
            border: 1px solid transparent;
        }
        
        .btn-logout:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        /* Main Content Styling */
        .main-content {
            padding: 2.5rem;
            min-height: calc(100vh - 65px);
        }

        /* Button Customization */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            letter-spacing: -0.01em;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0;
            letter-spacing: -0.025em;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
            }
            .sidebar.show {
                left: 0;
            }
            .main-wrapper {
                width: 100% !important;
            }
            .main-content {
                padding: 1.5rem;
            }
            .navbar {
                padding: 0.75rem 1rem;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar flex-shrink-0" id="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-compass text-white"></i>
                </div>
                <span>EXPLORER</span>
            </div>
            
            <div class="sidebar-menu-label">Main Menu</div>
            <nav>
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                    class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i> Bảng điều khiển
                </a>
                <a href="<?php echo e(route('admin.categories.index')); ?>"
                    class="<?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>">
                    <i class="fas fa-layer-group"></i> Quản lý Danh mục
                </a>
                <a href="<?php echo e(route('admin.locations.index')); ?>"
                    class="<?php echo e(request()->routeIs('admin.locations.*') ? 'active' : ''); ?>">
                    <i class="fas fa-map-marker-alt"></i> Quản lý Địa điểm
                </a>
            </nav>

            <div class="sidebar-menu-label mt-4">Content</div>
            <nav>
                <a href="<?php echo e(route('admin.news.index')); ?>"
                    class="<?php echo e(request()->routeIs('admin.news.*') ? 'active' : ''); ?>">
                    <i class="fas fa-newspaper"></i> Tin tức & Sự kiện
                </a>
                <a href="<?php echo e(route('admin.comments.index')); ?>"
                    class="<?php echo e(request()->routeIs('admin.comments.*') ? 'active' : ''); ?>">
                    <i class="fas fa-comments"></i> Bình luận
                </a>
            </nav>

            <div class="sidebar-menu-label mt-4">System</div>
            <nav>
                <a href="<?php echo e(route('admin.users.index')); ?>"
                    class="<?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i> Người dùng
                </a>
            </nav>
        </div>

        <!-- Page Content -->
        <div class="flex-grow-1 main-wrapper" style="width: calc(100% - 280px); transition: width 0.3s;">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid px-0">
                    <button class="btn btn-light d-md-none me-2" id="toggleSidebar" style="border: 1px solid #e2e8f0; background: #fff;">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-none d-md-block">
                        <span class="text-secondary fw-medium" style="font-size: 0.875rem;">
                            <?php if(request()->routeIs('admin.dashboard')): ?>
                                Overview
                            <?php else: ?>
                                <?php echo $__env->yieldContent('title'); ?>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="ms-auto navbar-user">
                        <div class="d-flex align-items-center">
                            <div class="text-end me-3 d-none d-sm-block">
                                <div class="fw-bold text-dark" style="font-size: 0.875rem;"><?php echo e(Auth::user()->display_name ?? Auth::user()->username ?? 'Admin'); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">Administrator</div>
                            </div>
                            <div class="user-avatar">
                                <?php echo e(strtoupper(substr(Auth::user()->display_name ?? Auth::user()->username ?? 'A', 0, 1))); ?>

                            </div>
                        </div>
                        
                        <div style="width: 1px; height: 24px; background-color: #e2e8f0;" class="mx-1"></div>
                        
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-logout d-flex align-items-center">
                                <i class="fas fa-sign-out-alt me-1"></i> <span class="d-none d-sm-inline">Log out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content">
                <?php if(session('success')): ?>
                    <div class="alert alert-success d-flex align-items-center shadow-sm" role="alert" style="border-radius: 0.5rem; border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
                        <i class="fas fa-check-circle me-3 fs-5 text-success"></i> 
                        <div class="fw-medium"><?php echo e(session('success')); ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center shadow-sm" role="alert" style="border-radius: 0.5rem; border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
                        <i class="fas fa-exclamation-circle me-3 fs-5 text-danger"></i> 
                        <div class="fw-medium"><?php echo e(session('error')); ?></div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(!request()->routeIs('admin.dashboard')): ?>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
                    <h2 class="page-title"><?php echo $__env->yieldContent('title'); ?></h2>
                    <div class="mt-3 mt-md-0">
                        <?php echo $__env->yieldContent('actions'); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH D:\laragon\www\Du_An_TN\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>