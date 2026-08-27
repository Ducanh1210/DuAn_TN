<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản Doanh Nghiệp - Ninh Bình Travel Hub</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    
    <style>
        :root {
            --primary: #1e3a5f;
            --primary-hover: #2b4c7e;
            --bg-body: #f1f5f9;
            --text-main: #1e3a5f;
            --text-sub: #52525b;
            --border-color: #cbdbe8;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        html {
            overflow-x: hidden;
        }

        h1, .h1 { font-size: 1.3rem !important; font-weight: 600 !important; color: #1e3a5f !important; }
        h2, .h2 { font-size: 1.15rem !important; font-weight: 600 !important; color: #1e3a5f !important; }
        h3, .h3 { font-size: 1.05rem !important; font-weight: 600 !important; color: #1e3a5f !important; }
        h4, .h4 { font-size: 0.95rem !important; font-weight: 600 !important; color: #1e3a5f !important; }
        h5, .h5 { font-size: 0.9rem !important; font-weight: 600 !important; color: #1e3a5f !important; }
        h6, .h6 { font-size: 0.85rem !important; font-weight: 600 !important; color: #1e3a5f !important; }

        /* Button design system override */
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 8px 20px;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
            color: #ffffff !important;
        }

        .btn-success {
            background-color: #2b4c7e !important;
            border-color: #2b4c7e !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }
        .btn-success:hover, .btn-success:focus {
            background-color: #1e3a5f !important;
            border-color: #1e3a5f !important;
            color: #ffffff !important;
        }

        .btn-outline-primary {
            color: var(--primary) !important;
            border-color: var(--border-color) !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #f1f5f9 !important;
            color: var(--primary) !important;
            border-color: var(--primary) !important;
        }

        .btn-light {
            background-color: #f1f5f9 !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-sub) !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }
        .btn-light:hover {
            background-color: #e2e8f0 !important;
            color: var(--text-main) !important;
        }

        /* Top Navigation Bar */
        .top-navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .btn-back {
            color: var(--text-sub);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: color 0.15s ease;
        }
        .btn-back:hover {
            color: var(--primary);
        }

        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 24px;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-main);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 8px;
            display: inline-block;
        }

        .form-label-clean {
            font-weight: 500;
            color: #3b5980;
            margin-bottom: 6px;
            font-size: 0.875rem;
            display: block;
        }
        
        .form-control-clean {
            width: 100%;
            padding: 9px 14px;
            border: 1px solid #cbdbe8;
            border-radius: 6px;
            background-color: var(--card-bg);
            color: var(--text-main);
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.15s ease;
        }
        .form-control-clean:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        }
        
        .is-invalid-clean {
            border-color: #ef4444 !important;
            box-shadow: none !important;
            background-color: #ffffff !important;
        }

        /* Business Account upgrade styles */
        .biz-type-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
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
            background-color: #f1f5f9;
        }
        .biz-type-card.selected {
            border-color: var(--primary);
            background-color: #f1f5f9;
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
            top: 100px;
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

        /* Stepper progress */
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 3;
            color: #64748b;
            transition: all 0.2s ease;
        }
        .step-progress-node.active {
            border-color: var(--primary);
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }
        .step-progress-node.completed {
            border-color: var(--primary);
            background-color: #ffffff;
            color: var(--primary);
            font-weight: 600;
        }

        /* Clean Custom Select Styles */
        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 9px 14px;
            font-size: 0.875rem;
            font-weight: 400;
            color: #0f172a;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .custom-select-trigger:hover {
            border-color: var(--primary);
        }
        .custom-select-trigger.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: #ffffff;
        }
        .custom-select-trigger .trigger-arrow {
            transition: transform 0.2s ease;
            font-size: 0.8rem;
            color: #64748b;
        }
        .custom-select-trigger.active .trigger-arrow {
            transform: rotate(180deg);
            color: var(--primary);
        }

        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000;
            padding: 8px;
            scrollbar-width: thin;
            animation: dropdownFadeIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-group-header {
            padding: 12px 16px;
            font-size: 0.92rem;
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            border-radius: 10px;
            margin-bottom: 4px;
        }
        .dropdown-group-header:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .dropdown-group-header.active {
            background-color: #f8fafc;
            color: var(--primary);
        }
        .dropdown-group-header .arrow-icon {
            font-size: 0.8rem;
            color: #94a3b8;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dropdown-group-header.active .arrow-icon {
            transform: rotate(90deg);
            color: var(--primary);
        }

        .dropdown-options-group {
            padding: 4px 0 8px 12px;
            margin-bottom: 6px;
            border-left: 2px solid #e2e8f0;
            margin-left: 26px;
        }
        .dropdown-option-item {
            padding: 10px 16px;
            font-size: 0.9rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px;
            margin-bottom: 2px;
        }
        .dropdown-option-item:hover {
            background-color: rgba(37, 99, 235, 0.05);
            color: var(--primary);
            padding-left: 20px;
        }
        .dropdown-option-item.selected {
            background-color: rgba(37, 99, 235, 0.08);
            color: var(--primary);
            font-weight: 600;
        }
        .dropdown-option-item::after {
            content: "→";
            opacity: 0;
            transition: all 0.2s ease;
            transform: translateX(-4px);
        }
        .dropdown-option-item:hover::after {
            opacity: 1;
            transform: translateX(0);
        }
        .dropdown-option-item.selected::after {
            content: "✓";
            opacity: 1;
            transform: translateX(0);
        }

        #businessMap {
            height: 380px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            margin-bottom: 15px;
            z-index: 1;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .biz-locate-control {
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            font-size: 15px;
            color: #334155;
            background: #fff;
            cursor: pointer;
        }
        .biz-locate-control:hover {
            background: #f8fafc;
            color: #1e3a5f;
        }
        .biz-locate-control.is-loading {
            opacity: 0.85;
            pointer-events: none;
        }
        .toast-custom.toast-locating {
            border-left-color: #3b82f6 !important;
        }

        .wizard-row {
            display: block;
            width: 100%;
        }
        .wizard-form-col {
            width: 100%;
        }
        
        /* Toast notification styling - Soft & Minimalist */
        .toast-container-custom {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
        }
        .toast-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #3b82f6;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            padding: 9px 15px;
            border-radius: 6px;
            margin-top: 8px;
            font-size: 0.8rem;
            color: #475569;
            font-weight: 400;
            display: flex;
            align-items: center;
            line-height: 1.4;
            animation: slideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideIn {
            from { transform: translateY(8px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ============ Registration redesign (Google-Ads style wizard) ============ */
        .reg-wrap {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 30px 36px 40px;
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 32px;
            align-items: start;
            flex: 1 0 auto;
            box-sizing: border-box;
        }

        /* Left step navigation (grouped phases + sub-steps) */
        .reg-aside { position: sticky; top: 84px; }

        /* Main content centered within the remaining space */
        .reg-main { display: flex; justify-content: center; }
        .reg-main > .content-panel { width: 100%; }
        .reg-nav { display: flex; flex-direction: column; }
        .reg-nav__group { display: flex; flex-direction: column; }
        .reg-nav__phase { display: flex; align-items: center; gap: 12px; padding: 9px 0; }
        .reg-nav__circle { width: 16px; height: 16px; border-radius: 50%; border: 2px solid #cbd5e1; flex-shrink: 0; position: relative; transition: all .2s ease; }
        .reg-nav__phase.active .reg-nav__circle { border-color: #1e3a5f; }
        .reg-nav__phase.active .reg-nav__circle::after { content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #1e3a5f; }
        .reg-nav__phase.done .reg-nav__circle { border-color: #1e3a5f; background: #1e3a5f; }
        .reg-nav__phase.done .reg-nav__circle::after { content: '✓'; position: absolute; inset: -1px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .62rem; font-weight: 700; }
        .reg-nav__label { font-size: .86rem; color: #5f6368; font-weight: 400; line-height: 1.3; }
        .reg-nav__phase.active .reg-nav__label { color: #202124; font-weight: 600; }
        .reg-nav__phase.done .reg-nav__label { color: #202124; }
        .reg-nav__phase.done { cursor: pointer; }
        .reg-nav__phase.done:hover .reg-nav__label { color: #1e3a5f; }
        .reg-nav__subs { display: none; flex-direction: column; margin: 0 0 6px 27px; padding-left: 13px; border-left: 1px solid #e8eaed; }
        .reg-nav__group.is-open .reg-nav__subs { display: flex; }
        .reg-nav__sub { font-size: .82rem; color: #5f6368; padding: 7px 0; cursor: default; transition: color .15s ease; }
        .reg-nav__sub.done { color: #80868b; cursor: pointer; }
        .reg-nav__sub.done:hover { color: #1e3a5f; }
        .reg-nav__sub.active { color: #1e3a5f; font-weight: 600; }

        /* Main content — centered card */
        .reg-main { min-width: 0; max-width: 100%; }
        .reg-main .content-panel {
            width: 100%;
            max-width: min(700px, 100%);
            margin: 0 auto;
            padding: 26px 30px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: max-width .2s ease;
            box-sizing: border-box;
        }
        .reg-main .content-panel.content-panel--wide { max-width: min(1040px, 100%); }
        .camera-verification-box:fullscreen,
        .camera-verification-box:-webkit-full-screen { background: #000; padding: 0; }
        .camera-verification-box:fullscreen #cameraVideo,
        .camera-verification-box:-webkit-full-screen #cameraVideo { width: 100%; height: 100%; max-height: none; object-fit: contain; border-radius: 0; }

        /* Phone-style round camera controls */
        .cam-controls { padding: 4px 0; }
        .cam-btn {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 50%; border: none; cursor: pointer; padding: 0;
            transition: transform .12s ease, background .15s ease, box-shadow .15s ease;
        }
        .cam-btn:active { transform: scale(0.92); }
        .cam-btn--shutter {
            width: 62px; height: 62px; background: #1e3a5f; color: #fff;
            font-size: 1.5rem; box-shadow: 0 0 0 4px #fff, 0 0 0 6px #1e3a5f;
        }
        .cam-btn--shutter:hover { background: #16304f; }
        .cam-btn--secondary {
            width: 44px; height: 44px; background: #eef2f7; color: #1e3a5f;
            font-size: 1.05rem; border: 1px solid #d7e0ea;
        }
        .cam-btn--secondary:hover { background: #e2e9f1; }

        /* Fullscreen overlay controls (hidden unless the camera box is fullscreen) */
        .cam-fs-controls {
            display: none; position: absolute; left: 0; right: 0; bottom: 28px;
            z-index: 5; align-items: center; justify-content: center; gap: 26px;
        }
        .camera-verification-box:fullscreen .cam-fs-controls,
        .camera-verification-box:-webkit-full-screen .cam-fs-controls { display: flex; }
        .cam-btn--fs { background: rgba(255,255,255,0.9); border: none; color: #1e3a5f; }
        .cam-btn--fs:hover { background: #fff; }

        /* Image lightbox for viewing captured verification photos */
        .biz-lightbox {
            display: none; position: fixed; inset: 0; z-index: 20000;
            background: rgba(15, 23, 42, 0.9); align-items: center; justify-content: center;
            flex-direction: column; gap: 14px; padding: 30px;
        }
        .biz-lightbox.is-open { display: flex; }
        .biz-lightbox__img {
            max-width: 92vw; max-height: 82vh; object-fit: contain;
            border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .biz-lightbox__caption { color: #e2e8f0; font-size: 0.9rem; }
        .biz-lightbox__close {
            position: absolute; top: 20px; right: 24px; width: 44px; height: 44px;
            border-radius: 50%; border: none; cursor: pointer;
            background: rgba(255,255,255,0.15); color: #fff; font-size: 1.1rem;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .biz-lightbox__close:hover { background: rgba(255,255,255,0.3); }
        .step-progress-container { display: none !important; }
        .reg-step-head { display: none; }

        /* Tighter typography inside the wizard */
        .biz-step-pane h4 { font-size: 0.98rem !important; margin-bottom: 6px !important; }
        .biz-step-pane > p { font-size: 0.82rem; }

        .biz-loc-suggest {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% - 4px);
            z-index: 40;
            background: #fff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(60, 64, 67, 0.18);
            max-height: 340px;
            overflow-y: auto;
            padding: 6px 0;
        }
        .biz-loc-suggest__create,
        .biz-loc-suggest__item {
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            padding: 10px 16px;
            cursor: pointer;
        }
        .biz-loc-suggest__create:hover,
        .biz-loc-suggest__item:hover { background: #f1f3f4; }
        .biz-loc-suggest__create-name,
        .biz-loc-suggest__name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #202124;
            line-height: 1.3;
        }
        .biz-loc-suggest__create-sub {
            margin-top: 2px;
            font-size: 0.78rem;
            color: #5f6368;
        }
        .biz-loc-suggest__sub {
            margin-top: 2px;
            font-size: 0.78rem;
            color: #5f6368;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .biz-loc-suggest__divider {
            height: 1px;
            background: #e8eaed;
            margin: 4px 0;
        }
        .biz-claim-panel {
            margin-top: 10px;
            border: 1px solid #c7d7e8;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }
        .biz-claim-panel__head {
            padding: 7px 12px;
            border-bottom: 1px solid #e2eaf3;
            background: rgba(30, 58, 95, 0.04);
        }
        .biz-claim-panel__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            font-weight: 650;
            color: #1e3a5f;
        }
        .biz-claim-panel__badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #16a34a;
            box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.18);
        }
        .biz-claim-panel__body {
            display: flex;
            gap: 10px;
            padding: 10px 12px;
            align-items: center;
        }
        .biz-claim-panel__thumb {
            width: 56px;
            height: 56px;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
            background: #e8eef5;
        }
        .biz-claim-panel__thumb img,
        .biz-claim-panel__thumb .biz-claim-card__ph {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .biz-claim-panel__thumb .biz-claim-card__ph {
            background: linear-gradient(135deg, #dbe5f0, #f1f5f9);
        }
        .biz-claim-panel__meta { flex: 1; min-width: 0; }
        .biz-claim-panel__title {
            margin: 0 0 2px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }
        .biz-claim-panel__rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            color: #475569;
            margin-bottom: 2px;
        }
        .biz-claim-panel__rating .biz-claim-card__stars { color: #eab308; letter-spacing: 0.5px; }
        .biz-claim-panel__addr {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .biz-claim-panel__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 0 12px 10px;
        }
        .biz-claim-manage-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            background: var(--primary, #1e3a5f);
            color: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
        }
        .biz-claim-manage-btn:hover { filter: brightness(1.08); }
        .biz-claim-other {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #fff;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 500;
            cursor: pointer;
        }
        .biz-claim-other:hover {
            border-color: #94a3b8;
            color: #0f172a;
        }
        @media (max-width: 560px) {
            .biz-claim-panel__actions { flex-direction: column; }
            .biz-claim-manage-btn,
            .biz-claim-other { width: 100%; }
        }
        .biz-claim-chip {
            margin-top: 10px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
        }
        .biz-claim-chip__body { flex: 1; min-width: 0; }
        .biz-claim-chip__label {
            font-size: 0.68rem;
            font-weight: 600;
            color: #15803d;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }
        .biz-claim-chip__name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #14532d;
        }
        .biz-claim-chip__addr { font-size: 0.75rem; }
        .biz-claim-chip__clear {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 1.25rem;
            line-height: 1;
            padding: 0 2px;
            cursor: pointer;
        }
        .biz-claim-chip__clear:hover { color: #0f172a; }
        .reg-nav__sub.is-claim-hidden,
        .step-progress-node.is-claim-hidden { display: none !important; }
        .form-label-clean { font-size: 0.82rem; }
        .form-control-clean { font-size: 0.85rem; padding: 8px 12px; }

        /* Quick-approval tip — subtle inline hint */
        .reg-tip { margin: -6px 0 20px; font-size: 0.78rem; color: #94a3b8; line-height: 1.5; }
        .reg-tip__label { display: none; }
        .reg-tip strong { color: #64748b; font-weight: 600; }

        /* Evidence suggestion list */
        .reg-evidence { margin-bottom: 20px; }
        .reg-evidence__label { font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .reg-evidence__list { margin: 0; padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 7px; }
        .reg-evidence__list li { position: relative; padding-left: 22px; font-size: 0.82rem; color: #52708f; line-height: 1.5; }
        .reg-evidence__list li::before { content: ''; position: absolute; left: 6px; top: 8px; width: 5px; height: 5px; border-radius: 50%; background: #94a3b8; }

        /* Public / private note blocks — neutral, sharp */
        .reg-note { display: flex; gap: 10px; align-items: flex-start; padding: 11px 13px; border-radius: 7px; margin-bottom: 22px; font-size: 0.82rem; line-height: 1.55; background: #f8fafc; border: 1px solid #e5e7eb; color: #475569; }
        .reg-tag { display: inline-flex; align-items: center; font-size: 0.66rem; font-weight: 600; letter-spacing: 0.4px; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; flex-shrink: 0; }
        .reg-tag--public { background: #1e3a5f; color: #ffffff; }
        .reg-tag--private { background: #94a3b8; color: #ffffff; }

        /* Dropzone — low radius, subtle */
        .dropzone-area { padding: 30px 20px; border-radius: 8px; }
        .dropzone-area .dropzone-icon { font-size: 1.8rem; color: #94a3b8; margin-bottom: 8px; display: block; }
        .preview-thumbnail { border-radius: 6px; }
        .biz-type-card { border-radius: 8px; }
        .form-control-clean, .custom-select-trigger { border-radius: 6px; }

        /* Actions footer */
        .reg-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 26px; padding-top: 18px; border-top: 1px solid #eef2f7; }
        .reg-actions__right { display: flex; gap: 10px; margin-left: auto; }
        .reg-skip { background: none; border: none; color: #5f6368; font-size: .85rem; font-weight: 500; padding: 8px 4px; cursor: pointer; }
        .reg-skip:hover { color: #1e3a5f; text-decoration: underline; }
        .reg-actions .btn { border-radius: 6px !important; font-weight: 500 !important; font-size: 0.84rem !important; padding: 7px 18px !important; }
        .reg-skip { font-size: 0.82rem; }
        #bizPrevBtn { background: #ffffff !important; border: 1px solid #e5e7eb !important; color: #5f6368 !important; }
        #bizPrevBtn:hover:not(:disabled) { border-color: #1e3a5f !important; color: #1e3a5f !important; }
        #bizPrevBtn:disabled { opacity: 0.4; }

        #bizNextBtn.is-locked, #bizNextBtn:disabled, #bizNextBtn[disabled] {
            opacity: 0.45 !important;
            pointer-events: none !important;
            cursor: not-allowed !important;
            background-color: #64748b !important;
            border-color: #64748b !important;
            box-shadow: none !important;
        }

        /* Top bar (Google-Ads style) */
        .top-navbar { padding: 12px 24px; }
        .nav-left { display: flex; align-items: center; gap: 14px; }
        .nav-back { color: #5f6368; font-size: 1rem; display: inline-flex; text-decoration: none; }
        .nav-back:hover { color: #1e3a5f; }
        .nav-logo { font-size: 1.05rem; font-weight: 600; color: #1e3a5f; letter-spacing: -0.01em; }
        .nav-logo strong { font-weight: 700; }
        .nav-sep { width: 1px; height: 20px; background: #e0e0e0; }
        .nav-page-title { font-size: 0.95rem; color: #3c4043; font-weight: 400; }
        .nav-right { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
        .nav-right .avatar-frame-wrapper {
            position: relative;
            overflow: visible;
            flex-shrink: 0;
        }
        .nav-help { font-size: 0.82rem; color: #5f6368; text-decoration: none; }
        .nav-help:hover { color: #1e3a5f; }
        .nav-user-email { font-size: 0.8rem; color: #5f6368; }

        /* Footer help bar */
        .reg-help-bar { border-top: 1px solid #e8eaed; text-align: center; padding: 16px 20px 24px; font-size: 0.8rem; color: #5f6368; }
        .reg-help-bar strong { color: #1e3a5f; font-weight: 600; }

        @media (max-width: 900px) {
            .reg-wrap { grid-template-columns: 1fr; gap: 20px; padding: 20px 14px 24px; }
            .reg-aside { position: static; }
        }
        @media (max-width: 640px) {
            .nav-page-title, .nav-sep, .nav-user-email { display: none; }
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-navbar">
    <div class="nav-left">
        <a href="{{ route('client.profile') }}" class="nav-back" title="Quay lại trang cá nhân"><i class="bi bi-chevron-left"></i></a>
        <span class="nav-logo">Ninh Bình <strong>Travel Hub</strong></span>
        <span class="nav-sep"></span>
        <span class="nav-page-title">Đăng ký địa điểm doanh nghiệp</span>
    </div>
    <div class="nav-right">
        <a href="{{ route('client.pano_service') }}" class="nav-help" target="_blank" rel="noopener">Trợ giúp</a>
        <span class="nav-user-email">{{ Auth::user()->email ?? '' }}</span>
        <x-user-avatar :user="Auth::user()" size="30" />
    </div>
</div>

<div class="reg-wrap">
    <!-- Left: grouped step navigation -->
    <aside class="reg-aside">
        <nav class="reg-nav" id="regNav">
            <div class="reg-nav__group" data-range="1-3">
                <div class="reg-nav__phase"><span class="reg-nav__circle"></span><span class="reg-nav__label">Thông tin doanh nghiệp</span></div>
                <div class="reg-nav__subs">
                    <div class="reg-nav__sub" data-step="1">Tên doanh nghiệp</div>
                    <div class="reg-nav__sub is-claim-hidden" data-step="2" hidden>Loại hình</div>
                    <div class="reg-nav__sub" data-step="3">Danh mục kinh doanh</div>
                </div>
            </div>
            <div class="reg-nav__group" data-range="4-6">
                <div class="reg-nav__phase"><span class="reg-nav__circle"></span><span class="reg-nav__label">Địa chỉ & liên hệ</span></div>
                <div class="reg-nav__subs">
                    <div class="reg-nav__sub" data-step="4">Địa chỉ</div>
                    <div class="reg-nav__sub" data-step="5">Thông tin liên hệ</div>
                    <div class="reg-nav__sub" data-step="6">Vị trí trên bản đồ</div>
                </div>
            </div>
            <div class="reg-nav__group" data-range="7-9">
                <div class="reg-nav__phase"><span class="reg-nav__circle"></span><span class="reg-nav__label">Giới thiệu & hình ảnh</span></div>
                <div class="reg-nav__subs">
                    <div class="reg-nav__sub" data-step="7">Mô tả giới thiệu</div>
                    <div class="reg-nav__sub" data-step="8">Ảnh địa điểm</div>
                    <div class="reg-nav__sub" data-step="9">Ảnh đại diện</div>
                </div>
            </div>
            <div class="reg-nav__group" data-range="10-11">
                <div class="reg-nav__phase"><span class="reg-nav__circle"></span><span class="reg-nav__label">Xác minh & hoàn tất</span></div>
                <div class="reg-nav__subs">
                    <div class="reg-nav__sub" data-step="10">Bằng chứng xác minh</div>
                    <div class="reg-nav__sub" data-step="11">Xác thực thực địa</div>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Right: form wizard -->
    <div class="reg-main" id="businessRegistrationWizard">
        <div class="content-panel">

            <!-- Step Progress Bar (hidden, kept for JS state) -->
            <div class="step-progress-container">
                <div class="step-progress-line"></div>
                <div class="step-progress-fill" id="bizStepFill"></div>
                <div class="step-progress-node active" data-step="1">1</div>
                <div class="step-progress-node" data-step="2">2</div>
                <div class="step-progress-node" data-step="3">3</div>
                <div class="step-progress-node" data-step="4">4</div>
                <div class="step-progress-node" data-step="5">5</div>
                <div class="step-progress-node" data-step="6">6</div>
                <div class="step-progress-node" data-step="7">7</div>
                <div class="step-progress-node" data-step="8">8</div>
                <div class="step-progress-node" data-step="9">9</div>
                <div class="step-progress-node" data-step="10">10</div>
                <div class="step-progress-node" data-step="11">11</div>
            </div>

            <div class="wizard-row">
                <!-- Left: Form steps -->
                <div class="wizard-form-col">
                    <form id="bizRegisterForm" onsubmit="event.preventDefault();">
                        @csrf
                        <!-- Step 1: Business Name -->
                        <div class="biz-step-pane" data-step="1">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Giúp khách hàng tìm thấy doanh nghiệp của bạn trên Tìm kiếm, Maps, v.v.</h4>
                            <p class="text-secondary small mb-4">Nhập tên doanh nghiệp. Nếu địa điểm đã có trên bản đồ, chọn từ gợi ý để chỉ cần xác minh.</p>
                            <div class="mb-4 position-relative">
                                <label class="form-label-clean">Tên doanh nghiệp *</label>
                                <input type="text" class="form-control-clean" id="input_business_name" name="business_name" required placeholder="Ví dụ: Nhà Hàng Dê Nướng Cố Đô" autocomplete="off">
                                <input type="hidden" id="input_claim_location_id" name="location_id" value="">
                                <div id="bizLocationSuggest" class="biz-loc-suggest d-none" role="listbox"></div>
                                <p class="text-secondary small mt-2 mb-0" id="bizClaimHint">Gõ tên để tạo mới hoặc chọn địa điểm đã có trên map.</p>

                                <div id="bizClaimPreview" class="biz-claim-panel d-none">
                                    <div class="biz-claim-panel__head">
                                        <span class="biz-claim-panel__badge">Đã có trên bản đồ</span>
                                    </div>
                                    <div class="biz-claim-panel__body">
                                        <div class="biz-claim-panel__thumb" id="bizClaimPhotos"></div>
                                        <div class="biz-claim-panel__meta">
                                            <h3 class="biz-claim-panel__title" id="bizClaimPreviewName">—</h3>
                                            <div class="biz-claim-panel__rating" id="bizClaimPreviewRating"></div>
                                            <div class="biz-claim-panel__addr" id="bizClaimPreviewAddr">—</div>
                                        </div>
                                    </div>
                                    <div class="biz-claim-panel__actions">
                                        <button type="button" class="biz-claim-manage-btn" id="bizClaimManageBtn">Đây là của tôi</button>
                                        <button type="button" class="biz-claim-other" id="bizClaimOtherBtn">Không phải địa điểm này</button>
                                    </div>
                                </div>

                                <div id="bizClaimChip" class="biz-claim-chip d-none">
                                    <div class="biz-claim-chip__body">
                                        <div class="biz-claim-chip__label">Đã chọn nhận quyền địa điểm</div>
                                        <div class="biz-claim-chip__name" id="bizClaimName"></div>
                                        <div class="biz-claim-chip__addr text-secondary" id="bizClaimAddr"></div>
                                    </div>
                                    <button type="button" class="biz-claim-chip__clear" id="bizClaimClear" title="Bỏ chọn">×</button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Business Type -->
                        <div class="biz-step-pane d-none" data-step="2">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Chọn loại hình doanh nghiệp của bạn</h4>
                            <p class="text-secondary small mb-4">Chọn tất cả các phương thức áp dụng cho doanh nghiệp.</p>
                            
                            <!-- Custom selectable cards -->
                            <div class="biz-type-card" data-val="online_retail">
                                <div class="biz-type-info">
                                    <div class="biz-type-name">Bán lẻ trực tuyến</div>
                                    <div class="biz-type-desc">Khách hàng có thể mua sản phẩm thông qua trang web của bạn</div>
                                </div>
                                <div class="biz-type-checkbox">✓</div>
                            </div>

                            <div class="biz-type-card" data-val="local_store">
                                <div class="biz-type-info">
                                    <div class="biz-type-name">Cửa hàng tại địa phương</div>
                                    <div class="biz-type-desc">Khách hàng có thể trực tiếp ghé thăm doanh nghiệp của bạn</div>
                                </div>
                                <div class="biz-type-checkbox">✓</div>
                            </div>

                            <div class="biz-type-card" data-val="service_business">
                                <div class="biz-type-info">
                                    <div class="biz-type-name">Doanh nghiệp dịch vụ</div>
                                    <div class="biz-type-desc">Doanh nghiệp của bạn cung cấp dịch vụ giao hàng tận nơi</div>
                                </div>
                                <div class="biz-type-checkbox">✓</div>
                            </div>
                            
                            <!-- Hidden input to store chosen values -->
                            <input type="hidden" name="business_types[]" id="input_business_types" value="">
                        </div>

                        <!-- Step 3: Business Category -->
                        <div class="biz-step-pane d-none" data-step="3">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Chọn danh mục kinh doanh</h4>
                            <p class="text-secondary small mb-4">Chọn loại hình danh mục kinh doanh phù hợp nhất với doanh nghiệp của bạn.</p>
                            
                            <div class="mb-4 position-relative">
                                <label class="form-label-clean">Danh mục kinh doanh *</label>
                                
                                <!-- Custom Select Box -->
                                <div class="custom-select-trigger" id="custom_category_select">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="selected-value">-- Chọn danh mục kinh doanh --</span>
                                    </div>
                                    <i class="bi bi-chevron-down trigger-arrow"></i>
                                </div>
                                
                                <!-- Custom Dropdown Menu -->
                                <div class="custom-select-dropdown d-none" id="custom_category_dropdown">
                                    <!-- Group 1 -->
                                    <div class="dropdown-group-header" data-target="group_food_drink">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>Thực phẩm & Đồ uống</span>
                                        </div>
                                        <i class="bi bi-chevron-right arrow-icon"></i>
                                    </div>
                                    <div class="dropdown-options-group d-none" id="group_food_drink">
                                        <div class="dropdown-option-item" data-value="5" data-name="Nhà hàng">Nhà hàng</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Quán ăn bình dân">Quán ăn bình dân</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Quán cà phê / Trà sữa">Quán cà phê / Trà sữa</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Quán nước / Giải khát">Quán nước / Giải khát</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Tiệm bánh / Tráng miệng">Tiệm bánh / Tráng miệng</div>
                                        <div class="dropdown-option-item dropdown-option-other" data-value="5" data-name="Khác">Khác...</div>
                                        <div class="other-category-input-wrapper d-none p-2 mt-1 border-top">
                                            <input type="text" class="form-control-clean input-custom-other-category" placeholder="Nhập tên danh mục chi tiết của bạn..." style="font-size: 0.85rem; padding: 7px 12px;">
                                        </div>
                                    </div>
                                    
                                    <!-- Group 2 -->
                                    <div class="dropdown-group-header" data-target="group_lodging">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>Khách sạn & Nơi lưu trú</span>
                                        </div>
                                        <i class="bi bi-chevron-right arrow-icon"></i>
                                    </div>
                                    <div class="dropdown-options-group d-none" id="group_lodging">
                                        <div class="dropdown-option-item" data-value="6" data-name="Khách sạn">Khách sạn</div>
                                        <div class="dropdown-option-item" data-value="6" data-name="Nhà nghỉ">Nhà nghỉ</div>
                                        <div class="dropdown-option-item" data-value="6" data-name="Homestay">Homestay</div>
                                        <div class="dropdown-option-item" data-value="6" data-name="Resort / Khu nghỉ dưỡng">Resort / Khu nghỉ dưỡng</div>
                                        <div class="dropdown-option-item" data-value="6" data-name="Biệt thự du lịch (Villa)">Biệt thự du lịch (Villa)</div>
                                        <div class="dropdown-option-item" data-value="6" data-name="Nhà khách">Nhà khách</div>
                                        <div class="dropdown-option-item dropdown-option-other" data-value="6" data-name="Khác">Khác...</div>
                                        <div class="other-category-input-wrapper d-none p-2 mt-1 border-top">
                                            <input type="text" class="form-control-clean input-custom-other-category" placeholder="Nhập tên danh mục chi tiết của bạn..." style="font-size: 0.85rem; padding: 7px 12px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden inputs for validation & payload -->
                            <input type="hidden" id="input_category_search" name="category_search_name">
                            <input type="hidden" id="input_category_id" name="category_id">

                            <div class="text-secondary small mt-2">
                                Bạn có thể thay đổi và thêm các loại hình doanh nghiệp khác sau.
                            </div>
                        </div>

                        <!-- Step 4: Business Address -->
                        <div class="biz-step-pane d-none" data-step="4">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Nhập địa chỉ doanh nghiệp của bạn</h4>
                            <p class="text-secondary small mb-4">Thêm một vị trí khách hàng có thể thực tế ghé thăm doanh nghiệp của bạn.</p>
                            
                            <!-- Row 1: 2 Fixed Defaults -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label-clean">Quốc gia / Vùng</label>
                                    <input type="text" class="form-control-clean" name="address_country" value="Việt Nam" readonly style="background-color: #f8fafc; color: #64748b;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-clean">Tỉnh / Thành phố</label>
                                    <input type="text" class="form-control-clean" id="input_address_province" name="address_province" value="Ninh Bình" readonly style="background-color: #f8fafc; color: #64748b;" required>
                                </div>
                            </div>

                            <!-- Row 2: Fillable City/District & Postal Code -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label-clean">Thành phố / Thị xã / Huyện *</label>
                                    <input type="text" class="form-control-clean" id="input_address_city" name="address_city" required placeholder="Ví dụ: TP. Ninh Bình, Hoa Lư...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-clean">Mã bưu chính *</label>
                                    <input type="text" class="form-control-clean" id="input_address_postal_code" name="address_postal_code" required value="430000" placeholder="Ví dụ: 430000">
                                </div>
                            </div>

                            <!-- Row 3: Street Address -->
                            <div class="mb-3">
                                <label class="form-label-clean">Đường phố / Số nhà *</label>
                                <input type="text" class="form-control-clean" id="input_address_street" name="address_street" required placeholder="Ví dụ: 123 Nguyễn Huệ">
                            </div>
                        </div>

                        <!-- Step 5: Contact Details -->
                        <div class="biz-step-pane d-none" data-step="5">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Bạn muốn hiển thị thông tin chi tiết liên hệ nào cho khách hàng?</h4>
                            <p class="text-secondary small mb-4">Thêm thông tin này vào danh mục hiển thị để giúp khách hàng dễ dàng liên hệ với doanh nghiệp của bạn.</p>
                            
                            <div class="mb-4">
                                <label class="form-label-clean">Số điện thoại liên hệ *</label>
                                <div class="d-flex gap-2">
                                    <div class="d-flex align-items-center px-3 border rounded-2 bg-light text-secondary" style="font-size: 0.875rem; border-color: var(--border-color) !important;">
                                        +84
                                    </div>
                                    <input type="tel" class="form-control-clean flex-grow-1" id="input_phone" name="phone" required placeholder="Ví dụ: 0912345678" style="margin-bottom: 0;">
                                </div>
                            </div>
                        </div>

                        <!-- Step 6: Map Coordinates -->
                        <div class="biz-step-pane d-none" data-step="6">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Doanh nghiệp của bạn ở đâu?</h4>
                            <p class="text-secondary small mb-3">Kéo marker hoặc nhấp trên bản đồ để ghim vị trí. Dùng nút định vị góc phải bản đồ để lấy vị trí hiện tại.</p>
                            
                            <div id="businessMap"></div>
                            
                            <!-- Hidden inputs for Lat / Lng -->
                            <input type="hidden" id="input_lat" name="lat" required>
                            <input type="hidden" id="input_lng" name="lng" required>
                        </div>

                        <!-- Step 7: Description -->
                        <div class="biz-step-pane d-none" data-step="7">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Thêm mô tả doanh nghiệp *</h4>
                            <p class="text-secondary small mb-4">Cho phép khách hàng tìm hiểu thêm về doanh nghiệp của bạn bằng cách thêm mô tả ngắn gọn.</p>
                            <div class="mb-3">
                                <label class="form-label-clean">Giới thiệu về doanh nghiệp *</label>
                                <textarea class="form-control-clean" id="input_description" name="description" rows="5" maxlength="750" required placeholder="Ví dụ: Nhà hàng chuyên phục vụ các món ăn đặc sản Ninh Bình như thịt dê núi, cơm cháy và các món ăn dân dã truyền thống..."></textarea>
                                <div class="text-end text-secondary small mt-1" id="descCharCount">0 / 750</div>
                            </div>
                        </div>

                        <!-- Step 8: Public Gallery Photos -->
                        <div class="biz-step-pane d-none" data-step="8">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;" id="step8Title">Thêm ảnh về địa điểm của bạn *</h4>
                            <p class="text-secondary small mb-2" id="step8Desc">Bao gồm ảnh mặt tiền, không gian, món ăn / dịch vụ, bảng giá... để khách hình dung rõ về bạn.</p>
                            <div class="reg-note reg-note--public">
                                <span class="reg-tag reg-tag--public">Công khai</span>
                                <div>Những ảnh này sẽ được <strong>hiển thị công khai</strong> trên trang địa điểm của bạn cho mọi khách xem.</div>
                            </div>

                            <div class="dropzone-area" id="menuDropzone">
                                <i class="bi bi-images dropzone-icon"></i>
                                <div class="fw-semibold small" id="step8Text">Kéo các hình ảnh vào đây</div>
                                <div class="text-secondary small mt-1">hoặc click để chọn từ máy tính <span class="fw-normal text-muted">(Tối đa 10 ảnh, ≤ 10MB/ảnh)</span></div>
                                <input type="file" id="menuFilesInput" class="d-none" multiple accept="image/*">
                            </div>

                            <div class="upload-previews" id="menuPreviews"></div>
                        </div>

                        <!-- Step 9: Avatar / Representative Photo (single, public) -->
                        <div class="biz-step-pane d-none" data-step="9">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Chọn ảnh đại diện *</h4>
                            <p class="text-secondary small mb-2">Đây là ảnh chính đại diện cho địa điểm của bạn (hiển thị trên bản đồ, danh sách và đầu trang địa điểm). Nên chọn ảnh mặt tiền hoặc ảnh nổi bật nhất.</p>
                            <div class="reg-note reg-note--public">
                                <span class="reg-tag reg-tag--public">Công khai</span>
                                <div>Ảnh đại diện sẽ <strong>hiển thị công khai</strong>. Chỉ chọn 1 ảnh từ thiết bị của bạn.</div>
                            </div>

                            <div class="dropzone-area" id="avatarDropzone">
                                <i class="bi bi-person-square dropzone-icon"></i>
                                <div class="fw-semibold small">Kéo ảnh đại diện vào đây</div>
                                <div class="text-secondary small mt-1">hoặc click để chọn từ máy tính (1 ảnh)</div>
                                <input type="file" id="avatarFileInput" class="d-none" accept="image/*">
                            </div>

                            <div class="upload-previews" id="avatarPreview"></div>
                        </div>

                        <!-- Step 10: Ownership Verification Documents (optional, private) -->
                        <div class="biz-step-pane d-none" data-step="10">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Bằng chứng bạn gắn với địa điểm này *</h4>
                            <p class="text-secondary small mb-3">Tải lên ít nhất 1 bằng chứng cho thấy bạn là chủ hoặc người quản lý địa điểm.</p>

                            <div class="reg-evidence">
                                <div class="reg-evidence__label">Gợi ý:</div>
                                <ul class="reg-evidence__list">
                                    <li>Ảnh bạn tại địa điểm / trước biển hiệu</li>
                                    <li>Ảnh biển hiệu, mặt bằng có tên</li>
                                    <li>Hóa đơn điện / nước / internet mang tên bạn</li>
                                    <li>Hợp đồng thuê hoặc giấy phép kinh doanh <span class="text-muted">(nếu có)</span></li>
                                </ul>
                            </div>

                            <div class="reg-tip">
                                <span class="reg-tip__label">Mẹo duyệt nhanh</span>
                                <div>Bằng chứng <strong>càng thuyết phục</strong>, hồ sơ <strong>càng được duyệt nhanh</strong>.</div>
                            </div>

                            <div class="dropzone-area" id="docDropzone">
                                <i class="bi bi-shield-check dropzone-icon"></i>
                                <div class="fw-semibold small">Kéo ảnh bằng chứng vào đây</div>
                                <div class="text-secondary small mt-1">hoặc click để chọn từ máy tính <span class="fw-normal text-muted">(Tối đa 10 ảnh, ≤ 10MB/ảnh)</span></div>
                                <input type="file" id="docFilesInput" class="d-none" multiple accept="image/*">
                            </div>

                            <div class="upload-previews" id="docPreviews"></div>
                        </div>

                        <!-- Step 11: Camera & Location Verification -->
                        <div class="biz-step-pane d-none" data-step="11">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #1e3a5f;">Xác thực thực địa (Camera & Vị trí GPS) *</h4>
                            <p class="text-secondary small mb-4">Mở camera trên thiết bị của bạn để chụp ảnh thực tế tại địa điểm kinh doanh đồng thời hệ thống sẽ tự động đối chiếu vị trí GPS trên bản đồ giúp tăng tối đa tính xác thực cho hồ sơ doanh nghiệp.</p>

                            <div class="row g-4">
                                <!-- Left Column: Camera Stream Viewport -->
                                <div class="col-lg-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100" style="border-color: #cbdbe8 !important;">
                                        <div class="fw-semibold text-dark small mb-3">
                                            Camera chụp ảnh trực tiếp cửa hàng
                                        </div>
                                        <div class="camera-verification-box border rounded-3 text-center position-relative mb-3" style="background: #1e293b; min-height: 440px; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; flex: 1;">
                                            <video id="cameraVideo" autoplay playsinline class="rounded-2 d-none" style="width: 100%; height: 100%; max-height: none; object-fit: cover;"></video>
                                            <canvas id="cameraCanvas" class="d-none"></canvas>

                                            <div id="cameraPlaceholder" class="text-white py-4">
                                                <p class="small text-secondary mb-3 px-3">Nhấn nút bên dưới để bật Camera chụp ảnh thực địa.</p>
                                                <button type="button" class="btn btn-primary btn-sm px-4" id="btnStartCamera">
                                                    Bật Camera Xác Thực
                                                </button>
                                            </div>

                                            <!-- Overlay controls shown only in fullscreen -->
                                            <div class="cam-fs-controls" id="camFsControls">
                                                <button type="button" class="cam-btn cam-btn--secondary cam-btn--fs" id="btnSwitchCameraFs" title="Đổi camera" aria-label="Đổi camera">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button type="button" class="cam-btn cam-btn--shutter" id="btnCapturePhotoFs" title="Chụp ảnh & ghi nhận GPS" aria-label="Chụp ảnh & ghi nhận GPS">
                                                    <i class="bi bi-camera-fill"></i>
                                                </button>
                                                <button type="button" class="cam-btn cam-btn--secondary cam-btn--fs" id="btnExitFullscreen" title="Thoát toàn màn hình" aria-label="Thoát toàn màn hình">
                                                    <i class="bi bi-fullscreen-exit"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Camera Action Controls -->
                                        <div class="cam-controls d-flex align-items-center justify-content-center gap-3">
                                            <button type="button" class="cam-btn cam-btn--secondary d-none" id="btnSwitchCamera" title="Đổi camera" aria-label="Đổi camera">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <button type="button" class="cam-btn cam-btn--shutter d-none" id="btnCapturePhoto" title="Chụp ảnh & ghi nhận GPS" aria-label="Chụp ảnh & ghi nhận GPS">
                                                <i class="bi bi-camera-fill"></i>
                                            </button>
                                            <button type="button" class="cam-btn cam-btn--secondary d-none" id="btnFullscreenCamera" title="Toàn màn hình" aria-label="Toàn màn hình">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Interactive GPS Leaflet Map & Captured Photos -->
                                <div class="col-lg-6">
                                    <!-- Leaflet GPS Map Card -->
                                    <div class="card border rounded-3 p-3 bg-white mb-3" id="gpsStatusCard" style="border-color: #cbdbe8 !important;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <div class="fw-semibold text-dark small" id="gpsStatusTitle">Đang xác định GPS...</div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btnRefreshGPS" style="font-size: 0.78rem;">
                                                Lấy lại GPS
                                            </button>
                                        </div>

                                        <!-- GPS Distance matching badge -->
                                        <div id="gpsDistanceBadge" class="mb-2 d-none">
                                            <span class="badge" id="gpsDistanceText" style="background:#f1f5f9; color:#1e3a5f; border:1px solid #cbdbe8; font-weight:500;">
                                                Trùng khớp vị trí bản đồ
                                            </span>
                                        </div>

                                        <!-- Leaflet Live Map Canvas Container -->
                                        <div id="verificationMiniMap" class="rounded-3 border overflow-hidden" style="height: 190px; width: 100%; z-index: 1; background: #f8fafc; border-color: #cbdbe8 !important;"></div>
                                    </div>

                                    <!-- Latest Snapshot Watermark Card -->
                                    <div id="capturedPreviewContainer" class="d-none w-100 position-relative mb-3 border rounded-3 overflow-hidden" style="border-color: #cbdbe8 !important;">
                                        <img id="capturedImagePreview" src="" class="img-fluid rounded-2" style="max-height: 160px; object-fit: cover; width: 100%; cursor: zoom-in;" title="Bấm để xem ảnh lớn">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2 text-white text-start" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); font-size: 0.75rem;">
                                            <div class="fw-bold" id="watermarkLocation">Tọa độ GPS: --</div>
                                            <div id="watermarkTime">Thời gian: --</div>
                                        </div>
                                    </div>

                                    <!-- Captured Verification Photos Gallery Grid -->
                                    <div class="card border rounded-3 p-3 bg-white" id="verificationGalleryWrapper" style="border-color: #cbdbe8 !important;">
                                        <div class="fw-semibold small mb-2 text-dark d-flex align-items-center justify-content-between">
                                            <span>Ảnh thực địa đã chụp (<span id="verificationCount">0</span> ảnh)</span>
                                            <span class="text-muted small" style="font-size: 0.72rem;">Chụp các góc cửa hàng</span>
                                        </div>
                                        <div class="upload-previews d-flex flex-wrap gap-2" id="verificationGalleryGrid">
                                            <div class="text-muted small py-2 w-100 text-center" id="verificationEmptyText">Chưa có ảnh nào được ghi nhận qua camera.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Row -->
                        <div class="reg-actions">
                            <button type="button" class="reg-skip d-none" id="bizSkipBtn">Bỏ qua bước này</button>
                            <div class="reg-actions__right">
                                <button type="button" class="btn" id="bizPrevBtn" disabled>Quay lại</button>
                                <button type="button" class="btn btn-primary" id="bizNextBtn">Tiếp tục</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer help bar -->
<div class="reg-help-bar">
    Bạn cần trợ giúp? Gọi <strong>1800&nbsp;400&nbsp;389</strong> để được hỗ trợ đăng ký miễn phí, Thứ Hai – Thứ Sáu, 8:00 – 17:30.
</div>

<div class="toast-container-custom" id="toastContainer"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Custom non-blocking toast notifications (Minimalist without icons)
    function showToast(message, isSuccess = true) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        // Remove any leading emoji icons
        const cleanMessage = (message || '').replace(/^[⚠️❌✅\s]+/, '');

        const toast = document.createElement('div');
        toast.className = 'toast-custom';
        if (!isSuccess) {
            toast.style.borderLeftColor = '#f87171';
        } else {
            toast.style.borderLeftColor = '#3b82f6';
        }
        
        toast.innerHTML = `<span>${cleanMessage}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.25s ease';
            setTimeout(() => toast.remove(), 250);
        }, 3000);
    }

    let bizLocateStatusToast = null;

    function showLocateStatusToast(message) {
        dismissLocateStatusToast();
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'toast-custom toast-locating';
        toast.style.borderLeftColor = '#3b82f6';
        toast.innerHTML = `<span>${message}</span>`;
        container.appendChild(toast);
        bizLocateStatusToast = toast;
    }

    function dismissLocateStatusToast() {
        if (bizLocateStatusToast) {
            bizLocateStatusToast.remove();
            bizLocateStatusToast = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const bizForm = document.getElementById('bizRegisterForm');
        if (bizForm) {
            let bizStep = 1;
            const totalBizSteps = 11;
            const claimBizSteps = [1, 5, 10, 11];
            let claimLocationId = null;
            let claimLocationMeta = null;
            const bizSuggestUrl = @json(route('client.profile.business.locations.suggest'));
            const bizPanes = document.querySelectorAll('.biz-step-pane');
            const bizPrevBtn = document.getElementById('bizPrevBtn');
            const bizNextBtn = document.getElementById('bizNextBtn');
            const bizSkipBtn = document.getElementById('bizSkipBtn');
            const bizStepFill = document.getElementById('bizStepFill');
            const bizStepNodes = document.querySelectorAll('.step-progress-node');
            const bizStepNames = {
                1: 'Tên doanh nghiệp', 2: 'Loại hình', 3: 'Danh mục kinh doanh',
                4: 'Địa chỉ', 5: 'Thông tin liên hệ', 6: 'Vị trí trên bản đồ',
                7: 'Mô tả giới thiệu', 8: 'Ảnh địa điểm', 9: 'Ảnh đại diện',
                10: 'Bằng chứng xác minh', 11: 'Xác thực thực địa'
            };

            // Form inputs and preview elements
            const inputBizName = document.getElementById('input_business_name');
            const inputClaimLocationId = document.getElementById('input_claim_location_id');
            const bizLocSuggestEl = document.getElementById('bizLocationSuggest');
            const bizClaimChip = document.getElementById('bizClaimChip');
            const bizClaimName = document.getElementById('bizClaimName');
            const bizClaimAddr = document.getElementById('bizClaimAddr');
            const bizClaimClear = document.getElementById('bizClaimClear');
            const bizClaimPreview = document.getElementById('bizClaimPreview');
            const bizClaimPhotos = document.getElementById('bizClaimPhotos');
            const bizClaimPreviewName = document.getElementById('bizClaimPreviewName');
            const bizClaimPreviewRating = document.getElementById('bizClaimPreviewRating');
            const bizClaimPreviewAddr = document.getElementById('bizClaimPreviewAddr');
            const bizClaimManageBtn = document.getElementById('bizClaimManageBtn');
            const bizClaimOtherBtn = document.getElementById('bizClaimOtherBtn');
            const bizClaimHint = document.getElementById('bizClaimHint');
            let pendingClaimCandidate = null;

            function isClaimMode() {
                return !!claimLocationId;
            }

            function getActiveStepList() {
                // Bỏ bước 2 (loại hình) — luôn mặc định cửa hàng địa phương
                const skipped = new Set([2]);
                const base = isClaimMode()
                    ? claimBizSteps
                    : Array.from({ length: totalBizSteps }, (_, i) => i + 1);
                return base.filter((s) => !skipped.has(s));
            }

            function getActiveStepIndex() {
                const list = getActiveStepList();
                const idx = list.indexOf(bizStep);
                return idx >= 0 ? idx : 0;
            }

            function getActiveStepCount() {
                return getActiveStepList().length;
            }

            function goToRelativeStep(delta) {
                const list = getActiveStepList();
                let idx = list.indexOf(bizStep);
                if (idx < 0) idx = 0;
                const nextIdx = Math.min(Math.max(idx + delta, 0), list.length - 1);
                bizStep = list[nextIdx];
                updateBizStepUI();
            }

            function haversineMeters(lat1, lng1, lat2, lng2) {
                const toRad = (d) => d * Math.PI / 180;
                const R = 6371000;
                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2
                    + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
                return 2 * R * Math.asin(Math.sqrt(a));
            }

            function hideSuggestDropdown() {
                if (!bizLocSuggestEl) return;
                bizLocSuggestEl.classList.add('d-none');
                bizLocSuggestEl.innerHTML = '';
            }

            function starString(rating) {
                const r = Math.max(0, Math.min(5, Math.round(Number(rating) || 0)));
                return '★'.repeat(r) + '☆'.repeat(5 - r);
            }

            function showClaimPreview(loc) {
                pendingClaimCandidate = loc || null;
                if (!bizClaimPreview) return;
                if (!loc) {
                    bizClaimPreview.classList.add('d-none');
                    if (bizClaimHint) bizClaimHint.classList.remove('d-none');
                    return;
                }
                hideSuggestDropdown();
                if (bizClaimHint) bizClaimHint.classList.add('d-none');
                if (bizClaimPreviewName) bizClaimPreviewName.textContent = loc.name || '—';
                if (bizClaimPreviewAddr) {
                    bizClaimPreviewAddr.textContent = [loc.address, loc.category].filter(Boolean).join(' · ') || '—';
                }
                if (bizClaimPreviewRating) {
                    if (loc.rating) {
                        const count = loc.review_count ? ` (${loc.review_count})` : '';
                        bizClaimPreviewRating.innerHTML =
                            `<strong>${Number(loc.rating).toFixed(1).replace('.', ',')}</strong>` +
                            `<span class="biz-claim-card__stars">${starString(loc.rating)}</span>` +
                            `<span>${count}</span>`;
                        bizClaimPreviewRating.classList.remove('d-none');
                    } else {
                        bizClaimPreviewRating.innerHTML = '';
                        bizClaimPreviewRating.classList.add('d-none');
                    }
                }
                if (bizClaimPhotos) {
                    const thumb = (Array.isArray(loc.images) && loc.images[0])
                        || loc.thumbnail
                        || '';
                    bizClaimPhotos.innerHTML = thumb
                        ? `<img src="${thumb}" alt="">`
                        : '<div class="biz-claim-card__ph"></div>';
                }
                bizClaimPreview.classList.remove('d-none');
            }

            function setClaimLocation(loc) {
                claimLocationId = loc ? loc.id : null;
                claimLocationMeta = loc || null;
                pendingClaimCandidate = null;
                if (inputClaimLocationId) inputClaimLocationId.value = claimLocationId || '';
                if (bizClaimChip) bizClaimChip.classList.toggle('d-none', !loc);
                if (bizClaimPreview) bizClaimPreview.classList.add('d-none');
                if (bizClaimHint) bizClaimHint.classList.toggle('d-none', !!loc);
                if (loc) {
                    if (inputBizName) inputBizName.value = loc.name || '';
                    if (bizClaimName) bizClaimName.textContent = loc.name || '';
                    if (bizClaimAddr) bizClaimAddr.textContent = [loc.address, loc.category].filter(Boolean).join(' · ');
                    if (inputCategoryId && loc.category_id) inputCategoryId.value = loc.category_id;
                    if (inputCategorySearch && loc.category) inputCategorySearch.value = loc.category;
                    const latEl = document.getElementById('input_lat');
                    const lngEl = document.getElementById('input_lng');
                    if (latEl) latEl.value = loc.lat;
                    if (lngEl) lngEl.value = loc.lng;
                    hideSuggestDropdown();
                    bizStep = 1;
                }
                updateBizStepUI();
                saveWizardState();
            }

            function clearClaimLocation() {
                setClaimLocation(null);
                showClaimPreview(null);
            }

            function chooseCreateNewBusiness() {
                pendingClaimCandidate = null;
                claimLocationId = null;
                claimLocationMeta = null;
                if (inputClaimLocationId) inputClaimLocationId.value = '';
                if (bizClaimChip) bizClaimChip.classList.add('d-none');
                if (bizClaimPreview) bizClaimPreview.classList.add('d-none');
                if (bizClaimHint) bizClaimHint.classList.remove('d-none');
                hideSuggestDropdown();
                updateBizStepUI();
                saveWizardState();
            }

            let suggestTimer = null;
            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            }

            function renderLocationSuggestions(items, typedName) {
                if (!bizLocSuggestEl) return;
                if (claimLocationId || pendingClaimCandidate) {
                    hideSuggestDropdown();
                    return;
                }
                const q = (typedName || '').trim();
                if (q.length < 2 && !items.length) {
                    hideSuggestDropdown();
                    return;
                }

                let html = '';
                if (q.length >= 2) {
                    html += `<button type="button" class="biz-loc-suggest__create" data-action="create">
                        <div class="biz-loc-suggest__create-name">${escapeHtml(q)}</div>
                        <div class="biz-loc-suggest__create-sub">Tạo một doanh nghiệp với tên này</div>
                    </button>`;
                }

                if (items.length) {
                    if (html) html += '<div class="biz-loc-suggest__divider"></div>';
                    html += items.map((item) => {
                        return `<button type="button" class="biz-loc-suggest__item" data-id="${item.id}">
                            <div class="biz-loc-suggest__name">${escapeHtml(item.name || '')}</div>
                            <div class="biz-loc-suggest__sub">${escapeHtml(item.address || item.category || '')}</div>
                        </button>`;
                    }).join('');
                }

                if (!html) {
                    hideSuggestDropdown();
                    return;
                }

                bizLocSuggestEl.innerHTML = html;
                bizLocSuggestEl.classList.remove('d-none');

                const createBtn = bizLocSuggestEl.querySelector('[data-action="create"]');
                if (createBtn) {
                    createBtn.addEventListener('click', function() {
                        chooseCreateNewBusiness();
                    });
                }
                bizLocSuggestEl.querySelectorAll('.biz-loc-suggest__item').forEach((el) => {
                    el.addEventListener('click', () => {
                        const id = parseInt(el.getAttribute('data-id'), 10);
                        const found = items.find((x) => x.id === id);
                        if (found) showClaimPreview(found);
                    });
                });
            }

            function fetchLocationSuggestions(q) {
                if (!bizSuggestUrl || claimLocationId || pendingClaimCandidate) return;
                fetch(bizSuggestUrl + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then((r) => r.json())
                    .then((json) => renderLocationSuggestions(Array.isArray(json.data) ? json.data : [], q))
                    .catch(() => hideSuggestDropdown());
            }

            if (inputBizName) {
                inputBizName.addEventListener('input', function() {
                    const q = this.value.trim();
                    clearTimeout(suggestTimer);
                    if (claimLocationId) return;
                    // Đang xem preview thì gõ lại sẽ hủy preview để tìm tiếp
                    if (pendingClaimCandidate) {
                        pendingClaimCandidate = null;
                        if (bizClaimPreview) bizClaimPreview.classList.add('d-none');
                        if (bizClaimHint) bizClaimHint.classList.remove('d-none');
                    }
                    if (q.length < 2) {
                        hideSuggestDropdown();
                        return;
                    }
                    suggestTimer = setTimeout(() => fetchLocationSuggestions(q), 280);
                });
                inputBizName.addEventListener('focus', function() {
                    if (!claimLocationId && !pendingClaimCandidate && this.value.trim().length >= 2) {
                        fetchLocationSuggestions(this.value.trim());
                    }
                });
            }
            if (bizClaimClear) {
                bizClaimClear.addEventListener('click', function() {
                    clearClaimLocation();
                });
            }
            if (bizClaimManageBtn) {
                bizClaimManageBtn.addEventListener('click', function() {
                    if (pendingClaimCandidate) {
                        setClaimLocation(pendingClaimCandidate);
                    }
                });
            }
            if (bizClaimOtherBtn) {
                bizClaimOtherBtn.addEventListener('click', function() {
                    pendingClaimCandidate = null;
                    if (bizClaimPreview) bizClaimPreview.classList.add('d-none');
                    if (bizClaimHint) bizClaimHint.classList.remove('d-none');
                    if (inputBizName) inputBizName.focus();
                    const q = inputBizName ? inputBizName.value.trim() : '';
                    if (q.length >= 2) fetchLocationSuggestions(q);
                });
            }
            document.addEventListener('click', function(e) {
                if (!bizLocSuggestEl || bizLocSuggestEl.classList.contains('d-none')) return;
                if (e.target.closest('#bizLocationSuggest') || e.target.closest('#input_business_name')) return;
                hideSuggestDropdown();
            });
            const mockBizName = document.getElementById('mockBizName');
            const mockSearchText = document.getElementById('mockSearchText');

            const inputCategorySearch = document.getElementById('input_category_search');
            const inputCategoryId = document.getElementById('input_category_id');
            const categorySuggestions = document.getElementById('categorySuggestions');
            const mockBizCategory = document.getElementById('mockBizCategory');

            const inputStreet = document.getElementById('input_address_street');
            const inputCity = document.getElementById('input_address_city');
            const inputProvince = document.getElementById('input_address_province');
            const mockBizAddress = document.getElementById('mockBizAddress');

            const inputPhone = document.getElementById('input_phone');
            const inputWebsite = document.getElementById('input_website');
            const mockBizPhone = document.getElementById('mockBizPhone');
            const mockBizWebsite = document.getElementById('mockBizWebsite');
            const mockBizWebsiteRow = document.getElementById('mockBizWebsiteRow');

            const inputDesc = document.getElementById('input_description');
            const mockBizDesc = document.getElementById('mockBizDesc');
            const descCharCount = document.getElementById('descCharCount');

            // Image uploading & Verification states
            let menuPhotos = [];
            let avatarPhoto = null;
            let businessDocs = [];
            let verificationPhotos = [];
            let verificationPhotoData = null;
            let verificationLat = null;
            let verificationLng = null;
            let verificationTime = null;
            let cameraStream = null;
            let cameraFacingMode = 'environment';
            let activeUploadsCount = 0;
            const VERIFICATION_MAX_DISTANCE_M = 500;
            let verificationDistanceM = null;

            function getMapPinCoords() {
                const lat = parseFloat(document.getElementById('input_lat')?.value);
                const lng = parseFloat(document.getElementById('input_lng')?.value);
                if (Number.isFinite(lat) && Number.isFinite(lng)) {
                    return { lat, lng };
                }
                if (claimLocationMeta && Number.isFinite(Number(claimLocationMeta.lat)) && Number.isFinite(Number(claimLocationMeta.lng))) {
                    return { lat: Number(claimLocationMeta.lat), lng: Number(claimLocationMeta.lng) };
                }
                return null;
            }

            function isVerificationTooFar() {
                return Number.isFinite(verificationDistanceM) && verificationDistanceM > VERIFICATION_MAX_DISTANCE_M;
            }

            function formatVerificationDistance(dist) {
                if (dist >= 1000) return (dist / 1000).toFixed(1) + 'km';
                return Math.round(dist) + 'm';
            }

            function setVerificationDistanceGate(dist) {
                verificationDistanceM = Number.isFinite(dist) ? dist : null;
                const tooFar = isVerificationTooFar();
                const startBtn = document.getElementById('btnStartCamera');
                const captureBtn = document.getElementById('btnCapturePhoto');
                const captureFsBtn = document.getElementById('btnCapturePhotoFs');
                const fallbackInput = document.getElementById('inputFallbackVerificationPhoto');
                const switchBtn = document.getElementById('btnSwitchCamera');
                const fsBtn = document.getElementById('btnFullscreenCamera');
                const videoEl = document.getElementById('cameraVideo');
                const placeholderEl = document.getElementById('cameraPlaceholder');

                if (startBtn) {
                    startBtn.disabled = tooFar;
                    startBtn.classList.toggle('disabled', tooFar);
                    startBtn.title = tooFar
                        ? 'Bạn đang cách vị trí được ghim quá xa; chỉ có thể xác thực trong khoảng 500m'
                        : '';
                }
                if (captureBtn) {
                    captureBtn.disabled = tooFar;
                    captureBtn.style.opacity = tooFar ? '0.45' : '';
                    captureBtn.style.pointerEvents = tooFar ? 'none' : '';
                }
                if (captureFsBtn) {
                    captureFsBtn.disabled = tooFar;
                    captureFsBtn.style.opacity = tooFar ? '0.45' : '';
                    captureFsBtn.style.pointerEvents = tooFar ? 'none' : '';
                }
                if (fallbackInput) fallbackInput.disabled = tooFar;

                if (tooFar && cameraStream) {
                    cameraStream.getTracks().forEach((track) => track.stop());
                    cameraStream = null;
                    if (videoEl) {
                        videoEl.srcObject = null;
                        videoEl.classList.add('d-none');
                    }
                    if (placeholderEl) placeholderEl.classList.remove('d-none');
                    if (captureBtn) captureBtn.classList.add('d-none');
                    if (switchBtn) switchBtn.classList.add('d-none');
                    if (fsBtn) fsBtn.classList.add('d-none');
                }

                if (bizNextBtn && bizStep === 11) {
                    if (tooFar) {
                        bizNextBtn.disabled = true;
                        bizNextBtn.classList.add('is-locked');
                        bizNextBtn.setAttribute('title', 'Bạn đang cách vị trí được ghim quá xa; chỉ có thể xác thực trong khoảng 500m');
                    }
                }
            }


            function updateNextButtonState() {
                if (!bizNextBtn) return;
                const spinners = document.querySelectorAll('.uploader-spinner');
                const isUploading = activeUploadsCount > 0 || spinners.length > 0;
                const tooFarOnVerify = bizStep === 11 && isVerificationTooFar();
                if (isUploading || tooFarOnVerify) {
                    bizNextBtn.disabled = true;
                    bizNextBtn.classList.add('is-locked');
                    bizNextBtn.setAttribute(
                        'title',
                        tooFarOnVerify
                            ? 'Bạn đang cách vị trí được ghim quá xa; chỉ có thể xác thực trong khoảng 500m'
                            : 'Đang tải lên hình ảnh, vui lòng chờ tất cả ảnh load xong...'
                    );
                } else {
                    bizNextBtn.disabled = false;
                    bizNextBtn.classList.remove('is-locked');
                    bizNextBtn.removeAttribute('title');
                }
            }

            setInterval(updateNextButtonState, 250);

            // Render Verification Photos Gallery
            // Lightbox: view a captured photo at full size
            function openImageLightbox(src, caption) {
                let overlay = document.getElementById('bizImageLightbox');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'bizImageLightbox';
                    overlay.className = 'biz-lightbox';
                    overlay.innerHTML = `
                        <button type="button" class="biz-lightbox__close" aria-label="Đóng"><i class="bi bi-x-lg"></i></button>
                        <img class="biz-lightbox__img" src="" alt="">
                        <div class="biz-lightbox__caption"></div>
                    `;
                    document.body.appendChild(overlay);
                    const close = () => overlay.classList.remove('is-open');
                    overlay.addEventListener('click', (e) => {
                        if (e.target === overlay || e.target.closest('.biz-lightbox__close')) close();
                    });
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') close();
                    });
                }
                overlay.querySelector('.biz-lightbox__img').src = src;
                overlay.querySelector('.biz-lightbox__caption').textContent = caption || '';
                overlay.classList.add('is-open');
            }

            function renderVerificationGallery() {
                const galleryGrid = document.getElementById('verificationGalleryGrid');
                const countSpan = document.getElementById('verificationCount');
                if (!galleryGrid) return;

                if (countSpan) countSpan.innerText = verificationPhotos.length;

                if (verificationPhotos.length === 0) {
                    galleryGrid.innerHTML = '<div class="text-muted small py-2 w-100 text-center" id="verificationEmptyText">Chưa có ảnh nào được chụp/tải lên.</div>';
                    return;
                }

                galleryGrid.innerHTML = '';
                verificationPhotos.forEach((src, idx) => {
                    const item = document.createElement('div');
                    item.className = 'preview-thumbnail position-relative';
                    item.style.cssText = 'width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; position: relative; display: inline-block;';

                    const imgSrc = (src.startsWith('data:') || src.startsWith('http') || src.startsWith('/storage')) ? src : `/storage/${src}`;
                    item.style.cursor = 'zoom-in';
                    item.title = 'Bấm để xem ảnh lớn';
                    item.innerHTML = `
                        <img src="${imgSrc}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 start-0 m-1" style="font-size: 0.6rem; padding: 1px 4px;">#${idx + 1}</span>
                    `;
                    item.addEventListener('click', () => openImageLightbox(imgSrc, `Ảnh thực địa #${idx + 1}`));

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'preview-remove-btn';
                    removeBtn.innerHTML = '✕';
                    removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        verificationPhotos.splice(idx, 1);
                        if (verificationPhotos.length > 0) {
                            verificationPhotoData = verificationPhotos[0];
                        } else {
                            verificationPhotoData = null;
                        }
                        renderVerificationGallery();
                        saveWizardState();
                    });
                    item.appendChild(removeBtn);
                    galleryGrid.appendChild(item);
                });
            }

            // Haversine distance calculator in meters
            function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return Math.round(R * c);
            }

            // Leaflet Mini Map state vars
            let verificationMap = null;
            let verificationDeviceMarker = null;
            let verificationBizMarker = null;
            let verificationPolyline = null;

            function renderVerificationMiniMap(lat, lng) {
                const mapContainer = document.getElementById('verificationMiniMap');
                if (!mapContainer || typeof L === 'undefined') return;

                const mapLat = parseFloat(document.getElementById('input_lat')?.value);
                const mapLng = parseFloat(document.getElementById('input_lng')?.value);

                setTimeout(() => {
                    if (!verificationMap) {
                        verificationMap = L.map('verificationMiniMap', {
                            center: [lat, lng],
                            zoom: 16,
                            zoomControl: true,
                            attributionControl: false
                        });

                        L.tileLayer(@json(config('services.carto.tile_url')), {
                            subdomains: 'abcd',
                            maxZoom: 19
                        }).addTo(verificationMap);
                    } else {
                        verificationMap.setView([lat, lng], 16);
                        verificationMap.invalidateSize();
                    }

                    // Device GPS Marker (Blue pulsing pin)
                    if (verificationDeviceMarker) verificationMap.removeLayer(verificationDeviceMarker);
                    const deviceIcon = L.divIcon({
                        className: 'custom-gps-pin',
                        html: `<div style="background:#0070ff; width:16px; height:16px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 10px rgba(0,112,255,0.7);"></div>`,
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });
                    verificationDeviceMarker = L.marker([lat, lng], { icon: deviceIcon }).addTo(verificationMap)
                        .bindPopup(`<b>Vị trí GPS chụp ảnh</b><br>${lat.toFixed(6)}, ${lng.toFixed(6)}`);

                    // Business Pinned Location from Step 6 if exists
                    if (mapLat && mapLng && !isNaN(mapLat) && !isNaN(mapLng)) {
                        if (verificationBizMarker) verificationMap.removeLayer(verificationBizMarker);

                        const bizIcon = L.divIcon({
                            className: 'custom-biz-pin',
                            html: `<div style="background:#10b981; width:16px; height:16px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 10px rgba(16,185,129,0.7);"></div>`,
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        });

                        verificationBizMarker = L.marker([mapLat, mapLng], { icon: bizIcon }).addTo(verificationMap)
                            .bindPopup(`<b>Vị trí ghim ở Bước 6</b><br>${mapLat.toFixed(6)}, ${mapLng.toFixed(6)}`);

                        if (verificationPolyline) verificationMap.removeLayer(verificationPolyline);
                        verificationPolyline = L.polyline([[lat, lng], [mapLat, mapLng]], {
                            color: '#3b82f6',
                            dashArray: '4, 8',
                            weight: 2
                        }).addTo(verificationMap);

                        const bounds = L.latLngBounds([[lat, lng], [mapLat, mapLng]]);
                        verificationMap.fitBounds(bounds, { padding: [30, 30] });
                    }
                    verificationMap.invalidateSize();
                }, 150);
            }

            // Real-time GPS Location Fetcher for Verification (cùng chiến lược với bước 6)
            let verificationGpsInProgress = false;
            let verificationGpsRequestId = 0;

            function applyVerificationGpsPosition(position) {
                const statusTitle = document.getElementById('gpsStatusTitle');

                verificationLat = position.coords.latitude;
                verificationLng = position.coords.longitude;
                verificationTime = new Date().toISOString();

                if (statusTitle) statusTitle.innerText = `GPS Đã Khóa: ${verificationLat.toFixed(6)}, ${verificationLng.toFixed(6)}`;

                const distanceBadge = document.getElementById('gpsDistanceBadge');
                const distanceText = document.getElementById('gpsDistanceText');
                const pin = getMapPinCoords();
                if (pin && distanceBadge && distanceText) {
                    const dist = calculateDistanceMeters(verificationLat, verificationLng, pin.lat, pin.lng);
                    distanceBadge.classList.remove('d-none');
                    if (dist <= 100) {
                        distanceText.style.cssText = 'background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-weight:600; padding:6px 10px;';
                        distanceText.innerHTML = `Trùng khớp vị trí bản đồ (lệch ${Math.round(dist)}m)`;
                    } else if (dist <= VERIFICATION_MAX_DISTANCE_M) {
                        distanceText.style.cssText = 'background:#fef3c7; color:#92400e; border:1px solid #fcd34d; font-weight:600; padding:6px 10px;';
                        distanceText.innerHTML = `Bạn đang cách ${Math.round(dist)}m vị trí được ghim`;
                    } else {
                        distanceText.style.cssText = 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; font-weight:600; padding:6px 10px;';
                        distanceText.innerHTML = `Bạn đang cách ${formatVerificationDistance(dist)} vị trí được ghim; chỉ có thể xác thực trong khoảng ${VERIFICATION_MAX_DISTANCE_M}m`;
                    }
                    setVerificationDistanceGate(dist);
                } else {
                    setVerificationDistanceGate(null);
                }

                renderVerificationMiniMap(verificationLat, verificationLng);
                saveWizardState();
            }

            function showVerificationGpsError(err, permissionState) {
                const statusTitle = document.getElementById('gpsStatusTitle');
                let title = 'Chưa lấy được tọa độ GPS. Thử nhấn Lấy lại GPS.';

                if (err && err.code === 1) {
                    if (permissionState === 'granted') {
                        title = 'Chưa lấy được tín hiệu GPS. Bật Vị trí trong Cài đặt hệ thống rồi nhấn Lấy lại GPS.';
                    } else if (permissionState === 'denied') {
                        title = 'Trình duyệt đang chặn Vị trí. Cho phép Vị trí rồi nhấn Lấy lại GPS.';
                    } else {
                        title = 'Hãy cho phép quyền Vị trí khi trình duyệt hỏi, rồi nhấn Lấy lại GPS.';
                    }
                } else if (err && err.code === 3) {
                    title = 'Định vị quá lâu. Thử lại sau vài giây.';
                } else if (err && err.code === 2) {
                    title = 'Chưa có tín hiệu GPS. Đợi vài giây rồi nhấn Lấy lại GPS.';
                }

                if (statusTitle) statusTitle.innerText = title;
            }

            async function fetchVerificationGPS() {
                const statusTitle = document.getElementById('gpsStatusTitle');
                const btn = document.getElementById('btnRefreshGPS');

                if (!navigator.geolocation) {
                    if (statusTitle) statusTitle.innerText = 'Trình duyệt không hỗ trợ Geolocation';
                    return;
                }
                if (verificationGpsInProgress) return;

                verificationGpsInProgress = true;
                const requestId = ++verificationGpsRequestId;
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('is-loading');
                }
                if (statusTitle) statusTitle.innerText = 'Đang định vị GPS...';
                showLocateStatusToast('Đang lấy vị trí...');

                const attempts = [
                    { mode: 'get', options: { enableHighAccuracy: false, timeout: 15000, maximumAge: 60000 } },
                    { mode: 'get', options: { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 } },
                    { mode: 'watch', options: { enableHighAccuracy: false, maximumAge: 0 }, waitMs: 22000 },
                ];

                let lastError = null;
                for (let i = 0; i < attempts.length; i++) {
                    if (requestId !== verificationGpsRequestId) return;
                    try {
                        if (i > 0) {
                            await new Promise((r) => setTimeout(r, 500));
                        }

                        const attempt = attempts[i];
                        const position = attempt.mode === 'watch'
                            ? await requestBizMapPositionWatch(attempt.options, attempt.waitMs)
                            : await requestBizMapPosition(attempt.options);

                        if (requestId !== verificationGpsRequestId) return;
                        dismissLocateStatusToast();
                        applyVerificationGpsPosition(position);
                        showToast('Đã lấy vị trí GPS xác thực.', true);
                        verificationGpsInProgress = false;
                        if (btn) {
                            btn.disabled = false;
                            btn.classList.remove('is-loading');
                        }
                        return;
                    } catch (err) {
                        lastError = err;
                        console.warn('Verification GPS attempt ' + (i + 1) + ' failed:', err);
                    }
                }

                if (requestId !== verificationGpsRequestId) return;
                dismissLocateStatusToast();
                const permissionState = await getBizGeolocationPermissionState();
                showVerificationGpsError(lastError, permissionState);
                verificationGpsInProgress = false;
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('is-loading');
                }
            }

            // Camera Controls & Handlers
            const btnStartCamera = document.getElementById('btnStartCamera');
            const btnCapturePhoto = document.getElementById('btnCapturePhoto');
            const btnSwitchCamera = document.getElementById('btnSwitchCamera');
            const btnFullscreenCamera = document.getElementById('btnFullscreenCamera');
            const btnRefreshGPS = document.getElementById('btnRefreshGPS');
            const cameraVideo = document.getElementById('cameraVideo');
            const cameraCanvas = document.getElementById('cameraCanvas');
            const cameraPlaceholder = document.getElementById('cameraPlaceholder');
            const capturedPreviewContainer = document.getElementById('capturedPreviewContainer');
            const capturedImagePreview = document.getElementById('capturedImagePreview');
            if (capturedImagePreview) {
                capturedImagePreview.addEventListener('click', function() {
                    if (this.src) openImageLightbox(this.src, 'Ảnh vừa chụp');
                });
            }
            const watermarkLocation = document.getElementById('watermarkLocation');
            const watermarkTime = document.getElementById('watermarkTime');
            const btnToggleFallbackUpload = document.getElementById('btnToggleFallbackUpload');
            const fallbackUploadContainer = document.getElementById('fallbackUploadContainer');
            const inputFallbackVerificationPhoto = document.getElementById('inputFallbackVerificationPhoto');

            async function initCameraStream() {
                if (isVerificationTooFar()) {
                    showToast('Bạn đang cách vị trí được ghim quá xa; chỉ có thể xác thực trong khoảng ' + VERIFICATION_MAX_DISTANCE_M + 'm. Hãy đến gần rồi nhấn Lấy lại GPS.', false);
                    return;
                }
                try {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(track => track.stop());
                    }
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: cameraFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false
                    });
                    if (cameraVideo) {
                        cameraVideo.srcObject = cameraStream;
                        cameraVideo.classList.remove('d-none');
                    }
                    if (cameraPlaceholder) cameraPlaceholder.classList.add('d-none');
                    if (btnCapturePhoto) btnCapturePhoto.classList.remove('d-none');
                    if (btnSwitchCamera) btnSwitchCamera.classList.remove('d-none');
                    if (btnFullscreenCamera) btnFullscreenCamera.classList.remove('d-none');
                    
                    fetchVerificationGPS();
                } catch (err) {
                    console.error('Camera access error:', err);
                    showToast('Không thể mở Camera. Vui lòng cấp quyền truy cập Camera trên trình duyệt thiết bị của bạn.', false);
                }
            }

            if (btnStartCamera) {
                btnStartCamera.addEventListener('click', initCameraStream);
            }

            if (btnSwitchCamera) {
                btnSwitchCamera.addEventListener('click', function() {
                    cameraFacingMode = (cameraFacingMode === 'environment') ? 'user' : 'environment';
                    initCameraStream();
                });
            }

            if (btnFullscreenCamera) {
                btnFullscreenCamera.addEventListener('click', function() {
                    const box = document.querySelector('.camera-verification-box');
                    const target = box || cameraVideo;
                    if (!target) return;
                    const req = target.requestFullscreen || target.webkitRequestFullscreen || target.msRequestFullscreen;
                    if (req) {
                        req.call(target).catch(function() {
                            if (cameraVideo && cameraVideo.webkitEnterFullscreen) cameraVideo.webkitEnterFullscreen();
                        });
                    } else if (cameraVideo && cameraVideo.webkitEnterFullscreen) {
                        cameraVideo.webkitEnterFullscreen();
                    }
                });
            }

            // Fullscreen overlay controls reuse the main handlers
            const btnCapturePhotoFs = document.getElementById('btnCapturePhotoFs');
            const btnSwitchCameraFs = document.getElementById('btnSwitchCameraFs');
            const btnExitFullscreen = document.getElementById('btnExitFullscreen');
            if (btnCapturePhotoFs) {
                btnCapturePhotoFs.addEventListener('click', function() {
                    if (btnCapturePhoto) btnCapturePhoto.click();
                });
            }
            if (btnSwitchCameraFs) {
                btnSwitchCameraFs.addEventListener('click', function() {
                    if (btnSwitchCamera) btnSwitchCamera.click();
                });
            }
            if (btnExitFullscreen) {
                btnExitFullscreen.addEventListener('click', function() {
                    const exit = document.exitFullscreen || document.webkitExitFullscreen || document.msExitFullscreen;
                    if (exit) exit.call(document);
                });
            }

            if (btnRefreshGPS) {
                btnRefreshGPS.addEventListener('click', function() {
                    fetchVerificationGPS();
                });
            }

            if (btnCapturePhoto) {
                btnCapturePhoto.addEventListener('click', function() {
                    if (isVerificationTooFar()) {
                        showToast('Bạn đang cách vị trí được ghim quá xa; chỉ có thể xác thực trong khoảng ' + VERIFICATION_MAX_DISTANCE_M + 'm.', false);
                        return;
                    }
                    if (!cameraVideo || cameraVideo.classList.contains('d-none')) return;

                    let width = cameraVideo.videoWidth || 640;
                    let height = cameraVideo.videoHeight || 480;

                    // Downscale canvas to max 1280px to keep payload size lightweight (~180KB instead of 8MB)
                    const MAX_DIM = 1280;
                    if (width > MAX_DIM || height > MAX_DIM) {
                        if (width > height) {
                            height = Math.round((height * MAX_DIM) / width);
                            width = MAX_DIM;
                        } else {
                            width = Math.round((width * MAX_DIM) / height);
                            height = MAX_DIM;
                        }
                    }

                    cameraCanvas.width = width;
                    cameraCanvas.height = height;

                    const ctx = cameraCanvas.getContext('2d');
                    ctx.drawImage(cameraVideo, 0, 0, width, height);

                    // Draw watermark bar
                    const barHeight = Math.max(60, Math.round(height * 0.14));
                    const gradient = ctx.createLinearGradient(0, height - barHeight, 0, height);
                    gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
                    gradient.addColorStop(1, 'rgba(0, 0, 0, 0.85)');
                    ctx.fillStyle = gradient;
                    ctx.fillRect(0, height - barHeight, width, barHeight);

                    const nowStr = new Date().toLocaleString('vi-VN');
                    const gpsStr = (verificationLat && verificationLng)
                        ? `GPS: ${verificationLat.toFixed(6)}, ${verificationLng.toFixed(6)}`
                        : 'GPS: Chưa có vị trí';

                    const fontSize1 = Math.max(13, Math.round(width * 0.026));
                    const fontSize2 = Math.max(11, Math.round(width * 0.022));

                    ctx.fillStyle = '#ffffff';
                    ctx.font = `bold ${fontSize1}px "Be Vietnam Pro", sans-serif`;
                    ctx.fillText(`XÁC THỰC THỰC ĐỊA • ${nowStr}`, 16, height - Math.round(barHeight * 0.5));
                    ctx.font = `${fontSize2}px "Be Vietnam Pro", sans-serif`;
                    ctx.fillText(gpsStr, 16, height - Math.round(barHeight * 0.2));

                    const capturedData = cameraCanvas.toDataURL('image/jpeg', 0.72);
                    verificationPhotos.push(capturedData);
                    verificationPhotoData = verificationPhotos[0];
                    if (!verificationTime) verificationTime = new Date().toISOString();

                    if (capturedPreviewContainer) {
                        capturedPreviewContainer.classList.remove('d-none');
                        capturedImagePreview.src = capturedData;
                    }

                    if (watermarkLocation) watermarkLocation.innerHTML = `<i class="bi bi-geo-alt me-1"></i>${gpsStr}`;
                    if (watermarkTime) watermarkTime.innerHTML = `<i class="bi bi-clock me-1"></i>Thời gian: ${nowStr}`;

                    renderVerificationGallery();
                    saveWizardState();
                    showToast(`Đã thêm ảnh xác thực thứ ${verificationPhotos.length}! Có thể chụp tiếp góc khác.`, true);
                });
            }

            // State Persistence Functions
            // Bản nháp gắn theo user — không dùng chung key trên trình duyệt khi đổi tài khoản
            const BIZ_USER_ID = {{ (int) auth()->id() }};
            const BIZ_STATE_KEY = 'biz_wizard_state_u' + BIZ_USER_ID;
            const BIZ_LEGACY_STATE_KEY = 'biz_wizard_state';
            function purgeForeignBizDrafts() {
                try {
                    localStorage.removeItem(BIZ_LEGACY_STATE_KEY);
                } catch (e) {}
            }
            // ---- IndexedDB persistence for large base64 camera photos (localStorage would exceed quota) ----
            const IDB_NAME = 'biz_wizard_db';
            const IDB_STORE = 'photos';
            const IDB_PHOTO_KEY = 'u' + BIZ_USER_ID;
            function idbOpen() {
                return new Promise((resolve, reject) => {
                    const req = indexedDB.open(IDB_NAME, 1);
                    req.onupgradeneeded = () => {
                        const db = req.result;
                        if (!db.objectStoreNames.contains(IDB_STORE)) db.createObjectStore(IDB_STORE);
                    };
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => reject(req.error);
                });
            }
            async function idbPutPhotos(data) {
                try {
                    const db = await idbOpen();
                    await new Promise((resolve, reject) => {
                        const tx = db.transaction(IDB_STORE, 'readwrite');
                        tx.objectStore(IDB_STORE).put(data, IDB_PHOTO_KEY);
                        tx.objectStore(IDB_STORE).delete('current');
                        tx.oncomplete = () => resolve();
                        tx.onerror = () => reject(tx.error);
                    });
                    db.close();
                } catch (e) { console.warn('IDB save failed:', e); }
            }
            async function idbGetPhotos() {
                try {
                    const db = await idbOpen();
                    const val = await new Promise((resolve, reject) => {
                        const tx = db.transaction(IDB_STORE, 'readonly');
                        const r = tx.objectStore(IDB_STORE).get(IDB_PHOTO_KEY);
                        r.onsuccess = () => resolve(r.result);
                        r.onerror = () => reject(r.error);
                    });
                    db.close();
                    return val || null;
                } catch (e) { console.warn('IDB load failed:', e); return null; }
            }
            async function idbClearPhotos() {
                try {
                    const db = await idbOpen();
                    await new Promise((resolve) => {
                        const tx = db.transaction(IDB_STORE, 'readwrite');
                        tx.objectStore(IDB_STORE).delete(IDB_PHOTO_KEY);
                        tx.objectStore(IDB_STORE).delete('current');
                        tx.oncomplete = () => resolve();
                        tx.onerror = () => resolve();
                    });
                    db.close();
                } catch (e) {}
            }
            let _idbSaveTimer = null;
            function saveVerificationPhotosDebounced() {
                clearTimeout(_idbSaveTimer);
                _idbSaveTimer = setTimeout(() => {
                    idbPutPhotos({
                        verificationPhotos: verificationPhotos || [],
                        verificationLat: typeof verificationLat !== 'undefined' ? verificationLat : null,
                        verificationLng: typeof verificationLng !== 'undefined' ? verificationLng : null,
                        verificationTime: typeof verificationTime !== 'undefined' ? verificationTime : null
                    });
                }, 300);
            }

            function saveWizardState() {
                try {
                    // Base64 camera photos are stored in IndexedDB (below), not localStorage, to avoid quota overflow
                    const cleanVerificationPhotos = (verificationPhotos || []).filter(p => typeof p === 'string' && !p.startsWith('data:'));

                    const selectedCategorySpan = document.querySelector('#custom_category_select .selected-value');
                    const categoryNameVal = selectedCategorySpan ? selectedCategorySpan.textContent : '';

                    const state = {
                        userId: BIZ_USER_ID,
                        bizStep: bizStep,
                        claimLocationId: claimLocationId || null,
                        claimLocationMeta: claimLocationMeta || null,
                        businessName: inputBizName ? inputBizName.value.trim() : '',
                        businessTypes: Array.from(document.querySelectorAll('.biz-type-card.selected')).map(c => c.getAttribute('data-val')),
                        categoryId: inputCategoryId ? inputCategoryId.value : '',
                        categoryName: categoryNameVal,
                        categorySearchName: inputCategorySearch ? inputCategorySearch.value : '',
                        addressStreet: inputStreet ? inputStreet.value.trim() : '',
                        addressCity: inputCity ? inputCity.value.trim() : '',
                        addressProvince: inputProvince ? inputProvince.value.trim() : '',
                        addressPostalCode: document.getElementById('input_address_postal_code') ? document.getElementById('input_address_postal_code').value.trim() : '',
                        phone: inputPhone ? inputPhone.value.trim() : '',
                        website: inputWebsite ? inputWebsite.value.trim() : '',
                        lat: document.getElementById('input_lat') ? document.getElementById('input_lat').value : '',
                        lng: document.getElementById('input_lng') ? document.getElementById('input_lng').value : '',
                        receiveTips: document.getElementById('receive_tips') ? document.getElementById('receive_tips').checked : false,
                        receiveSurveys: document.getElementById('receive_surveys') ? document.getElementById('receive_surveys').checked : false,
                        description: inputDesc ? inputDesc.value.trim() : '',
                        menuPhotos: menuPhotos || [],
                        avatarPhoto: avatarPhoto || null,
                        businessDocs: businessDocs || [],
                        verificationPhotos: cleanVerificationPhotos,
                        verificationLat: typeof verificationLat !== 'undefined' ? verificationLat : null,
                        verificationLng: typeof verificationLng !== 'undefined' ? verificationLng : null,
                        verificationTime: typeof verificationTime !== 'undefined' ? verificationTime : null
                    };
                    localStorage.setItem(BIZ_STATE_KEY, JSON.stringify(state));
                    localStorage.removeItem(BIZ_LEGACY_STATE_KEY);
                } catch (e) {
                    console.warn('Could not save biz wizard state:', e);
                }
                // Persist large base64 camera photos separately in IndexedDB so they survive reload
                saveVerificationPhotosDebounced();
            }

            // Restore base64 camera photos (+ GPS metadata) from IndexedDB after reload
            async function restoreVerificationPhotosFromIDB() {
                let data = null;
                try {
                    data = await idbGetPhotos();
                } catch (e) {
                    console.warn('Không đọc được ảnh thực địa đã lưu:', e);
                }
                if (!data) return;

                try {
                    if (Array.isArray(data.verificationPhotos) && data.verificationPhotos.length) {
                        verificationPhotos.length = 0;
                        data.verificationPhotos.forEach(p => verificationPhotos.push(p));
                        verificationPhotoData = verificationPhotos[0] || null;
                        renderVerificationGallery();
                        if (verificationPhotoData && capturedImagePreview && capturedPreviewContainer) {
                            capturedImagePreview.src = verificationPhotoData;
                            capturedPreviewContainer.classList.remove('d-none');
                        }
                    }
                    if (data.verificationLat) verificationLat = data.verificationLat;
                    if (data.verificationLng) verificationLng = data.verificationLng;
                    if (data.verificationTime) verificationTime = data.verificationTime;
                    if (verificationLat && verificationLng && watermarkLocation) {
                        watermarkLocation.innerText = 'Tọa độ GPS: ' + Number(verificationLat).toFixed(6) + ', ' + Number(verificationLng).toFixed(6);
                    }
                    if (verificationTime && watermarkTime) {
                        watermarkTime.innerText = 'Thời gian: ' + new Date(verificationTime).toLocaleString('vi-VN');
                    }
                } catch (e) {
                    console.warn('Không khôi phục được ảnh thực địa:', e);
                }
            }

            function loadWizardState() {
                purgeForeignBizDrafts();
                const raw = localStorage.getItem(BIZ_STATE_KEY);
                if (!raw) return;

                let state;
                try {
                    state = JSON.parse(raw);
                } catch (e) {
                    console.error('Error parsing wizard state:', e);
                    return;
                }

                if (state.userId && Number(state.userId) !== Number(BIZ_USER_ID)) {
                    localStorage.removeItem(BIZ_STATE_KEY);
                    return;
                }

                // Mỗi phần khôi phục chạy độc lập: một phần lỗi không làm mất các phần còn lại
                const restore = (label, fn) => {
                    try {
                        fn();
                    } catch (e) {
                        console.warn('Không khôi phục được "' + label + '":', e);
                    }
                };

                restore('bước hiện tại', () => {
                    if (state.bizStep && !isNaN(state.bizStep)) {
                        bizStep = parseInt(state.bizStep);
                    }
                    if (!getActiveStepList().includes(bizStep)) {
                        bizStep = getActiveStepList()[0] || 1;
                    }
                });

                restore('địa điểm nhận quyền', () => {
                    if (state.claimLocationId && state.claimLocationMeta) {
                        claimLocationId = state.claimLocationId;
                        claimLocationMeta = state.claimLocationMeta;
                        if (inputClaimLocationId) inputClaimLocationId.value = claimLocationId;
                        if (bizClaimChip) bizClaimChip.classList.remove('d-none');
                        if (bizClaimPreview) bizClaimPreview.classList.add('d-none');
                        if (bizClaimHint) bizClaimHint.classList.add('d-none');
                        if (bizClaimName) bizClaimName.textContent = claimLocationMeta.name || '';
                        if (bizClaimAddr) {
                            bizClaimAddr.textContent = [claimLocationMeta.address, claimLocationMeta.category]
                                .filter(Boolean).join(' · ');
                        }
                        if (isClaimMode() && !claimBizSteps.includes(bizStep)) {
                            bizStep = 1;
                        }
                    }
                });

                restore('tên doanh nghiệp', () => {
                    if (state.businessName) {
                        if (inputBizName) inputBizName.value = state.businessName;
                        if (mockBizName) mockBizName.innerText = state.businessName;
                        if (mockSearchText) mockSearchText.innerText = state.businessName;
                    }
                });

                restore('loại hình kinh doanh', () => {
                    if (state.businessTypes && Array.isArray(state.businessTypes)) {
                        document.querySelectorAll('.biz-type-card').forEach(card => {
                            const val = card.getAttribute('data-val');
                            if (state.businessTypes.includes(val)) {
                                card.classList.add('selected');
                            } else {
                                card.classList.remove('selected');
                            }
                        });
                        const inputTypes = document.getElementById('input_business_types');
                        if (inputTypes) inputTypes.value = JSON.stringify(state.businessTypes);
                    }
                });

                restore('danh mục', () => {
                    if (state.categoryId) {
                        if (inputCategoryId) inputCategoryId.value = state.categoryId;
                        const catName = state.categoryName || state.categorySearchName;
                        if (catName && catName !== '-- Chọn danh mục kinh doanh --') {
                            if (inputCategorySearch) inputCategorySearch.value = catName;
                            if (mockBizCategory) mockBizCategory.innerText = catName;

                            const selectedSpan = document.querySelector('#custom_category_select .selected-value');
                            if (selectedSpan) {
                                selectedSpan.textContent = catName;
                                selectedSpan.style.color = 'var(--text-main)';
                            }

                            const customSelectDropdown = document.getElementById('custom_category_dropdown');
                            if (customSelectDropdown) {
                                customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(opt => {
                                    if (opt.getAttribute('data-value') === String(state.categoryId) || opt.getAttribute('data-name') === catName) {
                                        opt.classList.add('selected');
                                        const parentGroup = opt.closest('.dropdown-options-group');
                                        if (parentGroup) {
                                            parentGroup.classList.remove('d-none');
                                            const groupId = parentGroup.getAttribute('id');
                                            const groupHeader = customSelectDropdown.querySelector(`.dropdown-group-header[data-target="${groupId}"]`);
                                            if (groupHeader) {
                                                groupHeader.classList.add('active');
                                            }
                                        }
                                    } else {
                                        opt.classList.remove('selected');
                                    }
                                });
                            }
                        }
                    }
                });

                restore('địa chỉ', () => {
                    if (state.addressStreet && inputStreet) inputStreet.value = state.addressStreet;
                    if (state.addressCity && inputCity) inputCity.value = state.addressCity;
                    if (state.addressProvince && inputProvince) inputProvince.value = state.addressProvince;
                    const postalEl = document.getElementById('input_address_postal_code');
                    if (state.addressPostalCode && postalEl) {
                        postalEl.value = state.addressPostalCode;
                    }
                    updateMockAddress();
                });

                restore('số điện thoại', () => {
                    if (state.phone) {
                        if (inputPhone) inputPhone.value = state.phone;
                        if (mockBizPhone) mockBizPhone.innerText = state.phone;
                    }
                });

                restore('website', () => {
                    if (state.website) {
                        if (inputWebsite) inputWebsite.value = state.website;
                        if (mockBizWebsite) mockBizWebsite.innerText = state.website;
                        if (mockBizWebsiteRow) mockBizWebsiteRow.classList.remove('d-none');
                    }
                });

                restore('vị trí bản đồ', () => {
                    const latEl = document.getElementById('input_lat');
                    const lngEl = document.getElementById('input_lng');
                    if (state.lat && state.lng) {
                        if (latEl) latEl.value = state.lat;
                        if (lngEl) lngEl.value = state.lng;
                    }
                });

                restore('tùy chọn nhận thông tin', () => {
                    const tipsEl = document.getElementById('receive_tips');
                    const surveysEl = document.getElementById('receive_surveys');
                    if (state.hasOwnProperty('receiveTips') && tipsEl) tipsEl.checked = state.receiveTips;
                    if (state.hasOwnProperty('receiveSurveys') && surveysEl) surveysEl.checked = state.receiveSurveys;
                });

                restore('mô tả', () => {
                    if (state.description) {
                        if (inputDesc) inputDesc.value = state.description;
                        if (mockBizDesc) mockBizDesc.innerText = state.description;
                        if (descCharCount) descCharCount.innerText = state.description.length + ' / 750';
                    }
                });

                restore('ảnh địa điểm', () => {
                    if (state.menuPhotos && Array.isArray(state.menuPhotos)) {
                        menuPhotos.length = 0;
                        state.menuPhotos.slice(0, 10).forEach(p => menuPhotos.push(p));
                        restorePhotoPreviews('menuPreviews', menuPhotos, 10);
                    }
                });

                restore('ảnh đại diện', () => {
                    if (state.avatarPhoto) {
                        avatarPhoto = state.avatarPhoto;
                        restoreAvatarPreview();
                    }
                });

                restore('giấy tờ doanh nghiệp', () => {
                    if (state.businessDocs && Array.isArray(state.businessDocs)) {
                        businessDocs.length = 0;
                        state.businessDocs.slice(0, 10).forEach(p => businessDocs.push(p));
                        restorePhotoPreviews('docPreviews', businessDocs, 10);
                    }
                });

                restore('ảnh bằng chứng', () => {
                    if (state.verificationPhotos && Array.isArray(state.verificationPhotos)) {
                        verificationPhotos.length = 0;
                        state.verificationPhotos.forEach(p => verificationPhotos.push(p));
                        renderVerificationGallery();
                    }
                    if (state.verificationLat) verificationLat = state.verificationLat;
                    if (state.verificationLng) verificationLng = state.verificationLng;
                    if (state.verificationTime) verificationTime = state.verificationTime;
                });

                restore('ảnh xem trước', () => {
                    updateMockPhotosGrid();
                });
            }

            function clearWizardState() {
                try {
                    localStorage.removeItem(BIZ_STATE_KEY);
                    localStorage.removeItem(BIZ_LEGACY_STATE_KEY);
                } catch (e) {}
            }

            function restorePhotoPreviews(containerId, photosArray, maxFiles = 10) {
                const container = document.getElementById(containerId);
                if (!container) return;
                container.innerHTML = '';
                
                if (photosArray.length > maxFiles) {
                    photosArray.splice(maxFiles);
                }
                
                photosArray.forEach(path => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-thumbnail';
                    previewItem.innerHTML = `
                        <img src="/storage/${path}" style="display:block;">
                    `;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'preview-remove-btn';
                    removeBtn.innerHTML = '✕';
                    removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        previewItem.remove();
                        
                        const index = photosArray.indexOf(path);
                        if (index > -1) {
                            photosArray.splice(index, 1);
                        }
                        updateMockPhotosGrid();
                        saveWizardState();
                    });
                    previewItem.appendChild(removeBtn);
                    container.appendChild(previewItem);
                });
            }

            // Update Step 8 text based on category
            function updateStep8Text(categoryName) {
                const s8Title = document.getElementById('step8Title');
                const s8Desc = document.getElementById('step8Desc');
                const s8Icon = document.getElementById('step8Icon');
                const s8Text = document.getElementById('step8Text');
                
                if (!s8Title || !s8Desc) return;
                
                const name = (categoryName || '').toLowerCase();
                if (name.includes('ẩm thực') || name.includes('ăn uống') || name.includes('nhà hàng') || name.includes('quán')) {
                    s8Title.innerText = "Thêm ảnh về địa điểm của bạn *";
                    s8Desc.innerText = "Ảnh mặt tiền, không gian, món ăn và thực đơn... giúp khách hình dung rõ về quán của bạn.";
                    if (s8Text) s8Text.innerText = "Kéo ảnh mặt tiền, không gian, món ăn vào đây";
                } else if (name.includes('lưu trú') || name.includes('khách sạn') || name.includes('nhà nghỉ') || name.includes('homestay') || name.includes('resort')) {
                    s8Title.innerText = "Thêm ảnh về địa điểm của bạn *";
                    s8Desc.innerText = "Ảnh mặt tiền, phòng nghỉ, không gian và dịch vụ... giúp khách quyết định đặt phòng.";
                    if (s8Text) s8Text.innerText = "Kéo ảnh mặt tiền, phòng nghỉ, dịch vụ vào đây";
                } else {
                    s8Title.innerText = "Thêm ảnh về địa điểm của bạn *";
                    s8Desc.innerText = "Ảnh mặt tiền, không gian, sản phẩm / dịch vụ và bảng giá... giúp khách hiểu rõ về bạn.";
                    if (s8Text) s8Text.innerText = "Kéo ảnh mặt tiền, không gian, dịch vụ vào đây";
                }
            }

            // Update step UI
            function updateBizStepUI() {
                if (bizStep === 8) {
                    const categoryName = (inputCategorySearch ? inputCategorySearch.value : '') || '';
                    updateStep8Text(categoryName);
                }
                bizPanes.forEach(pane => {
                    const stepNum = parseInt(pane.getAttribute('data-step'));
                    if (stepNum === bizStep) {
                        pane.classList.remove('d-none');
                    } else {
                        pane.classList.add('d-none');
                    }
                });

                // Widen the content panel on layout-heavy steps (camera + map)
                const contentPanelEl = document.querySelector('.reg-main .content-panel');
                if (contentPanelEl) {
                    contentPanelEl.classList.toggle('content-panel--wide', bizStep === 11);
                }

                // Update nodes
                const activeList = getActiveStepList();
                const activeIdx = getActiveStepIndex();
                const activeCount = getActiveStepCount();

                bizStepNodes.forEach(node => {
                    const stepNum = parseInt(node.getAttribute('data-step'));
                    const inFlow = activeList.includes(stepNum);
                    node.classList.toggle('is-claim-hidden', !inFlow);
                    if (!inFlow) return;

                    const listIdx = activeList.indexOf(stepNum);
                    if (listIdx < activeIdx) {
                        node.className = 'step-progress-node completed';
                    } else if (listIdx === activeIdx) {
                        node.className = 'step-progress-node active';
                    } else {
                        node.className = 'step-progress-node';
                    }
                });

                // Update fill line theo số bước đang dùng (claim = 4, tạo mới = 11)
                const fillPercent = activeCount <= 1 ? 0 : (activeIdx / (activeCount - 1)) * 100;
                bizStepFill.style.width = fillPercent + '%';

                // Update header step counter + name (if present)
                const regCur = document.getElementById('regStepCurrent');
                if (regCur) regCur.textContent = (activeIdx + 1);
                const regTotal = document.getElementById('regStepTotal');
                if (regTotal) regTotal.textContent = activeCount;
                const regName = document.getElementById('regStepName');
                if (regName) regName.textContent = bizStepNames[bizStep] || '';

                // Update the left step navigation (grouped phases + sub-steps)
                document.querySelectorAll('.reg-nav__sub').forEach(sub => {
                    const s = parseInt(sub.getAttribute('data-step'));
                    const visible = getActiveStepList().includes(s);
                    sub.classList.toggle('is-claim-hidden', !visible);
                    sub.classList.toggle('active', s === bizStep);
                    sub.classList.toggle('done', activeList.indexOf(s) > -1 && activeList.indexOf(s) < activeIdx);
                });
                document.querySelectorAll('.reg-nav__group').forEach(group => {
                    const subs = Array.from(group.querySelectorAll('.reg-nav__sub'))
                        .filter(sub => !sub.classList.contains('is-claim-hidden'));
                    const hasVisible = subs.length > 0;
                    group.style.display = hasVisible ? '' : 'none';
                    const phase = group.querySelector('.reg-nav__phase');
                    const stepNums = subs.map(s => parseInt(s.getAttribute('data-step')));
                    const isActive = stepNums.includes(bizStep);
                    const isDone = stepNums.length && Math.max(...stepNums.map(n => activeList.indexOf(n))) < activeIdx
                        && stepNums.every(n => activeList.indexOf(n) > -1 && activeList.indexOf(n) < activeIdx);
                    group.classList.toggle('is-open', isActive);
                    if (phase) {
                        phase.classList.toggle('active', isActive);
                        phase.classList.toggle('done', !!isDone && !isActive);
                    }
                });

                // Update buttons
                bizPrevBtn.disabled = (activeIdx === 0);
                if (activeIdx >= activeCount - 1) {
                    bizNextBtn.innerText = 'Hoàn tất & Gửi';
                } else {
                    bizNextBtn.innerText = 'Tiếp tục';
                }

                // Toggle skip button visibility
                if (bizSkipBtn) {
                    bizSkipBtn.classList.add('d-none');
                }

                updateNextButtonState();

                // If entering Map step, initialize/invalidate map
                if (bizStep === 6) {
                    initBizMap();
                }

                // If entering Camera Verification step, trigger GPS fetch & render mini map
                if (bizStep === 11) {
                    if (!verificationLat || !verificationLng) {
                        fetchVerificationGPS();
                    } else {
                        applyVerificationGpsPosition({
                            coords: {
                                latitude: verificationLat,
                                longitude: verificationLng,
                                accuracy: 0
                            }
                        });
                    }
                }

                saveWizardState();
            }

            // Leaflet Map vars & GeoJSON boundary
            let bizMap = null;
            let bizMarker = null;
            let bizLocateBtn = null;
            let bizLocateInProgress = false;
            let bizHaNamBoundaryGeoJSON = null;

            function isPointInHaNamGeoJSON(lat, lng, geojson) {
                if (!geojson) return true;
                function pointInRing(pLng, pLat, ring) {
                    let inside = false;
                    for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
                        const xi = ring[i][0], yi = ring[i][1];
                        const xj = ring[j][0], yj = ring[j][1];
                        const intersect = ((yi > pLat) !== (yj > pLat)) &&
                            (pLng < (xj - xi) * (pLat - yi) / (yj - yi) + xi);
                        if (intersect) inside = !inside;
                    }
                    return inside;
                }

                function pointInPolygon(pLng, pLat, polygon) {
                    if (!polygon || polygon.length === 0) return false;
                    if (!pointInRing(pLng, pLat, polygon[0])) return false;
                    for (let i = 1; i < polygon.length; i++) {
                        if (pointInRing(pLng, pLat, polygon[i])) return false;
                    }
                    return true;
                }

                const geom = geojson.geometry || geojson;
                if (geom.type === 'Polygon') {
                    return pointInPolygon(lng, lat, geom.coordinates);
                }
                if (geom.type === 'MultiPolygon') {
                    return geom.coordinates.some(polyCoords => pointInPolygon(lng, lat, polyCoords));
                }
                if (geojson.type === 'FeatureCollection') {
                    return geojson.features.some(f => isPointInHaNamGeoJSON(lat, lng, f));
                }
                return true;
            }

            // Map initialization matching Contribution Modal style
            function updateBizMarkerLocation(latlng) {
                const mapContainer = document.getElementById('businessMap');
                if (!mapContainer || !latlng) return false;

                const isInside = isPointInHaNamGeoJSON(latlng.lat, latlng.lng, bizHaNamBoundaryGeoJSON);
                if (!isInside) {
                    mapContainer.style.borderColor = '#ef4444';
                    mapContainer.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.18)';
                    showToast('Vị trí ngoài địa phận Ninh Bình. Vui lòng chọn lại trên bản đồ.', false);
                    return false;
                }

                mapContainer.style.borderColor = '#10b981';
                mapContainer.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.12)';
                document.getElementById('input_lat').value = latlng.lat.toFixed(6);
                document.getElementById('input_lng').value = latlng.lng.toFixed(6);
                saveWizardState();
                return true;
            }

            function applyBizMapPosition(position) {
                const latlng = L.latLng(position.coords.latitude, position.coords.longitude);
                if (bizMap && bizMarker && updateBizMarkerLocation(latlng)) {
                    bizMarker.setLatLng(latlng);
                    bizMap.setView(latlng, Math.max(bizMap.getZoom(), 16));
                    showToast('Đã lấy vị trí hiện tại của bạn.', true);
                    return true;
                }
                if (bizMap) {
                    bizMap.panTo(latlng);
                }
                return false;
            }

            async function getBizGeolocationPermissionState() {
                if (!navigator.permissions || !navigator.permissions.query) return null;
                try {
                    const result = await navigator.permissions.query({ name: 'geolocation' });
                    return result.state;
                } catch (e) {
                    return null;
                }
            }

            function showBizLocateError(err, permissionState) {
                let msg = 'Không lấy được vị trí. Thử lại hoặc kéo marker thủ công.';
                if (err && err.code === 1) {
                    if (permissionState === 'granted') {
                        msg = 'Trình duyệt đã cho phép nhưng thiết bị chưa lấy được GPS. Bật Vị trí trong Cài đặt Windows (hoặc điện thoại), rồi thử lại — hoặc kéo marker thủ công.';
                    } else if (permissionState === 'denied') {
                        msg = 'Trình duyệt đã chặn Vị trí. Bấm icon ổ khóa trên thanh địa chỉ → Cho phép Vị trí.';
                    } else {
                        msg = 'Hãy chọn Cho phép khi trình duyệt hỏi quyền Vị trí.';
                    }
                } else if (err && err.code === 3) {
                    msg = 'Định vị quá lâu. Thử lại hoặc kéo marker thủ công.';
                } else if (err && err.code === 2) {
                    msg = 'Chưa có tín hiệu GPS. Đợi vài giây rồi thử lại.';
                }
                showToast(msg, false);
            }

            function requestBizMapPosition(options) {
                return new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, options);
                });
            }

            function requestBizMapPositionWatch(options, maxWaitMs) {
                const waitMs = maxWaitMs || 25000;
                return new Promise((resolve, reject) => {
                    if (!navigator.geolocation.watchPosition) {
                        reject({ code: 2, message: 'watchPosition unavailable' });
                        return;
                    }

                    let watchId = null;
                    let settled = false;

                    const finish = (fn, value) => {
                        if (settled) return;
                        settled = true;
                        clearTimeout(timeoutId);
                        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                        fn(value);
                    };

                    const timeoutId = setTimeout(() => {
                        finish(reject, { code: 3, message: 'Timeout' });
                    }, waitMs);

                    watchId = navigator.geolocation.watchPosition(
                        (position) => finish(resolve, position),
                        (err) => {
                            if (err && err.code === 1) {
                                finish(reject, err);
                            }
                        },
                        options
                    );
                });
            }

            async function locateBizMapCurrentPosition(triggerBtn) {
                const btn = triggerBtn || bizLocateBtn;
                if (!navigator.geolocation) {
                    showToast('Trình duyệt không hỗ trợ định vị GPS.', false);
                    return;
                }
                if (bizLocateInProgress) return;

                bizLocateInProgress = true;
                if (btn) btn.classList.add('is-loading');
                showLocateStatusToast('Đang lấy vị trí...');

                const attempts = [
                    { mode: 'get', options: { enableHighAccuracy: false, timeout: 15000, maximumAge: 60000 } },
                    { mode: 'get', options: { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 } },
                    { mode: 'watch', options: { enableHighAccuracy: false, maximumAge: 0 }, waitMs: 22000 },
                ];

                let lastError = null;
                for (let i = 0; i < attempts.length; i++) {
                    try {
                        if (i > 0) {
                            await new Promise(r => setTimeout(r, 500));
                        }

                        const attempt = attempts[i];
                        const position = attempt.mode === 'watch'
                            ? await requestBizMapPositionWatch(attempt.options, attempt.waitMs)
                            : await requestBizMapPosition(attempt.options);

                        dismissLocateStatusToast();
                        if (applyBizMapPosition(position)) {
                            bizLocateInProgress = false;
                            if (btn) btn.classList.remove('is-loading');
                            return;
                        }

                        bizLocateInProgress = false;
                        if (btn) btn.classList.remove('is-loading');
                        return;
                    } catch (err) {
                        lastError = err;
                        console.warn('Geolocation attempt ' + (i + 1) + ' failed:', err);
                    }
                }

                dismissLocateStatusToast();
                const permissionState = await getBizGeolocationPermissionState();
                bizLocateInProgress = false;
                if (btn) btn.classList.remove('is-loading');
                showBizLocateError(lastError, permissionState);
            }

            function addBizLocateControl() {
                if (!bizMap || bizLocateBtn) return;

                const locateControl = L.control({ position: 'topright' });
                locateControl.onAdd = function() {
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    const btn = L.DomUtil.create('a', 'biz-locate-control', container);
                    btn.href = '#';
                    btn.title = 'Lấy vị trí hiện tại';
                    btn.setAttribute('aria-label', 'Lấy vị trí hiện tại');
                    btn.innerHTML = '<i class="bi bi-crosshair"></i>';
                    L.DomEvent.disableClickPropagation(btn);
                    L.DomEvent.on(btn, 'click', function(e) {
                        L.DomEvent.preventDefault(e);
                        locateBizMapCurrentPosition(btn);
                    });
                    bizLocateBtn = btn;
                    return container;
                };
                locateControl.addTo(bizMap);
            }

            function initBizMap() {
                setTimeout(() => {
                    const mapContainer = document.getElementById('businessMap');
                    if (!mapContainer) return;

                    if (!bizMap) {
                        let savedLat = parseFloat(document.getElementById('input_lat').value);
                        let savedLng = parseFloat(document.getElementById('input_lng').value);
                        const hasSavedPosition = !isNaN(savedLat) && !isNaN(savedLng);

                        const defaultLat = hasSavedPosition ? savedLat : 20.545;
                        const defaultLng = hasSavedPosition ? savedLng : 105.912; // Phủ Lý, Hà Nam

                        document.getElementById('input_lat').value = defaultLat.toFixed(6);
                        document.getElementById('input_lng').value = defaultLng.toFixed(6);

                        bizMap = L.map('businessMap', {
                            maxBoundsViscosity: 0.8,
                            zoomControl: true,
                            attributionControl: false,
                            minZoom: 10
                        }).setView([defaultLat, defaultLng], hasSavedPosition ? 16 : 12);

                        L.tileLayer(@json(config('services.carto.tile_url')), {
                            subdomains: 'abcd',
                            maxZoom: 19
                        }).addTo(bizMap);

                        fetch('{{ asset('geo/ha-nam-old.geojson') }}')
                            .then(res => res.json())
                            .then(data => {
                                bizHaNamBoundaryGeoJSON = data;
                                const border = L.geoJSON(data, {
                                    style: {
                                        color: '#7ba7d4',
                                        weight: 2,
                                        opacity: 0.55,
                                        fillColor: '#f8fafc',
                                        fillOpacity: 0.04
                                    }
                                }).addTo(bizMap);

                                const bounds = border.getBounds();
                                bizMap.setMaxBounds(bounds.pad(0.2));
                            })
                            .catch(err => console.error('Lỗi tải ranh giới Hà Nam:', err));

                        bizMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(bizMap);

                        bizMarker.on('dragend', function() {
                            updateBizMarkerLocation(bizMarker.getLatLng());
                        });

                        bizMap.on('click', function(e) {
                            if (updateBizMarkerLocation(e.latlng)) {
                                bizMarker.setLatLng(e.latlng);
                            }
                        });

                        updateBizMarkerLocation({ lat: defaultLat, lng: defaultLng });

                        addBizLocateControl();
                    } else {
                        bizMap.invalidateSize();
                    }
                }, 100);
            }

            // Real-time preview updates
            if (inputBizName) {
                inputBizName.addEventListener('input', function() {
                    const val = this.value.trim() || 'Tên doanh nghiệp';
                    if (mockBizName) mockBizName.innerText = val;
                    if (mockSearchText) mockSearchText.innerText = val;
                    saveWizardState();
                });
            }

            // Lưới an toàn: mọi ô trong form đều được lưu tạm, kể cả ô chưa gắn listener riêng
            const bizFormEl = document.getElementById('bizRegisterForm');
            if (bizFormEl) {
                let autoSaveTimer = null;
                const queueAutoSave = () => {
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(saveWizardState, 250);
                };
                bizFormEl.addEventListener('input', queueAutoSave);
                bizFormEl.addEventListener('change', queueAutoSave);
                window.addEventListener('beforeunload', function() {
                    clearTimeout(autoSaveTimer);
                    saveWizardState();
                });
            }

            // Custom Select dropdown logic with collapsible groups
            const customSelectTrigger = document.getElementById('custom_category_select');
            const customSelectDropdown = document.getElementById('custom_category_dropdown');
            const selectedValueSpan = customSelectTrigger ? customSelectTrigger.querySelector('.selected-value') : null;

            if (customSelectTrigger && customSelectDropdown) {
                // Toggle dropdown
                customSelectTrigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    customSelectTrigger.classList.toggle('active');
                    customSelectDropdown.classList.toggle('d-none');
                });

                // Group headers toggle
                customSelectDropdown.querySelectorAll('.dropdown-group-header').forEach(header => {
                    header.addEventListener('click', function(e) {
                        e.stopPropagation();
                        
                        const targetId = this.getAttribute('data-target');
                        const targetGroup = document.getElementById(targetId);
                        
                        if (targetGroup) {
                            const isCurrentlyHidden = targetGroup.classList.contains('d-none');
                            
                            // Close all groups first
                            customSelectDropdown.querySelectorAll('.dropdown-options-group').forEach(grp => {
                                grp.classList.add('d-none');
                            });
                            customSelectDropdown.querySelectorAll('.dropdown-group-header').forEach(hdr => {
                                hdr.classList.remove('active');
                            });

                            if (isCurrentlyHidden) {
                                targetGroup.classList.remove('d-none');
                                this.classList.add('active');
                            }
                        }
                    });
                });

                // Prevent dropdown closing when clicking inside input wrapper or typing in input
                customSelectDropdown.querySelectorAll('.other-category-input-wrapper').forEach(wrapper => {
                    wrapper.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });

                customSelectDropdown.querySelectorAll('.input-custom-other-category').forEach(input => {
                    input.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                    input.addEventListener('input', function(e) {
                        e.stopPropagation();
                        const typedVal = this.value.trim();
                        const finalCategoryName = typedVal || 'Khác';

                        if (inputCategorySearch) inputCategorySearch.value = finalCategoryName;
                        if (mockBizCategory) mockBizCategory.innerText = finalCategoryName;

                        if (selectedValueSpan) {
                            selectedValueSpan.textContent = typedVal ? `Khác: ${typedVal}` : 'Khác (Đang nhập...)';
                            selectedValueSpan.style.color = 'var(--text-main)';
                        }
                        saveWizardState();
                    });
                });

                // Option click
                customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        
                        const dbId = this.getAttribute('data-value');
                        const subName = this.getAttribute('data-name');
                        const isOther = this.classList.contains('dropdown-option-other');

                        // Set category ID
                        inputCategoryId.value = dbId;

                        // Hide all other category input wrappers across dropdown
                        customSelectDropdown.querySelectorAll('.other-category-input-wrapper').forEach(w => {
                            w.classList.add('d-none');
                        });

                        // Remove previous selection highlight
                        customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        this.classList.add('selected');

                        if (isOther) {
                            // Find the input wrapper under this group
                            const parentGroup = this.closest('.dropdown-options-group');
                            const wrapper = parentGroup ? parentGroup.querySelector('.other-category-input-wrapper') : null;
                            const customInput = wrapper ? wrapper.querySelector('.input-custom-other-category') : null;

                            if (wrapper) wrapper.classList.remove('d-none');
                            if (customInput) {
                                customInput.focus();
                                const typedVal = customInput.value.trim();
                                const finalCategoryName = typedVal || 'Khác';
                                if (inputCategorySearch) inputCategorySearch.value = finalCategoryName;
                                if (mockBizCategory) mockBizCategory.innerText = finalCategoryName;
                                if (selectedValueSpan) {
                                    selectedValueSpan.textContent = typedVal ? `Khác: ${typedVal}` : 'Khác (Vui lòng nhập...)';
                                    selectedValueSpan.style.color = 'var(--text-main)';
                                }
                            }
                            // Keep dropdown OPEN for user to type into the text field!
                        } else {
                            // Standard preset option selected
                            if (inputCategorySearch) inputCategorySearch.value = subName;
                            if (mockBizCategory) mockBizCategory.innerText = subName;

                            if (selectedValueSpan) {
                                selectedValueSpan.textContent = subName;
                                selectedValueSpan.style.color = 'var(--text-main)';
                            }

                            // Close dropdown
                            customSelectTrigger.classList.remove('active');
                            customSelectDropdown.classList.add('d-none');

                            showToast(`Đã chọn danh mục: ${subName}`, true);
                        }

                        saveWizardState();
                    });
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    customSelectTrigger.classList.remove('active');
                    customSelectDropdown.classList.add('d-none');
                });
            }

            // Select Business Type cards
            const bizTypeCards = document.querySelectorAll('.biz-type-card');
            bizTypeCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.classList.toggle('selected');
                    
                    // Collect selected values
                    const selectedVals = [];
                    document.querySelectorAll('.biz-type-card.selected').forEach(c => {
                        selectedVals.push(c.getAttribute('data-val'));
                    });

                    // Set value in hidden inputs or fields
                    document.getElementById('input_business_types').value = JSON.stringify(selectedVals);
                    saveWizardState();
                });
            });

            // Address updates
            function updateMockAddress() {
                const street = inputStreet ? inputStreet.value.trim() : '';
                const city = inputCity ? inputCity.value.trim() : '';
                const province = inputProvince ? inputProvince.value.trim() : '';

                let addr = '';
                if (street) addr += street;
                if (city) addr += (addr ? ', ' : '') + city;
                if (province) addr += (addr ? ', ' : '') + province;

                if (mockBizAddress) mockBizAddress.innerText = addr || 'Địa chỉ đường phố, thành phố';
                saveWizardState();
            }
            if (inputStreet) inputStreet.addEventListener('input', updateMockAddress);
            if (inputCity) inputCity.addEventListener('input', updateMockAddress);
            if (inputProvince) inputProvince.addEventListener('input', updateMockAddress);

            const inputPostalCode = document.getElementById('input_address_postal_code');
            if (inputPostalCode) {
                inputPostalCode.addEventListener('input', saveWizardState);
            }

            // Phone & website updates
            if (inputPhone) {
                inputPhone.addEventListener('input', function() {
                    const val = this.value.trim() || 'Chưa cập nhật SĐT';
                    if (mockBizPhone) mockBizPhone.innerText = val;
                    saveWizardState();
                });
            }
            if (inputWebsite) {
                inputWebsite.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (val) {
                        if (mockBizWebsite) mockBizWebsite.innerText = val;
                        if (mockBizWebsiteRow) mockBizWebsiteRow.classList.remove('d-none');
                    } else {
                        if (mockBizWebsiteRow) mockBizWebsiteRow.classList.add('d-none');
                    }
                    saveWizardState();
                });
            }

            const receiveTipsCheckbox = document.getElementById('receive_tips');
            if (receiveTipsCheckbox) {
                receiveTipsCheckbox.addEventListener('change', saveWizardState);
            }

            const receiveSurveysCheckbox = document.getElementById('receive_surveys');
            if (receiveSurveysCheckbox) {
                receiveSurveysCheckbox.addEventListener('change', saveWizardState);
            }

            // Description length & preview
            if (inputDesc) {
                inputDesc.addEventListener('input', function() {
                    const len = this.value.length;
                    if (descCharCount) descCharCount.innerText = len + ' / 750';
                    if (mockBizDesc) mockBizDesc.innerText = this.value.trim() || 'Chưa có mô tả nào được thêm...';
                    saveWizardState();
                });
            }

            // Image Drag and Drop and Select logic
            function setupUploader(dropzoneId, fileInputId, previewsId, previewArray, maxFiles = 10) {
                const dropzone = document.getElementById(dropzoneId);
                const fileInput = document.getElementById(fileInputId);
                const previews = document.getElementById(previewsId);

                if (!dropzone || !fileInput) return;

                dropzone.addEventListener('click', () => {
                    const totalExisting = Math.max(previewArray.length, previews.querySelectorAll('.preview-thumbnail').length);
                    if (totalExisting >= maxFiles) {
                        showToast(`Bạn chỉ được tải lên tối đa ${maxFiles} hình ảnh. Vui lòng xóa bớt ảnh hiện tại.`, false);
                        return;
                    }
                    fileInput.click();
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        dropzone.style.borderColor = 'var(--primary)';
                        dropzone.style.backgroundColor = 'rgba(0, 112, 255, 0.05)';
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        dropzone.style.borderColor = 'var(--border-color)';
                        dropzone.style.backgroundColor = 'rgba(248, 250, 252, 0.5)';
                    }, false);
                });

                dropzone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    handleFiles(files);
                });

                fileInput.addEventListener('change', function() {
                    handleFiles(this.files);
                    this.value = '';
                });

                function handleFiles(files) {
                    if (!files || files.length === 0) return;

                    const totalExisting = Math.max(previewArray.length, previews.querySelectorAll('.preview-thumbnail').length);
                    const remainingSlots = maxFiles - totalExisting;

                    if (remainingSlots <= 0) {
                        showToast(`Bạn chỉ được tải lên tối đa ${maxFiles} hình ảnh. Vui lòng xóa bớt ảnh hiện tại.`, false);
                        return;
                    }

                    const validFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
                    if (validFiles.length < files.length) {
                        showToast('Chỉ cho phép tải lên tệp hình ảnh.', false);
                    }

                    if (validFiles.length > remainingSlots) {
                        showToast(`Chỉ tải lên ${remainingSlots} ảnh đầu tiên (tối đa ${maxFiles} ảnh).`, false);
                    }

                    const filesToUpload = validFiles.slice(0, remainingSlots);

                    filesToUpload.forEach(file => {
                        activeUploadsCount++;
                        updateNextButtonState();

                        // Create unique preview item with progress spinner
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-thumbnail';
                        previewItem.innerHTML = `
                            <div class="position-absolute w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center uploader-spinner">
                                <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                            </div>
                            <img src="" style="display:none;">
                        `;
                        previews.appendChild(previewItem);

                        // File upload
                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch("{{ route('client.profile.business.upload_photo') }}", {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            const spinner = previewItem.querySelector('.uploader-spinner');
                            const img = previewItem.querySelector('img');

                            if (data.success) {
                                if (spinner) spinner.remove();
                                img.src = data.url;
                                img.style.display = 'block';

                                // Add remove button
                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'preview-remove-btn';
                                removeBtn.innerHTML = '✕';
                                removeBtn.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    previewItem.remove();
                                    
                                    // Remove from array
                                    const index = previewArray.indexOf(data.path);
                                    if (index > -1) {
                                        previewArray.splice(index, 1);
                                    }
                                    updateMockPhotosGrid();
                                    saveWizardState();
                                    updateNextButtonState();
                                });
                                previewItem.appendChild(removeBtn);

                                // Save path
                                previewArray.push(data.path);
                                updateMockPhotosGrid();
                                saveWizardState();
                            } else {
                                previewItem.remove();
                                showToast(data.message || 'Lỗi tải ảnh.', false);
                            }

                            activeUploadsCount = Math.max(0, activeUploadsCount - 1);
                            updateNextButtonState();
                        })
                        .catch(err => {
                            previewItem.remove();
                            activeUploadsCount = Math.max(0, activeUploadsCount - 1);
                            updateNextButtonState();
                            showToast('Có lỗi xảy ra khi tải ảnh lên.', false);
                            console.error(err);
                        });
                    });
                }
            }

            setupUploader('menuDropzone', 'menuFilesInput', 'menuPreviews', menuPhotos);
            setupUploader('docDropzone', 'docFilesInput', 'docPreviews', businessDocs);
            setupAvatarUploader();

            // Single-image uploader for the representative (avatar) photo
            function renderAvatarPreview(url, path) {
                const container = document.getElementById('avatarPreview');
                if (!container) return;
                container.innerHTML = '';
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-thumbnail';
                previewItem.innerHTML = `<img src="${url}" style="display:block;">`;
                const removeBtn = document.createElement('button');
                removeBtn.className = 'preview-remove-btn';
                removeBtn.innerHTML = '✕';
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    previewItem.remove();
                    avatarPhoto = null;
                    updateMockPhotosGrid();
                    saveWizardState();
                });
                previewItem.appendChild(removeBtn);
                container.appendChild(previewItem);
            }

            function restoreAvatarPreview() {
                if (avatarPhoto) renderAvatarPreview('/storage/' + avatarPhoto, avatarPhoto);
            }

            function setupAvatarUploader() {
                const dropzone = document.getElementById('avatarDropzone');
                const fileInput = document.getElementById('avatarFileInput');
                if (!dropzone || !fileInput) return;

                dropzone.addEventListener('click', () => fileInput.click());
                ['dragenter', 'dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = 'var(--primary)';
                    dropzone.style.backgroundColor = 'rgba(0, 112, 255, 0.05)';
                }, false));
                ['dragleave', 'drop'].forEach(ev => dropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = 'var(--border-color)';
                    dropzone.style.backgroundColor = 'rgba(248, 250, 252, 0.5)';
                }, false));
                dropzone.addEventListener('drop', (e) => handleAvatarFile(e.dataTransfer.files[0]));
                fileInput.addEventListener('change', function() { handleAvatarFile(this.files[0]); });

                function handleAvatarFile(file) {
                    if (!file) return;
                    if (!file.type.startsWith('image/')) {
                        showToast('Chỉ cho phép tải lên hình ảnh.', false);
                        return;
                    }

                    activeUploadsCount++;
                    updateNextButtonState();

                    const container = document.getElementById('avatarPreview');
                    container.innerHTML = `<div class="preview-thumbnail"><div class="position-absolute w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center uploader-spinner"><span class="spinner-border spinner-border-sm text-white" role="status"></span></div></div>`;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', '{{ csrf_token() }}');
                    fetch("{{ route('client.profile.business.upload_photo') }}", { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                avatarPhoto = data.path;
                                renderAvatarPreview(data.url, data.path);
                                updateMockPhotosGrid();
                                saveWizardState();
                            } else {
                                container.innerHTML = '';
                                showToast(data.message || 'Lỗi tải ảnh.', false);
                            }
                            activeUploadsCount = Math.max(0, activeUploadsCount - 1);
                            updateNextButtonState();
                        })
                        .catch(() => {
                            container.innerHTML = '';
                            activeUploadsCount = Math.max(0, activeUploadsCount - 1);
                            updateNextButtonState();
                            showToast('Có lỗi xảy ra khi tải ảnh lên.', false);
                        });
                }
            }

            // Load saved wizard state on page load
            loadWizardState();
            updateBizStepUI();
            // Restore camera photos from IndexedDB (async, large base64 data)
            restoreVerificationPhotosFromIDB();

            // Update Mock Phone Photos grid based on uploaded storefront and menu photos
            function updateMockPhotosGrid() {
                const mockGrid = document.getElementById('mockPhotosGrid');
                if (!mockGrid) return;

                mockGrid.innerHTML = '';
                const allPhotos = [avatarPhoto, ...menuPhotos].filter(Boolean);

                for (let i = 0; i < 3; i++) {
                    const item = document.createElement('div');
                    item.className = 'mock-photo-item';
                    
                    if (allPhotos[i]) {
                        item.innerHTML = `<img class="mock-photo-img" src="/storage/${allPhotos[i]}">`;
                    } else {
                        item.innerHTML = '📷';
                    }
                    mockGrid.appendChild(item);
                }
            }

            // Real-time listener to remove red error border on user input/change
            document.querySelectorAll('.form-control-clean, .custom-select-trigger').forEach(el => {
                el.addEventListener('input', function() {
                    this.classList.remove('is-invalid-clean');
                });
                el.addEventListener('change', function() {
                    this.classList.remove('is-invalid-clean');
                });
            });

            // Step validations
            function validateBizStep() {
                if (activeUploadsCount > 0 || document.querySelectorAll('.uploader-spinner').length > 0) {
                    showToast('Hình ảnh đang được tải lên, vui lòng chờ trong giây lát...', false);
                    return false;
                }

                // Clear previous red error highlights in current step pane
                const currentPane = document.querySelector(`.biz-step-pane[data-step="${bizStep}"]`);
                if (currentPane) {
                    currentPane.querySelectorAll('.is-invalid-clean').forEach(el => el.classList.remove('is-invalid-clean'));
                }

                if (bizStep === 1) {
                    if (!inputBizName.value.trim()) {
                        inputBizName.classList.add('is-invalid-clean');
                        inputBizName.focus();
                        showToast('Vui lòng điền tên doanh nghiệp.', false);
                        return false;
                    }
                } else if (bizStep === 3) {
                    if (!inputCategoryId.value) {
                        const selectTrigger = document.getElementById('custom_category_select');
                        if (selectTrigger) selectTrigger.classList.add('is-invalid-clean');
                        showToast('Vui lòng chọn đầy đủ danh mục chính và danh mục chi tiết.', false);
                        return false;
                    }
                } else if (bizStep === 4) {
                    let hasError = false;
                    const postalInput = document.getElementById('input_address_postal_code');

                    if (!inputCity.value.trim()) {
                        inputCity.classList.add('is-invalid-clean');
                        hasError = true;
                    }
                    if (postalInput && !postalInput.value.trim()) {
                        postalInput.classList.add('is-invalid-clean');
                        hasError = true;
                    }
                    if (!inputStreet.value.trim()) {
                        inputStreet.classList.add('is-invalid-clean');
                        hasError = true;
                    }

                    if (hasError) {
                        showToast('Vui lòng nhập đầy đủ các trường địa chỉ bắt buộc (*).', false);
                        const firstInvalid = currentPane ? currentPane.querySelector('.is-invalid-clean') : null;
                        if (firstInvalid) firstInvalid.focus();
                        return false;
                    }
                } else if (bizStep === 5) {
                    if (!inputPhone.value.trim()) {
                        inputPhone.classList.add('is-invalid-clean');
                        inputPhone.focus();
                        showToast('Vui lòng nhập số điện thoại liên hệ.', false);
                        return false;
                    }
                    const phoneVal = inputPhone.value.trim().replace(/\D/g, '');
                    if (phoneVal.length < 8) {
                        inputPhone.classList.add('is-invalid-clean');
                        inputPhone.focus();
                        showToast('Số điện thoại liên hệ không hợp lệ (ít nhất 8 số).', false);
                        return false;
                    }
                } else if (bizStep === 6) {
                    if (!document.getElementById('input_lat').value || !document.getElementById('input_lng').value) {
                        showToast('Vui lòng kéo ghim trên bản đồ để chọn tọa độ.', false);
                        return false;
                    }
                } else if (bizStep === 7) {
                    if (!inputDesc.value.trim()) {
                        inputDesc.classList.add('is-invalid-clean');
                        inputDesc.focus();
                        showToast('Vui lòng nhập mô tả giới thiệu về doanh nghiệp của bạn.', false);
                        return false;
                    }
                } else if (bizStep === 8) {
                    if (!menuPhotos || menuPhotos.length === 0) {
                        showToast('Vui lòng tải lên ít nhất 1 hình ảnh về địa điểm của bạn.', false);
                        return false;
                    }
                    if (menuPhotos.length > 10) {
                        showToast('Tối đa chỉ được chọn 10 hình ảnh. Vui lòng xóa bớt ảnh.', false);
                        return false;
                    }
                } else if (bizStep === 9) {
                    if (!avatarPhoto) {
                        showToast('Vui lòng chọn 1 ảnh đại diện cho địa điểm của bạn.', false);
                        return false;
                    }
                } else if (bizStep === 10) {
                    if (!businessDocs || businessDocs.length === 0) {
                        showToast('Vui lòng tải lên ít nhất 1 bằng chứng xác minh.', false);
                        return false;
                    }
                    if (businessDocs.length > 10) {
                        showToast('Tối đa chỉ được chọn 10 ảnh bằng chứng. Vui lòng xóa bớt ảnh.', false);
                        return false;
                    }
                } else if (bizStep === 11) {
                    if (!Number.isFinite(verificationLat) || !Number.isFinite(verificationLng)) {
                        showToast('Chưa lấy được GPS. Bật Vị trí trên trình duyệt rồi nhấn "Lấy lại GPS".', false);
                        return false;
                    }
                    const pin = getMapPinCoords();
                    if (pin) {
                        const dist = haversineMeters(verificationLat, verificationLng, pin.lat, pin.lng);
                        verificationDistanceM = dist;
                        if (dist > VERIFICATION_MAX_DISTANCE_M) {
                            showToast(
                                'Bạn đang cách ' + formatVerificationDistance(dist) +
                                ' vị trí được ghim; chỉ có thể xác thực trong khoảng ' + VERIFICATION_MAX_DISTANCE_M + 'm.',
                                false
                            );
                            setVerificationDistanceGate(dist);
                            return false;
                        }
                    }
                    if (!verificationPhotos || verificationPhotos.length === 0) {
                        showToast('Bắt buộc phải bật Camera chụp ảnh xác thực thực địa!', false);
                        return false;
                    }
                }
                return true;
            }

            // Next button clicked
            bizNextBtn.addEventListener('click', function() {
                if (!validateBizStep()) return;

                const list = getActiveStepList();
                const idx = getActiveStepIndex();
                if (idx < list.length - 1) {
                    goToRelativeStep(1);
                } else {
                    submitBizRegistrationForm();
                }
            });

            // Prev button clicked
            bizPrevBtn.addEventListener('click', function() {
                if (getActiveStepIndex() > 0) {
                    goToRelativeStep(-1);
                }
            });

            // Skip button (optional steps) — advance without validation
            if (bizSkipBtn) {
                bizSkipBtn.addEventListener('click', function() {
                    const list = getActiveStepList();
                    const idx = getActiveStepIndex();
                    if (idx < list.length - 1) {
                        goToRelativeStep(1);
                    } else {
                        submitBizRegistrationForm();
                    }
                });
            }

            // Click a completed sub-step in the left nav to jump back to it
            document.querySelectorAll('.reg-nav__sub').forEach(sub => {
                sub.addEventListener('click', function() {
                    const s = parseInt(sub.getAttribute('data-step'));
                    const list = getActiveStepList();
                    const targetIdx = list.indexOf(s);
                    const curIdx = getActiveStepIndex();
                    if (!isNaN(s) && targetIdx > -1 && targetIdx < curIdx) {
                        bizStep = s;
                        updateBizStepUI();
                    }
                });
            });

            // Click a completed phase header to jump back to its first finished step
            document.querySelectorAll('.reg-nav__phase').forEach(phase => {
                phase.addEventListener('click', function() {
                    if (!phase.classList.contains('done')) return;
                    const group = phase.closest('.reg-nav__group');
                    if (!group) return;
                    const list = getActiveStepList();
                    const curIdx = getActiveStepIndex();
                    const candidates = Array.from(group.querySelectorAll('.reg-nav__sub'))
                        .filter((sub) => !sub.classList.contains('is-claim-hidden'))
                        .map((sub) => parseInt(sub.getAttribute('data-step'), 10))
                        .filter((s) => {
                            const idx = list.indexOf(s);
                            return idx > -1 && idx < curIdx;
                        });
                    if (!candidates.length) return;
                    bizStep = candidates[0];
                    updateBizStepUI();
                });
            });

            // Final AJAX form submission
            function submitBizRegistrationForm() {
                if (!Number.isFinite(verificationLat) || !Number.isFinite(verificationLng)) {
                    showToast('Chưa lấy được GPS. Bật Vị trí trên trình duyệt rồi nhấn "Lấy lại GPS" trước khi gửi.', false);
                    return;
                }
                const pin = getMapPinCoords();
                if (pin) {
                    const dist = haversineMeters(verificationLat, verificationLng, pin.lat, pin.lng);
                    verificationDistanceM = dist;
                    if (dist > VERIFICATION_MAX_DISTANCE_M) {
                        showToast(
                            'Bạn đang cách ' + formatVerificationDistance(dist) +
                            ' vị trí được ghim; chỉ có thể xác thực trong khoảng ' + VERIFICATION_MAX_DISTANCE_M + 'm.',
                            false
                        );
                        setVerificationDistanceGate(dist);
                        return;
                    }
                }
                if (!verificationPhotos || verificationPhotos.length === 0) {
                    showToast('Bắt buộc phải chụp ảnh xác thực thực địa trước khi gửi.', false);
                    return;
                }

                const submitBtn = bizNextBtn;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang gửi...`;

                try {
                    const types = [];
                    document.querySelectorAll('.biz-type-card.selected').forEach(c => {
                        types.push(c.getAttribute('data-val'));
                    });

                    const payload = {
                        business_name: inputBizName.value.trim(),
                        business_types: types.length ? types : ['local_store'],
                        category_id: inputCategoryId.value || (claimLocationMeta && claimLocationMeta.category_id) || null,
                        address_country: 'Việt Nam',
                        address_street: inputStreet.value.trim() || (claimLocationMeta && claimLocationMeta.address) || '',
                        address_city: inputCity.value.trim() || 'Ninh Bình',
                        address_province: inputProvince.value.trim() || 'Ninh Bình',
                        address_postal_code: document.getElementById('input_address_postal_code') ? document.getElementById('input_address_postal_code').value.trim() : (isClaimMode() ? '00000' : ''),
                        phone: inputPhone.value.trim(),
                        website: inputWebsite ? inputWebsite.value.trim() : '',
                        lat: parseFloat(document.getElementById('input_lat').value) || (claimLocationMeta ? claimLocationMeta.lat : null),
                        lng: parseFloat(document.getElementById('input_lng').value) || (claimLocationMeta ? claimLocationMeta.lng : null),
                        receive_tips: (document.getElementById('receive_tips') && document.getElementById('receive_tips').checked) ? 1 : 0,
                        receive_surveys: (document.getElementById('receive_surveys') && document.getElementById('receive_surveys').checked) ? 1 : 0,
                        description: inputDesc.value.trim() || (claimLocationMeta && claimLocationMeta.description) || '',
                        menu_photos: menuPhotos || [],
                        avatar_photo: avatarPhoto || null,
                        business_documents: businessDocs || [],
                        verification_photo: (verificationPhotos && verificationPhotos.length > 0) ? verificationPhotos[0] : (verificationPhotoData || null),
                        verification_photos: verificationPhotos || [],
                        verification_lat: typeof verificationLat !== 'undefined' ? verificationLat : null,
                        verification_lng: typeof verificationLng !== 'undefined' ? verificationLng : null,
                        verification_time: typeof verificationTime !== 'undefined' ? verificationTime : null,
                        location_id: claimLocationId || null,
                        _token: '{{ csrf_token() }}'
                    };

                    fetch("{{ route('client.profile.business.register') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(async res => {
                        const data = await res.json().catch(() => null);
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Hoàn tất & Gửi';

                        if (res.ok && data && data.success) {
                            clearWizardState();
                            idbClearPhotos();
                            showToast(data.message || 'Đăng ký thành công!', true);
                            setTimeout(() => {
                                window.location.href = "{{ route('client.profile') }}";
                            }, 1200);
                        } else {
                            const errMsg = (data && data.message) ? data.message : `Lỗi gửi yêu cầu (Mã lỗi ${res.status}). Vui lòng kiểm tra và thử lại.`;
                            showToast(errMsg, false);
                            // Nếu bị chặn do đã có hồ sơ, đưa người dùng về trang cá nhân để xem trạng thái
                            if (data && data.redirect) {
                                setTimeout(() => { window.location.href = data.redirect; }, 1600);
                            }
                        }
                    })
                    .catch(err => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Hoàn tất & Gửi';
                        showToast('Đã xảy ra lỗi kết nối khi gửi yêu cầu nâng cấp.', false);
                        console.error('Submit Error:', err);
                    });
                } catch (err) {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Hoàn tất & Gửi';
                    showToast('Có lỗi xảy ra khi chuẩn bị dữ liệu gửi. Vui lòng thử lại.', false);
                    console.error('Payload Build Error:', err);
                }
            }
        }
    });
</script>

</body>
</html>
