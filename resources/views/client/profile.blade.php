<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt Tài Khoản - Hà Nam POI</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome & Avatar Frames CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    
    <style>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-sub: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        body { 
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            font-size: 0.875rem;
            line-height: 1.55;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        h1, .h1 { font-size: 1.3rem !important; }
        h2, .h2 { font-size: 1.15rem !important; }
        h3, .h3 { font-size: 1.05rem !important; }
        h4, .h4 { font-size: 0.95rem !important; }
        h5, .h5 { font-size: 0.9rem !important; }
        h6, .h6 { font-size: 0.85rem !important; }

        /* Top Header Navigation */
        .top-navbar {
            height: 54px;
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
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
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
        }
        .btn-back:hover {
            color: var(--text-main);
        }
        .back-chevron {
            font-size: 1.4rem;
            margin-right: 4px;
            line-height: 1;
            font-weight: 400;
            position: relative;
            top: -2px;
        }

        /* Main Screen Layout */
        .main-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 54px);
            overflow: hidden;
        }

        /* Sidebar Dashboard Navigation */
        .dashboard-sidebar {
            width: 260px;
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 20px;
            overflow-y: auto;
        }
        .sidebar-user-section {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 16px;
        }
        .avatar-container {
            width: 80px;
            height: 80px;
            position: relative;
            margin: 0 auto 12px auto;
            cursor: pointer;
        }
        .avatar-edit-badge {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 24px;
            height: 24px;
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
            font-size: 0.75rem;
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
            font-size: 0.725rem;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 4px;
            background-color: #f1f5f9;
            color: var(--text-sub);
            margin-top: 4px;
        }

        /* Sidebar Tabs Button */
        .sidebar-menu-tabs .nav-link {
            width: 100%;
            text-align: left;
            padding: 9px 12px;
            color: var(--text-sub);
            font-weight: 500;
            border-radius: 8px;
            border: none;
            background: transparent;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-menu-tabs .nav-link:hover {
            background-color: #f8fafc;
            color: var(--text-main);
        }
        .sidebar-menu-tabs .nav-link.active {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }
        .menu-count-badge {
            background-color: #e2e8f0;
            color: var(--text-sub);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
        }

        /* Content Workspace Area */
        .dashboard-content {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            padding: 24px;
        }
        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            max-width: 960px;
            margin: 0 auto;
        }
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 20px;
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
            padding: 7px 18px;
            font-weight: 500;
            font-size: 0.85rem;
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
            <div class="avatar-container {{ $user->equippedFrame ? ($user->equippedFrame->image_url ? 'avatar-frame-wrapper has-png-frame' : 'avatar-frame-wrapper ' . $user->equippedFrame->css_style) : '' }}" id="sidebarAvatarContainer" title="Nhấp để thay ảnh hoặc đổi khung avatar">
                <img src="{{ $user->avatar_formatted_url }}" 
                     alt="Avatar" 
                     class="user-avatar-img"
                     id="profileAvatarPreview"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=0072FF&color=fff';">
                @if($user->equippedFrame && $user->equippedFrame->image_url)
                    <img src="{{ asset($user->equippedFrame->image_url) }}" class="avatar-frame-png-overlay">
                @endif
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
            <div class="mt-2">
                <span style="font-size: 0.775rem; padding: 4px 12px; border-radius: 16px; font-weight: 500; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; display: inline-flex; align-items: center;">
                    {{ number_format($user->points) }} điểm tích lũy
                </span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav flex-column sidebar-menu-tabs" id="settings-tabs" role="tablist">
            <button class="nav-link active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab" aria-selected="true">
                <span>Thông tin cá nhân</span>
            </button>
            <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab" aria-selected="false">
                <span>Bảo mật & Mật khẩu</span>
            </button>
            <button class="nav-link" id="tab-points-btn" data-bs-toggle="pill" data-bs-target="#tab-points" type="button" role="tab" aria-selected="false">
                <span>Lịch sử tích điểm</span>
            </button>
            <button class="nav-link" id="tab-favorites-btn" data-bs-toggle="pill" data-bs-target="#tab-favorites" type="button" role="tab" aria-selected="false">
                <span>Địa điểm đã lưu</span>
                <span class="menu-count-badge" id="favoritesCountBadge">{{ $favorites->count() }}</span>
            </button>
            <button class="nav-link" id="tab-comments-btn" data-bs-toggle="pill" data-bs-target="#tab-comments" type="button" role="tab" aria-selected="false">
                <span>Nhận xét của tôi</span>
                <span class="menu-count-badge" id="commentsCountBadge">{{ $comments->count() }}</span>
            </button>
            <button class="nav-link" id="tab-business-btn" data-bs-toggle="pill" data-bs-target="#tab-business" type="button" role="tab" aria-selected="false">
                <span>Tài khoản doanh nghiệp</span>
                @if(isset($businessProfile))
                    @if($businessProfile->status === 'pending')
                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Chờ duyệt</span>
                    @elseif($businessProfile->status === 'approved')
                        <span class="badge bg-success" style="font-size: 0.65rem;">Doanh nghiệp</span>
                    @elseif($businessProfile->status === 'rejected')
                        <span class="badge bg-danger" style="font-size: 0.65rem;">Bị từ chối</span>
                    @endif
                @endif
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
                            <div class="info-label">Điểm tích lũy</div>
                            <div class="info-value text-primary fw-semibold">{{ $user->points }} điểm</div>
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

            <!-- Tab: Lịch sử tích điểm -->
            <div class="tab-pane fade" id="tab-points" role="tabpanel">
                <div class="content-panel">
                    <div class="section-title">Lịch sử tích điểm</div>
                    
                    <div class="alert alert-info rounded-3 border-0 p-3 mb-4 small" style="background-color: rgba(0, 114, 255, 0.05); color: var(--primary); border: 1px solid rgba(0, 114, 255, 0.1);">
                        Bạn có thể tích lũy điểm thưởng bằng cách đăng nhập mỗi ngày (+10 điểm), bình luận về địa điểm (+5 điểm), hoặc lưu địa điểm yêu thích (+2 điểm).
                    </div>

                    <div class="table-responsive">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Hành động</th>
                                    <th>Số điểm</th>
                                    <th>Nội dung chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->pointTransactions as $tx)
                                <tr>
                                    <td>{{ $tx->created_at->format('H:i d/m/Y') }}</td>
                                    <td>
                                        @if($tx->action === 'daily_login')
                                            <span class="badge bg-success bg-opacity-10 text-success border-0 px-2 py-1" style="font-size: 0.75rem;">Điểm danh</span>
                                        @elseif($tx->action === 'comment')
                                            <span class="badge bg-primary bg-opacity-10 text-primary border-0 px-2 py-1" style="font-size: 0.75rem;">Bình luận</span>
                                        @elseif($tx->action === 'favorite')
                                            <span class="badge bg-info bg-opacity-10 text-info border-0 px-2 py-1" style="font-size: 0.75rem;">Yêu thích</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 px-2 py-1" style="font-size: 0.75rem;">Khác</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->amount >= 0 ? '+' : '' }}{{ $tx->amount }}
                                    </td>
                                    <td>{{ $tx->description }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Chưa có lịch sử giao dịch điểm.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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

            <!-- Tab: Business Account -->
            <div class="tab-pane fade" id="tab-business" role="tabpanel">
                @if(isset($businessProfile))
                    @if($businessProfile->status === 'pending')
                        <div class="content-panel">
                            <div class="section-title">Yêu cầu nâng cấp tài khoản doanh nghiệp</div>
                            <div class="alert alert-warning rounded-3 border-0 p-4 mb-4 text-center">
                                <div class="fs-4 mb-2">Yêu cầu đang được chờ phê duyệt</div>
                                <p class="text-secondary mb-0 small">Chúng tôi đang xác minh thông tin doanh nghiệp của bạn. Thời gian xử lý thường từ 24h - 48h làm việc.</p>
                            </div>
                            <!-- Display filled details -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="fw-semibold text-secondary small">Tên doanh nghiệp</div>
                                    <div class="fw-bold fs-5 mt-1 text-primary">{{ $businessProfile->business_name }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="fw-semibold text-secondary small">Danh mục</div>
                                    <div class="fw-bold fs-5 mt-1">{{ $businessProfile->category ? $businessProfile->category->name : 'N/A' }}</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="fw-semibold text-secondary small">Địa chỉ</div>
                                    <div class="mt-1">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="fw-semibold text-secondary small font-monospace">Tọa độ: [{{ $businessProfile->lat }}, {{ $businessProfile->lng }}]</div>
                                </div>
                                <div class="col-12 mt-4 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm px-4 rounded-3" id="cancelBusinessRequestBtn">
                                        Hủy yêu cầu đăng ký
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($businessProfile->status === 'approved')
                        <div class="content-panel">
                            <div class="section-title">Quản lý tài khoản doanh nghiệp</div>
                            <div class="alert alert-success rounded-3 border-0 p-4 mb-4 text-center">
                                <div class="fs-4 mb-2">Đã nâng cấp lên Tài khoản doanh nghiệp!</div>
                                <p class="text-secondary mb-0 small">Chúc mừng! Bạn đã sở hữu tài khoản doanh nghiệp. Địa điểm của bạn đã được đưa lên hệ thống.</p>
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
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 text-center mb-3">
                                        <div class="fw-bold text-primary mb-1" style="font-size:1.5rem;">●</div>
                                        <div class="fw-semibold small mb-1">Xem trang địa điểm</div>
                                        <p class="text-secondary small mb-2">Xem hiển thị thực tế trên bản đồ Hà Nam POI</p>
                                        @php
                                            $loc = \App\Models\Location::where('created_by', $user->id)->first();
                                        @endphp
                                        @if($loc)
                                            <a href="{{ route('location.detail', $loc->slug ?? $loc->id) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 rounded-2">Xem chi tiết</a>
                                        @else
                                            <span class="text-muted small">Đang đồng bộ dữ liệu địa điểm...</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 text-center mb-3">
                                        <div class="fw-bold text-primary mb-1" style="font-size:1.5rem;">●</div>
                                        <div class="fw-semibold small mb-1">Trang quản trị</div>
                                        <p class="text-secondary small mb-2">Truy cập Dashboard dành cho chủ doanh nghiệp</p>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm px-3 rounded-2">Vào trang quản trị</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($businessProfile->status === 'rejected')
                        <div class="content-panel">
                            <div class="section-title">Đăng ký tài khoản doanh nghiệp</div>
                            <div class="alert alert-danger rounded-3 border-0 p-4 mb-4 text-center">
                                <div class="fs-5 mb-2 fw-semibold">Yêu cầu của bạn đã bị từ chối</div>
                                <p class="text-secondary mb-3 small"><strong>Lý do từ chối:</strong> {{ $businessProfile->reject_reason ?? 'Thông tin cung cấp chưa chính xác hoặc không đủ điều kiện.' }}</p>
                                <a href="{{ route('client.profile.business.upgrade') }}" class="btn btn-danger btn-sm rounded-3 px-4 py-2 fw-semibold">Đăng ký lại</a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="content-panel">
                        <div class="section-title">Nâng cấp tài khoản doanh nghiệp</div>
                        <div class="text-center py-4">

                            <h5 class="fw-bold mb-3">Đưa địa điểm kinh doanh của bạn lên bản đồ Hà Nam POI</h5>
                            <p class="text-secondary mx-auto mb-4" style="max-width: 600px;">
                                Quảng bá nhà hàng, khách sạn, cửa hàng hoặc dịch vụ của bạn hoàn toàn miễn phí. Tiếp cận hàng ngàn người dùng tìm kiếm địa điểm du lịch, ăn uống, và dịch vụ tại Hà Nam mỗi ngày.
                            </p>
                            
                            <div class="row g-3 mx-auto text-start mb-4" style="max-width: 600px;">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="text-primary fw-bold" style="font-size: 8px; margin-top: 6px;">●</span>
                                        <div>
                                            <div class="fw-semibold small">Xuất hiện trên Bản đồ</div>
                                            <p class="text-secondary small mb-0">Hiển thị vị trí chính xác trên bản đồ vệ tinh Hà Nam POI.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="text-primary fw-bold" style="font-size: 8px; margin-top: 6px;">●</span>
                                        <div>
                                            <div class="fw-semibold small">Trình bày hình ảnh</div>
                                            <p class="text-secondary small mb-0">Đăng tải ảnh mặt tiền, phòng nghỉ hoặc thực đơn của cửa hàng.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="text-primary fw-bold" style="font-size: 8px; margin-top: 6px;">●</span>
                                        <div>
                                            <div class="fw-semibold small">Tương tác trực tiếp</div>
                                            <p class="text-secondary small mb-0">Trả lời bình luận, nhận phản hồi chất lượng từ người dùng.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="text-primary fw-bold" style="font-size: 8px; margin-top: 6px;">●</span>
                                        <div>
                                            <div class="fw-semibold small">Trang quản trị (Admin)</div>
                                            <p class="text-secondary small mb-0">Quản lý nội dung, theo dõi thống kê số liệu tương tác địa điểm.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('client.profile.business.upgrade') }}" class="btn btn-primary px-5 py-2.5 rounded-3 fw-bold">
                                Bắt đầu đăng ký ngay
                            </a>
                        </div>
                    </div>
                @endif
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
                .then(res => {
                    if (res.status === 419) {
                        showToast('Phiên làm việc đã hết hạn. Đang tự động tải lại trang...', false);
                        setTimeout(() => window.location.reload(), 2000);
                        throw new Error('CSRF token mismatch (419)');
                    }
                    return res.json();
                })
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
                            localStorage.removeItem('biz_wizard_state');
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

</body>
</html>
