<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản Doanh Nghiệp - Ninh Bình POI</title>
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
            font-family: 'Be Vietnam Pro', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, .h1 { font-size: 1.3rem !important; font-weight: 600 !important; }
        h2, .h2 { font-size: 1.15rem !important; font-weight: 600 !important; }
        h3, .h3 { font-size: 1.05rem !important; font-weight: 600 !important; }
        h4, .h4 { font-size: 0.95rem !important; font-weight: 600 !important; }
        h5, .h5 { font-size: 0.9rem !important; font-weight: 600 !important; }
        h6, .h6 { font-size: 0.85rem !important; font-weight: 600 !important; }

        /* Top Navigation Bar */
        .top-navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
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
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .section-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-main);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 6px;
            display: inline-block;
        }

        .form-label-clean {
            font-weight: 500;
            color: #475569;
            margin-bottom: 6px;
            font-size: 0.875rem;
            display: block;
        }
        
        .form-control-clean {
            width: 100%;
            padding: 9px 14px;
            border: 1px solid #cbd5e1;
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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Business Account upgrade styles */
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
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-navbar">
    <a href="{{ route('client.profile') }}" class="btn-back">
        <i class="bi bi-chevron-left"></i> Quay lại trang cá nhân
    </a>
    <div style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
        Ninh Bình POI Doanh Nghiệp
    </div>
    <div></div>
</div>

<div class="container py-4">
    <!-- Form Wizard for Registration -->
    <div id="businessRegistrationWizard">
        <div class="content-panel">
            <div class="section-title">Đăng ký tài khoản doanh nghiệp</div>
            
            <!-- Step Progress Bar -->
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
            </div>

            <div class="wizard-row">
                <!-- Left: Form steps -->
                <div class="wizard-form-col">
                    <form id="bizRegisterForm" onsubmit="event.preventDefault();">
                        @csrf
                        <!-- Step 1: Business Name -->
                        <div class="biz-step-pane" data-step="1">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Giúp khách hàng tìm thấy doanh nghiệp của bạn trên Tìm kiếm, Maps, v.v.</h4>
                            <p class="text-secondary small mb-4">Nhập một vài thông tin doanh nghiệp để bắt đầu.</p>
                            <div class="mb-4">
                                <label class="form-label-clean">Tên doanh nghiệp *</label>
                                <input type="text" class="form-control-clean" id="input_business_name" name="business_name" required placeholder="Ví dụ: Quán Nhậu Anh">
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
                                        <div class="dropdown-option-item" data-value="5" data-name="Quán nhậu / Bar / Pub">Quán nhậu / Bar / Pub</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Tiệm bánh / Tráng miệng">Tiệm bánh / Tráng miệng</div>
                                        <div class="dropdown-option-item" data-value="5" data-name="Quán ăn vặt / Vỉa hè">Quán ăn vặt / Vỉa hè</div>
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
                                        <div class="dropdown-option-item" data-value="6" data-name="Nhà khách / Khác">Nhà khách / Khác</div>
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

                            <div class="mb-4">
                                <label class="form-label-clean">Trang web (không bắt buộc)</label>
                                <input type="url" class="form-control-clean" id="input_website" name="website" placeholder="Ví dụ: https://mybusiness.com">
                            </div>
                        </div>

                        <!-- Step 6: Map Coordinates -->
                        <div class="biz-step-pane d-none" data-step="6">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Doanh nghiệp của bạn ở đâu?</h4>
                            <p class="text-secondary small mb-3">Kéo và di chuyển điểm đánh dấu (marker) đến vị trí chính xác của doanh nghiệp.</p>
                            
                            <div id="businessMap"></div>
                            
                            <!-- Hidden inputs for Lat / Lng -->
                            <input type="hidden" id="input_lat" name="lat" required>
                            <input type="hidden" id="input_lng" name="lng" required>
                        </div>

                        <!-- Step 7: PUT BIZ ON MAP settings -->
                        <div class="biz-step-pane d-none" data-step="7">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Đưa doanh nghiệp của bạn lên bản đồ</h4>
                            <p class="text-secondary small mb-4">Bắt đầu kết nối với khách hàng trên Ninh Bình POI - tất cả ở cùng một nơi.</p>
                            
                            <div class="p-3 border rounded-2 bg-light mb-4">
                                <ul class="mb-0 ps-3 small text-secondary">
                                    <li class="mb-2">Giúp mọi người trong khu vực tìm thấy doanh nghiệp của bạn nhanh chóng.</li>
                                    <li class="mb-2">Trả lời các bài đánh giá về doanh nghiệp của bạn từ khách hàng địa phương.</li>
                                    <li>Quản lý toàn diện thông tin doanh nghiệp trên hệ thống bản đồ POI.</li>
                                </ul>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="receive_tips" id="receive_tips" value="1" checked>
                                <label class="form-check-label small text-secondary" for="receive_tips">
                                    Nhận tin báo và mẹo hay về cách cải thiện Trang doanh nghiệp của bạn
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="receive_surveys" id="receive_surveys" value="1" checked>
                                <label class="form-check-label small text-secondary" for="receive_surveys">
                                    Nhận lời mời tham gia các cuộc khảo sát và chương trình thí điểm không thường xuyên
                                </label>
                            </div>
                        </div>

                        <!-- Step 8: Description -->
                        <div class="biz-step-pane d-none" data-step="8">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Thêm mô tả doanh nghiệp *</h4>
                            <p class="text-secondary small mb-4">Cho phép khách hàng tìm hiểu thêm về doanh nghiệp của bạn bằng cách thêm mô tả ngắn gọn.</p>
                            <div class="mb-3">
                                <label class="form-label-clean">Giới thiệu về doanh nghiệp *</label>
                                <textarea class="form-control-clean" id="input_description" name="description" rows="5" maxlength="750" required placeholder="Ví dụ: Quán nhậu bình dân phục vụ các món ăn đặc sản Ninh Bình..."></textarea>
                                <div class="text-end text-secondary small mt-1" id="descCharCount">0 / 750</div>
                            </div>
                        </div>

                        <!-- Step 9: Menu Photos -->
                        <div class="biz-step-pane d-none" data-step="9">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;" id="step8Title">Thêm ảnh chụp thực đơn của bạn *</h4>
                            <p class="text-secondary small mb-4" id="step8Desc">Khách hàng chủ yếu dựa vào ảnh thực đơn khi quyết định ăn hoặc ghé thăm ở đâu.</p>
                            
                            <div class="dropzone-area" id="menuDropzone">
                                <div class="fw-semibold small" id="step8Text">Kéo các hình ảnh thực đơn vào đây</div>
                                <div class="text-secondary small mt-1">hoặc click để chọn từ máy tính</div>
                                <input type="file" id="menuFilesInput" class="d-none" multiple accept="image/*">
                            </div>

                            <div class="upload-previews" id="menuPreviews"></div>
                        </div>

                        <!-- Step 10: Storefront Photos -->
                        <div class="biz-step-pane d-none" data-step="10">
                            <h4 class="fw-semibold mb-2" style="font-size: 1.05rem; color: #0f172a;">Thêm ảnh mặt tiền cửa hàng *</h4>
                            <p class="text-secondary small mb-4">Việc chia sẻ ảnh mặt tiền của doanh nghiệp sẽ giúp khách hàng nhận ra vị trí của bạn ngay khi ghé qua.</p>
                            
                            <div class="dropzone-area" id="storefrontDropzone">
                                <div class="fw-semibold small">Kéo các hình ảnh mặt tiền vào đây</div>
                                <div class="text-secondary small mt-1">hoặc click để chọn từ máy tính</div>
                                <input type="file" id="storefrontFilesInput" class="d-none" multiple accept="image/*">
                            </div>

                            <div class="upload-previews" id="storefrontPreviews"></div>
                        </div>

                        <!-- Actions Row -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light rounded-2 btn-sm px-4" id="bizPrevBtn" disabled>Quay lại</button>
                            <button type="button" class="btn btn-primary rounded-2 btn-sm px-4" id="bizNextBtn">Tiếp tục</button>
                        </div>
                    </form>
                </div>

                <!-- Right: Mobile Preview Mockup -->
                <div class="wizard-mockup-col d-none d-lg-block">
                    <div class="phone-mockup-wrapper">
                        <div class="phone-mockup">
                            <div class="phone-notch"></div>
                            <div class="phone-screen">
                                <!-- Google Mock Search -->
                                <div class="mock-google-search">
                                    <div class="mock-google-logo">
                                        <span>G</span><span>o</span><span>o</span><span>g</span><span>l</span><span>e</span>
                                    </div>
                                    <div class="mock-search-text" id="mockSearchText">Tìm kiếm...</div>
                                </div>

                                <!-- Card Business info preview -->
                                <div class="mock-business-card">
                                    <div class="mock-biz-name" id="mockBizName">Tên doanh nghiệp</div>
                                    <div class="mock-biz-rating">
                                        ★★★★★ <span style="color:#70757a; font-size:0.65rem;">(5.0)</span>
                                    </div>
                                    <div class="mock-biz-category" id="mockBizCategory">Danh mục</div>
                                    
                                    <div class="mock-action-buttons">
                                        <div class="mock-action-btn">
                                            <div class="mock-action-icon"><i class="bi bi-telephone-fill" style="font-size: 0.65rem;"></i></div>
                                            <span>Gọi điện</span>
                                        </div>
                                        <div class="mock-action-btn">
                                            <div class="mock-action-icon"><i class="bi bi-geo-alt-fill" style="font-size: 0.65rem;"></i></div>
                                            <span>Chỉ đường</span>
                                        </div>
                                        <div class="mock-action-btn">
                                            <div class="mock-action-icon"><i class="bi bi-globe" style="font-size: 0.65rem;"></i></div>
                                            <span>Trang web</span>
                                        </div>
                                    </div>

                                    <div class="mock-info-rows">
                                        <div class="mock-info-row">
                                            <span class="mock-info-icon"><i class="bi bi-geo-alt"></i></span>
                                            <span id="mockBizAddress">Địa chỉ đường phố, thành phố</span>
                                        </div>
                                        <div class="mock-info-row">
                                            <span class="mock-info-icon"><i class="bi bi-clock"></i></span>
                                            <span>Đang mở cửa • Cả ngày</span>
                                        </div>
                                        <div class="mock-info-row">
                                            <span class="mock-info-icon"><i class="bi bi-telephone"></i></span>
                                            <span id="mockBizPhone">{{ Auth::user()->phone ?? 'Chưa cập nhật SĐT' }}</span>
                                        </div>
                                        <div class="mock-info-row d-none" id="mockBizWebsiteRow">
                                            <span class="mock-info-icon"><i class="bi bi-globe"></i></span>
                                            <span id="mockBizWebsite"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photos Grid preview -->
                                <div class="mock-business-card" style="flex:1;">
                                    <div class="fw-semibold mb-2">Hình ảnh</div>
                                    <div class="mock-photos-grid" id="mockPhotosGrid">
                                        <div class="mock-photo-item"><i class="bi bi-image" style="font-size: 0.75rem; color: #cbd5e1;"></i></div>
                                        <div class="mock-photo-item"><i class="bi bi-image" style="font-size: 0.75rem; color: #cbd5e1;"></i></div>
                                        <div class="mock-photo-item"><i class="bi bi-image" style="font-size: 0.75rem; color: #cbd5e1;"></i></div>
                                    </div>
                                    <div class="mt-3 text-secondary" style="font-size:0.65rem; line-height:1.3;" id="mockBizDesc">
                                        Chưa có mô tả nào được thêm...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        const bizForm = document.getElementById('bizRegisterForm');
        if (bizForm) {
            let bizStep = 1;
            const totalBizSteps = 10;
            const bizPanes = document.querySelectorAll('.biz-step-pane');
            const bizPrevBtn = document.getElementById('bizPrevBtn');
            const bizNextBtn = document.getElementById('bizNextBtn');
            const bizSkipBtn = document.getElementById('bizSkipBtn');
            const bizStepFill = document.getElementById('bizStepFill');
            const bizStepNodes = document.querySelectorAll('.step-progress-node');

            // Form inputs and preview elements
            const inputBizName = document.getElementById('input_business_name');
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

            // Image uploading states
            let menuPhotos = [];
            let storefrontPhotos = [];

            // State Persistence Functions
            function saveWizardState() {
                const state = {
                    bizStep: bizStep,
                    businessName: inputBizName.value.trim(),
                    businessTypes: Array.from(document.querySelectorAll('.biz-type-card.selected')).map(c => c.getAttribute('data-val')),
                    categoryId: inputCategoryId.value,
                    categorySearchName: inputCategorySearch ? inputCategorySearch.value : '',
                    addressStreet: inputStreet.value.trim(),
                    addressCity: inputCity.value.trim(),
                    addressProvince: inputProvince.value.trim(),
                    addressPostalCode: document.getElementById('input_address_postal_code') ? document.getElementById('input_address_postal_code').value.trim() : '',
                    phone: inputPhone ? inputPhone.value.trim() : '',
                    website: inputWebsite ? inputWebsite.value.trim() : '',
                    lat: document.getElementById('input_lat') ? document.getElementById('input_lat').value : '',
                    lng: document.getElementById('input_lng') ? document.getElementById('input_lng').value : '',
                    receiveTips: document.getElementById('receive_tips') ? document.getElementById('receive_tips').checked : false,
                    receiveSurveys: document.getElementById('receive_surveys') ? document.getElementById('receive_surveys').checked : false,
                    description: inputDesc.value.trim(),
                    menuPhotos: menuPhotos,
                    storefrontPhotos: storefrontPhotos
                };
                localStorage.setItem('biz_wizard_state', JSON.stringify(state));
            }

            function loadWizardState() {
                const raw = localStorage.getItem('biz_wizard_state');
                if (!raw) return;

                try {
                    const state = JSON.parse(raw);

                    if (state.bizStep) {
                        bizStep = parseInt(state.bizStep);
                    }

                    if (state.businessName) {
                        inputBizName.value = state.businessName;
                        mockBizName.innerText = state.businessName;
                        mockSearchText.innerText = state.businessName;
                    }

                    if (state.businessTypes && Array.isArray(state.businessTypes)) {
                        document.querySelectorAll('.biz-type-card').forEach(card => {
                            if (state.businessTypes.includes(card.getAttribute('data-val'))) {
                                card.classList.add('selected');
                            } else {
                                card.classList.remove('selected');
                            }
                        });
                        const inputTypes = document.getElementById('input_business_types');
                        if (inputTypes) inputTypes.value = JSON.stringify(state.businessTypes);
                    }

                    if (state.categoryId && state.categorySearchName) {
                        inputCategoryId.value = state.categoryId;
                        if (inputCategorySearch) inputCategorySearch.value = state.categorySearchName;
                        mockBizCategory.innerText = state.categorySearchName;

                        const selectedSpan = document.querySelector('#custom_category_select .selected-value');
                        if (selectedSpan) {
                            selectedSpan.textContent = state.categorySearchName;
                            selectedSpan.style.color = 'var(--text-main)';
                        }

                        const customSelectDropdown = document.getElementById('custom_category_dropdown');
                        if (customSelectDropdown) {
                            customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(opt => {
                                if (opt.getAttribute('data-name') === state.categorySearchName) {
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

                    if (state.addressStreet) inputStreet.value = state.addressStreet;
                    if (state.addressCity) inputCity.value = state.addressCity;
                    if (state.addressProvince) inputProvince.value = state.addressProvince;
                    if (state.addressPostalCode && document.getElementById('input_address_postal_code')) {
                        document.getElementById('input_address_postal_code').value = state.addressPostalCode;
                    }
                    updateMockAddress();

                    if (state.phone) {
                        if (inputPhone) inputPhone.value = state.phone;
                        if (mockBizPhone) mockBizPhone.innerText = state.phone;
                    }
                    if (state.website) {
                        if (inputWebsite) inputWebsite.value = state.website;
                        if (mockBizWebsite) mockBizWebsite.innerText = state.website;
                        if (mockBizWebsiteRow) mockBizWebsiteRow.classList.remove('d-none');
                    }

                    if (state.lat && state.lng) {
                        if (document.getElementById('input_lat')) document.getElementById('input_lat').value = state.lat;
                        if (document.getElementById('input_lng')) document.getElementById('input_lng').value = state.lng;
                    }

                    if (state.hasOwnProperty('receiveTips') && document.getElementById('receive_tips')) {
                        document.getElementById('receive_tips').checked = state.receiveTips;
                    }
                    if (state.hasOwnProperty('receiveSurveys') && document.getElementById('receive_surveys')) {
                        document.getElementById('receive_surveys').checked = state.receiveSurveys;
                    }

                    if (state.description) {
                        inputDesc.value = state.description;
                        mockBizDesc.innerText = state.description;
                        if (descCharCount) descCharCount.innerText = state.description.length + ' / 750';
                    }

                    if (state.menuPhotos && Array.isArray(state.menuPhotos)) {
                        menuPhotos.length = 0;
                        state.menuPhotos.forEach(p => menuPhotos.push(p));
                        restorePhotoPreviews('menuPreviews', menuPhotos);
                    }
                    if (state.storefrontPhotos && Array.isArray(state.storefrontPhotos)) {
                        storefrontPhotos.length = 0;
                        state.storefrontPhotos.forEach(p => storefrontPhotos.push(p));
                        restorePhotoPreviews('storefrontPreviews', storefrontPhotos);
                    }
                    updateMockPhotosGrid();

                } catch (e) {
                    console.error('Error loading wizard state:', e);
                }
            }

            function clearWizardState() {
                localStorage.removeItem('biz_wizard_state');
            }

            function restorePhotoPreviews(containerId, photosArray) {
                const container = document.getElementById(containerId);
                if (!container) return;
                container.innerHTML = '';
                
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
                    s8Title.innerText = "Thêm ảnh chụp thực đơn của bạn";
                    s8Desc.innerText = "Khách hàng chủ yếu dựa vào ảnh thực đơn khi quyết định ăn hoặc ghé thăm ở đâu.";
                    if (s8Icon) s8Icon.innerText = "📋";
                    if (s8Text) s8Text.innerText = "Kéo các hình ảnh thực đơn vào đây";
                } else if (name.includes('lưu trú') || name.includes('khách sạn') || name.includes('nhà nghỉ') || name.includes('homestay') || name.includes('resort')) {
                    s8Title.innerText = "Thêm ảnh phòng nghỉ & dịch vụ";
                    s8Desc.innerText = "Khách hàng chủ yếu dựa vào ảnh phòng nghỉ và bảng giá dịch vụ để quyết định đặt phòng.";
                    if (s8Icon) s8Icon.innerText = "🛌";
                    if (s8Text) s8Text.innerText = "Kéo các hình ảnh phòng nghỉ/dịch vụ vào đây";
                } else {
                    s8Title.innerText = "Thêm ảnh bảng giá & dịch vụ";
                    s8Desc.innerText = "Cung cấp hình ảnh dịch vụ giúp khách hàng hiểu rõ hơn về các gói dịch vụ và bảng giá của bạn.";
                    if (s8Icon) s8Icon.innerText = "💼";
                    if (s8Text) s8Text.innerText = "Kéo các hình ảnh bảng giá/dịch vụ vào đây";
                }
            }

            // Update step UI
            function updateBizStepUI() {
                if (bizStep === 9) {
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

                // Update nodes
                bizStepNodes.forEach(node => {
                    const stepNum = parseInt(node.getAttribute('data-step'));
                    if (stepNum < bizStep) {
                        node.className = 'step-progress-node completed';
                    } else if (stepNum === bizStep) {
                        node.className = 'step-progress-node active';
                    } else {
                        node.className = 'step-progress-node';
                    }
                });

                // Update fill line
                const fillPercent = ((bizStep - 1) / (totalBizSteps - 1)) * 100;
                bizStepFill.style.width = fillPercent + '%';

                // Update buttons
                bizPrevBtn.disabled = (bizStep === 1);
                if (bizStep === totalBizSteps) {
                    bizNextBtn.innerText = 'Hoàn tất & Gửi';
                } else {
                    bizNextBtn.innerText = 'Tiếp tục';
                }

                // Toggle skip button visibility
                if (bizSkipBtn) {
                    if (bizStep === 9 || bizStep === 10) {
                        bizSkipBtn.classList.remove('d-none');
                    } else {
                        bizSkipBtn.classList.add('d-none');
                    }
                }

                // If entering Map step, initialize/invalidate map
                if (bizStep === 6) {
                    initBizMap();
                }

                saveWizardState();
            }

            // Leaflet Map vars & GeoJSON boundary
            let bizMap = null;
            let bizMarker = null;
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
            function initBizMap() {
                setTimeout(() => {
                    const mapContainer = document.getElementById('businessMap');
                    if (!mapContainer) return;

                    if (!bizMap) {
                        let savedLat = parseFloat(document.getElementById('input_lat').value);
                        let savedLng = parseFloat(document.getElementById('input_lng').value);

                        const defaultLat = !isNaN(savedLat) ? savedLat : 20.545;
                        const defaultLng = !isNaN(savedLng) ? savedLng : 105.912; // Phủ Lý, Hà Nam

                        document.getElementById('input_lat').value = defaultLat.toFixed(6);
                        document.getElementById('input_lng').value = defaultLng.toFixed(6);

                        bizMap = L.map('businessMap', {
                            maxBoundsViscosity: 0.8,
                            zoomControl: true,
                            attributionControl: false,
                            minZoom: 10
                        }).setView([defaultLat, defaultLng], 12);

                        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
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

                        function updateMarkerLocation(latlng) {
                            const isInside = isPointInHaNamGeoJSON(latlng.lat, latlng.lng, bizHaNamBoundaryGeoJSON);

                            if (!isInside) {
                                mapContainer.style.borderColor = '#ef4444';
                                mapContainer.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.18)';
                                if (typeof showToastCustom === 'function') {
                                    showToastCustom('⚠️ Vị trí ngoài tỉnh Ninh Bình! Vui lòng nhấp chọn lại.');
                                }
                                return false;
                            }

                            mapContainer.style.borderColor = '#10b981';
                            mapContainer.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.12)';
                            document.getElementById('input_lat').value = latlng.lat.toFixed(6);
                            document.getElementById('input_lng').value = latlng.lng.toFixed(6);
                            saveWizardState();
                            return true;
                        }

                        // Drag end event
                        bizMarker.on('dragend', function(e) {
                            const pos = bizMarker.getLatLng();
                            updateMarkerLocation(pos);
                        });

                        // Map click event
                        bizMap.on('click', function(e) {
                            if (updateMarkerLocation(e.latlng)) {
                                bizMarker.setLatLng(e.latlng);
                            }
                        });

                        updateMarkerLocation({ lat: defaultLat, lng: defaultLng });
                    } else {
                        bizMap.invalidateSize();
                    }
                }, 100);
            }

            // Real-time preview updates
            if (inputBizName) {
                inputBizName.addEventListener('input', function() {
                    const val = this.value.trim() || 'Tên doanh nghiệp';
                    mockBizName.innerText = val;
                    mockSearchText.innerText = val;
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

                // Option click
                customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.stopPropagation();
                        
                        const dbId = this.getAttribute('data-value');
                        const subName = this.getAttribute('data-name');

                        // Set values
                        inputCategoryId.value = dbId;
                        if (inputCategorySearch) inputCategorySearch.value = subName;
                        mockBizCategory.innerText = subName;

                        // Update trigger text
                        if (selectedValueSpan) {
                            selectedValueSpan.textContent = subName;
                            selectedValueSpan.style.color = 'var(--text-main)';
                        }

                        // Remove previous selection highlight
                        customSelectDropdown.querySelectorAll('.dropdown-option-item').forEach(opt => {
                            opt.classList.remove('selected');
                        });
                        this.classList.add('selected');

                        // Close dropdown
                        customSelectTrigger.classList.remove('active');
                        customSelectDropdown.classList.add('d-none');

                        showToast(`Đã chọn danh mục: ${subName}`, true);
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
                const street = inputStreet.value.trim();
                const city = inputCity.value.trim();
                const province = inputProvince.value.trim();
                
                let addr = '';
                if (street) addr += street;
                if (city) addr += (addr ? ', ' : '') + city;
                if (province) addr += (addr ? ', ' : '') + province;

                mockBizAddress.innerText = addr || 'Địa chỉ đường phố, thành phố';
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
                    descCharCount.innerText = len + ' / 750';
                    mockBizDesc.innerText = this.value.trim() || 'Chưa có mô tả nào được thêm...';
                    saveWizardState();
                });
            }

            // Image Drag and Drop and Select logic
            function setupUploader(dropzoneId, fileInputId, previewsId, previewArray) {
                const dropzone = document.getElementById(dropzoneId);
                const fileInput = document.getElementById(fileInputId);
                const previews = document.getElementById(previewsId);

                if (!dropzone || !fileInput) return;

                dropzone.addEventListener('click', () => fileInput.click());

                // Drag and drop highlights
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
                });

                function handleFiles(files) {
                    Array.from(files).forEach(file => {
                        if (!file.type.startsWith('image/')) {
                            showToast('Chỉ cho phép tải lên hình ảnh.', false);
                            return;
                        }

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
                                spinner.remove();
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
                        })
                        .catch(err => {
                            previewItem.remove();
                            showToast('Có lỗi xảy ra khi tải ảnh lên.', false);
                            console.error(err);
                        });
                    });
                }
            }

            setupUploader('menuDropzone', 'menuFilesInput', 'menuPreviews', menuPhotos);
            setupUploader('storefrontDropzone', 'storefrontFilesInput', 'storefrontPreviews', storefrontPhotos);

            // Load saved wizard state on page load
            loadWizardState();
            updateBizStepUI();

            // Update Mock Phone Photos grid based on uploaded storefront and menu photos
            function updateMockPhotosGrid() {
                const mockGrid = document.getElementById('mockPhotosGrid');
                if (!mockGrid) return;

                mockGrid.innerHTML = '';
                const allPhotos = [...storefrontPhotos, ...menuPhotos];

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

            // Step validations
            function validateBizStep() {
                if (bizStep === 1) {
                    if (!inputBizName.value.trim()) {
                        showToast('Vui lòng điền tên doanh nghiệp.', false);
                        return false;
                    }
                } else if (bizStep === 2) {
                    const selected = document.querySelectorAll('.biz-type-card.selected');
                    if (selected.length === 0) {
                        showToast('Vui lòng chọn ít nhất một loại hình.', false);
                        return false;
                    }
                } else if (bizStep === 3) {
                    if (!inputCategoryId.value) {
                        showToast('Vui lòng chọn đầy đủ danh mục chính và danh mục chi tiết.', false);
                        return false;
                    }
                } else if (bizStep === 4) {
                    if (!inputStreet.value.trim() || !inputCity.value.trim() || !inputProvince.value.trim()) {
                        showToast('Vui lòng nhập đầy đủ các trường địa chỉ bắt buộc (*).', false);
                        return false;
                    }
                } else if (bizStep === 5) {
                    if (!inputPhone.value.trim()) {
                        showToast('Vui lòng nhập số điện thoại liên hệ.', false);
                        return false;
                    }
                    const phoneVal = inputPhone.value.trim().replace(/\D/g, '');
                    if (phoneVal.length < 8) {
                        showToast('Số điện thoại liên hệ không hợp lệ (ít nhất 8 số).', false);
                        return false;
                    }
                } else if (bizStep === 6) {
                    if (!document.getElementById('input_lat').value || !document.getElementById('input_lng').value) {
                        showToast('Vui lòng kéo ghim trên bản đồ để chọn tọa độ.', false);
                        return false;
                    }
                } else if (bizStep === 8) {
                    if (!inputDesc.value.trim()) {
                        showToast('Vui lòng nhập mô tả giới thiệu về doanh nghiệp của bạn.', false);
                        return false;
                    }
                } else if (bizStep === 9) {
                    if (!menuPhotos || menuPhotos.length === 0) {
                        showToast('Vui lòng tải lên ít nhất 1 hình ảnh thực đơn/bảng giá/dịch vụ.', false);
                        return false;
                    }
                } else if (bizStep === 10) {
                    if (!storefrontPhotos || storefrontPhotos.length === 0) {
                        showToast('Vui lòng tải lên ít nhất 1 hình ảnh mặt tiền cửa hàng.', false);
                        return false;
                    }
                }
                return true;
            }

            // Next button clicked
            bizNextBtn.addEventListener('click', function() {
                if (!validateBizStep()) return;

                if (bizStep < totalBizSteps) {
                    bizStep++;
                    updateBizStepUI();
                } else {
                    submitBizRegistrationForm();
                }
            });

            // Prev button clicked
            bizPrevBtn.addEventListener('click', function() {
                if (bizStep > 1) {
                    bizStep--;
                    updateBizStepUI();
                }
            });

            // Final AJAX form submission
            function submitBizRegistrationForm() {
                const submitBtn = bizNextBtn;
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang gửi...`;

                const types = [];
                document.querySelectorAll('.biz-type-card.selected').forEach(c => {
                    types.push(c.getAttribute('data-val'));
                });

                const payload = {
                    business_name: inputBizName.value.trim(),
                    business_types: types,
                    category_id: inputCategoryId.value,
                    address_country: 'Việt Nam',
                    address_street: inputStreet.value.trim(),
                    address_city: inputCity.value.trim(),
                    address_province: inputProvince.value.trim(),
                    address_postal_code: document.getElementById('input_address_postal_code').value.trim(),
                    phone: inputPhone.value.trim(),
                    website: inputWebsite.value.trim(),
                    lat: parseFloat(document.getElementById('input_lat').value),
                    lng: parseFloat(document.getElementById('input_lng').value),
                    receive_tips: document.getElementById('receive_tips').checked ? 1 : 0,
                    receive_surveys: document.getElementById('receive_surveys').checked ? 1 : 0,
                    description: inputDesc.value.trim(),
                    menu_photos: menuPhotos,
                    storefront_photos: storefrontPhotos,
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
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Hoàn tất & Gửi';

                    if (data.success) {
                        clearWizardState();
                        showToast(data.message, true);
                        setTimeout(() => {
                            window.location.href = "{{ route('client.profile') }}";
                        }, 1500);
                    } else {
                        showToast(data.message || 'Lỗi gửi yêu cầu.', false);
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Hoàn tất & Gửi';
                    showToast('Đã xảy ra lỗi khi gửi yêu cầu nâng cấp.', false);
                    console.error(err);
                });
            }
        }
    });
</script>

</body>
</html>
