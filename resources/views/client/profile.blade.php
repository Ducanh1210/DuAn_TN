<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt Tài Khoản - Ninh Bình Travel Hub</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome & Avatar Frames CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Custom SweetAlert2 Theme for Ninh Bình POI Profile */
        .swal2-container {
            z-index: 20000 !important;
        }
        .custom-swal-popup {
            border-radius: 16px !important;
            padding: 1.5rem 1.75rem !important;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            border: 1px solid #cbdbe8 !important;
            box-shadow: 0 20px 25px -5px rgba(30, 58, 95, 0.12), 0 8px 10px -6px rgba(30, 58, 95, 0.06) !important;
            background: #ffffff !important;
        }
        .custom-swal-title {
            color: #1e3a5f !important;
            font-size: 1.15rem !important;
            font-weight: 600 !important;
            padding-top: 0.25rem !important;
        }
        .custom-swal-text {
            color: #334155 !important;
            font-size: 0.875rem !important;
            margin-top: 0.4rem !important;
            line-height: 1.5 !important;
        }
        .custom-swal-confirm-btn {
            background-color: #1e3a5f !important;
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
        }

        :root {
            --primary: #1e3a5f;
            --primary-hover: #2b4c7e;
            --accent: #1e3a5f;
            --bg-body: #f4f7fb;
            --bg-sidebar: #ffffff;
            --bg-sidebar-muted: #f8fafc;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --text-on-dark: #334155;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
        }

        body { 
            font-family: 'Be Vietnam Pro', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            font-size: 0.8125rem;
            font-weight: 400;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, .h1 { font-size: 1.3rem !important; }
        h2, .h2 { font-size: 1.15rem !important; }
        h3, .h3 { font-size: 1.05rem !important; }
        h4, .h4 { font-size: 0.95rem !important; }
        h5, .h5 { font-size: 0.9rem !important; }
        h6, .h6 { font-size: 0.85rem !important; }

        /* Top Header Navigation */
        .top-navbar {
            height: 48px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px 0 8px;
            z-index: 100;
            color: var(--text-main);
        }
        .top-navbar-brand {
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.02em;
            color: var(--primary);
        }
        .top-navbar-left {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }
        .top-navbar-spacer {
            width: 72px;
            flex-shrink: 0;
        }
        .btn-back {
            background-color: transparent;
            border: none;
            padding: 6px 10px;
            font-weight: 500;
            color: var(--text-sub);
            text-decoration: none;
            transition: color 0.15s ease, background 0.15s ease;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            border-radius: var(--radius-sm);
        }
        .btn-back:hover {
            color: var(--text-main);
            background: #f1f5f9;
        }
        .back-chevron {
            font-size: 1.25rem;
            margin-right: 4px;
            line-height: 1;
            font-weight: 400;
            position: relative;
            top: -1px;
        }

        /* Main Screen Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 48px);
            overflow: hidden;
        }

        /* Sidebar — light console */
        .dashboard-sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow-y: auto;
            overflow-x: hidden;
            color: var(--text-main);
        }
        .sidebar-user-section {
            text-align: left;
            padding: 16px 14px 14px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 0;
            display: grid;
            grid-template-columns: 56px 1fr;
            grid-template-rows: auto auto;
            column-gap: 12px;
            row-gap: 10px;
            align-items: center;
            background: #f8fafc;
            overflow: visible;
        }
        .avatar-container {
            width: 52px;
            height: 52px;
            position: relative;
            margin: 0;
            cursor: pointer;
            grid-row: 1 / 2;
            overflow: visible;
            flex-shrink: 0;
        }
        .sidebar-user-meta {
            grid-column: 2;
            min-width: 0;
        }
        .sidebar-user-meta .user-role-badge,
        .sidebar-user-meta .sidebar-points-chip {
            margin-top: 0;
        }
        .sidebar-user-chips {
            grid-column: 1 / -1;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .dashboard-sidebar .avatar-frame-wrapper,
        .dashboard-sidebar .avatar-frame-wrapper.has-png-frame {
            width: 52px !important;
            height: 52px !important;
            position: relative !important;
            display: block !important;
            overflow: visible !important;
        }
        .dashboard-sidebar .avatar-frame-wrapper img.user-avatar-img,
        .dashboard-sidebar .avatar-frame-wrapper > img:not(.avatar-frame-png-overlay) {
            width: 100% !important;
            height: 100% !important;
            position: relative;
            z-index: 1;
        }
        /* Giữ translate(-50%,-50%) — đừng ghi đè chỉ bằng scale (gây lệch khung) */
        .dashboard-sidebar .avatar-frame-png-overlay {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            width: 132% !important;
            height: 132% !important;
            transform: translate(-50%, -50%) !important;
            object-fit: contain;
            z-index: 2;
            pointer-events: none;
        }
        .avatar-edit-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(15,23,42,0.08);
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
            color: #94a3b8;
            opacity: 0.8;
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
            background: rgba(15, 23, 42, 0.45);
            border-radius: 50%;
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            cursor: pointer;
            transition: opacity 0.2s ease;
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            z-index: 3;
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
            background: rgba(255, 255, 255, 0.85);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 4;
        }
        .user-role-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: var(--radius-sm);
            background-color: #f1f5f9;
            color: #475569;
            margin-top: 0;
            border: 1px solid #e2e8f0;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .sidebar-points-chip {
            font-size: 0.7rem !important;
            padding: 3px 8px !important;
            border-radius: var(--radius-sm) !important;
            font-weight: 600 !important;
            background: #f1f5f9 !important;
            color: #1e3a5f !important;
            border: 1px solid #cbdbe8 !important;
            display: inline-flex !important;
            align-items: center;
            font-variant-numeric: tabular-nums;
        }

        /* Sidebar Tabs Button */
        .sidebar-menu-tabs {
            padding: 10px 8px 16px;
            gap: 2px;
        }
        .sidebar-nav-group {
            margin: 12px 10px 4px;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
        }
        .sidebar-menu-tabs .nav-link {
            width: 100%;
            text-align: left;
            padding: 8px 10px;
            color: #475569;
            font-weight: 500;
            border-radius: var(--radius-sm);
            border: none;
            background: transparent;
            margin-bottom: 1px;
            transition: all 0.12s ease;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .sidebar-menu-tabs .nav-link .nav-link-left {
            display: inline-flex;
            align-items: center;
            min-width: 0;
        }
        .sidebar-business-label {
            min-width: 0;
            white-space: nowrap;
        }
        .sidebar-menu-tabs .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }
        .sidebar-menu-tabs .nav-link.active {
            background-color: #f1f5f9;
            color: #1e3a5f;
            font-weight: 600;
            box-shadow: inset 3px 0 0 #1e3a5f;
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important;
        }
        .menu-count-badge {
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: var(--radius-sm);
            border: 1px solid #e2e8f0;
            font-variant-numeric: tabular-nums;
        }
        .sidebar-menu-tabs .nav-link.active .menu-count-badge {
            background: #e8eef5;
            color: #1e3a5f;
            border-color: #cbdbe8;
        }
        .sidebar-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 1px 7px;
            border-radius: 999px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #b45309;
            font-size: 0.6rem;
            font-weight: 700;
            line-height: 1.35;
            white-space: nowrap;
        }
        /* Content Workspace Area */
        .dashboard-content {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            padding: 0;
            background: #f4f7fb;
        }
        .workspace-toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 20px;
            height: 46px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 0 rgba(15,23,42,0.03);
        }
        .workspace-crumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: var(--text-sub);
            min-width: 0;
        }
        .workspace-crumb strong {
            color: var(--text-main);
            font-weight: 600;
            font-size: 0.85rem;
        }
        .workspace-crumb .sep { opacity: 0.45; }
        .workspace-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .workspace-body {
            padding: 16px 20px 28px;
            max-width: 1180px;
        }
        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px;
            max-width: none;
            margin: 0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .content-panel:has(.panel-head) {
            padding: 0;
        }
        .content-panel + .content-panel { margin-top: 12px; }
        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            background: #f8fafc;
        }
        .panel-head .section-title {
            margin: 0;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .panel-body { padding: 16px; }
        .section-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-main);
        }
        /* Mọi tab: section-title đầu panel → header bar đồng bộ */
        .content-panel > .section-title:first-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: -16px -16px 14px;
            padding: 11px 16px;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .panel-sub {
            color: var(--text-sub);
            font-size: 0.78rem;
            margin: -6px 0 14px;
            line-height: 1.45;
        }
        .panel-note {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 10px 12px;
            margin-bottom: 14px;
            border: 1px solid #cbdbe8;
            background: #f1f5f9;
            border-radius: var(--radius-sm);
            color: #1e3a5f;
            font-size: 0.78rem;
            line-height: 1.45;
        }
        .panel-note.warn {
            border-color: #fde68a;
            background: #fffbeb;
            color: #92400e;
        }
        .panel-note.ok {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }
        .panel-note.danger {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }
        .empty-state {
            text-align: center;
            padding: 40px 16px;
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-md);
            background: #f8fafc;
        }
        .empty-state p {
            color: var(--text-sub);
            font-size: 0.8rem;
            margin-bottom: 12px;
        }
        .ops-form-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 14px;
            background: #fff;
            margin-bottom: 12px;
        }
        .ops-form-card .ops-form-title {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-sub);
            margin-bottom: 12px;
        }
        .settings-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 14px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background: #fff;
        }
        .profile-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }
        .stat-tile {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            min-height: 76px;
        }
        .stat-tile .stat-label {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-sub);
            margin-bottom: 6px;
        }
        .stat-tile .stat-value {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }
        .stat-tile .stat-hint {
            font-size: 0.7rem;
            color: var(--text-sub);
            margin-top: 4px;
        }
        .profile-split {
            display: grid;
            grid-template-columns: 1.4fr 0.9fr;
            gap: 12px;
        }
        .quick-ops {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .quick-ops a, .quick-ops button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background: #fff;
            color: var(--text-main);
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.12s ease, border-color 0.12s ease;
        }
        .quick-ops a:hover, .quick-ops button:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: var(--text-main);
        }
        .quick-ops .qo-meta {
            font-size: 0.7rem;
            color: var(--text-sub);
            font-weight: 400;
        }
        .notification-toolbar-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #dbe4ee;
            background: #f8fafc;
            color: #1e3a5f;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 7px 12px;
            white-space: nowrap;
        }
        .notification-toolbar-btn:hover {
            background: #f1f5f9;
        }
        .notification-unread-badge {
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: #ef4444;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -6px;
            right: -2px;
        }
        .notification-modal-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 58vh;
            overflow-y: auto;
        }
        .notification-modal-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            padding: 12px 14px;
        }
        .notification-modal-item.is-unread {
            border-color: #fecaca;
            background: #fffdfd;
        }
        .notification-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 4px;
        }
        .notification-modal-title {
            font-size: 0.84rem;
            font-weight: 600;
            color: #1e293b;
        }
        .notification-modal-time {
            font-size: 0.72rem;
            color: #94a3b8;
            white-space: nowrap;
        }
        .notification-modal-body {
            color: #475569;
            font-size: 0.8rem;
            line-height: 1.55;
            white-space: pre-line;
        }
        @media (max-width: 768px) {
            .notification-toolbar-btn { padding: 7px 10px; }
        }
        @media (max-width: 992px) {
            .profile-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .profile-split { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .dashboard-sidebar { width: 220px; }
            .workspace-body { padding: 12px; }
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
            border-radius: var(--radius-sm);
            padding: 9px 12px;
            font-size: 0.82rem;
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
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-weight: 600;
            font-size: 0.78rem;
            transition: background-color 0.2s ease;
        }
        .btn-action:hover {
            background-color: var(--primary-hover);
        }

        /* Soft Elegant Modal Tabs */
        .custom-modal-tabs .nav-link {
            background-color: transparent;
            color: var(--text-sub);
            font-weight: 500;
            font-size: 0.825rem;
            padding: 5px 13px;
            border-radius: 6px !important;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .custom-modal-tabs .nav-link:hover {
            background-color: #f8fafc;
            color: var(--text-main);
        }
        .custom-modal-tabs .nav-link.active {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #e2e8f0 !important;
            font-weight: 600;
        }

        /* Data list rows — dashboard dense lists */
        .data-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }
        .data-toolbar .dt-left {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            min-width: 0;
        }
        .data-toolbar .dt-meta {
            font-size: 0.72rem;
            color: var(--text-sub);
            font-variant-numeric: tabular-nums;
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
            background: #fff;
            color: #475569;
            border-radius: 3px;
            cursor: default;
        }
        button.filter-chip {
            cursor: pointer;
            font-family: inherit;
            line-height: 1.2;
        }
        .filter-chip.on {
            background: #0f172a;
            border-color: #0f172a;
            color: #fff;
        }
        .points-history-row {
            align-items: start;
        }
        .points-history-row.is-filtered-out,
        .points-history-row.is-paged-out {
            display: none !important;
        }
        .data-list {
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }
        .data-row {
            display: grid;
            grid-template-columns: 72px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 12px 14px;
            border-bottom: 1px solid #eef2f7;
            background: #fff;
            transition: background 0.12s ease;
        }
        .data-row:last-child { border-bottom: none; }
        .data-row:hover { background: #f8fafc; }
        .data-row.removing { opacity: 0; transform: translateX(8px); transition: all 0.25s ease; }
        .data-row.itinerary {
            grid-template-columns: 1fr auto;
        }
        .data-thumb {
            width: 72px;
            height: 56px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid var(--border-color);
            background: #e2e8f0;
        }
        .data-main { min-width: 0; }
        .data-title {
            font-size: 0.86rem;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .data-desc {
            font-size: 0.75rem;
            color: var(--text-sub);
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
        .data-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .btn-ghost {
            border: 1px solid var(--border-color);
            background: #fff;
            color: #334155;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-ghost:hover { background: #f1f5f9; color: #0f172a; }
        .btn-solid {
            border: 1px solid #0f172a;
            background: #0f172a;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-solid:hover { background: #1e293b; color: #fff; }
        .btn-danger-ghost {
            border: 1px solid #fecaca;
            background: #fff;
            color: #b91c1c;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 3px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-danger-ghost:hover { background: #fef2f2; }
        .sec-split {
            display: grid;
            grid-template-columns: 1.35fr 0.9fr;
            gap: 12px;
        }
        @media (max-width: 900px) {
            .sec-split { grid-template-columns: 1fr; }
            .data-row { grid-template-columns: 56px 1fr; }
            .data-actions { grid-column: 1 / -1; justify-content: flex-start; }
            .data-row.itinerary { grid-template-columns: 1fr; }
        }
        .checklist {
            display: flex;
            flex-direction: column;
            gap: 0;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding: 11px 12px;
            border-bottom: 1px solid #eef2f7;
            background: #fff;
            font-size: 0.8rem;
        }
        .checklist-item:last-child { border-bottom: none; }
        .checklist-item .ci-label { color: var(--text-sub); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
        .checklist-item .ci-value { color: var(--text-main); font-weight: 500; }
        .ci-ok { color: #15803d; font-size: 0.72rem; font-weight: 700; }
        .ci-warn { color: #b45309; font-size: 0.72rem; font-weight: 700; }
        .biz-hero {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
            align-items: stretch;
        }
        @media (max-width: 900px) {
            .biz-hero { grid-template-columns: 1fr; }
        }
        .biz-feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .biz-pending-shell {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .biz-pending-status {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid #fde68a;
            background: linear-gradient(180deg, #fffdf3 0%, #fffbeb 100%);
            border-radius: 8px;
        }
        .biz-pending-status strong {
            display: block;
            margin-bottom: 4px;
            font-size: 0.86rem;
            color: #78350f;
        }
        .biz-pending-status p {
            margin: 0;
            color: #92400e;
            font-size: 0.77rem;
            line-height: 1.55;
        }
        .biz-pending-grid {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(320px, 1.25fr);
            gap: 14px;
            align-items: stretch;
        }
        .biz-pending-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }
        .biz-pending-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
        }
        .biz-pending-card-title {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
        }
        .biz-pending-card-sub {
            margin: 2px 0 0;
            font-size: 0.72rem;
            color: var(--text-sub);
        }
        .biz-pending-card-body {
            padding: 14px;
        }
        .biz-pending-fields {
            display: grid;
            gap: 12px;
        }
        .biz-pending-field {
            padding-bottom: 12px;
            border-bottom: 1px solid #eef2f7;
        }
        .biz-pending-field:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }
        .biz-pending-label {
            display: block;
            margin-bottom: 4px;
            font-size: 0.69rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
        }
        .biz-pending-value {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.45;
        }
        .biz-pending-value.is-primary {
            color: #2563eb;
        }
        .biz-pending-map {
            height: 100%;
            min-height: 320px;
            width: 100%;
            border-radius: 0 0 8px 8px;
            border: 0;
            z-index: 1;
        }
        .biz-pending-actions {
            display: flex;
            justify-content: flex-end;
        }
        @media (max-width: 992px) {
            .biz-pending-grid {
                grid-template-columns: 1fr;
            }
            .biz-pending-map {
                min-height: 260px;
            }
        }
        @media (max-width: 640px) {
            .biz-pending-status {
                flex-direction: column;
                align-items: flex-start;
            }
            .biz-pending-actions {
                justify-content: stretch;
            }
            .biz-pending-actions .btn-danger-ghost {
                width: 100%;
            }
        }
        .biz-feature {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 12px;
            background: #fff;
        }
        .biz-feature strong {
            display: block;
            font-size: 0.8rem;
            margin-bottom: 4px;
            color: var(--text-main);
        }
        .biz-feature p {
            margin: 0;
            font-size: 0.72rem;
            color: var(--text-sub);
            line-height: 1.4;
        }
        .biz-pano-note {
            margin: 12px 0 0;
            font-size: 0.75rem;
            color: var(--text-sub);
            line-height: 1.5;
        }
        .biz-pano-note i {
            color: #94a3b8;
            margin-right: 5px;
        }
        .biz-pano-note a {
            color: #1e3a5f;
            font-weight: 500;
            text-decoration: none;
        }
        .biz-pano-note a:hover { text-decoration: underline; }
        .map-style-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }
        @media (max-width: 700px) {
            .map-style-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .simple-map-card {
            border: 1px solid var(--border-color) !important;
            border-radius: 4px !important;
            padding: 14px 8px !important;
            text-align: center;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 0.78rem;
            font-weight: 500;
            background: #fff;
        }
        .simple-map-card.active {
            border-color: #0f172a !important;
            background: #0f172a !important;
            color: #fff !important;
            font-weight: 600;
        }
        .workspace-body {
            max-width: none !important;
            width: 100%;
            box-sizing: border-box;
        }
        .sidebar-menu-tabs .nav-link.active {
            border-radius: 0 3px 3px 0 !important;
        }

        /* Simple Card for Favorites */

        .simple-fav-card {
            border: 1px solid var(--border-color);
            border-radius: 6px;
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

        /* Saved itinerary list cards */
        .itinerary-card-wrapper .simple-fav-card {
            border: 1px solid #e8eef5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .it-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }
        .it-card-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.72rem;
            font-weight: 500;
            color: #3b5980;
            background: #eef4fb;
            border: 1px solid #dbe7f3;
            border-radius: 4px;
            padding: 3px 8px;
        }

        /* Itinerary view modal */
        .it-modal-content {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 36, 66, 0.18);
        }
        .it-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 24px 16px;
            background: linear-gradient(135deg, #1e3a5f 0%, #2b4c7e 55%, #3b5980 100%);
            color: #fff;
        }
        .it-modal-kicker {
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.75;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .it-modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 6px;
            line-height: 1.35;
            color: #fff;
        }
        .it-modal-summary {
            font-size: 0.82rem;
            line-height: 1.5;
            color: rgba(255,255,255,0.82);
            max-width: 52ch;
        }
        .it-modal-close {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 10px;
            background: rgba(255,255,255,0.14);
            color: #fff;
            font-size: 1.4rem;
            line-height: 1;
            cursor: pointer;
            flex-shrink: 0;
        }
        .it-modal-close:hover { background: rgba(255,255,255,0.24); }
        .it-modal-body {
            padding: 18px 20px 8px;
            background: #f7f9fc;
            max-height: min(68vh, 640px);
        }
        .it-day {
            background: #fff;
            border: 1px solid #e8eef5;
            border-radius: 8px;
            padding: 14px 14px 8px;
            margin-bottom: 14px;
        }
        .it-day-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #edf2f7;
        }
        .it-day-badge {
            background: #1e3a5f;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            padding: 4px 8px;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .it-day-title {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1e3a5f;
            line-height: 1.35;
            margin: 0;
        }
        .it-slot {
            display: grid;
            grid-template-columns: 78px 14px 1fr;
            gap: 10px;
            padding: 10px 8px;
            border-radius: 10px;
            margin-bottom: 6px;
            background: #fafbfd;
            border: 1px solid transparent;
        }
        .it-slot:hover {
            background: #f0f5fa;
            border-color: #dbe7f3;
        }
        .it-slot-time {
            font-size: 0.72rem;
            font-weight: 700;
            color: #1e3a5f;
            line-height: 1.35;
            padding-top: 2px;
        }
        .it-slot-rail {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 5px;
        }
        .it-slot-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #94a3b8;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.18);
            flex-shrink: 0;
        }
        .it-slot-dot.visit { background: #64748b; box-shadow: 0 0 0 3px rgba(100,116,139,0.18); }
        .it-slot-dot.food { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.18); }
        .it-slot-dot.transport { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.18); }
        .it-slot-dot.rest { background: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.18); }
        .it-slot-dot.photo { background: #ec4899; box-shadow: 0 0 0 3px rgba(236,72,153,0.18); }
        .it-slot-activity {
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f2442;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        .it-slot-location {
            font-size: 0.78rem;
            font-weight: 600;
            color: #2b4c7e;
            margin-bottom: 2px;
        }
        .it-slot-meta {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 2px;
        }
        .it-slot-tip {
            font-size: 0.74rem;
            color: #64748b;
            font-style: italic;
            margin-top: 4px;
            line-height: 1.4;
        }
        .it-type-pill {
            display: inline-block;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 2px 7px;
            border-radius: 999px;
            margin-bottom: 5px;
            background: #eef2f7;
            color: #64748b;
        }
        .it-type-pill.food { background: #fff7ed; color: #c2410c; }
        .it-type-pill.visit { background: #f1f5f9; color: #475569; }
        .it-type-pill.transport { background: #f0fdf4; color: #15803d; }
        .it-type-pill.photo { background: #fdf2f8; color: #be185d; }
        .it-type-pill.rest { background: #f5f3ff; color: #6d28d9; }
        .it-tips {
            background: #fff;
            border: 1px solid #e8eef5;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 8px;
        }
        .it-tips-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e3a5f;
            margin-bottom: 8px;
        }
        .it-tips ul {
            margin: 0;
            padding-left: 18px;
        }
        .it-tips li {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 4px;
            line-height: 1.45;
        }
        .it-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px 18px;
            background: #fff;
            border-top: 1px solid #eef2f7;
        }
        .it-modal-cost {
            font-size: 0.82rem;
            font-weight: 600;
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 999px;
            padding: 6px 12px;
        }
        .it-modal-cost:empty { display: none; }
        .it-modal-btn {
            border: none;
            background: #1e3a5f;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 8px 18px;
            cursor: pointer;
        }
        .it-modal-btn:hover { background: #2b4c7e; }
        .dark-mode-active .it-modal-body { background: #0f172a; }
        .dark-mode-active .it-day,
        .dark-mode-active .it-tips { background: #1e293b; border-color: #334155; }
        .dark-mode-active .it-slot { background: #0f172a; }
        .dark-mode-active .it-slot-activity { color: #e2e8f0; }
        .dark-mode-active .it-day-title { color: #e2e8f0; }
        .dark-mode-active .it-modal-footer { background: #1e293b; border-color: #334155; }

        /* Clean Table Style */
        .clean-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .clean-table th {
            font-weight: 600;
            font-size: 0.68rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid var(--border-color);
            padding: 9px 12px;
            text-align: left;
            background-color: #f1f5f9;
            white-space: nowrap;
        }
        .clean-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            color: var(--text-main);
            vertical-align: middle;
        }
        .clean-table tbody tr:hover td { background: #f8fafc; }
        .clean-table tbody tr:last-child td { border-bottom: none; }
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
        .dark-mode-active .dashboard-sidebar {
            background-color: #0b1220;
            border-right: 1px solid #1e293b;
            color: #e2e8f0;
        }
        .dark-mode-active .top-navbar {
            background-color: #070b14;
            border-bottom: 1px solid #1e293b;
            color: #e2e8f0;
        }
        .dark-mode-active .top-navbar > div:last-child {
            color: #94a3b8 !important;
        }
        .dark-mode-active .sidebar-user-section {
            border-bottom: 1px solid #1e293b;
            background: #111827;
        }
        .dark-mode-active .sidebar-display-name { color: #f1f5f9; }
        .dark-mode-active .sidebar-display-name:hover { color: #fff; }
        .dark-mode-active .sidebar-name-input {
            background: #0b1220;
            border-color: #334155;
            color: #f8fafc;
        }
        .dark-mode-active .sidebar-menu-tabs .nav-link { color: #94a3b8; }
        .dark-mode-active .user-role-badge {
            background-color: #1e293b;
            color: #94a3b8;
            border-color: #334155;
        }
        .dark-mode-active .sidebar-points-chip {
            background: #1e293b !important;
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }
        .dark-mode-active .sidebar-menu-tabs .nav-link:hover {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .dark-mode-active .sidebar-menu-tabs .nav-link.active {
            background-color: rgba(30, 58, 95, 0.35);
            color: #e2e8f0;
            box-shadow: inset 3px 0 0 #6482a6;
            font-weight: 700;
        }
        .dark-mode-active .menu-count-badge {
            background-color: #1e293b;
            color: #94a3b8;
            border-color: #334155;
        }
        .dark-mode-active .dashboard-content { background: #090d16; }
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
            border-color: #6482a6;
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
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .info-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #eef2f7;
            gap: 12px;
            background: #fff;
        }
        .info-item:nth-child(even) { background: #fafbfc; }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 160px;
            font-weight: 500;
            color: #64748b;
            font-size: 0.8rem;
        }
        .info-value {
            font-weight: 500;
            color: var(--text-main);
            font-size: 0.82rem;
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
            margin-top: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 4px;
            min-width: 0;
        }
        .sidebar-display-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .sidebar-display-name:hover {
            color: var(--primary);
        }
        .sidebar-name-input {
            width: 100%;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 4px 8px;
            font-size: 0.8rem;
            background: #ffffff;
            color: var(--text-main);
        }
        .sidebar-name-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* Dark Mode for Minimal Info List */
        .dark-mode-active .form-control-minimal {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .dark-mode-active .form-control-minimal:focus {
            border-color: #6482a6;
        }
        .dark-mode-active .avatar-edit-badge {
            background-color: #1e293b;
            border-color: var(--border-color);
            color: var(--text-sub);
        }
        .dark-mode-active .avatar-container:hover .avatar-edit-badge {
            background-color: #2b4c7e;
            color: #f8fafc;
            border-color: #2b4c7e;
        }

        /* Business Account tab styles */
        .biz-type-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 12px;
        }
        .biz-type-card:hover {
            border-color: var(--primary);
            background-color: rgba(0, 112, 255, 0.02);
        }
        .biz-type-card.selected {
            border-color: var(--primary);
            background-color: rgba(0, 112, 255, 0.05);
            box-shadow: 0 0 0 1px var(--primary);
        }
        .biz-type-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-sub);
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .selected .biz-type-icon {
            background-color: rgba(0, 112, 255, 0.1);
            color: var(--primary);
        }
        .biz-type-info {
            flex: 1;
        }
        .biz-type-name {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        .biz-type-desc {
            font-size: 0.8rem;
            color: var(--text-sub);
        }
        .biz-type-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            font-size: 0.75rem;
            font-weight: bold;
            transition: all 0.15s ease;
        }
        .selected .biz-type-checkbox {
            border-color: var(--primary);
            background-color: var(--primary);
            color: white;
        }

        /* Phone Mockup Styling */
        .phone-mockup-wrapper {
            position: sticky;
            top: 20px;
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .phone-mockup {
            width: 310px;
            height: 570px;
            border: 10px solid #1e293b;
            border-radius: 36px;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            color: #3c4043;
        }
        .phone-notch {
            width: 120px;
            height: 16px;
            background-color: #1e293b;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            z-index: 100;
        }
        .phone-screen {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            padding-top: 24px;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            font-size: 0.75rem;
        }
        .mock-google-search {
            background: white;
            border-radius: 16px;
            padding: 5px 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .mock-google-logo {
            font-weight: 700;
            font-size: 0.8rem;
        }
        .mock-google-logo span:nth-child(1) { color: #4285F4; }
        .mock-google-logo span:nth-child(2) { color: #EA4335; }
        .mock-google-logo span:nth-child(3) { color: #FBBC05; }
        .mock-google-logo span:nth-child(4) { color: #4285F4; }
        .mock-google-logo span:nth-child(5) { color: #34A853; }
        .mock-google-logo span:nth-child(6) { color: #EA4335; }
        .mock-search-text {
            flex: 1;
            color: #202124;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.7rem;
        }
        .mock-business-card {
            background: white;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 1px 2px rgba(60,64,67,0.3);
            margin-bottom: 10px;
        }
        .mock-biz-name {
            font-size: 0.95rem;
            font-weight: bold;
            color: #202124;
            margin-bottom: 2px;
        }
        .mock-biz-rating {
            color: #fbbc05;
            font-size: 0.7rem;
            margin-bottom: 4px;
        }
        .mock-biz-category {
            color: #70757a;
            font-size: 0.7rem;
            margin-bottom: 6px;
        }
        .mock-action-buttons {
            display: flex;
            justify-content: space-around;
            border-top: 1px solid #f1f3f4;
            border-bottom: 1px solid #f1f3f4;
            padding: 6px 0;
            margin-bottom: 8px;
        }
        .mock-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #1a73e8;
            font-size: 0.65rem;
            gap: 3px;
        }
        .mock-action-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #e8f0fe;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mock-info-rows {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 6px;
        }
        .mock-info-row {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            color: #3c4043;
            font-size: 0.7rem;
        }
        .mock-info-icon {
            color: #70757a;
            width: 14px;
            text-align: center;
            margin-top: 1px;
        }
        .mock-photos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            margin-top: 8px;
        }
        .mock-photo-item {
            aspect-ratio: 1.5;
            background-color: #e8eaed;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dadce0;
        }
        .mock-photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Drag and Drop Uploader */
        .dropzone-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: rgba(248, 250, 252, 0.5);
            margin-bottom: 20px;
        }
        .dropzone-area:hover {
            border-color: var(--primary);
            background-color: rgba(0, 112, 255, 0.01);
        }
        .dropzone-icon {
            font-size: 2.2rem;
            color: var(--text-sub);
            margin-bottom: 10px;
        }
        .upload-previews {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .preview-thumbnail {
            position: relative;
            aspect-ratio: 1;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .preview-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 18px;
            height: 18px;
            background: rgba(239, 68, 68, 0.85);
            border-radius: 50%;
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            cursor: pointer;
        }

        /* Stepper navigation progress bar */
        .step-progress-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-progress-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--border-color);
            transform: translateY(-50%);
            z-index: 1;
        }
        .step-progress-fill {
            position: absolute;
            top: 50%;
            left: 0;
            height: 2px;
            background-color: var(--primary);
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.3s ease;
            width: 0%;
        }
        .step-progress-node {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 3;
            color: var(--text-sub);
            transition: all 0.3s ease;
        }
        .step-progress-node.active {
            border-color: var(--primary);
            background-color: var(--primary);
            color: white;
        }
        .step-progress-node.completed {
            border-color: var(--primary);
            background-color: var(--card-bg);
            color: var(--primary);
        }

        /* Autocomplete dropdown for categories */
        .category-autocomplete {
            position: relative;
        }
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
        }
        .autocomplete-item {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--text-main);
            transition: background-color 0.15s ease;
        }
        .autocomplete-item:hover {
            background-color: #f1f5f9;
        }
        .dark-mode-active .autocomplete-item:hover {
            background-color: #1e293b;
        }

        /* Map styling */
        #businessMap {
            height: 250px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 15px;
            z-index: 1;
        }

        /* Responsive mockup layouts */
        .wizard-row {
            display: flex;
            gap: 30px;
        }
        .wizard-form-col {
            flex: 1;
        }
        .wizard-mockup-col {
            width: 310px;
            flex-shrink: 0;
        }
        @media (max-width: 991px) {
            .wizard-row {
                flex-direction: column-reverse;
            }
            .wizard-mockup-col {
                width: 100%;
            }
        }

        /* Custom Delete Confirmation Modal Premium Styling */
        .modal-confirm-backdrop {
            backdrop-filter: blur(12px) saturate(180%);
            background-color: rgba(15, 23, 42, 0.65) !important;
        }

        #deleteReviewModal .modal-dialog {
            max-width: 440px !important;
        }

        #deleteReviewModal .modal-content {
            border-radius: 24px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.05) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            position: relative;
        }

        /* Top Red Accent Bar */
        .confirm-modal-top-bar {
            height: 5px;
            width: 100%;
            background: linear-gradient(90deg, #f43f5e 0%, #e11d48 50%, #be123c 100%);
        }

        .confirm-icon-badge {
            width: 64px;
            height: 64px;
            background: #ffe4e6 !important;
            color: #e11d48 !important;
            border-radius: 20px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.6rem !important;
            margin: 0 auto 16px auto !important;
            border: 1px solid #fecdd3 !important;
            box-shadow: 0 8px 16px -4px rgba(225, 29, 72, 0.15) !important;
        }

        .confirm-title-text {
            color: #0f172a !important;
            font-size: 1.2rem !important;
            font-weight: 700 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            letter-spacing: -0.01em;
        }

        .confirm-desc-text {
            color: #64748b !important;
            font-size: 0.875rem !important;
            line-height: 1.55 !important;
        }

        /* Preview Card Container */
        .confirm-preview-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            padding: 14px 16px !important;
            text-align: left !important;
            margin-bottom: 24px !important;
        }

        .confirm-preview-tag {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #f1f5f9 !important;
            color: #1e3a5f !important;
            font-weight: 600 !important;
            font-size: 0.775rem !important;
            padding: 4px 10px !important;
            border-radius: 20px !important;
            margin-bottom: 6px !important;
        }

        .confirm-preview-quote {
            color: #334155 !important;
            font-size: 0.85rem !important;
            font-style: italic !important;
            font-weight: 500 !important;
            line-height: 1.4 !important;
            word-break: break-word;
        }

        /* Buttons */
        .btn-confirm-cancel-styled {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            border-radius: 14px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 12px 18px !important;
            font-size: 0.875rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.2s ease !important;
        }
        .btn-confirm-cancel-styled:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }

        .btn-confirm-delete-styled {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 14px !important;
            border: none !important;
            padding: 12px 18px !important;
            font-size: 0.875rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35) !important;
            transition: all 0.2s ease !important;
        }
        .btn-confirm-delete-styled:hover {
            background: linear-gradient(135deg, #be123c 0%, #9f1239 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 8px 22px rgba(225, 29, 72, 0.45) !important;
            transform: translateY(-1px) !important;
        }

        /* Dark Mode Support */
        .dark-mode-active #deleteReviewModal .modal-content {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        .dark-mode-active #deleteReviewModal .confirm-title-text {
            color: #f8fafc !important;
        }
        .dark-mode-active #deleteReviewModal .confirm-desc-text {
            color: #94a3b8 !important;
        }
        .dark-mode-active #deleteReviewModal .confirm-preview-card {
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        .dark-mode-active #deleteReviewModal .confirm-preview-tag {
            background: rgba(30, 58, 95, 0.45) !important;
            color: #cbd5e1 !important;
        }
        .dark-mode-active #deleteReviewModal .confirm-preview-quote {
            color: #cbd5e1 !important;
        }
        .dark-mode-active #deleteReviewModal .btn-confirm-cancel-styled {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
        }
        .dark-mode-active #deleteReviewModal .btn-confirm-cancel-styled:hover {
            background-color: #475569 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-navbar">
    <div class="top-navbar-left">
        <a href="{{ route('home') }}" class="btn-back" title="Quay lại bản đồ">
            <span class="back-chevron">‹</span>
            Quay lại
        </a>
    </div>
    <div class="top-navbar-brand">Ninh Bình Travel Hub</div>
    <div class="top-navbar-spacer" aria-hidden="true"></div>
</div>

<!-- Main Layout Wrapper -->
<div class="main-layout" id="profile-app-container">
    <!-- Sidebar Navigation -->
    <div class="dashboard-sidebar">
        <div class="sidebar-user-section">
            <div class="avatar-container {{ $user->equippedFrame ? ($user->equippedFrame->image_url ? 'avatar-frame-wrapper has-png-frame' : 'avatar-frame-wrapper ' . $user->equippedFrame->css_style) : '' }}" id="sidebarAvatarContainer" title="Nhấp để thay ảnh hoặc đổi khung avatar">
                <img src="{{ $user->avatar_formatted_url }}" 
                     alt="Avatar" 
                     class="user-avatar-img"
                     id="profileAvatarPreview"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=1e3a5f&color=fff';">
                @if($user->equippedFrame && $user->equippedFrame->image_url)
                    <img src="{{ asset($user->equippedFrame->image_url) }}" class="avatar-frame-png-overlay">
                @endif
                <div class="avatar-upload-overlay">
                    Đổi
                </div>
                <div class="avatar-edit-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                </div>
                <div class="avatar-loader-spinner" id="avatarUploadSpinner">
                    <div class="spinner-border spinner-border-sm text-light" role="status"></div>
                </div>
            </div>
            <!-- Hidden File Input -->
            <input type="file" id="avatarFileInput" accept="image/*" class="d-none">

            <div class="sidebar-user-meta">
                <div class="sidebar-name-container">
                    <span id="sidebarDisplayNameText" class="sidebar-display-name" title="Nhấp để đổi tên">
                        <span id="sidebarDisplayNameVal">{{ $user->display_name ?? $user->username }}</span>
                        <span class="edit-name-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                        </span>
                    </span>
                    <input type="text" id="sidebarDisplayNameInput" class="sidebar-name-input d-none" value="{{ $user->display_name ?? $user->username }}" maxlength="120">
                </div>
            </div>

            <div class="sidebar-user-chips">
                <span class="user-role-badge">
                    {{ $user->role === 'admin' ? 'Admin' : ($user->role === 'moderator' ? 'Mod' : 'Member') }}
                </span>
                <span class="sidebar-points-chip">
                    {{ number_format($user->points) }} pts
                </span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav flex-column sidebar-menu-tabs" id="settings-tabs" role="tablist">
            <div class="sidebar-nav-group">Tài khoản</div>
            <button class="nav-link active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab" aria-selected="true">
                Thông tin cá nhân
            </button>
            @if($user->provider !== 'google')
            <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab" aria-selected="false">
                Bảo mật & Mật khẩu
            </button>
            @endif

            <div class="sidebar-nav-group">Hoạt động</div>
            <button class="nav-link" id="tab-points-btn" data-bs-toggle="pill" data-bs-target="#tab-points" type="button" role="tab" aria-selected="false">
                Lịch sử tích điểm
            </button>
            <button class="nav-link" id="tab-favorites-btn" data-bs-toggle="pill" data-bs-target="#tab-favorites" type="button" role="tab" aria-selected="false">
                <span>Địa điểm đã lưu</span>
                <span class="menu-count-badge" id="favoritesCountBadge">{{ $favorites->count() }}</span>
            </button>
            <button class="nav-link" id="tab-itineraries-btn" data-bs-toggle="pill" data-bs-target="#tab-itineraries" type="button" role="tab" aria-selected="false">
                <span>Lịch trình đã lưu</span>
                <span class="menu-count-badge" id="itinerariesCountBadge">{{ isset($itineraries) ? $itineraries->count() : 0 }}</span>
            </button>
            <button class="nav-link" id="tab-comments-btn" data-bs-toggle="pill" data-bs-target="#tab-comments" type="button" role="tab" aria-selected="false">
                <span>Nhận xét của tôi</span>
                <span class="menu-count-badge" id="commentsCountBadge">{{ $comments->count() }}</span>
            </button>

            <div class="sidebar-nav-group">Mở rộng</div>
            <button class="nav-link" id="tab-business-btn" data-bs-toggle="pill" data-bs-target="#tab-business" type="button" role="tab" aria-selected="false">
                <span class="sidebar-business-label">Doanh nghiệp của bạn</span>
                @if(isset($businessProfile) && $businessProfile->status === 'pending')
                    <span class="sidebar-status-pill">Chờ duyệt</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Content Workspace -->
    <div class="dashboard-content">
        <div class="workspace-toolbar">
            <div class="workspace-crumb">
                <span>Account</span>
                <span class="sep">/</span>
                <strong id="workspaceTabTitle">Thông tin cá nhân</strong>
            </div>
            <div class="workspace-actions">
                @if(isset($notifications) && $notifications->count() > 0)
                    @php $unreadCount = $notifications->whereNull('read_at')->count(); @endphp
                    <button type="button" class="notification-toolbar-btn" data-bs-toggle="modal" data-bs-target="#notificationListModal">
                        <span>Thông báo</span>
                        @if($unreadCount > 0)
                            <span class="notification-unread-badge">{{ $unreadCount }}</span>
                        @endif
                    </button>
                @endif
            </div>
        </div>

        <div class="workspace-body">
        <!-- Toast Alerts System -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
            <div id="settingsToast" class="toast align-items-center text-white bg-dark border-0 rounded-1" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">Thông báo</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-1 border-0 mb-3 py-2 px-3 small" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger rounded-1 border-0 mb-3 py-2 px-3 small" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rounded-1 border-0 mb-3 py-2 px-3 small" role="alert">
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
                <div class="content-panel" style="margin-bottom:12px;">
                    <div class="panel-head">
                        <div class="section-title">Thông tin nhanh</div>
                        @if($user->provider !== 'google')
                            <button
                                type="button"
                                class="btn-action"
                                style="padding:5px 12px;font-size:0.72rem;"
                                data-bs-toggle="pill"
                                data-bs-target="#tab-security"
                                onclick="document.getElementById('tab-security-btn')?.click()"
                            >
                                Đổi mật khẩu
                            </button>
                        @endif
                    </div>
                    <div class="panel-body" style="padding-top:12px;padding-bottom:12px;">
                        <div class="info-list mb-0">
                            <div class="info-item">
                                <div class="info-label">Tên hiển thị</div>
                                <div class="info-value" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <span id="profileTopDisplayName">{{ $user->display_name ?? $user->username }}</span>
                                    <span class="text-secondary" style="font-size:0.72rem;">Nhấp biểu tượng bút bên sidebar để đổi tên</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="notificationListModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 border-0 shadow-sm">
                            <div class="modal-header">
                                <h5 class="modal-title">Thông báo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="notification-modal-list">
                                    @if(isset($notifications) && $notifications->count() > 0)
                                        @foreach($notifications as $noti)
                                            <div class="notification-modal-item {{ is_null($noti->read_at) ? 'is-unread' : '' }}">
                                                <div class="notification-modal-head">
                                                    <div class="notification-modal-title">{{ $noti->title }}</div>
                                                    <div class="notification-modal-time">{{ $noti->created_at->format('d/m/Y H:i') }}</div>
                                                </div>
                                                <div class="notification-modal-body">{{ $noti->message ?: 'Không có nội dung chi tiết.' }}</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-secondary small">Chưa có thông báo nào.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-stat-grid">
                    <div class="stat-tile">
                        <div class="stat-label">Điểm tích lũy</div>
                        <div class="stat-value">{{ number_format($user->points) }}</div>
                        <div class="stat-hint">Điểm thưởng hiện có</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Địa điểm đã lưu</div>
                        <div class="stat-value">{{ $favorites->count() }}</div>
                        <div class="stat-hint">Favorites</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Lịch trình</div>
                        <div class="stat-value">{{ isset($itineraries) ? $itineraries->count() : 0 }}</div>
                        <div class="stat-hint">AI itineraries</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Nhận xét</div>
                        <div class="stat-value">{{ $comments->count() }}</div>
                        <div class="stat-hint">Reviews đã đăng</div>
                    </div>
                </div>

                <div class="profile-split">
                    <div class="content-panel">
                        <div class="panel-head">
                            <div class="section-title">Hồ sơ tài khoản</div>
                            <span class="user-role-badge" style="background:#f1f5f9;color:#475569;border-color:#e2e8f0;">
                                {{ $user->status === 'active' ? 'Active' : 'Locked' }}
                            </span>
                        </div>
                        <div class="panel-body">
                            <div class="info-list mb-0">
                                <div class="info-item">
                                    <div class="info-label">Tên hiển thị</div>
                                    <div class="info-value" id="profileFormDisplayNameVal">{{ $user->display_name ?? $user->username }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $user->email }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Nhóm quyền</div>
                                    <div class="info-value">
                                        {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'moderator' ? 'Kiểm duyệt viên' : 'Thành viên') }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Trạng thái</div>
                                    <div class="info-value">
                                        <span class="status-dot active"></span> {{ $user->status === 'active' ? 'Đang hoạt động' : 'Bị khóa' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Lịch sử tích điểm -->
            <div class="tab-pane fade" id="tab-points" role="tabpanel">
                <div class="profile-stat-grid" style="margin-bottom:12px;">
                    <div class="stat-tile">
                        <div class="stat-label">Số dư điểm</div>
                        <div class="stat-value">{{ number_format($user->points) }}</div>
                        <div class="stat-hint">Hiện có</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Mục nhật ký</div>
                        <div class="stat-value">{{ number_format(count($pointHistory ?? [])) }}</div>
                        <div class="stat-hint">Sau khi gộp · {{ number_format($pointTxTotal ?? 0) }} bản ghi gốc</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Điểm danh</div>
                        <div class="stat-value">10–70</div>
                        <div class="stat-hint">Theo chuỗi ngày</div>
                    </div>
                    <div class="stat-tile">
                        <div class="stat-label">Bình luận / Lưu</div>
                        <div class="stat-value">+5 / +2</div>
                        <div class="stat-hint">Mỗi hành động</div>
                    </div>
                </div>
                <div class="content-panel">
                    <div class="section-title">
                        <span>Nhật ký tích điểm</span>
                        <span class="dt-meta" id="pointsVisibleMeta">0 mục</span>
                    </div>
                    <p class="text-secondary mb-2" style="font-size:0.72rem;line-height:1.4;">
                        Các lần cộng +1 online cũ được gộp theo ngày để dễ đọc. Online mới không còn cộng xu từng phút.
                    </p>
                    <div class="data-toolbar" id="pointsFilterBar">
                        <div class="dt-left">
                            <button type="button" class="filter-chip on" data-points-filter="all">Tất cả</button>
                            <button type="button" class="filter-chip" data-points-filter="daily">Điểm danh</button>
                            <button type="button" class="filter-chip" data-points-filter="comment">Bình luận</button>
                            <button type="button" class="filter-chip" data-points-filter="favorite">Yêu thích</button>
                            <button type="button" class="filter-chip" data-points-filter="mission">Nhiệm vụ</button>
                            <button type="button" class="filter-chip" data-points-filter="session">Online (gộp)</button>
                        </div>
                        <div class="dt-meta">Mới nhất trước</div>
                    </div>
                    <div class="data-list" id="pointsHistoryList">
                        @forelse(($pointHistory ?? []) as $row)
                            @php
                                $badgeMap = [
                                    'daily_login' => ['Điểm danh', 'success'],
                                    'comment' => ['Bình luận', 'primary'],
                                    'favorite' => ['Yêu thích', 'info'],
                                    'mission_reward' => ['Nhiệm vụ', 'warning'],
                                    'active_session' => ['Online', 'secondary'],
                                    'location_suggestion_approved' => ['Đóng góp', 'success'],
                                    'feedback_resolved' => ['Đóng góp', 'success'],
                                ];
                                [$badgeLabel, $badgeTone] = $badgeMap[$row['action']] ?? ['Khác', 'secondary'];
                            @endphp
                            <div class="data-row points-history-row"
                                 data-filter="{{ $row['filter'] }}">
                                <div style="min-width:0;">
                                    <div style="font-variant-numeric:tabular-nums;font-size:0.72rem;font-weight:700;color:#0f172a;">
                                        {{ $row['created_at']->format('d/m') }}
                                    </div>
                                    <div style="font-variant-numeric:tabular-nums;font-size:0.68rem;color:#94a3b8;">
                                        {{ $row['aggregated'] ? 'cả ngày' : $row['created_at']->format('H:i') }}
                                    </div>
                                </div>
                                <div style="min-width:0;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="badge bg-{{ $badgeTone }} bg-opacity-10 text-{{ $badgeTone }} border-0 px-2 py-1" style="font-size:0.68rem;border-radius:3px!important;">{{ $badgeLabel }}</span>
                                        @if(!empty($row['aggregated']))
                                            <span style="font-size:0.65rem;color:#64748b;">{{ $row['count'] }} lần · đã gộp</span>
                                        @endif
                                    </div>
                                    <div style="font-size:0.78rem;color:#334155;line-height:1.35;word-break:break-word;">
                                        {{ $row['description'] ?: '—' }}
                                    </div>
                                </div>
                                <div class="fw-bold {{ $row['amount'] >= 0 ? 'text-success' : 'text-danger' }}"
                                     style="font-variant-numeric:tabular-nums;font-size:0.9rem;text-align:right;">
                                    {{ $row['amount'] >= 0 ? '+' : '' }}{{ $row['amount'] }}
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="border:0;background:transparent;">Chưa có lịch sử giao dịch điểm.</div>
                        @endforelse
                    </div>
                    <div class="d-flex justify-content-center mt-3" id="pointsLoadMoreWrap" hidden>
                        <button type="button" class="btn-action" id="pointsLoadMoreBtn" style="min-width:140px;">Xem thêm</button>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Security & Password (chỉ tài khoản email/mật khẩu, không áp dụng Google OAuth) -->
            @if($user->provider !== 'google')
            <div class="tab-pane fade" id="tab-security" role="tabpanel">
                <div class="sec-split">
                    <div class="content-panel">
                        <div class="section-title">Bảo mật & Mật khẩu</div>
                        <div class="ops-form-card">
                            <div class="ops-form-title">Cập nhật mật khẩu</div>
                            <form action="{{ route('client.profile.password') }}" method="POST">
                                @csrf
                                @if(!empty($user->password_hash))
                                    <div class="mb-3">
                                        <label class="form-label-clean">Mật khẩu hiện tại</label>
                                        <input type="password" class="form-control-clean" name="current_password" required autocomplete="current-password">
                                    </div>
                                @endif
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label-clean">Mật khẩu mới</label>
                                        <input type="password" class="form-control-clean" name="password" required autocomplete="new-password">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-clean">Xác nhận mật khẩu</label>
                                        <input type="password" class="form-control-clean" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-action">Lưu mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="content-panel">
                        <div class="section-title">Trạng thái bảo mật</div>
                        <div class="checklist">
                            <div class="checklist-item">
                                <div>
                                    <div class="ci-label">Email</div>
                                    <div class="ci-value">{{ $user->email }}</div>
                                </div>
                                <span class="ci-ok">Đã gắn</span>
                            </div>
                            <div class="checklist-item">
                                <div>
                                    <div class="ci-label">Phương thức đăng nhập</div>
                                    <div class="ci-value">Email / mật khẩu</div>
                                </div>
                                <span class="ci-ok">OK</span>
                            </div>
                            <div class="checklist-item">
                                <div>
                                    <div class="ci-label">Mật khẩu cục bộ</div>
                                    <div class="ci-value">{{ !empty($user->password_hash) ? 'Đã thiết lập' : 'Chưa thiết lập' }}</div>
                                </div>
                                @if(!empty($user->password_hash))
                                    <span class="ci-ok">OK</span>
                                @else
                                    <span class="ci-warn">Thiếu</span>
                                @endif
                            </div>
                            <div class="checklist-item">
                                <div>
                                    <div class="ci-label">Trạng thái tài khoản</div>
                                    <div class="ci-value">{{ $user->status === 'active' ? 'Đang hoạt động' : 'Bị khóa' }}</div>
                                </div>
                                <span class="{{ $user->status === 'active' ? 'ci-ok' : 'ci-warn' }}">{{ $user->status === 'active' ? 'Active' : 'Locked' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tab 3: Saved Locations -->
            <div class="tab-pane fade" id="tab-favorites" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">
                        <span>Địa điểm đã lưu</span>
                        <span class="dt-meta">{{ $favorites->count() }} mục</span>
                    </div>
                    <div class="data-toolbar">
                        <div class="dt-left">
                            <span class="filter-chip on">Tất cả</span>
                            <span class="dt-meta">Danh sách yêu thích trên bản đồ</span>
                        </div>
                        <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;">+ Thêm từ bản đồ</a>
                    </div>
                    <div class="data-list" id="favoritesGrid">
                        @forelse($favorites as $location)
                            <div class="data-row favorite-card-wrapper" id="fav-card-{{ $location->id }}">
                                <img src="{{ $location->thumbnail_url }}" alt="{{ $location->name }}" class="data-thumb" onerror="this.style.opacity='0.35'">
                                <div class="data-main">
                                    <div class="data-title">{{ $location->name }}</div>
                                    <p class="data-desc">{{ $location->short_description ?? 'Không có mô tả.' }}</p>
                                </div>
                                <div class="data-actions">
                                    <a href="{{ url('/#loc-' . $location->id) }}" class="btn-ghost">Bản đồ</a>
                                    <a href="{{ route('client.locations.360', $location->slug) }}" class="btn-solid">360°</a>
                                    <button type="button" class="btn-danger-ghost favorite-toggle-btn" data-location-id="{{ $location->id }}">Bỏ thích</button>
                                </div>
                            </div>
                        @empty
                            <div id="noFavoritesMsg" class="empty-state" style="border:0;border-radius:0;">
                                <p>Bạn chưa lưu địa điểm nào.</p>
                                <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;display:inline-block;">Tìm địa điểm trên bản đồ</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tab: Saved Itineraries -->
            <div class="tab-pane fade" id="tab-itineraries" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">
                        <span>Lịch trình đã lưu</span>
                        <span class="dt-meta">{{ isset($itineraries) ? $itineraries->count() : 0 }} mục</span>
                    </div>
                    <div class="data-toolbar">
                        <div class="dt-left">
                            <span class="filter-chip on">AI itineraries</span>
                            <span class="dt-meta">Lưu từ tính năng Lên lịch trình</span>
                        </div>
                        <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;" onclick="if(window.openTripPlanner){event.preventDefault(); window.openTripPlanner(true);}">+ Tạo lịch trình</a>
                    </div>
                    <div class="data-list" id="itinerariesGrid">
                        @forelse(($itineraries ?? collect()) as $it)
                            <div class="data-row itinerary itinerary-card-wrapper" id="itinerary-card-{{ $it->id }}">
                                <div class="data-main">
                                    <div class="data-title">{{ $it->title }}</div>
                                    <p class="data-desc">{{ $it->summary ?? $it->description ?? 'Không có tóm tắt.' }}</p>
                                    <div class="it-card-meta" style="margin:8px 0 0;">
                                        <span class="it-card-chip">{{ $it->total_days }} ngày</span>
                                        @if($it->estimated_cost)
                                            <span class="it-card-chip">{{ $it->estimated_cost }}</span>
                                        @endif
                                        <span class="it-card-chip">{{ $it->created_at?->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="data-actions">
                                    <a href="{{ route('home') }}?itinerary={{ $it->id }}" class="btn-solid itinerary-view-btn" style="text-decoration:none;">Xem lại</a>
                                    <button type="button" class="btn-danger-ghost itinerary-delete-btn" data-id="{{ $it->id }}">Xóa</button>
                                </div>
                            </div>
                        @empty
                            <div id="noItinerariesMsg" class="empty-state" style="border:0;border-radius:0;">
                                <p>Bạn chưa lưu lịch trình nào.</p>
                                <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;display:inline-block;" onclick="if(window.openTripPlanner){event.preventDefault(); window.openTripPlanner(true);}">Lên lịch trình AI</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tab 4: Comments list -->
            <div class="tab-pane fade" id="tab-comments" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">
                        <span>Nhận xét của tôi</span>
                        <span class="dt-meta">{{ $comments->count() }} mục</span>
                    </div>
                    <div class="data-toolbar">
                        <div class="dt-left">
                            <span class="filter-chip on">Tất cả</span>
                            <span class="dt-meta">Quản lý nhận xét đã đăng</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="clean-table" id="commentsTable">
                            <thead>
                                <tr>
                                    <th style="width:22%;">Địa điểm</th>
                                    <th>Nội dung</th>
                                    <th style="width:90px;">Đánh giá</th>
                                    <th style="width:120px;">Thời gian</th>
                                    <th style="width:110px;text-align:right;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                    <tr id="comment-row-{{ $comment->id }}">
                                        <td>
                                            <a href="{{ url('/#loc-' . $comment->location_id) }}" class="text-decoration-none fw-semibold" style="color:var(--text-main);font-size:0.8rem;">
                                                {{ $comment->location->name ?? 'Địa điểm ẩn' }}
                                            </a>
                                        </td>
                                        <td class="text-secondary" style="font-size:0.78rem;">{{ $comment->content }}</td>
                                        <td><span class="text-warning fw-bold" style="font-variant-numeric:tabular-nums;">{{ $comment->rating ?? 0 }} ★</span></td>
                                        <td style="font-size:0.72rem;font-variant-numeric:tabular-nums;white-space:nowrap;">{{ $comment->created_at?->format('d/m/Y H:i') }}</td>
                                        <td style="text-align:right;">
                                            <button type="button" class="btn-danger-ghost delete-comment-btn" data-comment-id="{{ $comment->id }}">Xóa</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noCommentsRow">
                                        <td colspan="5"><div class="empty-state" style="border:0;background:transparent;">Bạn chưa viết nhận xét nào.</div></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Business Account -->
            <div class="tab-pane fade" id="tab-business" role="tabpanel">
                @if(isset($businessProfile))
                    @if($businessProfile->status === 'pending')
                        <div class="content-panel">
                            <div class="section-title">Yêu cầu nâng cấp tài khoản doanh nghiệp</div>
                            <div class="biz-pending-shell">
                                <div class="biz-pending-status">
                                    <div>
                                        <strong>Đang chờ phê duyệt</strong>
                                        <p>Hồ sơ doanh nghiệp của bạn đã được gửi thành công. Hệ thống đang xác minh thông tin và vị trí bản đồ.</p>
                                    </div>
                                </div>

                                <div class="biz-pending-grid">
                                    <div class="biz-pending-card">
                                        <div class="biz-pending-card-head">
                                            <div>
                                                <div class="biz-pending-card-title">Thông tin đã gửi</div>
                                                <p class="biz-pending-card-sub">Bạn có thể kiểm tra lại hồ sơ trước khi được kích hoạt.</p>
                                            </div>
                                        </div>
                                        <div class="biz-pending-card-body">
                                            <div class="biz-pending-fields">
                                                <div class="biz-pending-field">
                                                    <span class="biz-pending-label">Tên doanh nghiệp</span>
                                                    <div class="biz-pending-value is-primary">{{ $businessProfile->business_name }}</div>
                                                </div>
                                                <div class="biz-pending-field">
                                                    <span class="biz-pending-label">Danh mục</span>
                                                    <div class="biz-pending-value">{{ $businessProfile->category ? $businessProfile->category->name : 'N/A' }}</div>
                                                </div>
                                                <div class="biz-pending-field">
                                                    <span class="biz-pending-label">Địa chỉ</span>
                                                    <div class="biz-pending-value">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="biz-pending-card">
                                        <div class="biz-pending-card-head">
                                            <div>
                                                <div class="biz-pending-card-title">Vị trí doanh nghiệp</div>
                                                <p class="biz-pending-card-sub">Địa điểm bạn đã ghim khi gửi yêu cầu nâng cấp.</p>
                                            </div>
                                        </div>
                                        <div id="pendingBusinessMap" class="biz-pending-map"></div>
                                    </div>
                                </div>

                                <div class="biz-pending-actions">
                                    <button type="button" class="btn-danger-ghost" id="cancelBusinessRequestBtn">
                                        Hủy yêu cầu đăng ký
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($businessProfile->status === 'approved')
                        @php
                            $loc = \App\Models\Location::where('created_by', $user->id)->first();
                        @endphp
                        @if(!$loc)
                            <div class="content-panel">
                                <div class="section-title">Tài khoản doanh nghiệp</div>
                                <div class="panel-note danger">
                                    <div>
                                        <strong>Địa điểm đã bị gỡ khỏi hệ thống.</strong>
                                        Hồ sơ “{{ $businessProfile->business_name }}” không còn trên bản đồ. Bạn có thể đăng ký lại nếu cần.
                                    </div>
                                </div>
                                <a href="{{ route('client.profile.business.upgrade') }}" class="btn-solid" style="text-decoration:none;display:inline-block;">Đăng ký lại</a>
                            </div>
                        @else
                        <div class="content-panel">
                            <div class="section-title">Quản lý tài khoản doanh nghiệp</div>
                            <div class="panel-note ok">
                                <div><strong>Đã kích hoạt doanh nghiệp.</strong> Địa điểm của bạn đã được đưa lên hệ thống.</div>
                            </div>
                            <div class="card border border-success bg-success bg-opacity-10 p-3 rounded-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-success">{{ $businessProfile->business_name }}</h6>
                                        <p class="text-secondary mb-0 small">Danh mục: {{ $businessProfile->category ? $businessProfile->category->name : 'N/A' }}</p>
                                    </div>
                                    <span class="badge bg-success">Đã kích hoạt</span>
                                </div>
                            </div>
                            
                            <div class="biz-feature-grid">
                                <div class="biz-feature">
                                    <strong>Xem trang địa điểm</strong>
                                    <p class="mb-2">Hiển thị thực tế trên bản đồ / 360°.</p>
                                    <a href="{{ route('client.locations.360', $loc->slug) }}" target="_blank" class="btn-ghost" style="display:inline-block;">Xem chi tiết</a>
                                </div>
                                <div class="biz-feature">
                                    <strong>Trang quản trị</strong>
                                    <p class="mb-2">Dashboard dành cho chủ doanh nghiệp.</p>
                                    <a href="{{ route('business.dashboard') }}" class="btn-solid" style="display:inline-block;text-decoration:none;">Vào quản trị</a>
                                </div>
                                <div class="biz-feature">
                                    <strong>Dịch vụ tour 360</strong>
                                    <p class="mb-2">Quay & dựng panorama theo nhu cầu, báo giá sau tư vấn.</p>
                                    <a href="{{ route('client.pano_service') }}" class="btn-ghost" style="display:inline-block;">Tìm hiểu & gửi yêu cầu</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    @elseif($businessProfile->status === 'rejected')
                        @php
                            $rejectReason = (string) ($businessProfile->reject_reason ?? '');
                            $isLocationSoftRemoved = str_starts_with(
                                $rejectReason,
                                \App\Models\Location::BIZ_SOFT_DELETE_REASON_PREFIX
                            );
                            $isBusinessRevoked = str_starts_with(
                                $rejectReason,
                                \App\Models\BusinessProfile::BIZ_REVOKED_REASON_PREFIX
                            );
                            $hideRejectBanner = $isLocationSoftRemoved || $isBusinessRevoked;
                        @endphp
                        @if($hideRejectBanner)
                            {{-- Lý do gỡ địa điểm đã gửi qua mục Thông báo — hiện UI đăng ký như chưa có hồ sơ --}}
                            <div class="content-panel">
                                <div class="section-title">
                                    <span>Nâng cấp tài khoản doanh nghiệp</span>
                                    <span class="dt-meta">Miễn phí</span>
                                </div>
                                <div class="biz-hero">
                                    <div>
                                        <h5 class="fw-bold mb-2" style="font-size:1.05rem;letter-spacing:-0.02em;">Đưa địa điểm lên bản đồ Ninh Bình Travel Hub</h5>
                                        <p class="text-secondary mb-3" style="font-size:0.82rem;line-height:1.55;max-width:48ch;">
                                            Quảng bá nhà hàng, khách sạn, cửa hàng hoặc dịch vụ miễn phí. Tiếp cận người dùng đang tìm địa điểm tại Ninh Bình.
                                        </p>
                                        <a href="{{ route('client.profile.business.upgrade') }}" class="btn-solid" style="text-decoration:none;display:inline-block;padding:9px 16px;">Bắt đầu đăng ký</a>
                                    </div>
                                    <div class="biz-feature-grid">
                                        <div class="biz-feature">
                                            <strong>Xuất hiện trên bản đồ</strong>
                                            <p>Hiển thị vị trí chính xác trên bản đồ vệ tinh.</p>
                                        </div>
                                        <div class="biz-feature">
                                            <strong>Trình bày hình ảnh</strong>
                                            <p>Đăng ảnh mặt tiền, phòng nghỉ hoặc thực đơn.</p>
                                        </div>
                                        <div class="biz-feature">
                                            <strong>Tương tác trực tiếp</strong>
                                            <p>Trả lời bình luận và nhận phản hồi từ khách.</p>
                                        </div>
                                        <div class="biz-feature">
                                            <strong>Trang quản trị</strong>
                                            <p>Quản lý nội dung và theo dõi tương tác.</p>
                                        </div>
                                    </div>
                                </div>

                                <p class="biz-pano-note">
                                    <i class="fa-solid fa-panorama"></i>
                                    Muốn địa điểm nổi bật hơn với không gian 360°?
                                    <a href="{{ route('client.pano_service') }}" target="_blank" rel="noopener">Tìm hiểu dịch vụ chụp Tour 360°</a>
                                </p>
                            </div>
                        @else
                            <div class="content-panel">
                                <div class="section-title">Đăng ký tài khoản doanh nghiệp</div>
                                <div class="panel-note danger">
                                    <div>
                                        <strong>Yêu cầu bị từ chối.</strong>
                                        Lý do: {{ $businessProfile->reject_reason ?? 'Thông tin cung cấp chưa chính xác hoặc không đủ điều kiện.' }}
                                    </div>
                                </div>
                                <a href="{{ route('client.profile.business.upgrade') }}" class="btn-danger-ghost" style="text-decoration:none;display:inline-block;">Đăng ký lại</a>
                            </div>
                        @endif
                    @endif
                @else
                    <div class="content-panel">
                        <div class="section-title">
                            <span>Nâng cấp tài khoản doanh nghiệp</span>
                            <span class="dt-meta">Miễn phí</span>
                        </div>
                        <div class="biz-hero">
                            <div>
                                <h5 class="fw-bold mb-2" style="font-size:1.05rem;letter-spacing:-0.02em;">Đưa địa điểm lên bản đồ Ninh Bình Travel Hub</h5>
                                <p class="text-secondary mb-3" style="font-size:0.82rem;line-height:1.55;max-width:48ch;">
                                    Quảng bá nhà hàng, khách sạn, cửa hàng hoặc dịch vụ miễn phí. Tiếp cận người dùng đang tìm địa điểm tại Ninh Bình.
                                </p>
                                <a href="{{ route('client.profile.business.upgrade') }}" class="btn-solid" style="text-decoration:none;display:inline-block;padding:9px 16px;">Bắt đầu đăng ký</a>
                            </div>
                            <div class="biz-feature-grid">
                                <div class="biz-feature">
                                    <strong>Xuất hiện trên bản đồ</strong>
                                    <p>Hiển thị vị trí chính xác trên bản đồ vệ tinh.</p>
                                </div>
                                <div class="biz-feature">
                                    <strong>Trình bày hình ảnh</strong>
                                    <p>Đăng ảnh mặt tiền, phòng nghỉ hoặc thực đơn.</p>
                                </div>
                                <div class="biz-feature">
                                    <strong>Tương tác trực tiếp</strong>
                                    <p>Trả lời bình luận và nhận phản hồi từ khách.</p>
                                </div>
                                <div class="biz-feature">
                                    <strong>Trang quản trị</strong>
                                    <p>Quản lý nội dung và theo dõi tương tác.</p>
                                </div>
                            </div>
                        </div>

                        <p class="biz-pano-note">
                            <i class="fa-solid fa-panorama"></i>
                            Muốn địa điểm nổi bật hơn với không gian 360°?
                            <a href="{{ route('client.pano_service') }}" target="_blank" rel="noopener">Tìm hiểu dịch vụ chụp Tour 360°</a>
                        </p>
                    </div>
                @endif
            </div>

        </div>
        </div><!-- /.workspace-body -->
    </div><!-- /.dashboard-content -->
</div><!-- /.main-layout -->

<!-- Cancel Business Registration Modal -->
<div class="modal fade" id="cancelBusinessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Xác nhận hủy</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary small mb-0">Bạn có chắc chắn muốn hủy yêu cầu nâng cấp tài khoản doanh nghiệp? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-2 btn-sm px-3" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger rounded-2 btn-sm px-3" id="confirmCancelBizBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="cancelBizSpinner" role="status"></span>
                    Hủy yêu cầu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Avatar View, Edit & Frame Collection Modal -->
<div class="modal fade" id="avatarViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 540px;">
        <div class="modal-content rounded-3 border-0 shadow-sm overflow-hidden">
            <div class="modal-header border-bottom px-3 py-2.5 bg-white d-flex align-items-center justify-content-between">
                <ul class="nav nav-pills custom-modal-tabs gap-2 mb-0" id="avatarModalTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-avatar-photo" data-bs-toggle="pill" data-bs-target="#pane-avatar-photo" type="button" role="tab">
                            Ảnh đại diện
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-avatar-frames" data-bs-toggle="pill" data-bs-target="#pane-avatar-frames" type="button" role="tab">
                            Khung avatar
                        </button>
                    </li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="tab-content" id="avatarModalTabContent">
                    <!-- TAB 1: PHOTO -->
                    <div class="tab-pane fade show active text-center" id="pane-avatar-photo" role="tabpanel">
                        <div class="mb-4 d-flex justify-content-center position-relative">
                            <div class="avatar-frame-wrapper {{ $user->equippedFrame ? ($user->equippedFrame->image_url ? 'has-png-frame' : $user->equippedFrame->css_style) : '' }}" style="width: 200px; height: 200px;">
                                <img src="{{ $user->avatar_formatted_url }}" 
                                     alt="Avatar" 
                                     id="avatarModalLargePreview" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=0072FF&color=fff';"
                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @if($user->equippedFrame && $user->equippedFrame->image_url)
                                    <img src="{{ asset($user->equippedFrame->image_url) }}" class="avatar-frame-png-overlay">
                                @endif
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-primary btn-sm fw-medium px-3 py-1.5 rounded-2" id="avatarModalChangeBtn" style="font-size: 0.825rem;">
                                Thay ảnh mới
                            </button>
                            <button type="button" class="btn btn-light btn-sm fw-medium px-3 py-1.5 rounded-2 border" data-bs-dismiss="modal" style="font-size: 0.825rem;">
                                Đóng
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: FRAMES -->
                    <div class="tab-pane fade" id="pane-avatar-frames" role="tabpanel">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold mb-0">Bộ sưu tập Khung Avatar</h6>
                        </div>

                        <div class="row g-3 style-scroll" style="max-height: 380px; overflow-y: auto; padding-right: 4px;">
                            @foreach($allFrames as $frame)
                                @php
                                    $isUnlocked = in_array($frame->id, $unlockedFrameIds);
                                    $isEquipped = ($user->equipped_frame_id == $frame->id);
                                    
                                    if ($frame->type === 'rank' || $frame->required_points > 0) {
                                        $conditionText = "Tích lũy đạt " . number_format($frame->required_points) . " xu để tự động mở khóa khung này.";
                                    } else {
                                        $conditionText = "Hoàn thành các nhiệm vụ thành tựu hoặc đạt chuỗi điểm danh 7 ngày để mở khóa.";
                                    }
                                @endphp
                                <div class="col-6 col-sm-4">
                                    <div class="p-3 border rounded-3 text-center h-100 d-flex flex-column justify-content-between bg-white position-relative {{ $isEquipped ? 'border-primary shadow-sm' : '' }}">
                                        @if($isEquipped)
                                            <span class="position-absolute top-0 start-50 translate-middle badge bg-dark text-white px-2 py-1 rounded-2" style="font-size: 0.65rem; font-weight: 500;">
                                                Đang đeo
                                            </span>
                                        @endif

                                        <div>
                                            <div class="avatar-frame-wrapper {{ $frame->image_url ? 'has-png-frame' : $frame->css_style }} mx-auto my-2" style="width: 68px; height: 68px;">
                                                <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=0072FF&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                @if($frame->image_url)
                                                    <img src="{{ asset($frame->image_url) }}" class="avatar-frame-png-overlay">
                                                @endif
                                            </div>
                                            <h6 class="fw-bold text-dark mb-2 text-truncate" style="font-size: 0.85rem;" title="{{ $frame->name }}">{{ $frame->name }}</h6>
                                        </div>

                                        <div>
                                            @if($isEquipped)
                                                <button type="button" class="btn btn-secondary btn-sm w-100 fw-medium rounded-2 btn-unequip-frame" style="font-size: 0.775rem;">
                                                    Tháo khung
                                                </button>
                                            @elseif($isUnlocked)
                                                <button type="button" class="btn btn-primary btn-sm w-100 fw-medium rounded-2 btn-equip-frame" data-id="{{ $frame->id }}" style="font-size: 0.775rem;">
                                                    Trang bị
                                                </button>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-light text-muted btn-sm w-100 fw-medium rounded-2 border btn-lock-info" 
                                                        data-name="{{ $frame->name }}"
                                                        data-desc="{{ $frame->description }}"
                                                        data-style="{{ $frame->css_style }}"
                                                        data-image="{{ $frame->image_url ? asset($frame->image_url) : '' }}"
                                                        data-condition="{{ $conditionText }}"
                                                        style="font-size: 0.73rem;">
                                                    Chưa mở khóa
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Frame Lock Condition Custom Modal -->
<div class="modal fade" id="frameConditionModal" tabindex="-1" aria-hidden="true" style="z-index: 1060; backdrop-filter: blur(6px); background: rgba(0, 0, 0, 0.45);">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden text-center p-2">
            <div class="modal-body p-3">
                <div class="position-relative d-inline-block mx-auto mb-2">
                    <div id="modalLockFrameWrapper" class="avatar-frame-wrapper" style="width: 76px; height: 76px;">
                        <img src="{{ $user->avatar_formatted_url }}" 
                             id="modalLockAvatarPreview"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=0072FF&color=fff';"
                             style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    </div>
                    <span class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px; font-size: 11px; border: 2px solid white;">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                </div>

                <h6 class="fw-bold text-dark mb-2" id="modalLockFrameName" style="font-size: 0.95rem;">Tên Khung</h6>

                <div class="p-3 bg-light rounded-3 border text-center mb-3" style="background-color: #f8fafc !important;">
                    <div class="text-secondary small fw-medium" id="modalLockConditionText" style="line-height: 1.5; font-size: 0.82rem;">Tải điều kiện...</div>
                </div>

                <button type="button" class="btn btn-primary fw-bold w-100 rounded-pill py-2" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                    Đã hiểu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Confirm Delete Review Modal -->
<div class="modal fade modal-confirm-backdrop" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Top Red Accent Bar -->
            <div class="confirm-modal-top-bar"></div>

            <div class="modal-body text-center p-4">
                <!-- Icon Badge -->
                <div class="confirm-icon-badge">
                    <i class="fa-solid fa-trash-can"></i>
                </div>

                <h5 class="confirm-title-text mb-1">
                    Xác nhận xóa nhận xét
                </h5>
                <p class="confirm-desc-text mb-4">
                    Bạn có chắc chắn muốn xóa nhận xét này không?<br>Thao tác này không thể hoàn tác.
                </p>

                <!-- Contextual Preview Card -->
                <div class="confirm-preview-card" id="deleteReviewPreviewBox" style="display: none;">
                    <div class="confirm-preview-tag" id="deleteReviewLocationName">
                        <i class="fa-solid fa-location-dot"></i> <span>--</span>
                    </div>
                    <div class="confirm-preview-quote" id="deleteReviewSnippetText">
                        "--"
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-confirm-cancel-styled flex-fill" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark me-1"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-confirm-delete-styled flex-fill" id="confirmDeleteReviewBtn">
                        <i class="fa-solid fa-trash-can me-1"></i> Xóa nhận xét
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
                    .then(res => {
                        if (res.status === 419) {
                            showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                            setTimeout(() => window.location.reload(), 2000);
                            throw new Error('CSRF token mismatch (419)');
                        }
                        return res.json();
                    })
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

        // --- Avatar Frames Equip / Unequip / Lock Info Handlers ---
        document.querySelectorAll('.btn-equip-frame').forEach(btn => {
            btn.addEventListener('click', function() {
                const frameId = this.dataset.id;
                fetch("{{ route('client.avatar_frames.equip') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ frame_id: frameId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, true);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        showToast(data.message, false);
                    }
                });
            });
        });

        document.querySelectorAll('.btn-unequip-frame').forEach(btn => {
            btn.addEventListener('click', function() {
                fetch("{{ route('client.avatar_frames.equip') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ frame_id: null })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, true);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        showToast(data.message, false);
                    }
                });
            });
        });

        const frameConditionModalEl = document.getElementById('frameConditionModal');
        const frameConditionModal = frameConditionModalEl ? new bootstrap.Modal(frameConditionModalEl) : null;
        const modalLockFrameName = document.getElementById('modalLockFrameName');
        const modalLockFrameDesc = document.getElementById('modalLockFrameDesc');
        const modalLockConditionText = document.getElementById('modalLockConditionText');
        const modalLockFrameWrapper = document.getElementById('modalLockFrameWrapper');

        document.querySelectorAll('.btn-lock-info').forEach(btn => {
            btn.addEventListener('click', function() {
                const name = this.dataset.name;
                const desc = this.dataset.desc;
                const condition = this.dataset.condition;
                const style = this.dataset.style;
                const image = this.dataset.image;

                if (modalLockFrameName) modalLockFrameName.innerText = name;
                if (modalLockFrameDesc) modalLockFrameDesc.innerText = desc || 'Khung Avatar độc quyền';
                if (modalLockConditionText) modalLockConditionText.innerText = condition;
                if (modalLockFrameWrapper) {
                    let existingPng = modalLockFrameWrapper.querySelector('.avatar-frame-png-overlay');
                    if (existingPng) existingPng.remove();

                    if (image) {
                        modalLockFrameWrapper.className = `avatar-frame-wrapper has-png-frame`;
                        const imgEl = document.createElement('img');
                        imgEl.src = image;
                        imgEl.className = 'avatar-frame-png-overlay';
                        modalLockFrameWrapper.appendChild(imgEl);
                    } else {
                        modalLockFrameWrapper.className = `avatar-frame-wrapper ${style || ''}`;
                    }
                }

                if (frameConditionModal) {
                    frameConditionModal.show();
                }
            });
        });

        // --- Sidebar Display Name Inline Edit ---
        const displayNameText = document.getElementById('sidebarDisplayNameText');
        const displayNameVal = document.getElementById('sidebarDisplayNameVal');
        const displayNameInput = document.getElementById('sidebarDisplayNameInput');
        const formDisplayNameVal = document.getElementById('profileFormDisplayNameVal');
        const profileTopDisplayName = document.getElementById('profileTopDisplayName');

        function startDisplayNameEdit() {
            if (!displayNameText || !displayNameInput) return;
            displayNameText.classList.add('d-none');
            displayNameInput.classList.remove('d-none');
            displayNameInput.focus();
            displayNameInput.select();
        }

        if (displayNameText && displayNameInput && displayNameVal) {
            displayNameText.addEventListener('click', startDisplayNameEdit);

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
                .then(res => {
                    if (res.status === 419) {
                        showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                        setTimeout(() => window.location.reload(), 2000);
                        throw new Error('CSRF token mismatch (419)');
                    }
                    return res.json();
                })
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
                        if (profileTopDisplayName) {
                            profileTopDisplayName.textContent = data.display_name;
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
                .then(res => {
                    if (res.status === 419) {
                        showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                        setTimeout(() => window.location.reload(), 2000);
                        throw new Error('CSRF token mismatch (419)');
                    }
                    return res.json();
                })
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
                                        <div id="noFavoritesMsg" class="empty-state" style="border:0;border-radius:0;">
                                            <p>Bạn chưa lưu địa điểm nào.</p>
                                            <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;display:inline-block;">Tìm địa điểm trên bản đồ</a>
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

        // --- Saved Itineraries: delete ---
        const itinerariesGrid = document.getElementById('itinerariesGrid');
        const itinerariesCountBadge = document.getElementById('itinerariesCountBadge');

        if (itinerariesGrid) {
            itinerariesGrid.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.itinerary-delete-btn');

                if (deleteBtn) {
                    const id = deleteBtn.getAttribute('data-id');
                    
                    const doDelete = () => {
                        deleteBtn.disabled = true;
                        fetch(`/trip-planner/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                deleteBtn.disabled = false;
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        iconColor: '#dc2626',
                                        title: 'Thất bại',
                                        text: data.message || 'Xóa lịch trình thất bại.',
                                        confirmButtonText: 'Đóng',
                                        customClass: {
                                            popup: 'custom-swal-popup',
                                            title: 'custom-swal-title',
                                            htmlContainer: 'custom-swal-text',
                                            confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                                        },
                                        buttonsStyling: false
                                    });
                                } else {
                                    showToast(data.message || 'Xóa thất bại.', false);
                                }
                                return;
                            }
                            const card = document.getElementById(`itinerary-card-${id}`);
                            if (card) card.remove();
                            let count = parseInt(itinerariesCountBadge?.innerText || '0') || 0;
                            count = Math.max(0, count - 1);
                            if (itinerariesCountBadge) itinerariesCountBadge.innerText = count;
                            if (count === 0 && itinerariesGrid) {
                                itinerariesGrid.innerHTML = `
                                    <div id="noItinerariesMsg" class="empty-state" style="border:0;border-radius:0;">
                                        <p>Bạn chưa lưu lịch trình nào.</p>
                                        <a href="{{ route('home') }}" class="btn-solid" style="text-decoration:none;display:inline-block;" onclick="if(window.openTripPlanner){event.preventDefault();window.openTripPlanner(true);}">Lên lịch trình AI</a>
                                    </div>`;
                            }
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    iconColor: '#166534',
                                    title: 'Đã xóa',
                                    text: 'Đã xóa lịch trình khỏi danh sách.',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    customClass: {
                                        popup: 'custom-swal-toast'
                                    }
                                });
                            } else {
                                showToast('Đã xóa lịch trình.', true);
                            }
                        })
                        .catch(() => {
                            deleteBtn.disabled = false;
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    iconColor: '#dc2626',
                                    title: 'Lỗi',
                                    text: 'Không thể kết nối máy chủ.',
                                    confirmButtonText: 'Đóng',
                                    customClass: {
                                        popup: 'custom-swal-popup',
                                        title: 'custom-swal-title',
                                        htmlContainer: 'custom-swal-text',
                                        confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                                    },
                                    buttonsStyling: false
                                });
                            } else {
                                showToast('Có lỗi xảy ra.', false);
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Xóa lịch trình',
                            html: 'Bạn có chắc chắn muốn xóa lịch trình này không? Thao tác này không thể hoàn tác.',
                            icon: 'warning',
                            iconColor: '#dc2626',
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý xóa',
                            cancelButtonText: 'Hủy bỏ',
                            reverseButtons: true,
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger',
                                cancelButton: 'custom-swal-cancel-btn'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                doDelete();
                            }
                        });
                    } else {
                        if (confirm('Bạn chắc chắn muốn xóa lịch trình này?')) {
                            doDelete();
                        }
                    }
                }
            });
        }

        // Deep-link: /profile#itineraries
        if (window.location.hash === '#itineraries') {
            const tabBtn = document.getElementById('tab-itineraries-btn');
            if (tabBtn) tabBtn.click();
        }

        // Cập nhật tiêu đề workspace theo tab đang mở
        const workspaceTabTitle = document.getElementById('workspaceTabTitle');
        document.querySelectorAll('#settings-tabs [data-bs-toggle="pill"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', () => {
                if (!workspaceTabTitle) return;
                const label = btn.querySelector('.nav-link-left span:last-child') || btn.querySelector('span');
                workspaceTabTitle.textContent = (label?.textContent || '').trim() || 'Tài khoản';
            });
        });

        // --- Delete Comment via AJAX with Custom Animated Modal ---
        const commentsTable = document.getElementById('commentsTable');
        const commentsCountBadge = document.getElementById('commentsCountBadge');
        const deleteReviewModalEl = document.getElementById('deleteReviewModal');
        const deleteReviewModal = deleteReviewModalEl ? new bootstrap.Modal(deleteReviewModalEl) : null;
        const confirmDeleteReviewBtn = document.getElementById('confirmDeleteReviewBtn');

        const previewBox = document.getElementById('deleteReviewPreviewBox');
        const locNameEl = document.getElementById('deleteReviewLocationName');
        const snippetEl = document.getElementById('deleteReviewSnippetText');

        let pendingDeleteCommentId = null;
        let pendingDeleteRow = null;

        if (commentsTable) {
            commentsTable.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.delete-comment-btn');
                if (!deleteBtn) return;

                pendingDeleteCommentId = deleteBtn.getAttribute('data-comment-id');
                pendingDeleteRow = document.getElementById(`comment-row-${pendingDeleteCommentId}`);

                // Populate preview snippet if row exists
                if (pendingDeleteRow) {
                    const locName = pendingDeleteRow.children[0]?.innerText?.trim() || '';
                    const snippet = pendingDeleteRow.children[1]?.innerText?.trim() || '';

                    if (previewBox && locName) {
                        if (locNameEl) {
                            const spanEl = locNameEl.querySelector('span');
                            if (spanEl) spanEl.innerText = locName;
                        }
                        if (snippetEl) snippetEl.innerText = `"${snippet}"`;
                        previewBox.style.display = 'block';
                    }
                } else if (previewBox) {
                    previewBox.style.display = 'none';
                }

                if (deleteReviewModal) {
                    deleteReviewModal.show();
                } else {
                    executeDeleteComment(pendingDeleteCommentId, pendingDeleteRow);
                }
            });
        }

        if (confirmDeleteReviewBtn) {
            confirmDeleteReviewBtn.addEventListener('click', function() {
                if (!pendingDeleteCommentId) return;

                const commentId = pendingDeleteCommentId;
                const row = pendingDeleteRow;

                if (deleteReviewModal) {
                    deleteReviewModal.hide();
                }

                executeDeleteComment(commentId, row);
            });
        }

        function executeDeleteComment(commentId, row) {
            fetch(`/profile/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => {
                if (res.status === 419) {
                    showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                    setTimeout(() => window.location.reload(), 2000);
                    throw new Error('CSRF token mismatch (419)');
                }
                return res.json();
            })
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
        }

        // Clear leftover profile dark-mode preference (feature removed)
        try {
            localStorage.removeItem('profile-dark-mode');
            document.getElementById('profile-app-container')?.classList.remove('dark-mode-active');
            document.body.style.backgroundColor = '';
        } catch (e) {}

        // Cancel pending request logic
        const cancelRequestBtn = document.getElementById('cancelBusinessRequestBtn');
        const cancelBizModalEl = document.getElementById('cancelBusinessModal');
        const confirmCancelBizBtn = document.getElementById('confirmCancelBizBtn');
        const cancelBizSpinner = document.getElementById('cancelBizSpinner');

        if (cancelRequestBtn && cancelBizModalEl) {
            const cancelBizModal = new bootstrap.Modal(cancelBizModalEl);

            cancelRequestBtn.addEventListener('click', function() {
                cancelBizModal.show();
            });

            if (confirmCancelBizBtn) {
                confirmCancelBizBtn.addEventListener('click', function() {
                    confirmCancelBizBtn.disabled = true;
                    if (cancelBizSpinner) cancelBizSpinner.classList.remove('d-none');

                    fetch("{{ route('client.profile.business.cancel') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => {
                        if (res.status === 419) {
                            showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                            setTimeout(() => window.location.reload(), 2000);
                            throw new Error('CSRF token mismatch');
                        }
                        return res.json();
                    })
                    .then(data => {
                        confirmCancelBizBtn.disabled = false;
                        if (cancelBizSpinner) cancelBizSpinner.classList.add('d-none');
                        cancelBizModal.hide();

                        if (data.success) {
                            try {
                                Object.keys(localStorage).forEach(function (k) {
                                    if (k === 'biz_wizard_state' || k.indexOf('biz_wizard_state_') === 0) {
                                        localStorage.removeItem(k);
                                    }
                                });
                            } catch (e) {}
                            try { indexedDB.deleteDatabase('biz_wizard_db'); } catch (e) {}
                            showToast(data.message, true);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Lỗi hủy yêu cầu.', false);
                        }
                    })
                    .catch(err => {
                        confirmCancelBizBtn.disabled = false;
                        if (cancelBizSpinner) cancelBizSpinner.classList.add('d-none');
                        showToast('Đã xảy ra lỗi khi hủy yêu cầu.', false);
                        console.error(err);
                    });
                });
            }
        }
    });
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($businessProfile) && $businessProfile->lat && $businessProfile->lng)
            let pendingBizMap = null;

            function initPendingBizMap() {
                const mapEl = document.getElementById('pendingBusinessMap');
                if (!mapEl) return;

                const lat = parseFloat("{{ $businessProfile->lat }}");
                const lng = parseFloat("{{ $businessProfile->lng }}");

                if (isNaN(lat) || isNaN(lng)) return;

                if (!pendingBizMap) {
                    pendingBizMap = L.map('pendingBusinessMap', {
                        zoomControl: true,
                        attributionControl: false
                    }).setView([lat, lng], 15);

                    L.tileLayer(@json(config('services.carto.tile_url')), {
                        subdomains: 'abcd',
                        maxZoom: 19
                    }).addTo(pendingBizMap);

                    // Add GeoJSON boundary if available
                    fetch('{{ asset('geo/ha-nam-old.geojson') }}')
                        .then(res => res.json())
                        .then(data => {
                            L.geoJSON(data, {
                                style: {
                                    color: '#7ba7d4',
                                    weight: 2,
                                    opacity: 0.55,
                                    fillColor: '#f8fafc',
                                    fillOpacity: 0.04
                                }
                            }).addTo(pendingBizMap);
                        })
                        .catch(err => console.error(err));

                    // Add marker with custom popup
                    const marker = L.marker([lat, lng]).addTo(pendingBizMap);
                    marker.bindPopup(`
                        <div style="font-family: 'Be Vietnam Pro', sans-serif; font-size: 0.85rem; padding: 2px;">
                            <strong style="color: #1e3a5f;">{{ $businessProfile->business_name }}</strong><br>
                            <span style="color: #64748b; font-size: 0.775rem;">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}</span>
                        </div>
                    `).openPopup();
                }

                setTimeout(() => {
                    if (pendingBizMap) pendingBizMap.invalidateSize();
                }, 200);
            }

            // Handle tab switching
            const bizTabBtn = document.getElementById('tab-business-btn');
            if (bizTabBtn) {
                bizTabBtn.addEventListener('shown.bs.tab', function () {
                    initPendingBizMap();
                });
            }

            // If business tab is active on load
            const bizTabPane = document.getElementById('tab-business');
            if (bizTabPane && (bizTabPane.classList.contains('active') || bizTabPane.classList.contains('show') || window.location.hash === '#business')) {
                setTimeout(initPendingBizMap, 300);
            }
        @endif

        // Points history: filter chips + load more
        (function initPointsHistoryUi() {
            const list = document.getElementById('pointsHistoryList');
            const bar = document.getElementById('pointsFilterBar');
            const meta = document.getElementById('pointsVisibleMeta');
            const moreWrap = document.getElementById('pointsLoadMoreWrap');
            const moreBtn = document.getElementById('pointsLoadMoreBtn');
            if (!list || !bar) return;

            const pageSize = 12;
            let filter = 'all';
            let visibleLimit = pageSize;

            function applyPointsView() {
                const rows = Array.from(list.querySelectorAll('.points-history-row'));
                const matched = rows.filter(row => filter === 'all' || row.dataset.filter === filter);

                rows.forEach(row => {
                    const match = filter === 'all' || row.dataset.filter === filter;
                    row.classList.toggle('is-filtered-out', !match);
                    row.classList.remove('is-paged-out');
                });

                matched.forEach((row, idx) => {
                    row.classList.toggle('is-paged-out', idx >= visibleLimit);
                });

                if (meta) {
                    meta.textContent = matched.length
                        ? Math.min(visibleLimit, matched.length) + ' / ' + matched.length + ' mục'
                        : '0 mục';
                }

                if (moreWrap) {
                    moreWrap.hidden = matched.length <= visibleLimit;
                }
            }

            bar.querySelectorAll('[data-points-filter]').forEach(chip => {
                chip.addEventListener('click', function () {
                    bar.querySelectorAll('[data-points-filter]').forEach(c => c.classList.remove('on'));
                    this.classList.add('on');
                    filter = this.getAttribute('data-points-filter') || 'all';
                    visibleLimit = pageSize;
                    applyPointsView();
                });
            });

            if (moreBtn) {
                moreBtn.addEventListener('click', function () {
                    visibleLimit += pageSize;
                    applyPointsView();
                });
            }

            applyPointsView();
        })();
    });
</script>

</body>
</html>
