<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Ninh Bình Travel Hub</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Avatar Frames CSS -->
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f2442;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --accent-primary: #1e3a5f;
        }

        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-body);
            font-size: 0.85rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Minimal White Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid var(--border-light);
            padding: 1.5rem 1rem;
            transition: all 0.2s ease;
        }

        .sidebar-brand {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-heading);
            padding: 0 0.75rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            letter-spacing: -0.01em;
        }

        .sidebar-brand-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-primary);
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .sidebar-group-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 500;
            padding: 0 0.75rem;
            margin-top: 1.5rem;
            margin-bottom: 0.4rem;
        }

        .sidebar nav a {
            display: block;
            padding: 0.5rem 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 6px;
            font-weight: 400;
            font-size: 0.825rem;
            margin-bottom: 0.1rem;
            transition: all 0.15s ease;
        }

        .sidebar nav a:hover {
            color: var(--text-heading);
            background-color: #f1f5f9;
        }

        .sidebar nav a.active {
            color: var(--accent-primary);
            background-color: #f1f5f9;
            font-weight: 600;
        }

        /* Top Navbar */
        .navbar-main {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 0.75rem 2rem;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-heading);
        }

        .user-avatar-minimal {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Main Content Container */
        .main-wrapper {
            width: calc(100% - 240px);
        }

        .content-area {
            padding: 2rem;
        }

        .page-header-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-heading);
            margin: 0;
        }

        /* Cards & Metric Strip */
        .metric-strip {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .metric-item {
            padding: 0 1.25rem;
            border-right: 1px solid var(--border-light);
        }

        .metric-item:last-child {
            border-right: none;
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 400;
            margin-bottom: 0.25rem;
        }

        .metric-value {
            font-size: 1.35rem;
            font-weight: 500;
            color: var(--text-heading);
            line-height: 1.2;
        }

        /* Minimalist Tables */
        .card-minimal {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }

        .card-header-minimal {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-light);
            font-weight: 500;
            color: var(--text-heading);
            font-size: 0.875rem;
        }

        .table-minimal {
            margin: 0;
        }

        .table-minimal th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 0.65rem 1rem;
            border-bottom: 1px solid var(--border-light);
            white-space: nowrap;
        }

        .table-minimal td {
            padding: 0.75rem 1rem;
            color: var(--text-body);
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-minimal tr:last-child td {
            border-bottom: none;
        }

        /* Buttons & Badges */
        .btn-minimal {
            font-size: 0.8rem;
            font-weight: 400;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            background: #ffffff;
            color: var(--text-body);
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-minimal:hover {
            background: #f8fafc;
            color: var(--text-heading);
        }

        .btn-minimal-primary {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: #ffffff;
        }

        .btn-minimal-primary:hover {
            background: #2b4c7e;
            color: #ffffff;
        }

        .badge-minimal {
            font-size: 0.725rem;
            font-weight: 500;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            background: #f1f5f9;
            color: var(--text-muted);
            white-space: nowrap;
            display: inline-block;
        }

        .badge-minimal-success {
            background: #f0fdf4;
            color: #166534;
        }

        /* Forms in Admin */
        .form-control, .form-select {
            font-size: 0.825rem;
            border-color: #cbdbe8;
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            color: var(--text-heading);
            background-color: #ffffff;
            transition: all 0.15s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.08);
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -240px;
                z-index: 1050;
            }
            .sidebar.show {
                left: 0;
            }
            .main-wrapper {
                width: 100% !important;
            }
            .content-area {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="d-flex">
        <!-- Minimal White Sidebar (No Icons) -->
        <div class="sidebar flex-shrink-0" id="sidebar">
            <div class="sidebar-brand">
                <span class="sidebar-brand-dot"></span>
                <span>Ninh Bình Travel Hub</span>
            </div>
            
            <div class="sidebar-group-title">Quản lý</div>
            <nav>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Bảng điều khiển
                </a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    Danh mục địa điểm
                </a>
                <a href="{{ route('admin.locations.index') }}" class="{{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    Địa điểm du lịch
                </a>
                <a href="{{ route('admin.business-profiles.index') }}" class="d-flex justify-content-between align-items-center {{ request()->routeIs('admin.business-profiles.*') ? 'active' : '' }}">
                    <span>Duyệt doanh nghiệp</span>
                    @php
                        $pendingBizCount = \App\Models\BusinessProfile::where('status', 'pending')->count();
                    @endphp
                    @if($pendingBizCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">{{ $pendingBizCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="sidebar-group-title">Nội dung</div>
            <nav>
                <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                    Tin tức & Bài viết
                </a>
                <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    Quản lý bình luận
                </a>
            </nav>

            <div class="sidebar-group-title">Hệ thống</div>
            <nav>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    Tài khoản người dùng
                </a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    Báo cáo vi phạm
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-grow-1 main-wrapper">
            <!-- Navbar -->
            <div class="navbar-main d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light d-md-none me-2 border" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="text-muted" style="font-size: 0.8rem;">Quản trị hệ thống</span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="user-pill">
                        <x-user-avatar :user="Auth::user()" size="28" />
                        <span>{{ Auth::user()->display_name ?? Auth::user()->username ?? 'Admin' }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn-minimal">Đăng xuất</button>
                    </form>
                </div>
            </div>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <div class="alert border-0 py-2 px-3 mb-3 bg-white border-start border-3 border-success shadow-sm" style="font-size: 0.8rem; color: #166534;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert border-0 py-2 px-3 mb-3 bg-white border-start border-3 border-danger shadow-sm" style="font-size: 0.8rem; color: #991b1b;">
                        {{ session('error') }}
                    </div>
                @endif

                @if(!request()->routeIs('admin.dashboard'))
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h2 class="page-header-title">@yield('title')</h2>
                    <div>
                        @yield('actions')
                    </div>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('show');
            });
        }
    </script>
    @stack('scripts')
</body>

</html>