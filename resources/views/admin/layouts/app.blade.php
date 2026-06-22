<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Quản Trị Hệ Thống</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; overflow-x: hidden; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: #fff; padding-top: 20px; transition: all 0.3s; }
        .sidebar a { color: #c2c7d0; text-decoration: none; padding: 10px 20px; display: block; border-radius: 4px; margin: 4px 10px; }
        .sidebar a:hover, .sidebar a.active { background-color: #007bff; color: #fff; }
        .sidebar .nav-icon { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { padding: 20px; min-height: calc(100vh - 56px); }
        .navbar { background-color: #fff; box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
    </style>
    @stack('styles')
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar flex-shrink-0" id="sidebar" style="width: 250px;">
        <h4 class="text-center mb-4 pb-2 border-bottom border-secondary">ADMIN PANEL</h4>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon"></i> Bảng điều khiển
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-list nav-icon"></i> Quản lý Danh mục
            </a>
            <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt nav-icon"></i> Quản lý Địa điểm
            </a>
            <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper nav-icon"></i> Quản lý Tin tức & Sự kiện
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users nav-icon"></i> Quản lý Người dùng
            </a>
            <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <i class="fas fa-comments nav-icon"></i> Quản lý Bình luận
            </a>
        </nav>
    </div>

    <!-- Page Content -->
    <div class="flex-grow-1" style="width: calc(100% - 250px);">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg px-4">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none me-2" id="toggleSidebar"><i class="fas fa-bars"></i></button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 fw-bold">{{ Auth::user()->display_name ?? Auth::user()->username }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>@yield('title')</h2>
                @yield('actions')
            </div>

            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (often needed for ajax or old plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        if (sidebar.style.display === 'none') {
            sidebar.style.display = 'block';
        } else {
            sidebar.style.display = 'none';
        }
    });
</script>

@stack('scripts')
</body>
</html>
