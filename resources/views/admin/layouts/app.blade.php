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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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

        /* Custom SweetAlert2 Theme for Ninh Bình POI Admin */
        .swal2-container {
            z-index: 1060 !important;
        }
        .custom-swal-popup {
            border-radius: 16px !important;
            padding: 1.5rem 1.75rem !important;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            border: 1px solid var(--border-light, #e2e8f0) !important;
            box-shadow: 0 20px 25px -5px rgba(15, 36, 66, 0.12), 0 8px 10px -6px rgba(15, 36, 66, 0.06) !important;
            background: #ffffff !important;
        }
        .custom-swal-title {
            color: var(--text-heading, #0f2442) !important;
            font-size: 1.15rem !important;
            font-weight: 600 !important;
            padding-top: 0.25rem !important;
        }
        .custom-swal-text {
            color: var(--text-body, #334155) !important;
            font-size: 0.875rem !important;
            margin-top: 0.4rem !important;
            line-height: 1.5 !important;
        }
        .custom-swal-confirm-btn {
            background-color: var(--accent-primary, #1e3a5f) !important;
            color: #ffffff !important;
            font-size: 0.825rem !important;
            font-weight: 500 !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px !important;
            border: none !important;
            margin: 0.25rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: none !important;
        }
        .custom-swal-confirm-btn:hover {
            background-color: #0f2442 !important;
            color: #ffffff !important;
        }
        .custom-swal-confirm-danger {
            background-color: #dc2626 !important;
            color: #ffffff !important;
        }
        .custom-swal-confirm-danger:hover {
            background-color: #b91c1c !important;
            color: #ffffff !important;
        }
        .custom-swal-cancel-btn {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            font-size: 0.825rem !important;
            font-weight: 500 !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px !important;
            border: 1px solid #cbdbe8 !important;
            margin: 0.25rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: none !important;
        }
        .custom-swal-cancel-btn:hover {
            background-color: #e2e8f0 !important;
            color: #1e3a5f !important;
        }
        .custom-swal-toast {
            border-radius: 10px !important;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, sans-serif !important;
            box-shadow: 0 10px 15px -3px rgba(15, 36, 66, 0.1) !important;
            border: 1px solid #cbdbe8 !important;
            font-size: 0.825rem !important;
            background: #ffffff !important;
        }

        /* Form Select Polish & Arrow Spacing */
        .form-select {
            border-color: var(--border-light, #e2e8f0);
            border-radius: 6px;
            font-size: 0.85rem;
            padding-right: 2.25rem !important;
            background-position: right 0.75rem center !important;
            color: var(--text-body, #334155);
            transition: all 0.15s ease;
        }

        .form-select:focus {
            border-color: var(--accent-primary, #1e3a5f);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
        }

        .form-select-sm {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
            padding-left: 0.75rem !important;
            padding-right: 2.25rem !important;
            background-position: right 0.65rem center !important;
            font-size: 0.78rem !important;
            border-radius: 6px !important;
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
            position: relative;
            padding: 0.5rem 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 0 6px 6px 0;
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
            box-shadow: inset 3px 0 0 var(--accent-primary);
        }

        .sidebar-nav-group {
            margin-bottom: 0.1rem;
        }

        .sidebar-nav-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-radius: 6px;
            font-weight: 400;
            font-size: 0.825rem;
            text-align: left;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .sidebar-nav-toggle:hover,
        .sidebar-nav-group.open > .sidebar-nav-toggle {
            color: var(--text-heading);
            background-color: #f1f5f9;
        }

        .sidebar-nav-group.open > .sidebar-nav-toggle {
            font-weight: 500;
        }

        .sidebar-nav-toggle .sidebar-chevron {
            font-size: 0.65rem;
            transition: transform 0.2s ease;
            opacity: 0.55;
            flex-shrink: 0;
        }

        .sidebar-nav-group.open .sidebar-nav-toggle .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-subnav {
            display: none;
            padding: 0.15rem 0 0.25rem 0.5rem;
        }

        .sidebar-nav-group.open .sidebar-subnav {
            display: block;
        }

        .sidebar-subnav a {
            display: block;
            position: relative;
            padding: 0.4rem 0.75rem 0.4rem 1.35rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 0 6px 6px 0;
            font-size: 0.8rem;
            margin-bottom: 0.05rem;
            transition: all 0.15s ease;
        }

        .sidebar-subnav a:hover {
            color: var(--text-heading);
            background-color: #f8fafc;
        }

        .sidebar-subnav a.active {
            color: var(--accent-primary);
            background-color: #f1f5f9;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--accent-primary);
        }

        .admin-workspace-crumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: var(--text-muted);
            min-width: 0;
        }

        .admin-workspace-crumb strong {
            color: var(--text-heading);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .admin-workspace-crumb .sep {
            opacity: 0.45;
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

        .badge-minimal-warning {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .badge-minimal-danger {
            background: #fef2f2;
            color: #991b1b;
        }

        .badge-minimal-info {
            background: #f0f9ff;
            color: #0369a1;
        }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            background: #e8eef5;
            color: #1e3a5f;
            border: 1px solid #cbdbe8;
            font-size: 0.65rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            line-height: 1.3;
        }

        .pending-task-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0.55rem 0.85rem;
            font-size: 0.825rem;
            color: #334155;
            background: #fff;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .pending-task-link:hover {
            background: #f8fafc;
            border-color: #cbdbe8;
            color: #0f2442;
        }

        .pending-task-link.has-pending {
            background: #f8fafc;
            border-color: #cbdbe8;
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
                        <span class="badge-count">{{ $pendingBizCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.panorama-requests.index') }}" class="d-flex justify-content-between align-items-center {{ request()->routeIs('admin.panorama-requests.*') ? 'active' : '' }}">
                    <span>Yêu cầu tour 360</span>
                    @php
                        $pendingPanoReqCount = \App\Models\PanoramaServiceRequest::where('status', 'pending')->count();
                    @endphp
                    @if($pendingPanoReqCount > 0)
                        <span class="badge-count">{{ $pendingPanoReqCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="sidebar-group-title">Nội dung</div>
            @php
                $contentOpen = request()->routeIs('admin.news.*') || request()->routeIs('admin.events.*');
                $contribOpen = request()->routeIs('admin.contributions.*');
                $pendingSuggestions = \App\Models\LocationSuggestion::where('status', 'pending')->count();
                $reportsOpen = request()->routeIs('admin.reports.*');
                $reportTab = request('tab', 'locations');
                $pendingReportLocations = \App\Models\Report::whereIn('reportable_type', \App\Models\Report::morphTypes(\App\Models\Location::class))->where('status', 'pending')->count();
                $pendingReportComments = \App\Models\Report::whereIn('reportable_type', \App\Models\Report::morphTypes(\App\Models\Comment::class))->where('status', 'pending')->count();
                $pendingFeedbacks = \App\Models\FeedbackReport::where('status', 'pending')->count();
                $pendingReportsTotal = $pendingReportLocations + $pendingReportComments + $pendingFeedbacks;
            @endphp
            <nav>
                <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                    Tin tức & sự kiện
                </a>
                <a href="{{ route('admin.comments.index') }}" class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    Quản lý bình luận
                </a>
                <div class="sidebar-nav-group {{ $contribOpen ? 'open' : '' }}">
                    <button type="button" class="sidebar-nav-toggle" aria-expanded="{{ $contribOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <span>Quản lý đóng góp</span>
                            @if($pendingSuggestions > 0)
                                <span class="badge-count">{{ $pendingSuggestions }}</span>
                            @endif
                        </span>
                        <i class="fas fa-chevron-down sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-subnav">
                        <a href="{{ route('admin.contributions.index') }}" class="{{ $contribOpen ? 'active' : '' }}">
                            Đề xuất địa điểm
                            @if($pendingSuggestions > 0)
                                <span class="badge-count ms-1">{{ $pendingSuggestions }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </nav>

            <div class="sidebar-group-title">Kiểm duyệt</div>
            <nav>
                <div class="sidebar-nav-group {{ $reportsOpen ? 'open' : '' }}" id="sidebarReportsGroup">
                    <button type="button" class="sidebar-nav-toggle" id="sidebarReportsToggle" aria-expanded="{{ $reportsOpen ? 'true' : 'false' }}">
                        <span class="d-flex align-items-center gap-2">
                            <span>Báo cáo vi phạm</span>
                            @if($pendingReportsTotal > 0)
                                <span class="badge-count">{{ $pendingReportsTotal }}</span>
                            @endif
                        </span>
                        <i class="fas fa-chevron-down sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-subnav">
                        <a href="{{ route('admin.reports.index', ['tab' => 'locations']) }}"
                           class="{{ $reportsOpen && $reportTab === 'locations' ? 'active' : '' }}">
                            Địa điểm
                            @if($pendingReportLocations > 0)
                                <span class="badge-count ms-1">{{ $pendingReportLocations }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.reports.index', ['tab' => 'comments']) }}"
                           class="{{ $reportsOpen && $reportTab === 'comments' ? 'active' : '' }}">
                            Bình luận
                            @if($pendingReportComments > 0)
                                <span class="badge-count ms-1">{{ $pendingReportComments }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.reports.index', ['tab' => 'feedbacks']) }}"
                           class="{{ $reportsOpen && $reportTab === 'feedbacks' ? 'active' : '' }}">
                            Góp ý / báo lỗi
                            @if($pendingFeedbacks > 0)
                                <span class="badge-count ms-1">{{ $pendingFeedbacks }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </nav>

            <div class="sidebar-group-title">Hệ thống</div>
            <nav>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    Tài khoản người dùng
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
                @php
                    $adminCrumbGroup = match (true) {
                        request()->routeIs('admin.dashboard') => 'Quản lý',
                        request()->routeIs('admin.categories.*'),
                        request()->routeIs('admin.locations.*'),
                        request()->routeIs('admin.business-profiles.*') => 'Quản lý',
                        request()->routeIs('admin.news.*'),
                        request()->routeIs('admin.events.*'),
                        request()->routeIs('admin.comments.*'),
                        request()->routeIs('admin.contributions.*') => 'Nội dung',
                        request()->routeIs('admin.reports.*') => 'Kiểm duyệt',
                        request()->routeIs('admin.users.*') => 'Hệ thống',
                        default => 'Admin',
                    };
                @endphp
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="admin-workspace-crumb">
                        <span>{{ $adminCrumbGroup }}</span>
                        <span class="sep">/</span>
                        <strong>@yield('title')</strong>
                    </div>
                    <div>
                        @yield('actions')
                    </div>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('show');
            });
        }

        document.querySelectorAll('.sidebar-nav-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const group = toggle.closest('.sidebar-nav-group');
                if (!group) return;
                group.classList.toggle('open');
                toggle.setAttribute(
                    'aria-expanded',
                    group.classList.contains('open') ? 'true' : 'false'
                );
            });
        });

        // Global SweetAlert2 Interceptor for confirm popups across Admin
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.dataset.confirmed === 'true') {
                return true;
            }

            const confirmText = form.getAttribute('data-confirm-text') || form.getAttribute('data-confirm');
            const onsubmitAttr = form.getAttribute('onsubmit');

            let textToShow = confirmText;
            let titleToShow = form.getAttribute('data-confirm-title') || 'Xác nhận thao tác';
            let confirmBtnText = form.getAttribute('data-confirm-btn') || 'Đồng ý';
            let isDanger = form.getAttribute('data-confirm-type') === 'danger' || (textToShow && textToShow.toLowerCase().includes('xóa'));
            
            if (!textToShow && onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    textToShow = match[1];
                }
            }

            if (textToShow) {
                e.preventDefault();
                e.stopPropagation();

                Swal.fire({
                    title: titleToShow,
                    html: textToShow,
                    icon: isDanger ? 'warning' : 'question',
                    iconColor: isDanger ? '#eab308' : '#1e3a5f',
                    showCancelButton: true,
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Hủy bỏ',
                    reverseButtons: true,
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        htmlContainer: 'custom-swal-text',
                        confirmButton: 'custom-swal-confirm-btn ' + (isDanger ? 'custom-swal-confirm-danger' : ''),
                        cancelButton: 'custom-swal-cancel-btn'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.removeAttribute('onsubmit');
                        form.submit();
                    }
                });

                return false;
            }
        }, true);
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        iconColor: '#166534',
                        title: 'Thành công',
                        text: @json(session('success')),
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'custom-swal-toast'
                        }
                    });
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: 'Có lỗi xảy ra',
                        text: @json(session('error')),
                        confirmButtonText: 'Đóng',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                        },
                        buttonsStyling: false
                    });
                }
            });
        </script>
    @endif
    @stack('scripts')
</body>

</html>