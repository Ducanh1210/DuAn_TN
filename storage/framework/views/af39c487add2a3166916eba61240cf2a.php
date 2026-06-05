<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Đồ - Hệ Thống POI</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>


    <style>
        :root {
            --primary: #0072FF;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            --region-line: #7ba7d4;
            --region-dim: #94a3b8;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
            background-color: #f0f2f5;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .leaflet-container {
            background: #e4e9ef;
        }



        /* Customizes Leaflet Zoom Control */
        .leaflet-bottom.leaflet-right {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(-40px); /* Tránh đè nút khi đóng */
            z-index: 999;
        }

        body.drawer-open .leaflet-bottom.leaflet-right {
            transform: translateY(-180px); /* Đẩy lên cao khi mở khay */
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--glass-shadow) !important;
        }
        .leaflet-control-zoom a {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(12px) !important;
            color: #333 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        .leaflet-control-zoom a:hover {
            background: #fff !important;
            color: var(--primary) !important;
        }
        
        /* Custom Popup Styling */
        .leaflet-popup-content-wrapper {
            border-radius: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .leaflet-popup-content {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            width: 260px !important;
        }
        .leaflet-popup-close-button {
            color: white !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8) !important;
            font-size: 22px !important;
            padding: 4px 8px !important;
            z-index: 10;
        }
        .leaflet-popup-close-button:hover {
            color: #f1f5f9 !important;
            background: transparent !important;
        }
        .poi-popup-inner {
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .poi-thumbnail {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: #f1f5f9;
        }
        .poi-content {
            padding: 16px;
        }
        .poi-title {
            font-weight: 700;
            font-size: 17px;
            color: #1a1a1a;
            margin-bottom: 6px;
        }
        .poi-desc {
            font-size: 13px;
            color: #555;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }
        .poi-btn-360 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: transparent;
            color: var(--poi-color, var(--primary)) !important;
            padding: 6px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none !important;
            transition: all 0.2s;
            border: 2px solid var(--poi-color, var(--primary));
            width: 100%;
            box-sizing: border-box;
        }
        .poi-btn-360:hover {
            filter: brightness(0.85);
            transform: translateY(-1px);
        }
        .poi-rate {
            display: inline-block;
            background: #f0fdf4;
            color: #16a34a;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Custom Map Pin */
        .custom-map-pin {
            position: relative;
            width: 30px;
            height: 40px;
            filter: drop-shadow(0px 3px 4px rgba(0,0,0,0.35));
        }
        .custom-map-pin svg {
            position: absolute;
            top: 0;
            left: 0;
        }
        .leaflet-container .leaflet-marker-pane .pin-icon-img {
            position: absolute !important;
            top: 4px !important;
            left: 4px !important;
            width: 22px !important;
            height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
            object-fit: cover !important;
            z-index: 999 !important;
            border-radius: 50% !important;
        }
        .custom-map-pin svg, .leaflet-container .leaflet-marker-pane .pin-icon-img {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Hiệu ứng hover CSS thuần - Khắc phục lỗi kẹt tooltip */
        .leaflet-marker-icon:has(.custom-map-pin:hover) {
            z-index: 10000 !important;
        }
        .custom-map-pin:hover svg, 
        .custom-map-pin:hover .pin-icon-img {
            transform: scale(1.05) translateY(-3px);
        }
        .custom-map-pin:hover .custom-pin-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translate(10px, -50%);
        }
        .custom-pin-tooltip {
            position: absolute;
            top: 15px; /* Căn giữa theo phần thân tròn của icon (cao 30px) */
            left: 100%;
            transform: translate(0px, -50%);
            background: linear-gradient(to right, color-mix(in srgb, var(--tip-color) 40%, black), var(--tip-color));
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 10001;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .custom-pin-tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -5px;
            transform: translateY(-50%);
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-right: 6px solid color-mix(in srgb, var(--tip-color) 40%, black);
        }

        /* Cluster Coverage Polygon on Hover */
        .leaflet-cluster-anim .leaflet-marker-icon,
        .leaflet-cluster-anim .leaflet-marker-shadow {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .marker-cluster {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .marker-cluster:hover {
            transform: scale(1.1);
        }

        /* Cluster coverage polygon animation */
        .cluster-coverage-polygon {
            transition: opacity 0.3s ease;
        }
        .leaflet-overlay-pane svg path {
            transition: fill-opacity 0.3s ease, stroke-opacity 0.3s ease;
        }

        /* Custom Locate Control integrated into Leaflet Zoom block */
        .leaflet-control-zoom a.leaflet-control-locate {
            border-bottom: none !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .leaflet-control-zoom a.leaflet-control-locate.loading span {
            animation: spin 1.5s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Pulsing User Location Marker */
        .user-location-marker {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .user-location-marker::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid var(--primary);
            animation: pulse-ring 1.8s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
            opacity: 0;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 0.8;
            }
            80%, 100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }

        /* Toast Notification Styling */
        .toast-container {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
            width: max-content;
            max-width: 90%;
        }

        .toast {
            pointer-events: auto;
            background: rgba(24, 24, 27, 0.88); /* translucent deep charcoal */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 8px 16px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 500;
            color: #ffffff;
            transform: translateY(-12px);
            opacity: 0;
            width: max-content;
            max-width: 280px;
            margin: 0 auto;
        }

        .toast-content {
            line-height: 1.4;
            white-space: nowrap;
        }

        .toast-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            flex-shrink: 0;
        }

        /* Top Floating Search Panel */
        .top-search-panel {
            position: absolute;
            top: 16px;
            left: 24px;
            right: 24px;
            z-index: 1000; /* Above map */
            display: flex;
            align-items: center;
            gap: 12px;
            pointer-events: none; /* Let clicks pass through empty areas */
        }

        .search-box-container {
            position: relative;
            display: flex;
            flex-direction: column;
            pointer-events: auto;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            padding: 2px 4px 2px 14px;
            height: 36px;
            box-sizing: border-box;
            width: 260px;
            flex-shrink: 0;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: 0 8px 32px 0 rgba(0, 114, 255, 0.25);
        }

        .search-input {
            flex: 1;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 15px;
            color: #333;
            background: transparent;
        }
        
        .search-input::placeholder {
            color: #666;
        }

        .search-actions {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .icon-btn {
            background: transparent;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #555;
            transition: background 0.2s;
        }

        .icon-btn span {
            font-size: 20px;
        }

        .icon-btn:hover {
            background: rgba(0,0,0,0.05);
            color: var(--primary);
        }

        .icon-btn.primary {
            background: var(--primary);
            width: 28px;
            height: 28px;
            border-radius: 8px;
            transform: rotate(45deg);
            margin: 0 6px;
            box-shadow: 0 2px 8px rgba(0, 114, 255, 0.4);
            transition: transform 0.2s, filter 0.2s;
        }
        
        .icon-btn.primary:hover {
            filter: brightness(1.1);
            transform: rotate(45deg) scale(1.05);
        }

        .icon-btn.primary span {
            color: white;
            font-size: 20px;
            transform: rotate(-45deg);
        }

        .search-box .divider {
            width: 1px;
            height: 24px;
            background: rgba(0,0,0,0.1);
            margin: 0 6px;
        }

        /* Search Suggestions */
        .search-suggestions {
            position: absolute;
            top: 42px;
            left: 0;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 12px;
            box-shadow: var(--glass-shadow);
            border: 1px solid var(--glass-border);
            overflow: hidden;
            display: none;
            flex-direction: column;
            max-height: 320px;
            overflow-y: auto;
            z-index: 1001;
        }

        /* Mini Status Bar Wrapper & Dropdown Banner */
        .mini-status-bar-wrapper {
            position: absolute;
            top: 16px;
            right: 24px;
            z-index: 1000;
        }

        .mini-status-bar {
            background: #ffffff;
            border-radius: 8px; /* Vuông vắn, chuyên nghiệp */
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 260px;
            box-sizing: border-box;
            padding: 8px 12px;
            gap: 14px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            z-index: 1002;
        }

        .mini-status-bar-wrapper:hover .mini-status-bar {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Mũi tên chĩa xuống từ thanh trạng thái (chỉ hiện khi click mở tin tức) */
        .mini-status-bar::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 34px; /* Căn thẳng với nút chevron */
            width: 10px;
            height: 10px;
            background: #ffffff;
            transform: rotate(45deg);
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 2px 2px 3px rgba(0,0,0,0.05);
            z-index: -1; /* Nằm dưới nền trắng của thanh trạng thái */
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
        }

        .mini-status-bar-wrapper.active .mini-status-bar::after {
            opacity: 1;
            visibility: visible;
        }


        .msb-weather {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 13px;
            color: #111827;
        }

        .msb-weather .material-symbols-rounded {
            font-size: 18px;
            color: #f59e0b;
        }

        .msb-divider {
            width: 1px;
            height: 16px;
            background: #d1d5db;
        }

        .msb-news {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #4b5563;
        }

        .msb-news .material-symbols-rounded {
            font-size: 18px;
            color: #6b7280;
        }

        .msb-news-text {
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .msb-chevron {
            font-size: 18px !important;
            color: #9ca3af !important;
            transition: transform 0.3s;
        }

        .mini-status-bar-wrapper.active .msb-chevron {
            transform: rotate(180deg);
        }

        /* Dropdown Banner Thời tiết */
        .weather-dropdown-banner {
            position: absolute;
            top: 0;
            right: 100%;
            margin-right: 12px;
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateX(15px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1000;
            padding: 16px;
            box-sizing: border-box;
        }

        .mini-status-bar-wrapper.weather-active .weather-dropdown-banner {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }

        /* Bottom Featured Drawer (Full Width Glassmorphism Theme) */
        .bottom-drawer-wrapper {
            position: absolute;
            bottom: 0;
            left: 0;
            z-index: 1000;
            width: 100%;
            display: flex;
            flex-direction: column;
            pointer-events: none;
        }

        .drawer-toggle-btn {
            pointer-events: auto;
            align-self: flex-end; /* Căn phải */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 8px 16px;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            cursor: pointer;
            margin-right: 20px;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-bottom: none;
        }

        .drawer-toggle-btn:hover {
            background: #ffffff;
        }

        .drawer-toggle-btn .material-symbols-rounded {
            font-size: 18px !important;
            color: var(--primary);
        }

        .drawer-chevron {
            transition: transform 0.3s;
            color: #6b7280 !important;
        }

        .bottom-drawer-wrapper.open .drawer-chevron {
            transform: rotate(180deg);
        }

        .drawer-content {
            pointer-events: auto;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            width: 100%;
            height: 0;
            opacity: 0;
            visibility: hidden;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            box-sizing: border-box;
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.6);
        }

        .bottom-drawer-wrapper.open .drawer-content {
            height: 160px; /* Chiều cao thẻ hình ảnh */
            opacity: 1;
            visibility: visible;
        }

        .featured-loc-scroll {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            width: 100%;
            height: 100%;
            align-items: center;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.4) transparent;
            padding: 16px;
            padding-bottom: 20px;
        }
        .featured-loc-scroll::-webkit-scrollbar { height: 6px; }
        .featured-loc-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.4); border-radius: 4px; }

        .featured-loc-card {
            position: relative;
            width: 200px;
            height: 120px;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 2px solid transparent;
        }

        .featured-loc-card:hover {
            transform: translateY(-4px);
            border-color: #ffffff;
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        .featured-loc-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .featured-loc-info {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px 10px 8px 10px;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
            display: flex;
            align-items: center;
            gap: 6px;
            box-sizing: border-box;
        }

        .featured-loc-title {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .featured-loc-info .material-symbols-rounded {
            font-size: 16px;
            color: #ffffff;
        }

        .weather-main {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }

        .weather-big-icon {
            font-size: 42px;
            color: #f59e0b;
        }

        .weather-temp-box {
            display: flex;
            flex-direction: column;
        }

        .weather-temp-box #weather-detail-temp {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .weather-desc {
            font-size: 13px;
            color: #4b5563;
            margin-top: 2px;
            font-weight: 500;
        }

        .weather-metrics {
            display: flex;
            justify-content: space-between;
        }

        .w-metric {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .w-metric .material-symbols-rounded {
            font-size: 20px;
            color: #3b82f6;
        }

        .w-metric span:not(.material-symbols-rounded) {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .w-metric small {
            font-size: 11px;
            color: #6b7280;
        }

        /* Dropdown Banner - Không khoảng trắng, ảnh tràn viền 100% */
        .news-dropdown-banner {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 10px; /* Cách mũi tên một khoảng nhỏ tinh tế */
            width: 260px;
            height: 150px;
            border-radius: 6px;
            overflow: hidden; /* Cắt gọn ảnh tràn góc */
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1000;
            background: #111827;
        }

        .mini-status-bar-wrapper.active .news-dropdown-banner {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .banner-track {
            display: flex;
            width: 400%; /* 4 items (3 real + 1 clone) */
            height: 100%;
            animation: bannerSlide 12s infinite ease-in-out;
        }

        .banner-track:hover {
            animation-play-state: paused; /* Dừng cuộn khi di chuột vào banner */
        }

        .banner-item {
            width: 25%; /* 100% / 4 */
            height: 100%;
            position: relative;
            flex-shrink: 0;
            display: block;
        }

        .banner-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .banner-item:hover img {
            transform: scale(1.05);
        }

        .banner-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, transparent 100%);
            padding: 30px 12px 10px 12px;
            box-sizing: border-box;
            pointer-events: none;
        }

        .banner-title {
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }

        .banner-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.8);
            font-size: 10px;
            margin-top: 6px;
            font-weight: 400;
        }

        .meta-left {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .banner-meta .material-symbols-rounded {
            font-size: 14px;
            opacity: 0.8;
        }

        .meta-left .material-symbols-rounded {
            font-size: 11px;
            opacity: 1;
        }

        @keyframes bannerSlide {
            0%, 30% { transform: translateX(0); }
            33.33%, 63.33% { transform: translateX(-25%); }
            66.66%, 96.66% { transform: translateX(-50%); }
            100% { transform: translateX(-75%); } /* Lướt nhanh trong 0.4s */
        }

        .search-suggestions.active {
            display: flex;
        }

        .suggestion-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-item:hover {
            background: rgba(0, 114, 255, 0.08);
        }
        .suggestion-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        .suggestion-icon span {
            font-size: 18px;
        }
        .suggestion-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .suggestion-name {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .suggestion-cat {
            font-size: 12px;
            color: #666;
        }
        .no-results {
            padding: 16px 20px;
            font-size: 14px;
            color: #666;
            text-align: center;
        }

        .categories-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            pointer-events: auto;
            padding: 4px 0;
            /* Hide scrollbar */
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .categories-scroll::-webkit-scrollbar {
            display: none;
        }

        .category-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            padding: 0 14px;
            height: 36px;
            box-sizing: border-box;
            border-radius: 20px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }

        .category-pill:hover, .category-pill.active {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 114, 255, 0.15);
        }

        .category-pill span {
            font-size: 18px;
            color: inherit;
        }
        
        /* Custom Leaflet Zoom Controls */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15) !important;
            border-radius: 8px !important;
            overflow: hidden;
            margin-bottom: 24px !important;
            margin-right: 24px !important;
        }

        .leaflet-control-zoom a {
            background-color: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #374151 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
            font-size: 18px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .leaflet-control-zoom a:last-child {
            border-bottom: none !important;
        }

        .leaflet-control-zoom a:hover {
            background-color: #ffffff !important;
            color: #3b82f6 !important;
        }

        /* Reposition toast container so it doesn't overlap search */
        .toast-container {
            top: 80px !important;
        }
    </style>
</head>
<body>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Top Floating Search Panel -->
    <div class="top-search-panel">
        <div class="search-box-container">
            <div class="search-box">
                <input type="text" id="map-search-input" placeholder="Tìm kiếm địa điểm..." class="search-input" autocomplete="off">
                <div class="search-actions">
                    <button class="icon-btn" title="Tìm kiếm"><span class="material-symbols-rounded">search</span></button>
                </div>
            </div>
            
            <!-- Dropdown Gợi ý tìm kiếm -->
            <div class="search-suggestions" id="search-suggestions">
                <!-- Nội dung được sinh ra bằng Javascript -->
            </div>
        </div>
        
        <div class="categories-scroll" id="map-categories">
            <!-- Render danh mục bằng Javascript từ dữ liệu thật -->
        </div>
    </div>

    <!-- Mini Status Bar Wrapper -->
    <div class="mini-status-bar-wrapper active" id="news-widget-wrapper">
        <!-- Thanh Mini gọn nhẹ nằm ngang -->
        <div class="mini-status-bar" id="news-widget-toggle">
            <div class="msb-weather" id="weather-toggle-btn" title="Xem chi tiết thời tiết">
                <span class="material-symbols-rounded" id="weather-icon">hourglass_empty</span>
                <span id="weather-temp">...</span>
            </div>
            <div class="msb-divider"></div>
            <a href="/tin-tuc" class="msb-news" id="news-toggle-btn" title="Xem tất cả tin tức" style="text-decoration: none; color: inherit;">
                <span class="material-symbols-rounded">newspaper</span>
                <span class="msb-news-text">Tin tức và sự kiện</span>
                <span class="material-symbols-rounded msb-chevron" style="transform: rotate(-45deg);">arrow_forward</span>
            </a>
        </div>
        
        <!-- Dropdown Thời tiết -->
        <div class="weather-dropdown-banner">
            <div class="weather-main">
                <span class="material-symbols-rounded weather-big-icon" id="weather-detail-icon">partly_cloudy_day</span>
                <div class="weather-temp-box">
                    <span id="weather-detail-temp">--°C</span>
                    <span class="weather-desc" id="weather-detail-desc">Đang cập nhật...</span>
                </div>
            </div>
            <div class="weather-metrics">
                <div class="w-metric">
                    <span class="material-symbols-rounded">water_drop</span>
                    <span id="weather-humidity">--%</span>
                    <small>Độ ẩm</small>
                </div>
                <div class="w-metric">
                    <span class="material-symbols-rounded">air</span>
                    <span id="weather-wind">-- km/h</span>
                    <small>Gió</small>
                </div>
                <div class="w-metric">
                    <span class="material-symbols-rounded">thermostat</span>
                    <span id="weather-feels">--°C</span>
                    <small>Cảm giác</small>
                </div>
            </div>
        </div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($newsList) && $newsList->count() > 0): ?>
        <!-- Dropdown Banner tự động chạy -->
        <div class="news-dropdown-banner">
            <div class="banner-track">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $newsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="#" class="banner-item">
                    <img src="<?php echo e($news->featured_image ? asset('storage/' . $news->featured_image) : 'https://placehold.co/260x150/1e293b/f8fafc?text=NEWS'); ?>" alt="<?php echo e($news->title); ?>">
                    <div class="banner-overlay">
                        <div class="banner-title"><?php echo e($news->title); ?></div>
                        <div class="banner-meta">
                            <div class="meta-left">
                                <span class="material-symbols-rounded">calendar_today</span>
                                <?php echo e($news->published_at ? $news->published_at->format('d/m/Y') : $news->created_at->format('d/m/Y')); ?>

                            </div>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <!-- Clone of first item for seamless infinite loop -->
                <?php $firstNews = $newsList->first(); ?>
                <a href="#" class="banner-item" aria-hidden="true">
                    <img src="<?php echo e($firstNews->featured_image ? asset('storage/' . $firstNews->featured_image) : 'https://placehold.co/260x150/1e293b/f8fafc?text=NEWS'); ?>" alt="<?php echo e($firstNews->title); ?> Clone">
                    <div class="banner-overlay">
                        <div class="banner-title"><?php echo e($firstNews->title); ?></div>
                        <div class="banner-meta">
                            <div class="meta-left">
                                <span class="material-symbols-rounded">calendar_today</span>
                                <?php echo e($firstNews->published_at ? $firstNews->published_at->format('d/m/Y') : $firstNews->created_at->format('d/m/Y')); ?>

                            </div>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Bottom Featured Drawer (Full Width) -->
    <div class="bottom-drawer-wrapper open" id="featured-drawer">
        <div class="drawer-toggle-btn" id="drawer-toggle-btn" title="Địa điểm nổi bật">
            <span class="material-symbols-rounded">star</span>
            <span class="drawer-title">Địa điểm nổi bật</span>
            <span class="material-symbols-rounded drawer-chevron">expand_less</span>
        </div>
        <div class="drawer-content">
            <div class="featured-loc-scroll">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locations->sortByDesc('view_count')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="#" class="featured-loc-card" onclick="flyToLocation(<?php echo e($loc->id); ?>); return false;">
                    <img src="<?php echo e($loc->thumbnail_url ?: 'https://placehold.co/300x200/1e3a8a/ffffff?text=No+Image'); ?>" alt="<?php echo e($loc->name); ?>" class="featured-loc-img">
                    <div class="featured-loc-info">
                        <span class="material-symbols-rounded">account_balance</span>
                        <div class="featured-loc-title"><?php echo e($loc->name); ?></div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>



    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Leaflet JS & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script src="https://unpkg.com/@turf/turf@7.2.0/dist/turf.min.js"></script>

    <script>

        const HA_NAM_BOUNDARY_URL = <?php echo json_encode(asset('geo/ha-nam-old.geojson'), 15, 512) ?>;

        let haNamGeo = null;
        let outsideMask = null;

        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false, // Ẩn dòng chữ bản quyền Leaflet ở góc dưới cùng bên phải
            maxBoundsViscosity: 0.8, // Giảm độ cứng của ranh giới (để kéo được dãn ra và tự nảy về)
            preferCanvas: true,
        });

        map.createPane('dimPane');
        map.getPane('dimPane').style.zIndex = 450;

        // Giữ padding ở mức vừa phải (0.5) để tối ưu hiệu năng render khi zoom
        // Padding quá cao (như 2.0) sẽ tạo ra canvas khổng lồ (gấp 25 lần màn hình) gây giật lag
        const vectorRenderer = L.canvas({ padding: 0.5 });
        // Thêm Base Map
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20,
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);



        function ringsFromGeo(geo) {
            const holes = [];
            if (geo.type === 'MultiPolygon') {
                geo.coordinates.forEach((polygon) => {
                    holes.push(polygon[0].map(([lng, lat]) => [lat, lng]));
                });
            } else if (geo.type === 'Polygon') {
                holes.push(geo.coordinates[0].map(([lng, lat]) => [lat, lng]));
            }
            return holes;
        }

        function setOutsideDimMask(geo) {
            if (outsideMask) {
                map.removeLayer(outsideMask);
            }
            const world = [[-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]];
            const holes = ringsFromGeo(geo);
            outsideMask = L.polygon([world, ...holes], {
                pane: 'dimPane',
                renderer: vectorRenderer,
                fillColor: '#94a3b8',
                fillOpacity: 0.22,
                stroke: false,
                interactive: false,
            }).addTo(map);
        }

        function refreshMask() {
            if (outsideMask) {
                outsideMask.redraw();
            }
        }

        // Tắt tính năng ép vẽ lại liên tục (redraw) gây giật lag khi kéo bản đồ
        // map.on('move', refreshMask);
        // map.on('zoomanim', refreshMask);
        // map.on('resize', refreshMask);

        // Ranh giới tỉnh Hà Nam cũ (OSM relation 1901010, boundary=historic, hết hiệu lực 30/06/2025)
        fetch(HA_NAM_BOUNDARY_URL)
            .then((res) => res.json())
            .then((geo) => {
                haNamGeo = geo;
                setOutsideDimMask(geo);

                const border = L.geoJSON(geo, {
                    style: {
                        color: '#7ba7d4',
                        weight: 2,
                        opacity: 0.55,
                        fillColor: '#f8fafc',
                        fillOpacity: 0.04,
                    },
                    renderer: vectorRenderer,
                    interactive: false,
                }).addTo(map);
                border.bringToFront();

                const bounds = border.getBounds();
                // Căn giữa bản đồ vào Hà Nam
                map.fitBounds(bounds);
                
                // Mặc định zoom cận cảnh hơn 1 mức (như trong ảnh bạn yêu cầu)
                map.setZoom(map.getZoom() + 1);
                
                // Khóa không cho zoom out xa hơn mức mặc định này
                map.setMinZoom(map.getZoom());
                
                // Nới rộng giới hạn kéo thả để người dùng xem được các vùng lân cận rộng hơn
                map.setMaxBounds(bounds.pad(0.5));

            })
            .catch((err) => console.error('Không tải được ranh giới Hà Nam:', err));

        function isInsideHaNam(lat, lon) {
            if (!haNamGeo || typeof turf === 'undefined') {
                return true;
            }
            return turf.booleanPointInPolygon(turf.point([lon, lat]), haNamGeo);
        }

        // Render markers for locations
        const locations = <?php echo json_encode($locations, 15, 512) ?>;

        // Tạo pane riêng cho coverage polygon (z-index cao hơn dimPane)
        map.createPane('coveragePane');
        map.getPane('coveragePane').style.zIndex = 460;
        const coverageSvgRenderer = L.svg({ pane: 'coveragePane' });

        let coveragePolygon = null;

        const markers = L.markerClusterGroup({
            maxClusterRadius: 80,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false, // Tự implement thủ công
            zoomToBoundsOnClick: false,
            iconCreateFunction: function(cluster) {
                const count = cluster.getChildCount();
                let size = 'small';
                if (count >= 10) size = 'medium';
                if (count >= 30) size = 'large';
                return L.divIcon({
                    html: '<div><span>' + count + '</span></div>',
                    className: 'marker-cluster marker-cluster-' + size,
                    iconSize: L.point(40, 40)
                });
            }
        });

        // Custom hover coverage polygon
        function convexHull(points) {
            // Graham scan
            if (points.length < 3) return points.slice();
            points = points.slice().sort((a, b) => a[0] - b[0] || a[1] - b[1]);
            const cross = (O, A, B) => (A[0]-O[0])*(B[1]-O[1]) - (A[1]-O[1])*(B[0]-O[0]);
            const lower = [];
            for (const p of points) { while (lower.length >= 2 && cross(lower[lower.length-2], lower[lower.length-1], p) <= 0) lower.pop(); lower.push(p); }
            const upper = [];
            for (let i = points.length - 1; i >= 0; i--) { const p = points[i]; while (upper.length >= 2 && cross(upper[upper.length-2], upper[upper.length-1], p) <= 0) upper.pop(); upper.push(p); }
            upper.pop(); lower.pop();
            return lower.concat(upper);
        }

        // Tự động xóa polygon khi bản đồ bắt đầu di chuyển hoặc zoom
        // Khắc phục lỗi kẹt polygon khi click vào cluster hoặc kéo map nhanh
        function clearCoveragePolygon() {
            if (coveragePolygon) {
                map.removeLayer(coveragePolygon);
                coveragePolygon = null;
            }
        }
        map.on('zoomstart', clearCoveragePolygon);
        map.on('movestart', clearCoveragePolygon);

        markers.on('clustermouseover', function(e) {
            if (coveragePolygon) { map.removeLayer(coveragePolygon); coveragePolygon = null; }

            const childMarkers = e.layer.getAllChildMarkers();
            const points = childMarkers.map(m => {
                const ll = m.getLatLng();
                return [ll.lat, ll.lng];
            });

            if (points.length < 2) return;

            let latlngs;
            if (points.length === 2) {
                latlngs = points.map(p => L.latLng(p[0], p[1]));
            } else {
                const hull = convexHull(points);
                latlngs = hull.map(p => L.latLng(p[0], p[1]));
            }

            coveragePolygon = L.polygon(latlngs, {
                pane: 'coveragePane',
                renderer: coverageSvgRenderer,
                fillColor: '#3388ff',
                fillOpacity: 0.15,
                weight: 2.5,
                opacity: 0.7,
                color: '#3388ff',
                smoothFactor: 1,
                interactive: false,
                className: 'cluster-coverage-polygon'
            }).addTo(map);
        });

        markers.on('clustermouseout', function(e) {
            if (coveragePolygon) {
                map.removeLayer(coveragePolygon);
                coveragePolygon = null;
            }
        });

        markers.on('clusterclick', function (a) {
            if (coveragePolygon) { map.removeLayer(coveragePolygon); coveragePolygon = null; }

            // Zoom dần dần: mỗi lần click tăng tối đa 3 level
            // Dùng vị trí cluster (chỗ anh bấm) thay vì tâm bounds để zoom thẳng vào, không bị lệch
            var clusterLatLng = a.layer.getLatLng();
            var currentZoom = map.getZoom();
            var maxZoom = map.getMaxZoom() || 20;
            var targetZoom = Math.min(currentZoom + 3, maxZoom);

            map.setView(clusterLatLng, targetZoom, { animate: true, duration: 0.4 });
        });

        locations.forEach(loc => {
            if (loc.lat && loc.lng) {
                let markerOptions = {};
                const iconUrl = loc.category && loc.category.icon_url ? loc.category.icon_url : null;

                if (iconUrl) {
                    const iconColor = loc.category && loc.category.icon_color ? loc.category.icon_color : '#ef4444';
                    const pinHtml = '<div class="custom-map-pin">'
                        + '<svg class="pin-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="30" height="40">'
                        + '<path fill="' + iconColor + '" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0z"/>'
                        + '</svg>'
                        + '<img class="pin-icon-img" src="' + iconUrl + '">'
                        + '<div class="custom-pin-tooltip" style="--tip-color: ' + iconColor + ';">' + loc.name + '</div>'
                        + '</div>';

                    const customIcon = L.divIcon({
                        className: '',
                        html: pinHtml,
                        iconSize: [30, 40],
                        iconAnchor: [15, 40],
                        popupAnchor: [0, -40]
                    });
                    markerOptions = { icon: customIcon };
                }

                const marker = L.marker([loc.lat, loc.lng], markerOptions);
                const thumbUrl = loc.thumbnail_url ? loc.thumbnail_url : 'https://placehold.co/400x250/e2e8f0/475569?text=No+Image';
                const iconColor = loc.category && loc.category.icon_color ? loc.category.icon_color : '#ef4444';
                
                const popupHtml = '<div class="poi-popup-inner" style="--poi-color: ' + iconColor + ';">'
                    + '<img src="' + thumbUrl + '" class="poi-thumbnail" alt="' + loc.name + '">'
                    + '<div class="poi-content">'
                    + '<div class="poi-title">' + loc.name + '</div>'
                    + (loc.short_description ? '<div class="poi-desc">' + loc.short_description + '</div>' : '')
                    + '<a href="/locations/' + loc.slug + '/360" class="poi-btn-360">'
                    + 'Khám phá ngay'
                    + '</a>'
                    + '</div>'
                    + '</div>';
                
                marker.bindPopup(popupHtml, { minWidth: 260, maxWidth: 260, closeButton: false });
                
                // Lưu lại marker vào object loc để dùng cho chức năng tìm kiếm
                loc.marker = marker;

                markers.addLayer(marker);
            }
        });

        map.addLayer(markers);

        // --- Logic Tìm kiếm và Danh mục ---
        const searchInput = document.getElementById('map-search-input');
        const suggestionsBox = document.getElementById('search-suggestions');
        const categoriesScroll = document.getElementById('map-categories');

        // Lấy danh sách danh mục duy nhất từ locations
        const uniqueCategories = [];
        const catMap = {};
        locations.forEach(loc => {
            if (loc.category && !catMap[loc.category.id]) {
                catMap[loc.category.id] = true;
                uniqueCategories.push(loc.category);
            }
        });

        // Render category pills
        uniqueCategories.forEach(cat => {
            const btn = document.createElement('button');
            btn.className = 'category-pill';
            // Giả lập icon từ google font, nếu có icon url thì dùng img, ở đây dùng tạm một icon chung nếu không parse được
            const iconColor = cat.icon_color || 'var(--primary)';
            btn.innerHTML = `<span class="material-symbols-rounded" style="color: ${iconColor};">location_on</span> ${cat.name}`;
            
            btn.addEventListener('click', () => {
                // Nếu đang active thì bấm để bỏ lọc (hiển thị tất cả)
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');
                    markers.clearLayers();
                    locations.forEach(loc => {
                        if (loc.marker) {
                            markers.addLayer(loc.marker);
                        }
                    });
                    return;
                }

                // Remove active class from all
                document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                
                // Filter markers
                markers.clearLayers();
                locations.forEach(loc => {
                    if (loc.category && loc.category.id === cat.id && loc.marker) {
                        markers.addLayer(loc.marker);
                    }
                });
            });
            categoriesScroll.appendChild(btn);
        });

        // Hàm loại bỏ dấu tiếng Việt
        function removeAccents(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
        }

        // Xử lý tìm kiếm gợi ý
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            const valNoAccent = removeAccents(val);
            suggestionsBox.innerHTML = '';
            
            if (val.length === 0) {
                suggestionsBox.classList.remove('active');
                return;
            }

            const results = locations.filter(loc => {
                const nameNoAccent = removeAccents(loc.name.toLowerCase());
                return nameNoAccent.includes(valNoAccent);
            });
            
            if (results.length > 0) {
                results.slice(0, 10).forEach(loc => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';
                    const color = loc.category?.icon_color || 'var(--primary)';
                    
                    item.innerHTML = `
                        <div class="suggestion-icon" style="background: ${color};">
                            <span class="material-symbols-rounded">location_on</span>
                        </div>
                        <div class="suggestion-info">
                            <div class="suggestion-name">${loc.name}</div>
                            <div class="suggestion-cat">${loc.category?.name || 'Chưa phân loại'}</div>
                        </div>
                    `;
                    
                    item.addEventListener('click', () => {
                        suggestionsBox.classList.remove('active');
                        searchInput.value = loc.name;
                        
                        // Đảm bảo marker đang hiển thị (nếu đang filter)
                        if (!markers.hasLayer(loc.marker)) {
                            markers.addLayer(loc.marker);
                        }
                        
                        // Zoom từng cấp cụm một (step-by-step) thay vì nhảy vọt
                        stepZoomToMarker(loc, () => {
                            let targetZoom = Math.max(18, map.getZoom());
                            let dist = map.getCenter().distanceTo([loc.lat, loc.lng]);
                            
                            if (dist > 500) {
                                map.flyTo([loc.lat, loc.lng], targetZoom, { duration: 1.2 });
                            } else {
                                map.setView([loc.lat, loc.lng], targetZoom, { animate: true, duration: 1.2 });
                            }
                            
                            // Đợi bay đến giữa rồi mới mở popup để tránh giật hình
                            setTimeout(() => {
                                loc.marker.openPopup();
                            }, 800);
                        });
                    });
                    
                    suggestionsBox.appendChild(item);
                });
            } else {
                suggestionsBox.innerHTML = '<div class="no-results">Không tìm thấy địa điểm nào</div>';
            }
            
            suggestionsBox.classList.add('active');
        });

        // Ẩn gợi ý khi click ra ngoài
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.remove('active');
            }
        });

        let loadingToast = null;

        // Toast Notification System with GSAP
        function showToast(message, type = 'info', duration = 4000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            if (type === 'loading') {
                toast.innerHTML = `<div class="toast-spinner"></div><div class="toast-content">${message}</div>`;
            } else {
                toast.innerHTML = `<div class="toast-content">${message}</div>`;
            }
            
            container.appendChild(toast);

            // GSAP Enter Animation
            gsap.to(toast, {
                y: 0,
                opacity: 1,
                duration: 0.3,
                ease: 'power2.out'
            });

            let autoDismissTimeout = null;

            // Auto dismiss after duration if duration > 0
            if (duration > 0) {
                autoDismissTimeout = setTimeout(() => {
                    dismissToast(toast);
                }, duration);
            }

            // Custom dismiss function
            toast.dismiss = () => {
                if (autoDismissTimeout) clearTimeout(autoDismissTimeout);
                dismissToast(toast);
            };

            return toast;
        }

        function dismissToast(toast) {
            gsap.to(toast, {
                y: -15,
                opacity: 0,
                duration: 0.25,
                ease: 'power2.in',
                onComplete: () => {
                    toast.remove();
                }
            });
        }

        // Global variables for user location
        let userCoords = null;
        let userMarker = null;
        let isLocatingInProgress = false;
        let pendingFlyTo = false;

        // Function to update user location marker
        function updateUserMarker(lat, lng) {
            const latlng = L.latLng(lat, lng);
            if (userMarker) {
                userMarker.setLatLng(latlng);
            } else {
                const userIcon = L.divIcon({
                    className: '',
                    html: '<div class="user-location-marker"></div>',
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });
                userMarker = L.marker(latlng, { icon: userIcon, zIndexOffset: 1000 }).addTo(map);
            }
        }

        // Function to request user location
        function requestUserLocation(silent = false) {
            if (!navigator.geolocation) {
                if (!silent) showToast('Trình duyệt của bạn không hỗ trợ định vị.', 'error');
                return;
            }

            const btn = document.querySelector('.leaflet-control-locate');
            
            isLocatingInProgress = true;
            if (!silent && btn) {
                btn.classList.add('loading');
                pendingFlyTo = true;
            }

            // If user-initiated, show loading toast
            if (!silent) {
                if (loadingToast) {
                    loadingToast.dismiss();
                }
                loadingToast = showToast('Đang xác định vị trí của bạn...', 'loading', 0);
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    userCoords = { lat: latitude, lng: longitude };
                    isLocatingInProgress = false;
                    
                    if (btn) {
                        btn.classList.remove('loading');
                    }

                    if (loadingToast) {
                        loadingToast.dismiss();
                        loadingToast = null;
                    }
                    
                    if (pendingFlyTo) {
                        pendingFlyTo = false;
                        updateUserMarker(latitude, longitude);
                        flyToUserLocation();
                    }
                },
                (error) => {
                    isLocatingInProgress = false;
                    if (btn) {
                        btn.classList.remove('loading');
                    }

                    if (loadingToast) {
                        loadingToast.dismiss();
                        loadingToast = null;
                    }

                    const wasPending = pendingFlyTo;
                    pendingFlyTo = false;

                    console.warn('Geolocation error:', error.message);
                    if (!silent || wasPending) {
                        let msg = 'Không thể lấy vị trí của bạn.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Vui lòng cấp quyền vị trí trong cài đặt trình duyệt để sử dụng tính năng này.';
                        }
                        showToast(msg, 'warning');
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 30000 // Cache position for 30 seconds to make subsequent clicks instant
                }
            );
        }

        // Function to fly map to user location
        function flyToUserLocation() {
            const btn = document.querySelector('.leaflet-control-locate');
            
            if (userCoords) {
                if (loadingToast) {
                    loadingToast.dismiss();
                    loadingToast = null;
                }
                
                updateUserMarker(userCoords.lat, userCoords.lng);
                map.setView([userCoords.lat, userCoords.lng], 16, {
                    animate: true,
                    duration: 1.2
                });

                // Check if user is inside Ha Nam province
                if (haNamGeo && !isInsideHaNam(userCoords.lat, userCoords.lng)) {
                    showToast('Bạn đang ở ngoài khu vực Hà Nam.', 'warning');
                } else {
                    showToast('Đã định vị thành công vị trí của bạn.', 'success');
                }
                return;
            }

            // If a request is already running, wait for it
            if (isLocatingInProgress) {
                pendingFlyTo = true;
                if (btn) {
                    btn.classList.add('loading');
                }
                if (!loadingToast) {
                    loadingToast = showToast('Đang xác định vị trí của bạn...', 'loading', 0);
                }
                return;
            }

            // If no request is running and no coordinates are saved, start new request
            requestUserLocation(false);
        }

        // Create and append Locate Button to Leaflet Zoom Control container
        const zoomContainer = document.querySelector('.leaflet-control-zoom');
        if (zoomContainer) {
            const locateBtn = document.createElement('a');
            locateBtn.className = 'leaflet-control-locate';
            locateBtn.href = '#';
            locateBtn.title = 'Vị trí của tôi';
            locateBtn.role = 'button';
            locateBtn.innerHTML = '<span class="material-symbols-rounded" style="font-size: 18px; vertical-align: middle; line-height: 30px;">my_location</span>';
            
            // Prevent map dragging/clicking when clicking the control button
            L.DomEvent.disableClickPropagation(locateBtn);
            
            locateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                flyToUserLocation();
            });
            
            zoomContainer.appendChild(locateBtn);
        }

        // Fetch real weather data for Ha Nam (Phu Ly coordinates: ~20.5453, 105.9122)
        function fetchWeatherForHaNam() {
            const url = 'https://api.open-meteo.com/v1/forecast?latitude=20.5453&longitude=105.9122&current=temperature_2m,relative_humidity_2m,apparent_temperature,wind_speed_10m,weather_code,is_day';
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.current) {
                        const temp = Math.round(data.current.temperature_2m) + '°C';
                        const code = data.current.weather_code;
                        const isDay = data.current.is_day; // 0 or 1
                        
                        document.getElementById('weather-temp').textContent = temp;
                        document.getElementById('weather-detail-temp').textContent = temp;
                        document.getElementById('weather-humidity').textContent = data.current.relative_humidity_2m + '%';
                        document.getElementById('weather-wind').textContent = data.current.wind_speed_10m + ' km/h';
                        document.getElementById('weather-feels').textContent = Math.round(data.current.apparent_temperature) + '°C';
                        
                        let icon = 'partly_cloudy_day';
                        let desc = 'Có mây';
                        // WMO Weather interpretation codes
                        if (code === 0) { icon = isDay ? 'sunny' : 'clear_night'; desc = 'Quang đãng'; }
                        else if (code === 1 || code === 2) { icon = isDay ? 'partly_cloudy_day' : 'partly_cloudy_night'; desc = 'Ít mây'; }
                        else if (code === 3) { icon = 'cloudy'; desc = 'Nhiều mây'; }
                        else if (code >= 45 && code <= 48) { icon = 'foggy'; desc = 'Sương mù'; }
                        else if (code >= 51 && code <= 67) { icon = 'rainy'; desc = 'Có mưa'; }
                        else if (code >= 71 && code <= 77) { icon = 'ac_unit'; desc = 'Tuyết rơi'; }
                        else if (code >= 80 && code <= 82) { icon = 'rainy'; desc = 'Mưa rào'; }
                        else if (code >= 95 && code <= 99) { icon = 'thunderstorm'; desc = 'Mưa dông'; }
                        
                        document.getElementById('weather-icon').textContent = icon;
                        document.getElementById('weather-detail-icon').textContent = icon;
                        document.getElementById('weather-detail-desc').textContent = desc;
                    }
                })
                .catch(err => {
                    console.error('Weather API error:', err);
                    document.getElementById('weather-temp').textContent = '--°C';
                });
        }

        // Request user location automatically on page load
        window.addEventListener('DOMContentLoaded', () => {
            requestUserLocation(true);
            fetchWeatherForHaNam(); // Kích hoạt lấy thời tiết thực tế
        });

        // ==========================================
        // TIN TỨC & THỜI TIẾT DROPDOWN TOGGLE
        // ==========================================
        const newsToggleBtn = document.getElementById('news-toggle-btn');
        const weatherToggleBtn = document.getElementById('weather-toggle-btn');
        const newsWidgetWrapper = document.getElementById('news-widget-wrapper');
        const newsWidgetToggle = document.getElementById('news-widget-toggle'); // Container của 2 nút
        
        if (newsWidgetWrapper) {
            // Nút tin tức giờ đã thành thẻ <a> để chuyển hướng, không còn dùng JS để đóng mở nữa
            if (weatherToggleBtn) {
                weatherToggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    newsWidgetWrapper.classList.toggle('weather-active');
                });
            }
            
            // Ngăn sự kiện click bên trong các banner truyền ra ngoài
            const dropdownBanner = newsWidgetWrapper.querySelector('.news-dropdown-banner');
            if(dropdownBanner) {
                dropdownBanner.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                L.DomEvent.disableClickPropagation(dropdownBanner);
                L.DomEvent.disableScrollPropagation(dropdownBanner);
            }
            
            const weatherBanner = newsWidgetWrapper.querySelector('.weather-dropdown-banner');
            if(weatherBanner) {
                weatherBanner.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                L.DomEvent.disableClickPropagation(weatherBanner);
                L.DomEvent.disableScrollPropagation(weatherBanner);
            }
            
            // Ngăn chặn click vào thanh mini truyền qua bản đồ
            if(newsWidgetToggle) {
                L.DomEvent.disableClickPropagation(newsWidgetToggle);
                L.DomEvent.disableScrollPropagation(newsWidgetToggle);
            }
        }

        // Click ra ngoài bản đồ để đóng bảng thời tiết (nhưng giữ nguyên bảng tin tức)
        document.addEventListener('click', function(e) {
            if (newsWidgetWrapper && !newsWidgetWrapper.contains(e.target)) {
                newsWidgetWrapper.classList.remove('weather-active');
            }
        });

        // ==========================================
        // FEATURED DRAWER TOGGLE
        // ==========================================
        const drawerToggleBtn = document.getElementById('drawer-toggle-btn');
        const featuredDrawer = document.getElementById('featured-drawer');
        
        if (drawerToggleBtn && featuredDrawer) {
            // Đồng bộ trạng thái ban đầu
            if(featuredDrawer.classList.contains('open')) {
                document.body.classList.add('drawer-open');
            }

            drawerToggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                featuredDrawer.classList.toggle('open');
                document.body.classList.toggle('drawer-open', featuredDrawer.classList.contains('open'));
            });
        }
        
        // Prevent map click when clicking drawer
        if(featuredDrawer) {
            featuredDrawer.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            L.DomEvent.disableClickPropagation(featuredDrawer);
            L.DomEvent.disableScrollPropagation(featuredDrawer);
        }

        // Hàm đệ quy để zoom mở từng lớp cụm (cluster) một
        function stepZoomToMarker(loc, finalCallback) {
            if (!markers.hasLayer(loc.marker)) {
                markers.addLayer(loc.marker);
            }

            function step() {
                let parent = markers.getVisibleParent(loc.marker);
                
                if (parent && parent.getChildCount) {
                    let bounds = parent.getBounds();
                    let targetZoom = map.getBoundsZoom(bounds, false, [40, 40]);
                    let currentZoom = map.getZoom();
                    
                    if (currentZoom >= targetZoom || currentZoom >= map.getMaxZoom()) {
                        parent.spiderfy();
                        setTimeout(finalCallback, 450);
                        return;
                    }

                    let moved = false;
                    function onMoveEnd() {
                        if (moved) return;
                        moved = true;
                        map.off('moveend', onMoveEnd);
                        setTimeout(step, 450); // Dừng 0.45s ở mỗi cấp cụm để người dùng nhìn rõ
                    }
                    
                    map.on('moveend', onMoveEnd);
                    parent.zoomToBounds({ padding: [40, 40] });
                    
                    setTimeout(() => {
                        if (!moved) onMoveEnd();
                    }, 1500); // fallback
                } else {
                    finalCallback();
                }
            }
            
            step();
        }

        // Helper function to fly to a location from the featured drawer
        function flyToLocation(id) {
            const loc = locations.find(l => l.id === id);
            if (!loc || !loc.marker) return;

            if(featuredDrawer) featuredDrawer.classList.remove('open');
            document.body.classList.remove('drawer-open');
            
            // Zoom từng lớp cụm một y hệt như tìm kiếm
            stepZoomToMarker(loc, () => {
                // Đưa marker ra chính giữa màn hình nhưng KHÔNG BAO GIỜ thu nhỏ lại
                let targetZoom = Math.max(18, map.getZoom());
                let dist = map.getCenter().distanceTo([loc.lat, loc.lng]);
                
                if (dist > 500) {
                    map.flyTo([loc.lat, loc.lng], targetZoom, { duration: 1.2 });
                } else {
                    map.setView([loc.lat, loc.lng], targetZoom, { animate: true, duration: 1.2 });
                }
                
                // Đợi bay đến giữa rồi mới mở popup để tránh giật hình
                setTimeout(() => {
                    loc.marker.openPopup();
                }, 800);
            });
        }
        // Cho phép cuộn ngang bằng con lăn chuột (mouse wheel)
        const horizontalScrolls = document.querySelectorAll('.featured-loc-scroll, .categories-scroll');
        horizontalScrolls.forEach(container => {
            container.addEventListener('wheel', (evt) => {
                if (evt.deltaY !== 0) {
                    evt.preventDefault(); // Ngăn cuộn trang dọc
                    container.scrollLeft += evt.deltaY; // Chuyển sang cuộn ngang
                }
            });
        });
    </script>
</body>
</html>
<?php /**PATH D:\laragon\www\Du_An_TN\resources\views/client/home.blade.php ENDPATH**/ ?>