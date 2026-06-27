<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt Tài Khoản - Hà Nam POI</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0072FF;
            --primary-hover: #0052cc;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Top Header Navigation */
        .top-navbar {
            height: 64px;
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }
        .btn-back {
            background-color: transparent;
            border: none;
            padding: 0;
            font-weight: 500;
            color: var(--text-sub);
            text-decoration: none;
            transition: color 0.15s ease;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
        }
        .btn-back:hover {
            color: var(--text-main);
        }
        .back-chevron {
            font-size: 1.6rem;
            margin-right: 6px;
            line-height: 1;
            font-weight: 400;
            position: relative;
            top: -4px;
        }

        /* Main Screen Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 64px);
            overflow: hidden;
        }

        /* Sidebar Dashboard Navigation */
        .dashboard-sidebar {
            width: 280px;
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 24px;
            overflow-y: auto;
        }
        .sidebar-user-section {
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }
        .avatar-container {
            width: 90px;
            height: 90px;
            position: relative;
            margin: 0 auto 16px auto;
            cursor: pointer;
        }
        .avatar-edit-badge {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 26px;
            height: 26px;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: var(--text-sub);
            transition: all 0.2s ease;
            z-index: 10;
        }
        .avatar-container:hover .avatar-edit-badge {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        .edit-name-icon {
            color: var(--text-sub);
            opacity: 0.5;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
        }
        .sidebar-display-name:hover .edit-name-icon {
            opacity: 1;
            color: var(--primary);
        }
        .user-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }
        .avatar-upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 50%;
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            cursor: pointer;
            transition: opacity 0.2s ease;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .avatar-container:hover .avatar-upload-overlay {
            opacity: 1;
        }
        .avatar-loader-spinner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .user-role-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 2px 10px;
            border-radius: 4px;
            background-color: #f1f5f9;
            color: var(--text-sub);
            margin-top: 4px;
        }

        /* Sidebar Tabs Button */
        .sidebar-menu-tabs .nav-link {
            width: 100%;
            text-align: left;
            padding: 12px 16px;
            color: var(--text-sub);
            font-weight: 500;
            border-radius: 8px;
            border: none;
            background: transparent;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-menu-tabs .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }
        .sidebar-menu-tabs .nav-link.active {
            background-color: #f1f5f9;
            color: var(--text-main);
            font-weight: 700;
        }
        .menu-count-badge {
            background-color: #e2e8f0;
            color: var(--text-sub);
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* Content Workspace Area */
        .dashboard-content {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            padding: 40px;
        }
        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 32px;
            max-width: 960px;
            margin: 0 auto;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--text-main);
        }
        
        /* Clean Inputs */
        .form-label-clean {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-sub);
            margin-bottom: 8px;
            display: block;
        }
        .form-control-clean {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #ffffff;
            transition: border-color 0.15s ease;
            width: 100%;
        }
        .form-control-clean:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-control-clean:disabled {
            background-color: #f8fafc;
            color: var(--text-sub);
            cursor: not-allowed;
        }

        /* Simple Buttons */
        .btn-action {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .btn-action:hover {
            background-color: var(--primary-hover);
        }

        /* Simple Card for Favorites */
        .simple-fav-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .simple-fav-card:hover {
            border-color: #cbd5e1;
        }
        .simple-fav-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }
        .simple-fav-body {
            padding: 16px;
        }
        .simple-fav-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .simple-fav-desc {
            font-size: 0.8rem;
            color: var(--text-sub);
            margin-bottom: 16px;
            height: 36px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .btn-card-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .btn-card-outline:hover {
            background-color: #f1f5f9;
        }
        .btn-card-primary {
            background-color: var(--primary);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .btn-card-primary:hover {
            background-color: var(--primary-hover);
        }
        .btn-card-remove {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0;
            cursor: pointer;
        }

        /* Clean Table Style */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .clean-table th {
            font-weight: 600;
            color: var(--text-sub);
            border-bottom: 1px solid var(--border-color);
            padding: 12px;
            text-align: left;
            background-color: #f8fafc;
        }
        .clean-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }
        .btn-text-danger {
            background: none;
            border: none;
            color: #ef4444;
            padding: 0;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .btn-text-danger:hover {
            text-decoration: underline;
        }

        /* Avatar minimal picker */
        .avatar-picker-btn {
            background-color: transparent;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
        }
        .avatar-picker-btn:hover {
            background-color: #f1f5f9;
        }

        /* Map selection cards */
        .simple-map-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .simple-map-card.active {
            border-color: var(--primary);
            background-color: rgba(0, 114, 255, 0.04);
            font-weight: 600;
        }

        /* Dark Mode overrides scoped */
        .dark-mode-active {
            --bg-body: #090d16;
            --card-bg: #131c2e;
            --text-main: #f8fafc;
            --text-sub: #94a3b8;
            --border-color: #243049;
        }
        .dark-mode-active body {
            background-color: var(--bg-body);
            color: var(--text-main);
        }
        .dark-mode-active .top-navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
        }
        .dark-mode-active .dashboard-sidebar {
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
        }
        .dark-mode-active .sidebar-user-section {
            border-bottom: 1px solid var(--border-color);
        }
        .dark-mode-active .user-role-badge {
            background-color: #1e293b;
            color: #94a3b8;
        }
        .dark-mode-active .sidebar-menu-tabs .nav-link:hover {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .dark-mode-active .sidebar-menu-tabs .nav-link.active {
            background-color: #1e293b;
            color: var(--text-main);
            font-weight: 700;
        }
        .dark-mode-active .menu-count-badge {
            background-color: #1e293b;
            color: #94a3b8;
        }
        .dark-mode-active .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }
        .dark-mode-active .form-control-clean {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .dark-mode-active .form-control-clean:focus {
            border-color: #38bdf8;
        }
        .dark-mode-active .form-control-clean:disabled {
            background-color: #0f172a;
            color: #64748b;
        }
        .dark-mode-active .simple-fav-card {
            background-color: #1e293b;
            border-color: var(--border-color);
        }
        .dark-mode-active .simple-fav-card:hover {
            border-color: #334155;
        }
        .dark-mode-active .clean-table th {
            background-color: #1e293b;
            border-bottom: 1px solid var(--border-color);
        }
        .dark-mode-active .clean-table td {
            border-bottom: 1px solid var(--border-color);
        }
        .dark-mode-active .btn-back {
            color: var(--text-sub);
        }
        .dark-mode-active .btn-back:hover {
            color: var(--text-main);
        }
        .dark-mode-active .btn-card-outline {
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .dark-mode-active .btn-card-outline:hover {
            background-color: #1e293b;
        }
        .dark-mode-active .modal-content {
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        /* Clean Settings Info List */
        .info-list {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .info-item {
            display: flex;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 220px;
            font-weight: 600;
            color: var(--text-sub);
            font-size: 0.95rem;
        }
        .info-value {
            font-weight: 500;
            color: var(--text-main);
            font-size: 0.95rem;
            flex: 1;
        }
        .info-input-wrapper {
            flex: 1;
            max-width: 420px;
        }
        .form-control-minimal {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.95rem;
            color: var(--text-main);
            background-color: #ffffff;
            width: 100%;
            transition: all 0.15s ease;
        }
        .form-control-minimal:focus {
            outline: none;
            border-color: var(--primary);
        }
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ef4444;
            margin-right: 6px;
            vertical-align: middle;
        }
        .status-dot.active {
            background-color: #22c55e;
        }

        /* Sidebar name inline editing styling */
        .sidebar-name-container {
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
        }
        .sidebar-display-name {
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.15s ease;
        }
        .sidebar-display-name:hover {
            color: var(--primary);
        }
        .sidebar-name-input {
            font-family: inherit;
            font-weight: 700;
            font-size: 0.95rem;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 3px 6px;
            background-color: var(--card-bg);
            color: var(--text-main);
            width: 90%;
            outline: none;
        }
        .sidebar-name-input:focus {
            border-color: var(--primary);
        }

        /* Dark Mode for Minimal Info List */
        .dark-mode-active .form-control-minimal {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .dark-mode-active .form-control-minimal:focus {
            border-color: #38bdf8;
        }
        .dark-mode-active .avatar-edit-badge {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-sub);
        }
        .dark-mode-active .avatar-container:hover .avatar-edit-badge {
            background-color: #38bdf8;
            color: #0f172a;
            border-color: #38bdf8;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-navbar">
    <a href="{{ url('/') }}" class="btn-back">
        <span class="back-chevron">&lsaquo;</span> Quay lại
    </a>
    <div style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
        Hà Nam POI
    </div>
</div>

<!-- Main Layout Wrapper -->
<div class="main-layout" id="profile-app-container">
    <!-- Sidebar Navigation -->
    <div class="dashboard-sidebar">
        <div class="sidebar-user-section">
            <div class="avatar-container" id="sidebarAvatarContainer" title="Nhấp để thay ảnh đại diện">
                <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->display_name ?? $user->username).'&background=0072FF&color=fff' }}" 
                     alt="Avatar" 
                     class="user-avatar-img"
                     id="profileAvatarPreview">
                <div class="avatar-upload-overlay">
                    Thay ảnh
                </div>
                <div class="avatar-edit-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>
                <div class="avatar-loader-spinner" id="avatarUploadSpinner">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </div>
            <!-- Hidden File Input -->
            <input type="file" id="avatarFileInput" accept="image/*" class="d-none">
            
            <div class="sidebar-name-container">
                <span id="sidebarDisplayNameText" class="sidebar-display-name" title="Nhấp để đổi tên">
                    <span id="sidebarDisplayNameVal">{{ $user->display_name ?? $user->username }}</span>
                    <span class="edit-name-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px; vertical-align: middle;">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </span>
                </span>
                <input type="text" id="sidebarDisplayNameInput" class="sidebar-name-input d-none" value="{{ $user->display_name ?? $user->username }}" maxlength="120">
            </div>
            <span class="user-role-badge">
                {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'moderator' ? 'Kiểm duyệt viên' : 'Thành viên') }}
            </span>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav flex-column sidebar-menu-tabs" id="settings-tabs" role="tablist">
            <button class="nav-link active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab" aria-selected="true">
                <span>Thông tin cá nhân</span>
            </button>
            <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab" aria-selected="false">
                <span>Bảo mật & Mật khẩu</span>
            </button>
            <button class="nav-link" id="tab-favorites-btn" data-bs-toggle="pill" data-bs-target="#tab-favorites" type="button" role="tab" aria-selected="false">
                <span>Địa điểm đã lưu</span>
                <span class="menu-count-badge" id="favoritesCountBadge">{{ $favorites->count() }}</span>
            </button>
            <button class="nav-link" id="tab-comments-btn" data-bs-toggle="pill" data-bs-target="#tab-comments" type="button" role="tab" aria-selected="false">
                <span>Nhận xét của tôi</span>
                <span class="menu-count-badge" id="commentsCountBadge">{{ $comments->count() }}</span>
            </button>
            <button class="nav-link" id="tab-preferences-btn" data-bs-toggle="pill" data-bs-target="#tab-preferences" type="button" role="tab" aria-selected="false">
                <span>Tùy chỉnh hệ thống</span>
            </button>
        </div>
    </div>

    <!-- Content Workspace -->
    <div class="dashboard-content">
        <!-- Toast Alerts System -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
            <div id="settingsToast" class="toast align-items-center text-white bg-dark border-0 rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">Thông báo</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 border-0 mb-4 py-2 px-3 small" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-3 border-0 mb-4 py-2 px-3 small" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab contents -->
        <div class="tab-content" id="settings-tabContent">
            
            <!-- Tab 1: Profile Details -->
            <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Thông tin cá nhân</div>
                    
                    <div class="info-list mb-4">
                        <div class="info-item">
                            <div class="info-label">Tên tài khoản</div>
                            <div class="info-value">{{ $user->username }}</div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Tên hiển thị</div>
                            <div class="info-value" id="profileFormDisplayNameVal">{{ $user->display_name ?? $user->username }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Địa chỉ Email</div>
                            <div class="info-value">{{ $user->email }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Nhóm quyền</div>
                            <div class="info-value">
                                {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'moderator' ? 'Kiểm duyệt viên' : 'Thành viên') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Trạng thái tài khoản</div>
                            <div class="info-value">
                                <span class="status-dot active"></span> {{ $user->status === 'active' ? 'Đang hoạt động' : 'Bị khóa' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Security & Password -->
            <div class="tab-pane fade" id="tab-security" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Bảo mật & Mật khẩu</div>

                    @if($user->provider === 'google')
                        <div class="alert alert-info rounded-3 border-0 p-3 mb-4 small">
                            Tài khoản liên kết với Google OAuth. Bạn có thể thiết lập mật khẩu riêng để đăng nhập độc lập.
                        </div>
                    @endif

                    <form action="{{ route('client.profile.password') }}" method="POST" class="mb-5">
                        @csrf
                        
                        @if(!empty($user->password_hash) && !$user->provider)
                            <div class="mb-3">
                                <label class="form-label-clean">Mật khẩu hiện tại</label>
                                <input type="password" class="form-control-clean" name="current_password" required>
                            </div>
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label-clean">Mật khẩu mới</label>
                                <input type="password" class="form-control-clean" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-clean">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control-clean" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-action">Cập nhật mật khẩu</button>
                        </div>
                    </form>

                    <hr class="my-4" style="border-color: var(--border-color);">

                    <div class="mb-4">
                        <div class="fw-bold mb-2">Đăng xuất & Vô hiệu hóa tài khoản</div>
                        <p class="text-secondary small mb-3">Hủy kích hoạt tài khoản sẽ tạm dừng hoạt động và ẩn bình luận của bạn.</p>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-2" data-bs-toggle="modal" data-bs-target="#deactivateAccountModal">
                            Yêu cầu hủy kích hoạt tài khoản
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Saved Locations -->
            <div class="tab-pane fade" id="tab-favorites" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Địa điểm đã lưu</div>

                    <div class="row g-3" id="favoritesGrid">
                        @forelse($favorites as $location)
                            <div class="col-md-6 favorite-card-wrapper" id="fav-card-{{ $location->id }}">
                                <div class="simple-fav-card">
                                    <img src="{{ $location->thumbnail_url }}" alt="{{ $location->name }}" class="simple-fav-img">
                                    <div class="simple-fav-body">
                                        <div class="simple-fav-title">{{ $location->name }}</div>
                                        <p class="simple-fav-desc">{{ $location->short_description ?? 'Không có mô tả.' }}</p>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex gap-2">
                                                <a href="{{ url('/#loc-' . $location->id) }}" class="btn-card-outline">Xem bản đồ</a>
                                                <a href="{{ route('client.locations.360', $location->slug) }}" class="btn-card-primary">Xem 360°</a>
                                            </div>
                                            <button class="btn-card-remove favorite-toggle-btn" data-location-id="{{ $location->id }}">Bỏ thích</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5" id="noFavoritesMsg">
                                <p class="text-secondary small mb-3">Bạn chưa lưu địa điểm nào.</p>
                                <a href="{{ url('/') }}" class="btn btn-primary btn-sm rounded-pill px-4">Tìm kiếm địa điểm</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tab 4: Comments list -->
            <div class="tab-pane fade" id="tab-comments" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Nhận xét của tôi</div>

                    <div class="table-responsive">
                        <table class="clean-table" id="commentsTable">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Địa điểm</th>
                                    <th style="width: 45%;">Nội dung bình luận</th>
                                    <th style="width: 15%;">Đánh giá</th>
                                    <th style="width: 15%; text-align: right;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr id="comment-row-{{ $comment->id }}">
                                        <td>
                                            <a href="{{ url('/#loc-' . $comment->location_id) }}" class="text-decoration-none text-dark fw-medium">
                                                {{ $comment->location->name ?? 'Địa điểm ẩn' }}
                                            </a>
                                        </td>
                                        <td class="text-secondary small">{{ $comment->content }}</td>
                                        <td>
                                            <span class="text-warning fw-bold">{{ $comment->rating ?? 0 }} &starf;</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <button type="button" class="btn-text-danger delete-comment-btn" data-comment-id="{{ $comment->id }}">
                                                Xóa nhận xét
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noCommentsRow">
                                        <td colspan="4" class="text-center py-4 text-secondary small">
                                            Bạn chưa viết nhận xét nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Preferences -->
            <div class="tab-pane fade" id="tab-preferences" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Tùy chỉnh hệ thống</div>

                    <div class="mb-4">
                        <div class="fw-bold mb-1">Chế độ giao diện cá nhân</div>
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light mt-2">
                            <div>
                                <div class="fw-semibold small">Chế độ tối (Dark Mode)</div>
                                <small class="text-secondary">Giảm độ chói và bảo vệ mắt của bạn</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="darkModeSwitch">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--border-color);">

                    <div>
                        <div class="fw-bold mb-2">Loại bản đồ mặc định</div>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label class="w-100">
                                    <input type="radio" name="map_style" value="standard" checked class="d-none">
                                    <div class="simple-map-card active" data-style="standard">Chuẩn</div>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="w-100">
                                    <input type="radio" name="map_style" value="satellite" class="d-none">
                                    <div class="simple-map-card" data-style="satellite">Vệ tinh</div>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="w-100">
                                    <input type="radio" name="map_style" value="terrain" class="d-none">
                                    <div class="simple-map-card" data-style="terrain">Địa hình</div>
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="w-100">
                                    <input type="radio" name="map_style" value="dark" class="d-none">
                                    <div class="simple-map-card" data-style="dark">Bản đồ tối</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Deactivation Confirmation Modal -->
<div class="modal fade" id="deactivateAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Xác nhận hủy kích hoạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary small">Vui lòng điền thông tin để xác thực trước khi tiếp tục:</p>
                
                @if($user->provider)
                    <div class="mb-3">
                        <label class="form-label-clean">Xác nhận email của bạn (<strong>{{ $user->email }}</strong>):</label>
                        <input type="text" class="form-control-clean" id="confirm_username" placeholder="Nhập email">
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label-clean">Nhập mật khẩu hiện tại của bạn:</label>
                        <input type="password" class="form-control-clean" id="confirm_password" placeholder="Nhập mật khẩu">
                    </div>
                @endif
                <div class="text-danger small d-none" id="deactivateErrorMsg">Thông tin xác minh chưa khớp.</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-2 btn-sm px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" class="btn btn-danger rounded-2 btn-sm px-3" id="confirmDeactivationBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="deactivateSpinner" role="status"></span>
                    Hủy kích hoạt ngay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Avatar View & Edit Modal -->
<div class="modal fade" id="avatarViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-center w-100">Ảnh đại diện</h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4 d-flex justify-content-center">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->display_name ?? $user->username).'&background=0072FF&color=fff' }}" 
                         alt="Avatar" 
                         id="avatarModalLargePreview" 
                         style="width: 340px; height: 340px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border-color);">
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-sm rounded-2 py-2" id="avatarModalChangeBtn">
                        Thay ảnh mới
                    </button>
                    <button type="button" class="btn btn-light btn-sm rounded-2 py-2" data-bs-dismiss="modal">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle URL hash to open correct tab
        const hash = window.location.hash;
        if (hash) {
            const tabBtn = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }

        const toastEl = document.getElementById('settingsToast');
        const settingsToast = new bootstrap.Toast(toastEl);
        const toastMsg = document.getElementById('toastMessage');

        function showToast(message, isSuccess = true) {
            toastMsg.innerText = message;
            toastEl.className = `toast align-items-center text-white border-0 rounded-3 ${isSuccess ? 'bg-success' : 'bg-danger'}`;
            settingsToast.show();
        }

        // --- Avatar Upload Action ---
        const sidebarAvatarContainer = document.getElementById('sidebarAvatarContainer');
        const fileInput = document.getElementById('avatarFileInput');
        const avatarPreview = document.getElementById('profileAvatarPreview');
        const uploadSpinner = document.getElementById('avatarUploadSpinner');
        const avatarModalLargePreview = document.getElementById('avatarModalLargePreview');
        const avatarModalChangeBtn = document.getElementById('avatarModalChangeBtn');
        const avatarViewModalEl = document.getElementById('avatarViewModal');
        const avatarViewModal = avatarViewModalEl ? new bootstrap.Modal(avatarViewModalEl) : null;

        if (sidebarAvatarContainer && avatarViewModal) {
            sidebarAvatarContainer.addEventListener('click', function() {
                avatarViewModal.show();
            });
        }

        if (avatarModalChangeBtn) {
            avatarModalChangeBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length === 0) return;
                
                const file = e.target.files[0];
                uploadSpinner.style.display = 'flex';

                // Image compression helper function
                const compressAvatar = (inputFile, callback) => {
                    // Only compress if size > 1MB (1,000,000 bytes)
                    if (inputFile.size <= 1000000) {
                        callback(inputFile);
                        return;
                    }

                    const reader = new FileReader();
                    reader.readAsDataURL(inputFile);
                    reader.onload = function(event) {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = function() {
                            const maxDim = 800; // max dimension for avatars
                            let width = img.width;
                            let height = img.height;

                            if (width > maxDim || height > maxDim) {
                                if (width > height) {
                                    height = Math.round((height * maxDim) / width);
                                    width = maxDim;
                                } else {
                                    width = Math.round((width * maxDim) / height);
                                    height = maxDim;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob(function(blob) {
                                const compressedFile = new File([blob], 'compressed_' + inputFile.name.substring(0, inputFile.name.lastIndexOf('.')) + '.jpg', {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                callback(compressedFile);
                            }, 'image/jpeg', 0.85); // 85% JPEG quality is excellent
                        };
                    };
                };

                compressAvatar(file, function(processedFile) {
                    const formData = new FormData();
                    formData.append('avatar', processedFile);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch("{{ route('client.profile.avatar') }}", {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        uploadSpinner.style.display = 'none';
                        if (data.success) {
                            avatarPreview.src = data.avatar_url;
                            if (avatarModalLargePreview) {
                                avatarModalLargePreview.src = data.avatar_url;
                            }
                            if (avatarViewModal) {
                                avatarViewModal.hide();
                            }
                            showToast(data.message, true);
                        } else {
                            showToast(data.message || 'Tải ảnh thất bại.', false);
                        }
                    })
                    .catch(err => {
                        uploadSpinner.style.display = 'none';
                        showToast('Có lỗi xảy ra khi tải ảnh lên.', false);
                        console.error(err);
                    });
                });
            });
        }

        // --- Sidebar Display Name Inline Edit ---
        const displayNameText = document.getElementById('sidebarDisplayNameText');
        const displayNameVal = document.getElementById('sidebarDisplayNameVal');
        const displayNameInput = document.getElementById('sidebarDisplayNameInput');
        const formDisplayNameVal = document.getElementById('profileFormDisplayNameVal');

        if (displayNameText && displayNameInput && displayNameVal) {
            displayNameText.addEventListener('click', function() {
                displayNameText.classList.add('d-none');
                displayNameInput.classList.remove('d-none');
                displayNameInput.focus();
                displayNameInput.select();
            });

            const saveDisplayName = () => {
                const newValue = displayNameInput.value.trim();
                const originalValue = displayNameVal.textContent.trim();

                if (!newValue) {
                    displayNameInput.value = originalValue;
                    displayNameInput.classList.add('d-none');
                    displayNameText.classList.remove('d-none');
                    return;
                }

                if (newValue === originalValue) {
                    displayNameInput.classList.add('d-none');
                    displayNameText.classList.remove('d-none');
                    return;
                }

                // Show saving state
                displayNameInput.disabled = true;

                fetch("{{ route('client.profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ display_name: newValue })
                })
                .then(res => res.json())
                .then(data => {
                    displayNameInput.disabled = false;
                    displayNameInput.classList.add('d-none');
                    displayNameText.classList.remove('d-none');

                    if (data.success) {
                        displayNameVal.textContent = data.display_name;
                        displayNameInput.value = data.display_name;
                        
                        // Sync with right form text value if present
                        if (formDisplayNameVal) {
                            formDisplayNameVal.textContent = data.display_name;
                        }

                        showToast(data.message, true);
                    } else {
                        displayNameInput.value = originalValue;
                        showToast(data.message || 'Cập nhật thất bại.', false);
                    }
                })
                .catch(err => {
                    displayNameInput.disabled = false;
                    displayNameInput.classList.add('d-none');
                    displayNameText.classList.remove('d-none');
                    displayNameInput.value = originalValue;
                    showToast('Có lỗi xảy ra khi cập nhật tên.', false);
                    console.error(err);
                });
            };

            displayNameInput.addEventListener('blur', saveDisplayName);
            displayNameInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    displayNameInput.blur(); // Triggers saveDisplayName via blur handler
                } else if (e.key === 'Escape') {
                    displayNameInput.value = displayNameVal.textContent.trim();
                    displayNameInput.classList.add('d-none');
                    displayNameText.classList.remove('d-none');
                }
            });
        }

        // --- Remove Saved Location via AJAX ---
        const favoritesGrid = document.getElementById('favoritesGrid');
        const favoritesCountBadge = document.getElementById('favoritesCountBadge');

        if (favoritesGrid) {
            favoritesGrid.addEventListener('click', function(e) {
                const toggleBtn = e.target.closest('.favorite-toggle-btn');
                if (!toggleBtn) return;

                const locationId = toggleBtn.getAttribute('data-location-id');
                const cardWrapper = document.getElementById(`fav-card-${locationId}`);

                fetch("{{ route('client.profile.favorite.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ location_id: locationId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && !data.is_favorited) {
                        showToast('Đã bỏ thích địa điểm.', true);
                        if (cardWrapper) {
                            cardWrapper.classList.add('removing');
                            setTimeout(() => {
                                cardWrapper.remove();
                                let currentCount = parseInt(favoritesCountBadge.innerText) || 0;
                                currentCount = Math.max(0, currentCount - 1);
                                favoritesCountBadge.innerText = currentCount;

                                if (currentCount === 0) {
                                    favoritesGrid.innerHTML = `
                                        <div class="col-12 text-center py-5" id="noFavoritesMsg">
                                            <p class="text-secondary small mb-3">Bạn chưa lưu địa điểm nào.</p>
                                            <a href="{{ url('/') }}" class="btn btn-primary btn-sm rounded-pill px-4">Tìm kiếm địa điểm</a>
                                        </div>
                                    `;
                                }
                            }, 300);
                        }
                    } else {
                        showToast(data.message || 'Thao tác thất bại.', false);
                    }
                })
                .catch(err => {
                    showToast('Có lỗi xảy ra.', false);
                    console.error(err);
                });
            });
        }

        // --- Delete Comment via AJAX ---
        const commentsTable = document.getElementById('commentsTable');
        const commentsCountBadge = document.getElementById('commentsCountBadge');

        if (commentsTable) {
            commentsTable.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.delete-comment-btn');
                if (!deleteBtn) return;

                const commentId = deleteBtn.getAttribute('data-comment-id');
                const row = document.getElementById(`comment-row-${commentId}`);

                if (!confirm('Bạn có chắc chắn muốn xóa nhận xét này không?')) return;

                fetch(`/profile/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, true);
                        if (row) {
                            row.remove();
                            let currentCount = parseInt(commentsCountBadge.innerText) || 0;
                            currentCount = Math.max(0, currentCount - 1);
                            commentsCountBadge.innerText = currentCount;

                            const tbody = commentsTable.querySelector('tbody');
                            if (currentCount === 0 && tbody && tbody.children.length === 0) {
                                tbody.innerHTML = `
                                    <tr id="noCommentsRow">
                                        <td colspan="4" class="text-center py-4 text-secondary small">
                                            Bạn chưa viết nhận xét nào.
                                        </td>
                                    </tr>
                                `;
                            }
                        }
                    } else {
                        showToast(data.message || 'Xóa thất bại.', false);
                    }
                })
                .catch(err => {
                    showToast('Có lỗi xảy ra.', false);
                    console.error(err);
                });
            });
        }

        // --- Dark Mode Switcher ---
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const profileContainer = document.getElementById('profile-app-container');

        if (localStorage.getItem('profile-dark-mode') === 'enabled') {
            if (darkModeSwitch) darkModeSwitch.checked = true;
            if (profileContainer) profileContainer.classList.add('dark-mode-active');
            document.body.style.backgroundColor = '#090d16';
        }

        if (darkModeSwitch) {
            darkModeSwitch.addEventListener('change', function() {
                if (this.checked) {
                    if (profileContainer) profileContainer.classList.add('dark-mode-active');
                    document.body.style.backgroundColor = '#090d16';
                    localStorage.setItem('profile-dark-mode', 'enabled');
                } else {
                    if (profileContainer) profileContainer.classList.remove('dark-mode-active');
                    document.body.style.backgroundColor = '#f8fafc';
                    localStorage.setItem('profile-dark-mode', 'disabled');
                }
            });
        }

        // --- Map Preference Cards Switch ---
        const mapCards = document.querySelectorAll('.simple-map-card');
        mapCards.forEach(card => {
            card.addEventListener('click', function() {
                mapCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const radio = this.closest('label').querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            });
        });

        // --- Account Deactivation ---
        const confirmDeactivationBtn = document.getElementById('confirmDeactivationBtn');
        const deactivateSpinner = document.getElementById('deactivateSpinner');
        const deactivateErrorMsg = document.getElementById('deactivateErrorMsg');

        if (confirmDeactivationBtn) {
            confirmDeactivationBtn.addEventListener('click', function() {
                const currentPasswordInput = document.getElementById('confirm_password');
                const emailInput = document.getElementById('confirm_username');

                const bodyData = {};
                if (currentPasswordInput) bodyData.confirm_password = currentPasswordInput.value;
                if (emailInput) bodyData.confirm_username = emailInput.value;

                deactivateSpinner.classList.remove('d-none');
                deactivateErrorMsg.classList.add('d-none');
                confirmDeactivationBtn.disabled = true;

                fetch("{{ route('client.profile.delete') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(bodyData)
                })
                .then(res => res.json())
                .then(data => {
                    deactivateSpinner.classList.add('d-none');
                    confirmDeactivationBtn.disabled = false;

                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deactivateAccountModal'));
                        if (modal) modal.hide();
                        showToast(data.message, true);
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1200);
                    } else {
                        deactivateErrorMsg.innerText = data.message || 'Xác thực thất bại.';
                        deactivateErrorMsg.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    deactivateSpinner.classList.add('d-none');
                    confirmDeactivationBtn.disabled = false;
                    deactivateErrorMsg.innerText = 'Xác thực thông tin không chính xác.';
                    deactivateErrorMsg.classList.remove('d-none');
                    console.error(err);
                });
            });
        }
    });
</script>

</body>
</html>
