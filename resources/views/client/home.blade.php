<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Đồ - Hệ Thống POI</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Avatar Frames CSS -->
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">

    <style>
        :root {
            --primary: #1e3a5f;
            --glass-bg: rgba(255, 255, 255, 0.92);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: none;
            --region-line: #7ba7d4;
            --region-dim: #94a3b8;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
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
            transform: translateY(-40px);
            /* Tránh đè nút khi đóng */
            z-index: 999;
        }

        body.drawer-open .leaflet-bottom.leaflet-right {
            transform: translateY(-180px);
            /* Đẩy lên cao khi mở khay */
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: none;
        }

        .leaflet-control-zoom a {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(12px) !important;
            color: #333 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .leaflet-control-zoom a:hover {
            background: #fff !important;
            color: var(--primary) !important;
        }

        /* Custom Popup Styling */
        .leaflet-popup-content-wrapper {
            border-radius: 4px;
            box-shadow: none;
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .leaflet-popup-content {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            width: 260px !important;
        }

        .leaflet-popup-close-button {
            color: white !important;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8) !important;
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
            width: 26px;
            height: 35px;
        }

        .custom-map-pin svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 26px;
            height: 35px;
        }

        .leaflet-container .leaflet-marker-pane .pin-icon-img {
            position: absolute !important;
            top: 3px !important;
            left: 3px !important;
            width: 20px !important;
            height: 20px !important;
            max-width: 20px !important;
            max-height: 20px !important;
            object-fit: cover !important;
            z-index: 999 !important;
            border-radius: 50% !important;
        }

        .custom-map-pin svg,
        .leaflet-container .leaflet-marker-pane .pin-icon-img {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Hiệu ứng hover CSS thuần - Khắc phục lỗi kẹt tooltip */
        .leaflet-marker-icon:has(.custom-map-pin:hover) {
            z-index: 99999999 !important;
        }

        .custom-map-pin:hover svg,
        .custom-map-pin:hover .pin-icon-img {
            transform: scale(1.08) translateY(-2px);
        }

        .custom-map-pin:hover .custom-pin-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translate(6px, -50%);
        }

        .custom-pin-tooltip {
            position: absolute;
            top: 16px;
            /* Căn giữa theo phần thân tròn của icon */
            left: calc(100% - 2px);
            transform: translate(0px, -50%);
            background: linear-gradient(to right, color-mix(in srgb, var(--tip-color) 40%, black), var(--tip-color));
            color: white;
            padding: 4px 11px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: none;
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
            left: -3px;
            transform: translateY(-50%);
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-right: 6px solid color-mix(in srgb, var(--tip-color) 40%, black);
        }

        /* Đảm bảo marker đang hover luôn nổi lên trên cùng */
        .my-custom-marker {
            transition: z-index 0.2s;
        }

        .my-custom-marker:hover {
            z-index: 99999999 !important;
        }

        /* Flat UI — tắt bóng marker Leaflet */
        .leaflet-marker-shadow,
        .leaflet-shadow-pane img {
            display: none !important;
            opacity: 0 !important;
        }

        /* Cluster Coverage Polygon on Hover */
        .leaflet-cluster-anim .leaflet-marker-icon,
        .leaflet-cluster-anim .leaflet-marker-shadow {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .marker-cluster {
            transition: transform 0.2s ease;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: none !important;
        }
        .marker-cluster-small,
        .marker-cluster-medium,
        .marker-cluster-large,
        .marker-cluster div {
            box-shadow: none !important;
        }

        .marker-cluster div {
            width: 78% !important;
            height: 78% !important;
            margin: 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center;
            border-radius: 50%;
            font-size: 13px !important;
            font-weight: 700;
        }

        .marker-cluster div span {
            line-height: 22px !important;
        }

        .marker-cluster:hover {
            transform: scale(1.15);
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Pulsing User Location Marker */
        .user-location-marker {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid #fff;
            box-shadow: none;
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

            80%,
            100% {
                transform: scale(2.5);
                opacity: 0;
            }
        }

        /* Toast — flat, sạch, không icon trang trí */
        .toast-container {
            position: absolute;
            top: 72px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
            width: max-content;
            max-width: min(420px, calc(100% - 32px));
        }

        .toast {
            pointer-events: auto;
            background: #ffffff;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            border: 1px solid #e2e8f0;
            padding: 8px 14px;
            border-radius: 6px;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
            transform: translateY(-8px);
            opacity: 0;
            width: max-content;
            max-width: 100%;
            margin: 0 auto;
        }

        .toast.success {
            border-color: #cbd5e1;
            color: #1e3a5f;
        }
        .toast.warning {
            border-color: #e2e8f0;
            color: #475569;
        }
        .toast.error {
            border-color: #fecaca;
            color: #991b1b;
            background: #fff;
        }
        .toast.info,
        .toast.loading {
            border-color: #e2e8f0;
            color: #334155;
        }

        .toast-content {
            line-height: 1.4;
            white-space: nowrap;
        }

        .toast-spinner {
            width: 12px;
            height: 12px;
            border: 1.5px solid #cbd5e1;
            border-top-color: #1e3a5f;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            flex-shrink: 0;
        }

        /* Unified Floating Command Dock */
        .top-search-panel {
            position: absolute;
            top: 14px;
            left: 72px;
            z-index: 1000;
            pointer-events: none;
        }

        .unified-command-dock {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: none;
            height: 38px;
            padding: 0 4px 0 10px;
            gap: 8px;
            pointer-events: auto;
            transition: all 0.2s ease;
        }

        .unified-command-dock:focus-within,
        .unified-command-dock:hover {
            border-color: #475569;
            box-shadow: none;
        }

        .dock-search-box {
            display: flex;
            align-items: center;
            gap: 6px;
            width: 195px;
        }

        .dock-category-select {
            height: 30px;
            border: 1px solid #d7dde5;
            background: #ffffff;
            color: #334155;
            font-family: inherit;
            font-size: 0.76rem;
            font-weight: 600;
            border-radius: 4px;
            padding: 0 30px 0 10px;
            outline: none;
            min-width: 140px;
            appearance: none;
            -webkit-appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, #64748b 50%), linear-gradient(135deg, #64748b 50%, transparent 50%);
            background-position: calc(100% - 16px) calc(50% - 2px), calc(100% - 11px) calc(50% - 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
        }

        .dock-category-select:focus,
        .dock-category-select:hover {
            border-color: #475569;
        }

        .dock-search-box .search-icon {
            font-size: 18px;
            color: #64748b;
            flex-shrink: 0;
        }

        .dock-search-box .search-input {
            flex: 1;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 500;
            color: #1e293b;
            background: transparent;
            width: 100%;
        }

        .dock-search-box .search-input::placeholder {
            color: #94a3b8;
        }

        .dock-divider {
            width: 1px;
            height: 20px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .dock-filter-wrapper {
            position: relative;
        }

        .dock-filter-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            background: transparent;
            border: none;
            padding: 0 4px;
            height: 30px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dock-filter-btn:hover {
            color: #0f172a;
        }

        .dock-filter-btn .material-symbols-rounded {
            font-size: 16px;
            color: #64748b;
            transition: transform 0.2s ease;
        }

        .dock-filter-btn.active {
            color: #1e3a5f;
        }

        .dock-filter-btn.active .material-symbols-rounded {
            color: #1e3a5f;
        }

        .dock-filter-btn.menu-open .arrow-icon {
            transform: rotate(180deg);
        }

        .dock-random-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0 8px;
            height: 28px;
            font-family: inherit;
            font-size: 0.74rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .dock-random-btn:hover {
            background: #1e3a5f;
            color: #ffffff;
            border-color: #1e3a5f;
        }

        .dock-random-btn .material-symbols-rounded {
            font-size: 15px;
            color: #1e3a5f;
            transition: color 0.2s ease;
        }

        .dock-random-btn:hover .material-symbols-rounded {
            color: #ffffff;
        }

        .radius-section {
            padding: 4px 6px 8px 6px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        .radius-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .radius-btn-group {
            display: flex;
            gap: 4px;
        }

        .radius-btn {
            flex: 1;
            padding: 3px 0;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
        }

        .radius-btn:hover {
            background: #cbd5e1;
        }

        .radius-btn.active {
            background: #1e3a5f;
            color: #ffffff;
            border-color: #1e3a5f;
        }

        .dock-filter-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: none;
            padding: 6px;
            display: none;
            flex-direction: column;
            gap: 2px;
            z-index: 1010;
            animation: popoverFadeIn 0.18s ease-out;
        }

        @keyframes popoverFadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dock-filter-menu.show {
            display: flex;
        }

        .filter-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 6px 6px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.65rem;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        .filter-reset-btn {
            background: transparent;
            border: none;
            color: #1e3a5f;
            font-size: 0.68rem;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
        }

        .filter-reset-btn:hover {
            text-decoration: underline;
        }

        .filter-menu-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            max-height: 240px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .filter-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            transition: all 0.15s ease;
        }

        .filter-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .filter-item.active {
            background: #f1f5f9;
            color: #1e3a5f;
            font-weight: 700;
        }

        .filter-item.active .filter-count-badge {
            background: #1e3a5f;
            color: #ffffff;
        }

        .filter-item-left {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-item-left .material-symbols-rounded {
            font-size: 16px;
            color: #64748b;
        }

        .filter-count-badge {
            font-size: 0.65rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #64748b;
            padding: 1px 6px;
            border-radius: 10px;
        }
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #555;
            transition: background 0.2s;
        }

        .icon-btn span {
            font-size: 17px;
        }

        .icon-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--primary);
        }

        .icon-btn.primary {
            background: var(--primary);
            width: 24px;
            height: 24px;
            border-radius: 6px;
            transform: rotate(45deg);
            margin: 0 4px;
            box-shadow: none;
            transition: transform 0.2s, filter 0.2s;
        }

        .icon-btn.primary:hover {
            filter: brightness(1.1);
            transform: rotate(45deg) scale(1.05);
        }

        .icon-btn.primary span {
            color: white;
            font-size: 16px;
            transform: rotate(-45deg);
        }

        .search-box .divider {
            width: 1px;
            height: 18px;
            background: rgba(0, 0, 0, 0.1);
            margin: 0 4px;
        }

        /* Search Suggestions */
        .search-suggestions {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 340px;
            max-width: calc(100vw - 32px);
            background: #ffffff;
            border-radius: 10px;
            box-shadow: none;
            border: 1px solid #cbdbe8;
            overflow: hidden;
            display: none;
            flex-direction: column;
            max-height: 240px;
            overflow-y: auto;
            z-index: 99999;
            pointer-events: auto;
        }

        /* Mini Status Bar Wrapper & Dropdown Banner */
        .mini-status-bar-wrapper {
            position: absolute;
            top: 12px;
            right: 8px;
            z-index: 1000;
        }

        .mini-status-bar {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 220px;
            box-sizing: border-box;
            padding: 5px 10px;
            gap: 8px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            z-index: 1002;
        }

        .mini-status-bar-wrapper:hover .mini-status-bar {
            box-shadow: none;
        }

        .mini-status-bar::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 24px;
            width: 8px;
            height: 8px;
            background: #ffffff;
            transform: rotate(45deg);
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: none;
            z-index: -1;
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
            gap: 4px;
            font-weight: 600;
            font-size: 12px;
            color: #111827;
        }

        .msb-weather .material-symbols-rounded {
            font-size: 15px;
            color: #f59e0b;
        }

        .msb-divider {
            width: 1px;
            height: 14px;
            background: #d1d5db;
        }

        .msb-news {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: #4b5563;
        }

        .msb-news .material-symbols-rounded {
            font-size: 15px;
            color: #6b7280;
        }

        .msb-news-text {
            max-width: 110px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .msb-chevron {
            font-size: 15px !important;
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
            margin-right: 8px;
            width: 220px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 8px;
            box-shadow: none;
            border: 1px solid rgba(255, 255, 255, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateX(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1000;
            padding: 12px;
            box-sizing: border-box;
        }

        .mini-status-bar-wrapper.weather-active .weather-dropdown-banner {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            pointer-events: auto;
        }

        /* Bottom Featured Drawer */
        .bottom-drawer-wrapper {
            /* Chiều cao dải tab trên cùng — tab "Khám phá" và tab icon dùng chung mốc này */
            --drawer-rail-h: 32px;
            position: absolute;
            bottom: 0;
            left: 66px;
            right: 0;
            z-index: 1000;
            pointer-events: none;
            transition: left 0.35s cubic-bezier(0.16, 1, 0.3, 1), bottom 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            overflow: visible !important;
        }

        body.sidebar-expanded .bottom-drawer-wrapper {
            left: 260px;
        }

        .bottom-drawer-wrapper:not(.open) {
            bottom: 0;
        }

        .drawer-header {
            display: flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            max-width: calc(100% - 24px);
            height: var(--drawer-rail-h);
            padding: 0 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-bottom-color: #ffffff;
            border-radius: 7px 7px 0 0;
            box-shadow: none;
            cursor: pointer;
            user-select: none;
            box-sizing: border-box;
            pointer-events: auto;
            margin-left: 12px;
            position: relative;
            z-index: 2;
            transition: border-color 0.2s ease;
        }

        .bottom-drawer-wrapper:not(.open) .drawer-header {
            border-bottom-color: #e2e8f0;
        }

        .bottom-drawer-wrapper:not(.open) .drawer-header {
            border-bottom-color: #e2e8f0;
        }

        .drawer-header-left {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .drawer-header-divider {
            width: 1px;
            height: 12px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .drawer-title {
            font-size: 0.72rem;
            font-weight: 600;
            color: #1e3a5f;
            letter-spacing: -0.15px;
        }

        .drawer-count-badge {
            font-size: 0.62rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #64748b;
            padding: 1px 6px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .drawer-close-btn {
            background: transparent;
            border: none;
            padding: 0;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #94a3b8;
            margin-left: 0;
            transition: background 0.15s, color 0.15s;
        }

        .drawer-close-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .drawer-chevron {
            font-size: 15px;
            transition: transform 0.3s ease;
        }

        .bottom-drawer-wrapper.open .drawer-chevron {
            transform: rotate(180deg);
        }

        .drawer-content {
            width: 100%;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            box-shadow: none;
            pointer-events: auto;
            position: relative;
            z-index: 1;
        }

        .bottom-drawer-wrapper.open .drawer-content {
            max-height: 145px;
            opacity: 1;
        }

        /* Tab “Khám phá” đè lên đường viền nội dung — cùng màu trắng, liền màu */
        .bottom-drawer-wrapper.open .drawer-header {
            margin-bottom: -1px;
        }

        .featured-loc-scroll {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 12px 16px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .featured-loc-card {
            position: relative;
            width: 175px;
            height: 110px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            text-decoration: none;
            box-shadow: none;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .featured-loc-card:hover {
            border-color: rgba(30, 58, 95, 0.4);
            box-shadow: none;
            transform: none;
        }

        .featured-loc-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .featured-loc-card:hover .featured-loc-img {
            transform: none;
        }

        .featured-loc-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px 8px 6px 8px;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0) 100%);
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 2;
        }

        .featured-loc-info .material-symbols-rounded {
            font-size: 14px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .featured-loc-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Home Page White-Space & Sizing Enhancements */
        .featured-loc-card {
            width: 205px !important;
            height: 115px !important;
            border-radius: 4px !important;
        }
        .news-dropdown-banner .banner-title {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            font-weight: 600 !important;
            font-size: 0.775rem !important;
            color: #ffffff !important;
        }
        .news-dropdown-banner .banner-meta {
            white-space: nowrap !important;
        }
        .msb-news-text {
            max-width: 140px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .weather-main {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
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
            margin-top: 8px;
            width: 220px;
            height: 130px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1000;
            background: #111827;
        }

        /* Bảng Nhiệm Vụ — thu gọn = nút tròn, mở = card */
        .mission-widget-wrapper {
            position: absolute;
            top: 195px;
            right: 10px;
            z-index: 1000;
            pointer-events: auto;
            box-sizing: border-box;
        }

        .mission-fab-btn {
            position: relative;
            display: none;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 1.5px solid #cbdbe8;
            background: #ffffff;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            box-shadow: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
        }

        .mission-fab-btn .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-size: 24px;
            line-height: 1;
            color: #1e3a5f;
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
            -webkit-font-smoothing: antialiased;
            transform: translateY(0.5px);
        }

        .mission-fab-btn:hover {
            border-color: #94a3b8;
            background: #fff;
            box-shadow: none;
            transform: none;
        }

        /* Chấm / số báo hiệu */
        .mission-fab-signal {
            position: absolute;
            top: 2px;
            right: 2px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            border-radius: 999px;
            background: #1e3a5f;
            color: #fff;
            font-size: 0.58rem;
            font-weight: 700;
            font-family: 'Be Vietnam Pro', sans-serif;
            line-height: 16px;
            text-align: center;
            border: 2px solid #fff;
            box-sizing: border-box;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .mission-fab-btn.has-signal .mission-fab-signal {
            display: inline-flex;
        }

        .mission-fab-signal.is-dot {
            min-width: 10px;
            width: 10px;
            height: 10px;
            padding: 0;
            top: 4px;
            right: 4px;
        }

        /* Vòng pulse khi còn việc cần làm */
        .mission-fab-btn.has-signal::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1.5px solid rgba(30, 58, 95, 0.35);
            animation: missionFabPulse 2s ease-out infinite;
            pointer-events: none;
        }

        @keyframes missionFabPulse {
            0% { transform: scale(0.92); opacity: 0.7; }
            70% { transform: scale(1.12); opacity: 0; }
            100% { transform: scale(1.12); opacity: 0; }
        }

        .mission-widget-wrapper.collapsed .mission-fab-btn {
            display: inline-flex;
        }

        .mission-widget-wrapper.collapsed .mission-panel {
            display: none;
        }

        .mission-panel {
            width: 220px;
            background: #ffffff;
            border-radius: 6px;
            box-shadow: none;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            padding: 10px 10px 8px 10px;
            box-sizing: border-box;
        }

        .mission-widget-wrapper .mission-header {
            padding: 0 2px 8px 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            user-select: none;
            background: transparent;
            width: 100%;
        }

        .mission-widget-wrapper .mission-header-label {
            font-size: 0.76rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #1e3a5f;
        }

        .mission-widget-wrapper .mission-fab-icon {
            font-family: 'Material Symbols Outlined';
            font-size: 18px;
            line-height: 1;
            color: #64748b;
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
            -webkit-font-smoothing: antialiased;
        }

        .mission-widget-wrapper .mission-header-meta {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .quest-coin-badge-3d {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #fef08a, #f59e0b 60%, #b45309 100%);
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
            border: 2px solid #fffbea;
            z-index: 2;
        }

        .mini-status-bar-wrapper.active .news-dropdown-banner {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .banner-track {
            display: flex;
            width: 400%;
            /* 4 items (3 real + 1 clone) */
            height: 100%;
            animation: bannerSlide 12s infinite ease-in-out;
        }

        .banner-track:hover {
            animation-play-state: paused;
            /* Dừng cuộn khi di chuột vào banner */
        }

        .banner-item {
            width: 25%;
            /* 100% / 4 */
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
            transform: none;
        }

        .banner-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.6) 50%, transparent 100%);
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
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
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

            0%,
            30% {
                transform: translateX(0);
            }

            33.33%,
            63.33% {
                transform: translateX(-25%);
            }

            66.66%,
            96.66% {
                transform: translateX(-50%);
            }

            100% {
                transform: translateX(-75%);
            }

            /* Lướt nhanh trong 0.4s */
        }

        .search-suggestions.active {
            display: flex;
        }

        .suggestion-item {
            padding: 10px 14px;
            display: block;
            cursor: pointer;
            transition: background 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background: #f8fafc;
        }

        .suggestion-info {
            display: block;
            overflow: hidden;
        }

        .suggestion-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .suggestion-cat {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 2px;
        }

        .no-results {
            padding: 12px 14px;
            font-size: 0.8rem;
            color: #64748b;
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
            gap: 5px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 0 10px;
            height: 32px;
            box-sizing: border-box;
            border-radius: 6px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            white-space: nowrap;
            box-shadow: none;
            transition: all 0.2s ease;
        }

        .category-pill:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        .category-pill.active {
            background: #334155;
            border-color: #334155;
            color: #ffffff;
            box-shadow: none;
        }

        .category-pill.active span {
            color: #ffffff !important;
        }

        .category-pill span {
            font-size: 14px;
            color: #64748b;
            transition: color 0.2s ease;
        }

        /* Custom Leaflet Zoom Controls */
        .leaflet-top.leaflet-right {
            top: auto !important;
            bottom: 65px !important;
            left: 82px !important;
            right: auto !important;
            transition: left 0.35s cubic-bezier(0.16, 1, 0.3, 1), bottom 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        body.sidebar-expanded .leaflet-top.leaflet-right {
            left: 276px !important;
        }

        body:has(.bottom-drawer-wrapper.open) .leaflet-top.leaflet-right {
            bottom: 200px !important;
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: none;
            border-radius: 7px !important;
            overflow: hidden;
            margin-bottom: 0 !important;
            margin-right: 0 !important;
        }

        .leaflet-control-zoom a {
            background-color: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #374151 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            width: 32px !important;
            height: 32px !important;
            line-height: 32px !important;
            font-size: 15px !important;
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

        /* Reposition toast so it doesn't overlap search */
        .toast-container {
            top: 72px !important;
        }

        /* ========================================= */
        /* USER SIDEBAR MENU (Admin Style for Map)   */
        /* ========================================= */

        .user-sidebar-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 2000;
            pointer-events: none;
        }

        .user-sidebar {
            position: relative;
            width: 260px;
            height: 100%;
            background: #ffffff;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            box-shadow: none;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e2e8f0;
            transition: width 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            pointer-events: auto;
        }

        /* Collapsed State */
        .user-sidebar.collapsed {
            width: 66px;
        }

        .user-sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 10px 0;
        }

        .user-sidebar .sidebar-header {
            display: flex;
            align-items: center;
            padding: 12px 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            min-height: 56px;
            box-sizing: border-box;
            cursor: pointer;
            white-space: nowrap;
        }

        .user-sidebar .sidebar-header:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .user-sidebar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            transition: gap 0.3s ease;
        }

        .user-sidebar.collapsed .user-info {
            gap: 0 !important;
            justify-content: center !important;
            width: 100% !important;
            margin: 0 auto;
        }

        .user-sidebar .user-avatar {
            box-sizing: border-box;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            transition: transform 0.2s, width 0.2s, height 0.2s;
        }

        .user-sidebar.collapsed .user-avatar {
            width: 42px !important;
            height: 42px !important;
            margin: 0 auto !important;
        }

        .user-sidebar .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-sidebar .user-details {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            max-width: 180px;
            opacity: 1;
            visibility: visible;
            transition: max-width 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
        }

        .user-sidebar .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            white-space: nowrap;
            line-height: 1.2;
        }

        .user-sidebar .user-role {
            font-size: 11px;
            color: #666;
            margin-top: 1px;
            white-space: nowrap;
        }

        /* Smooth hiding of text */
        .user-sidebar.collapsed .user-details {
            max-width: 0;
            opacity: 0;
            visibility: hidden;
            transition: max-width 0.2s ease, opacity 0.2s ease, visibility 0.2s ease;
        }

        .user-sidebar .sidebar-menu {
            list-style: none;
            padding: 10px 0;
            margin: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .user-sidebar .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .user-sidebar.collapsed .sidebar-menu::-webkit-scrollbar {
            width: 0px;
            display: none;
        }

        .user-sidebar.collapsed .sidebar-menu {
            overflow-y: hidden;
            overflow-x: hidden;
        }

        .user-sidebar .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .user-sidebar .sidebar-menu li {
            margin-bottom: 4px;
            padding: 0;
        }

        .user-sidebar .menu-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            color: #475569;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
            border-radius: 0;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }

        .user-sidebar .menu-item:hover,
        .user-sidebar .menu-item.active {
            background: rgba(255, 255, 255, 0.45);
            color: #1e3a5f;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            box-shadow: none;
        }

        .user-sidebar .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 16px;
            width: 3px;
            background: #1e3a5f;
            border-radius: 0 3px 3px 0;
        }

        .user-sidebar .menu-icon {
            width: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-sidebar .menu-icon .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 22px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .user-sidebar .menu-item:hover .menu-icon .material-symbols-outlined,
        .user-sidebar .menu-item.active .menu-icon .material-symbols-outlined {
            color: #1e3a5f;
        }

        .user-sidebar .menu-text {
            font-size: 13px;
            font-weight: 500;
            margin-left: 8px;
            overflow: hidden;
            max-width: 180px;
            opacity: 1;
            visibility: visible;
            transition: max-width 0.3s ease, opacity 0.3s ease, visibility 0.3s ease, margin 0.3s ease;
        }

        /* Tour 360 — nhóm với Đăng xuất ở sát đáy */
        .user-sidebar .sidebar-bottom-item {
            margin-top: auto !important;
            padding-top: 4px !important;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        .user-sidebar .menu-item--muted {
            opacity: 0.72;
        }
        .user-sidebar .menu-item--muted:hover,
        .user-sidebar .menu-item--muted:focus {
            opacity: 1;
        }
        .user-sidebar .menu-item--text-only {
            justify-content: center;
            padding-left: 10px;
            padding-right: 10px;
        }
        .user-sidebar .menu-item--text-only .menu-text {
            margin-left: 0;
            white-space: normal;
            line-height: 1.25;
            text-align: left;
            max-width: none;
        }
        .user-sidebar .logout-item {
            margin-top: 0 !important;
        }
        .user-sidebar.collapsed .sidebar-bottom-item {
            margin-top: auto !important;
            margin-bottom: 0 !important;
            padding-top: 2px !important;
        }
        .user-sidebar.collapsed .menu-item--text-only {
            padding: 2px 4px 2px;
        }
        .user-sidebar.collapsed .menu-item--text-only .menu-text {
            white-space: normal !important;
            line-height: 1.2 !important;
            font-size: 0.58rem;
            letter-spacing: -0.2px;
            padding-bottom: 0;
        }

        /* Google Maps Style Collapsed Sidebar Items */
        .user-sidebar.collapsed .sidebar-menu li {
            padding: 0;
            margin-bottom: 8px;
        }

        .user-sidebar.collapsed .menu-item {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 0;
            border-radius: 0;
            width: 100%;
        }

        .user-sidebar.collapsed .menu-icon {
            width: auto;
            margin-left: 0;
            margin-bottom: 4px;
        }

        .user-sidebar.collapsed .menu-icon .material-symbols-outlined {
            font-size: 22px;
        }

        .user-sidebar.collapsed .menu-text {
            max-width: 100% !important;
            margin-left: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
            font-size: 0.60rem;
            font-weight: 500;
            color: #475569;
            text-align: center;
            line-height: 1.3;
            letter-spacing: -0.15px;
            white-space: nowrap;
            overflow: visible;
            width: 100%;
            padding-bottom: 2px;
        }

        .user-sidebar.collapsed .menu-item:hover .menu-text,
        .user-sidebar.collapsed .menu-item.active .menu-text {
            color: #1e3a5f;
            font-weight: 600;
        }

        /* Logout Item in Collapsed State: Icon only, sát dưới Tour 360 */
        .user-sidebar.collapsed .logout-item {
            margin-top: 0 !important;
            margin-bottom: 6px !important;
            padding-top: 2px !important;
            padding-bottom: 0 !important;
        }

        .user-sidebar.collapsed .logout-item .menu-text {
            display: none !important;
        }

        .user-sidebar.collapsed .logout-item .menu-item {
            padding: 10px 0 !important;
        }

        .user-sidebar.collapsed .logout-item .menu-icon {
            margin-bottom: 0 !important;
        }

        /* Tooltip for collapsed state (Disabled as text is already visible inside items) */
        .user-sidebar .menu-item .tooltip {
            display: none !important;
        }

        /* Edge Toggle Button (Seamless Tab) */
        .sidebar-edge-toggle {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 260px;
            margin-left: -1px;
            width: 14px;
            height: 44px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-left: none;
            border-radius: 0 6px 6px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2001;
            box-shadow: none;
            transition: left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), background 0.2s, color 0.2s, width 0.2s;
            pointer-events: auto;
            color: #666;
            padding: 0;
            outline: none;
        }

        .sidebar-edge-toggle:hover {
            color: var(--primary);
        }

        body.sidebar-collapsed .sidebar-edge-toggle {
            left: 66px;
        }

        .sidebar-edge-toggle .icon-arrow {
            font-size: 18px;
            /* Scaled down slightly */
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: rotate(180deg);
            /* Left pointing when expanded */
        }

        body.sidebar-collapsed .sidebar-edge-toggle .icon-arrow {
            transform: rotate(0deg);
            /* Right pointing when collapsed */
        }

        /* Adjust top search panel when sidebar is expanded */
        .top-search-panel {
            transition: left 0.3s cubic-bezier(0.4, 0.2, 0.2, 1);
            left: 90px;
            /* default with collapsed sidebar: 66px + 24px */
        }

        body.sidebar-expanded .top-search-panel {
            left: 284px;
            /* 260px + 24px */
        }

        /* ============================================================= */
        /* ICON TAB BAR — cùng ngôn ngữ với tab "Khám phá" của drawer    */
        /* ============================================================= */
        /* Đáy tab tụt thêm 1px để đè lên viền trên của drawer, tạo cảm giác liền khối */
        .vr-floating-hex-menu {
            position: absolute;
            left: 50%;
            top: 0;
            transform: translate(-50%, calc(-100% + var(--drawer-rail-h) + 1px));
            display: flex;
            align-items: stretch;
            height: 40px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-bottom-color: #ffffff;
            border-radius: 7px 7px 0 0;
            box-shadow: none;
            z-index: 2500;
            pointer-events: auto;
        }

        .bottom-drawer-wrapper:not(.open) .vr-floating-hex-menu {
            border-bottom-color: #e2e8f0;
        }

        .vr-floating-hex-menu .vr-hex-btn {
            position: relative;
            width: 46px;
            background: transparent;
            border: none;
            border-left: 1px solid #e2e8f0;
            padding: 0;
            cursor: pointer;
            outline: none;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .vr-floating-hex-menu .vr-hex-btn:first-child {
            border-left: none;
            border-radius: 6px 0 0 0;
        }

        .vr-floating-hex-menu .vr-hex-btn:last-child {
            border-radius: 0 6px 0 0;
        }

        .vr-floating-hex-menu .vr-hex-btn .material-symbols-rounded {
            font-size: 21px;
            line-height: 1;
        }

        .vr-floating-hex-menu .vr-hex-btn:hover {
            background: #f1f5f9;
            color: #1e3a5f;
        }

        .vr-floating-hex-menu .vr-hex-btn:active {
            background: #e2e8f0;
        }

        /* Tooltip */
        .vr-floating-hex-menu .vr-hex-tooltip {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%) translateY(3px);
            background: #1e3a5f;
            color: #f8fafc;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.66rem;
            font-weight: 600;
            letter-spacing: -0.15px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s;
            pointer-events: none;
        }

        .vr-floating-hex-menu .vr-hex-btn:hover .vr-hex-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Left Brand Text inside bottom bar */
        .vr-bar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 2;
        }

        .vr-bar-left .vr-brand-title {
            font-size: 13px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        /* Center Hexagon Menu floating ABOVE the bar line */
        .vr-hex-menu-bottom {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -62%);
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 25;
            pointer-events: auto;
        }

        .vr-hex-menu-bottom .vr-hex-btn {
            position: relative;
            width: 48px;
            height: 52px;
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            outline: none;
            text-decoration: none;
            display: inline-block;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.45));
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .vr-hex-menu-bottom .vr-hex-btn-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #1e3a5f 0%, #12243e 100%);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: all 0.25s ease;
        }

        .vr-hex-menu-bottom .vr-hex-btn-inner span {
            font-size: 22px;
            color: #ffffff;
        }

        .vr-hex-menu-bottom .vr-hex-btn:hover {
            transform: translateY(-6px) scale(1.12);
        }

        .vr-hex-menu-bottom .vr-hex-btn:hover .vr-hex-btn-inner {
            background: linear-gradient(180deg, #2d5282 0%, #1e3a5f 100%);
            box-shadow: 0 0 16px rgba(30, 58, 95, 0.6);
        }

        .vr-hex-menu-bottom .vr-hex-btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Tooltip */
        .vr-hex-menu-bottom .vr-hex-tooltip {
            position: absolute;
            top: -34px;
            left: 50%;
            transform: translateX(-50%) translateY(-4px);
            background: rgba(15, 23, 42, 0.95);
            color: #f1f5f9;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .vr-hex-menu-bottom .vr-hex-btn:hover .vr-hex-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Right Action Group inside bottom bar */
        .vr-bar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 2;
        }

        .vr-monuments-pill-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            color: #f1f5f9;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .vr-bottom-bar:hover .vr-monuments-pill-btn {
            background: rgba(255, 255, 255, 0.22);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .bottom-drawer-wrapper.open .drawer-chevron {
            transform: rotate(180deg);
        }

        .drawer-chevron {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .vr-bar-left .vr-brand-title {
                display: none;
            }
            .vr-hex-menu-bottom {
                gap: 6px;
                transform: translate(-50%, -60%);
            }
            .vr-hex-menu-bottom .vr-hex-btn {
                width: 38px;
                height: 42px;
            }
            .vr-hex-menu-bottom .vr-hex-btn-inner span {
                font-size: 17px;
            }
            .vr-monuments-pill-btn span:not(.material-symbols-rounded) {
                display: none;
            }
        }

        /* ========== Monuments Modal ========== */
        .monuments-modal-backdrop {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.6) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            z-index: 999999999 !important;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }
        .monuments-modal-backdrop.show {
            display: flex !important;
        }
        .monuments-modal-dialog {
            width: 100%;
            max-width: 960px;
            max-height: 85vh;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: mmFadeIn .2s ease-out;
        }
        @keyframes mmFadeIn {
            from { opacity: 0; transform: translateY(12px) scale(.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .monuments-modal-header {
            padding: 16px 22px 12px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .monuments-modal-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .monuments-modal-title {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e3a5f;
            letter-spacing: .5px;
            text-transform: uppercase;
            text-align: center;
            flex: 1;
        }
        .monuments-close-btn {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 26px;
            font-weight: 900;
            cursor: pointer;
            line-height: 1;
            padding: 0 2px;
            transition: transform .15s;
        }
        .monuments-close-btn:hover {
            color: #dc2626;
            transform: scale(1.15);
        }
        .monuments-controls-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .monuments-controls-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 200px;
        }
        .monuments-cat-select {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            color: #1e3a5f;
            outline: none;
            background: #f8fafc;
            cursor: pointer;
            max-width: 170px;
        }
        .monuments-cat-select:focus { border-color: #1e3a5f; background: #fff; }
        .monuments-search-wrapper {
            position: relative;
            flex: 1;
            min-width: 160px;
        }
        .monuments-search-wrapper .search-icon {
            position: absolute;
            left: 9px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
        }
        .monuments-search-input {
            width: 100%;
            padding: 6px 10px 6px 32px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-family: inherit;
            font-size: .84rem;
            color: #0f172a;
            outline: none;
            background: #f8fafc;
            box-sizing: border-box;
        }
        .monuments-search-input:focus { border-color: #1e3a5f; background: #fff; }
        .monuments-count-info {
            font-size: .82rem;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .monuments-modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            flex: 1;
            background: #f8fafc;
        }
        .monuments-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        @media (max-width: 860px) { .monuments-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 540px) {
            .monuments-grid { grid-template-columns: 1fr; }
            .monuments-controls-left { flex-direction: column; align-items: stretch; }
            .monuments-cat-select { max-width: 100%; }
        }
        .monument-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            cursor: pointer;
            transition: all .2s ease;
        }
        .monument-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(30,58,95,0.14);
            border-color: #1e3a5f;
        }
        .monument-card-img-box {
            position: relative;
            width: 100%;
            height: 155px;
            overflow: hidden;
            background: #cbd5e1;
        }
        .monument-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease;
        }
        .monument-card:hover .monument-card-img { transform: scale(1.06); }
        .monument-card-title-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.48);
            color: #fff;
            padding: 7px 10px;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: .3px;
            text-shadow: 0 1px 3px rgba(0,0,0,.8);
        }
    </style>
</head>

<body class="sidebar-collapsed">

    <!-- User Sidebar Menu -->
    <div class="user-sidebar-wrapper" id="userSidebarWrapper">
        <div class="user-sidebar collapsed" id="userSidebar">
            <div class="sidebar-header">
                @auth
                <a href="{{ route('client.profile') }}" class="user-info" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; width: 100%;">
                    <x-user-avatar :user="Auth::user()" size="40" class="user-avatar" />
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->display_name ?? Auth::user()->username }}</span>
                        <span class="user-role">{{ Auth::user()->role === 'admin' ? 'Quản trị viên' : (Auth::user()->role === 'moderator' ? 'Kiểm duyệt viên' : 'Thành viên') }}</span>
                    </div>
                </a>
                @else
                    <div class="user-info">
                        <div class="user-avatar"
                            style="display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-color: #d1d5db;">
                            <span class="material-symbols-rounded"
                                style="color: #9ca3af; font-size: 24px; margin: 0;">person</span>
                        </div>
                        <div class="user-details">
                            <span class="user-name">Khách truy cập</span>
                            <span class="user-role">Chưa đăng nhập</span>
                        </div>
                    </div>
                @endauth
            </div>
            <ul class="sidebar-menu">
                @auth
                <li>
                    <a href="{{ route('client.missions') }}" class="menu-item">
                        <span class="menu-icon"><span class="material-symbols-outlined">emoji_events</span></span>
                        <span class="menu-text">Nhiệm vụ</span>
                        <span class="tooltip">Nhiệm vụ & điểm thưởng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.profile') }}#tab-favorites" class="menu-item">
                        <span class="menu-icon"><span class="material-symbols-outlined">bookmark</span></span>
                        <span class="menu-text">Đã lưu</span>
                        <span class="tooltip">Địa điểm đã lưu</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.profile') }}#tab-itineraries" class="menu-item">
                        <span class="menu-icon"><span class="material-symbols-outlined">route</span></span>
                        <span class="menu-text">Lịch trình</span>
                        <span class="tooltip">Lịch trình đã lưu</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="openModal('suggestLocationModal'); return false;" class="menu-item">
                        <span class="menu-icon"><span class="material-symbols-outlined">add_location_alt</span></span>
                        <span class="menu-text">Đề xuất</span>
                        <span class="tooltip">Đề xuất địa điểm</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.profile') }}" class="menu-item">
                        <span class="menu-icon"><span class="material-symbols-outlined">person</span></span>
                        <span class="menu-text">Hồ sơ</span>
                        <span class="tooltip">Trang cá nhân</span>
                    </a>
                </li>
                <li class="sidebar-bottom-item">
                    <a href="{{ route('client.pano_service') }}" class="menu-item menu-item--muted menu-item--text-only">
                        <span class="menu-text">Dịch vụ<br>tour 360</span>
                        <span class="tooltip">Thuê làm tour 360</span>
                    </a>
                </li>
                <li class="logout-item" style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 6px;">
                    <form action="{{ route('logout') }}" method="POST" style="display: none;" id="logout-form">
                        @csrf
                    </form>
                    <a href="#" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="menu-icon"><span class="material-symbols-outlined">logout</span></span>
                        <span class="menu-text">Đăng xuất</span>
                        <span class="tooltip">Đăng xuất</span>
                    </a>
                </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="menu-item">
                            <span class="menu-icon"><span class="material-symbols-outlined">login</span></span>
                            <span class="menu-text">Đăng nhập</span>
                            <span class="tooltip">Đăng nhập</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="menu-item">
                            <span class="menu-icon"><span class="material-symbols-outlined">person_add</span></span>
                            <span class="menu-text">Đăng ký</span>
                            <span class="tooltip">Đăng ký tài khoản</span>
                        </a>
                    </li>
                    <li class="sidebar-bottom-item">
                        <a href="{{ route('client.pano_service') }}" class="menu-item menu-item--muted menu-item--text-only">
                            <span class="menu-text">Dịch vụ<br>tour 360</span>
                            <span class="tooltip">Giới thiệu dịch vụ tour 360</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
        <!-- Edge Toggle Button -->
        <button class="sidebar-edge-toggle" id="sidebarToggle" title="Thu gọn/Mở rộng">
            <span class="material-symbols-rounded icon-arrow">chevron_right</span>
        </button>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Top Floating Search & Filter Command Dock -->
    <div class="top-search-panel" id="topSearchPanel">
        <div class="unified-command-dock">
            <!-- Search Box -->
            <div class="dock-search-box">
                <span class="material-symbols-rounded search-icon">search</span>
                <input type="text" id="map-search-input" placeholder="Tìm kiếm địa điểm..." class="search-input" autocomplete="off">
            </div>

            <!-- Divider -->
            <div class="dock-divider"></div>

            <select id="mapCategoryFilter" class="dock-category-select" aria-label="Lọc theo danh mục">
                <option value="all">Tất cả chủ đề</option>
                @foreach($locations->pluck('category')->filter()->unique('id') as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <div class="dock-divider"></div>

            <!-- Trip Planner Recommendation Button -->
            <button type="button" class="dock-random-btn" id="randomFlyBtn" title="Lịch trình chuyến đi cho bạn">
                <span class="material-symbols-rounded">auto_awesome</span>
                <span>Lịch trình cho bạn</span>
            </button>
        </div>

        <!-- Dropdown Gợi ý tìm kiếm -->
        <div class="search-suggestions" id="search-suggestions">
            <!-- Nội dung được sinh ra bằng Javascript -->
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
            <a href="/tin-tuc" class="msb-news" id="news-toggle-btn" title="Xem tất cả tin tức"
                style="text-decoration: none; color: inherit;">
                <span class="material-symbols-rounded">newspaper</span>
                <span class="msb-news-text">Tin tức và sự kiện</span>
                <span class="material-symbols-rounded msb-chevron"
                    style="transform: rotate(-45deg);">arrow_forward</span>
            </a>
        </div>

        <!-- Dropdown Thời tiết -->
        <div class="weather-dropdown-banner">
            <div class="weather-main">
                <span class="material-symbols-rounded weather-big-icon"
                    id="weather-detail-icon">partly_cloudy_day</span>
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

        @if(isset($newsList) && $newsList->count() > 0)
            <!-- Dropdown Banner tự động chạy -->
            <div class="news-dropdown-banner">
                <div class="banner-track">
                    @foreach($newsList as $news)
                        <a href="/tin-tuc/{{ $news->slug }}" class="banner-item">
                            <img src="{{ $news->featured_image ? asset('storage/' . $news->featured_image) : 'https://placehold.co/260x150/1e293b/f8fafc?text=NEWS' }}"
                                alt="{{ $news->title }}">
                            <div class="banner-overlay">
                                <div class="banner-title">{{ $news->title }}</div>
                                <div class="banner-meta">
                                    <div class="meta-left">
                                        <span class="material-symbols-rounded">calendar_today</span>
                                        {{ $news->published_at ? $news->published_at->format('d/m/Y') : $news->created_at->format('d/m/Y') }}
                                    </div>
                                    <span class="material-symbols-rounded">arrow_forward</span>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    <!-- Clone of first item for seamless infinite loop -->
                    @php $firstNews = $newsList->first(); @endphp
                    <a href="/tin-tuc/{{ $firstNews->slug }}" class="banner-item" aria-hidden="true">
                        <img src="{{ $firstNews->featured_image ? asset('storage/' . $firstNews->featured_image) : 'https://placehold.co/260x150/1e293b/f8fafc?text=NEWS' }}"
                            alt="{{ $firstNews->title }} Clone">
                        <div class="banner-overlay">
                            <div class="banner-title">{{ $firstNews->title }}</div>
                            <div class="banner-meta">
                                <div class="meta-left">
                                    <span class="material-symbols-rounded">calendar_today</span>
                                    {{ $firstNews->published_at ? $firstNews->published_at->format('d/m/Y') : $firstNews->created_at->format('d/m/Y') }}
                                </div>
                                <span class="material-symbols-rounded">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        @endif

    </div>

    <!-- Bottom Featured Drawer (Full Width Sheet) -->
    <div class="bottom-drawer-wrapper open" id="featured-drawer">
        <!-- Icon tab bar seated on the top edge of the drawer, same style as the "Khám phá" tab -->
        <div class="vr-floating-hex-menu">
            <a href="{{ route('client.landing') }}" class="vr-hex-btn" id="vrBtnHome" title="Trang chủ" onclick="event.stopPropagation();">
                <span class="material-symbols-rounded">home</span>
                <span class="vr-hex-tooltip">Trang chủ</span>
            </a>

            <button type="button" class="vr-hex-btn" id="vrBtnCategory" title="Danh sách di tích" onclick="event.stopPropagation(); openMonumentsModal();">
                <span class="material-symbols-rounded">format_list_bulleted</span>
                <span class="vr-hex-tooltip">Danh mục</span>
            </button>

            <a href="{{ route('client.missions') }}" class="vr-hex-btn" id="vrBtnGame" title="Trò chơi & Nhiệm vụ" onclick="event.stopPropagation();">
                <span class="material-symbols-rounded">sports_esports</span>
                <span class="vr-hex-tooltip">Nhiệm vụ</span>
            </a>

            <a href="{{ route('client.pano_service') }}" class="vr-hex-btn" id="vrBtn360" title="Tour VR 360" onclick="event.stopPropagation();">
                <span class="material-symbols-rounded">vrpano</span>
                <span class="vr-hex-tooltip">Tour 360</span>
            </a>
        </div>

        <div class="drawer-header" id="drawer-toggle-btn" title="Nhấn để thu gọn/mở rộng">
            <div class="drawer-header-left">
                <span class="drawer-title">Khám phá</span>
            </div>

            <div class="drawer-header-divider"></div>

            <button type="button" class="drawer-close-btn" id="drawerCloseBtn">
                <span class="material-symbols-rounded drawer-chevron">expand_less</span>
            </button>
        </div>
        <div class="drawer-content">
            <div class="featured-loc-scroll" id="featuredLocScroll">
                @foreach($locations as $loc)
                    <a href="#" class="featured-loc-card" data-cat="{{ $loc->category ? $loc->category->id : '' }}" onclick="flyToLocation({{ $loc->id }}); return false;">
                        <img src="{{ $loc->thumbnail_url ?: 'https://placehold.co/300x200/1e3a8a/ffffff?text=No+Image' }}"
                            alt="{{ $loc->name }}" class="featured-loc-img">
                        <div class="featured-loc-info">
                            <span class="material-symbols-rounded">location_on</span>
                            <div class="featured-loc-title">{{ $loc->name }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>



    <!-- ========== Monuments Modal ========== -->
    <div class="monuments-modal-backdrop" id="monumentsModal" onclick="if(event.target===this) closeMonumentsModal();">
        <div class="monuments-modal-dialog">
            <div class="monuments-modal-header">
                <div class="monuments-modal-title-row">
                    <h3 class="monuments-modal-title">DANH SÁCH ĐỊA ĐIỂM</h3>
                    <button type="button" class="monuments-close-btn" onclick="closeMonumentsModal()" title="Đóng">✕</button>
                </div>
                <div class="monuments-controls-bar">
                    <div class="monuments-controls-left">
                        <select id="monumentsCatSelect" class="monuments-cat-select" onchange="filterMonumentsModal()">
                            <option value="all">Tất cả</option>
                            @foreach($locations->pluck('category')->filter()->unique('id') as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="monuments-count-info" id="monumentsCountInfo">
                        <span><strong id="monumentsCurrentCount">{{ $locations->count() }}</strong> địa điểm</span>
                    </div>
                </div>
            </div>
            <div class="monuments-modal-body">
                <div class="monuments-grid" id="monumentsGrid">
                    @foreach($locations as $loc)
                        <div class="monument-card"
                             data-cat="{{ $loc->category ? $loc->category->id : '' }}"
                             data-name="{{ mb_strtolower($loc->name, 'UTF-8') }}"
                             onclick="selectMonumentFromModal({{ $loc->id }})">
                            <div class="monument-card-img-box">
                                <img src="{{ $loc->thumbnail_url ?: 'https://placehold.co/400x250/1e293b/ffffff?text=Ninh+Binh' }}"
                                     alt="{{ $loc->name }}"
                                     class="monument-card-img"
                                     loading="lazy">
                                <div class="monument-card-title-overlay">{{ mb_strtoupper($loc->name, 'UTF-8') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
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

        const HA_NAM_BOUNDARY_URL = @json(asset('geo/ha-nam-old.geojson'));

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
        L.tileLayer(@json(config('services.carto.tile_url')), {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20,
        }).addTo(map);

        L.control.zoom({ position: 'topright' }).addTo(map);

        // Prevent Leaflet Map events from propagating on search panel
        const topSearchPanelEl = document.getElementById('topSearchPanel');
        if (topSearchPanelEl) {
            L.DomEvent.disableClickPropagation(topSearchPanelEl);
            L.DomEvent.disableScrollPropagation(topSearchPanelEl);
        }



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
        const locations = @json($locations);

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
            iconCreateFunction: function (cluster) {
                const count = cluster.getChildCount();
                let size = 'small';
                let dim = 34;
                if (count >= 10) { size = 'medium'; dim = 40; }
                if (count >= 30) { size = 'large'; dim = 46; }
                return L.divIcon({
                    html: '<div><span>' + count + '</span></div>',
                    className: 'marker-cluster marker-cluster-' + size,
                    iconSize: L.point(dim, dim)
                });
            }
        });

        // Custom hover coverage polygon
        function convexHull(points) {
            // Graham scan
            if (points.length < 3) return points.slice();
            points = points.slice().sort((a, b) => a[0] - b[0] || a[1] - b[1]);
            const cross = (O, A, B) => (A[0] - O[0]) * (B[1] - O[1]) - (A[1] - O[1]) * (B[0] - O[0]);
            const lower = [];
            for (const p of points) { while (lower.length >= 2 && cross(lower[lower.length - 2], lower[lower.length - 1], p) <= 0) lower.pop(); lower.push(p); }
            const upper = [];
            for (let i = points.length - 1; i >= 0; i--) { const p = points[i]; while (upper.length >= 2 && cross(upper[upper.length - 2], upper[upper.length - 1], p) <= 0) upper.pop(); upper.push(p); }
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

        markers.on('clustermouseover', function (e) {
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

        markers.on('clustermouseout', function (e) {
            if (coveragePolygon) {
                map.removeLayer(coveragePolygon);
                coveragePolygon = null;
            }
        });

        markers.on('clusterclick', function (a) {
            if (coveragePolygon) { map.removeLayer(coveragePolygon); coveragePolygon = null; }

            var clusterLatLng = a.layer.getLatLng();
            var currentZoom = map.getZoom();
            var maxZoom = map.getMaxZoom() || 20;

            // Tính khoảng cách thực tế (mét) giữa 2 góc bounds
            var bounds = a.layer.getBounds();
            var boundsDistance = bounds.getNorthEast().distanceTo(bounds.getSouthWest());

            // Nếu đã zoom max, hoặc tất cả marker nằm trong bán kính 50m → spiderfy ngay
            if (currentZoom >= maxZoom || boundsDistance < 50) {
                a.layer.spiderfy();
                return;
            }

            // Zoom dần dần: mỗi lần click tăng tối đa 3 level
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
                        + '<svg class="pin-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="26" height="35">'
                        + '<path fill="' + iconColor + '" d="M172.3 501.7C27 291 0 269.4 0 192 0 86 86 0 192 0s192 86 192 192c0 77.4-27 99-172.3 309.7-9.5 13.8-29.9 13.8-39.5 0z"/>'
                        + '</svg>'
                        + '<img class="pin-icon-img" src="' + iconUrl + '">'
                        + '<div class="custom-pin-tooltip" style="--tip-color: ' + iconColor + ';">' + loc.name + '</div>'
                        + '</div>';

                    const customIcon = L.divIcon({
                        className: 'my-custom-marker',
                        html: pinHtml,
                        iconSize: [26, 35],
                        iconAnchor: [13, 35],
                        popupAnchor: [0, -35]
                    });
                    markerOptions = { icon: customIcon };
                }

                const marker = L.marker([loc.lat, loc.lng], markerOptions);

                // Đẩy z-index DOM trực tiếp khi hover (Lưu lại z-index gốc của Leaflet vốn có trị số lên tới hàng triệu dựa trên Latitude)
                marker.on('mouseover', function () {
                    if (this._icon) {
                        this._originalZIndex = this._icon.style.zIndex;
                        this._icon.style.zIndex = 99999999;
                    }
                });
                marker.on('mouseout', function () {
                    if (this._icon) {
                        this._icon.style.zIndex = this._originalZIndex || '';
                    }
                });

                const thumbUrl = loc.thumbnail_url ? loc.thumbnail_url : 'https://placehold.co/400x250/e2e8f0/475569?text=No+Image';
                const iconColor = loc.category && loc.category.icon_color ? loc.category.icon_color : '#ef4444';

                const ctaLabel = 'Khám phá ngay';
                const popupHtml = '<div class="poi-popup-inner" style="--poi-color: ' + iconColor + ';">'
                    + '<img src="' + thumbUrl + '" class="poi-thumbnail" alt="' + loc.name + '">'
                    + '<div class="poi-content">'
                    + '<div class="poi-title">' + loc.name + '</div>'
                    + (loc.short_description ? '<div class="poi-desc">' + loc.short_description + '</div>' : '')
                    + '<a href="/locations/' + loc.slug + '/360" class="poi-btn-360">'
                    + ctaLabel
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
        window.mapPoiCluster = markers;

        // --- Logic Tìm kiếm và Danh mục ---
        const searchInput = document.getElementById('map-search-input');
        const suggestionsBox = document.getElementById('search-suggestions');

        // Calculate location counts per category
        const catMap = {};
        const catCounts = {};
        const uniqueCategories = [];
        locations.forEach(loc => {
            if (loc.category) {
                catCounts[loc.category.id] = (catCounts[loc.category.id] || 0) + 1;
                if (!catMap[loc.category.id]) {
                    catMap[loc.category.id] = true;
                    uniqueCategories.push(loc.category);
                }
            }
        });

        // Helper function to render matching category icon
        function getCategoryIconHtml(cat) {
            if (!cat) return '<span class="material-symbols-rounded">location_on</span>';

            const iconPath = cat.icon_url || (cat.icon ? (cat.icon.startsWith('http') ? cat.icon : '{{ asset("") }}' + cat.icon.replace(/^\//, '')) : null);

            if (iconPath) {
                return `<img src="${iconPath}" alt="${cat.name}" class="cat-icon-img" style="width: 20px; height: 20px; object-fit: contain; flex-shrink: 0;" onError="this.onerror=null; this.outerHTML='<span class=\\'material-symbols-rounded\\'>location_on</span>';">`;
            }

            const name = (cat.name || '').toLowerCase();
            if (name.includes('tâm linh')) {
                return `<i class="fa-solid fa-place-of-worship" style="color: ${cat.icon_color || '#f59e0b'}; font-size: 16px;"></i>`;
            } else if (name.includes('văn hóa') || name.includes('lịch sử')) {
                return `<i class="fa-solid fa-monument" style="color: ${cat.icon_color || '#8b5cf6'}; font-size: 16px;"></i>`;
            } else if (name.includes('sinh thái')) {
                return `<i class="fa-solid fa-tree" style="color: ${cat.icon_color || '#10b981'}; font-size: 16px;"></i>`;
            } else if (name.includes('ẩm thực')) {
                return `<i class="fa-solid fa-utensils" style="color: ${cat.icon_color || '#f43f5e'}; font-size: 16px;"></i>`;
            } else if (name.includes('lưu trú')) {
                return `<i class="fa-solid fa-hotel" style="color: ${cat.icon_color || '#3b82f6'}; font-size: 16px;"></i>`;
            } else if (name.includes('check-in') || name.includes('check in')) {
                return `<i class="fa-solid fa-camera-retro" style="color: ${cat.icon_color || '#ec4899'}; font-size: 16px;"></i>`;
            }

            return `<span class="material-symbols-rounded" style="color: ${cat.icon_color || 'var(--primary)'}">location_on</span>`;
        }

        // --- Category filter placed in top search dock ---
        const mapCategoryFilter = document.getElementById('mapCategoryFilter');
        const featuredCards = document.querySelectorAll('#featuredLocScroll .featured-loc-card');
        const featuredCountBadge = document.getElementById('featuredCountBadge');
        let activeCategoryFilter = 'all';

        function locationMatchesActiveCategory(loc) {
            if (activeCategoryFilter === 'all') return true;
            return String(loc.category?.id || '') === String(activeCategoryFilter);
        }

        function applyTopCategoryFilter() {
            markers.clearLayers();

            locations.forEach(loc => {
                if (locationMatchesActiveCategory(loc)) {
                    markers.addLayer(loc.marker);
                }
            });
        }

        if (mapCategoryFilter) {
            mapCategoryFilter.addEventListener('change', function () {
                activeCategoryFilter = this.value || 'all';
                applyTopCategoryFilter();
                if (searchInput.value.trim()) {
                    searchInput.dispatchEvent(new Event('input'));
                }
            });
        }

        // --- Nút Gợi Ý Cho Bạn (Lên Lịch Trình AI) ---
        const randomFlyBtn = document.getElementById('randomFlyBtn');
        if (randomFlyBtn) {
            randomFlyBtn.addEventListener('click', () => {
                if (window.openTripPlanner) {
                    window.openTripPlanner();
                }
            });
        }

        // --- Lọc Bán Kính Khám Phá (chỉ quanh vị trí GPS thật) ---
        const radiusBtnGroup = document.getElementById('radiusBtnGroup');
        let currentRadiusCircle = null;
        let pendingRadiusKm = null;

        function setRadiusBtnActive(rVal) {
            if (!radiusBtnGroup) return;
            radiusBtnGroup.querySelectorAll('.radius-btn').forEach(b => {
                b.classList.toggle('active', b.getAttribute('data-radius') === String(rVal));
            });
        }

        function clearRadiusFilter() {
            if (currentRadiusCircle) {
                map.removeLayer(currentRadiusCircle);
                currentRadiusCircle = null;
            }
            markers.clearLayers();
            locations.forEach(loc => {
                if (loc.marker) markers.addLayer(loc.marker);
            });
        }

        function applyRadiusFilter(radiusKm) {
            if (!userCoords) return false;

            if (currentRadiusCircle) {
                map.removeLayer(currentRadiusCircle);
                currentRadiusCircle = null;
            }

            const radiusMeters = radiusKm * 1000;
            const centerPoint = L.latLng(userCoords.lat, userCoords.lng);

            updateUserMarker(userCoords.lat, userCoords.lng);

            currentRadiusCircle = L.circle(centerPoint, {
                radius: radiusMeters,
                color: '#1e3a5f',
                weight: 1.5,
                dashArray: '5,5',
                fillColor: '#1e3a5f',
                fillOpacity: 0.06
            }).addTo(map);

            markers.clearLayers();
            const filtered = locations.filter(loc => {
                const dist = centerPoint.distanceTo(L.latLng(loc.lat, loc.lng));
                return dist <= radiusMeters;
            });

            filtered.forEach(loc => {
                if (loc.marker) markers.addLayer(loc.marker);
            });

            map.fitBounds(currentRadiusCircle.getBounds(), { padding: [30, 30] });
            setRadiusBtnActive(radiusKm);
            showToast(`Tìm thấy ${filtered.length} địa điểm trong bán kính ${radiusKm}km`, 'info', 3000);
            return true;
        }

        if (radiusBtnGroup) {
            radiusBtnGroup.querySelectorAll('.radius-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();

                    const rVal = btn.getAttribute('data-radius');

                    if (rVal === 'all') {
                        pendingRadiusKm = null;
                        setRadiusBtnActive('all');
                        clearRadiusFilter();
                        return;
                    }

                    const radiusKm = parseFloat(rVal);

                    // Chưa có vị trí GPS → xin định vị trước, không khoanh tâm bản đồ giả
                    if (!userCoords) {
                        pendingRadiusKm = radiusKm;
                        setRadiusBtnActive(rVal);
                        showToast('Cần vị trí hiện tại để lọc bán kính gần bạn.', 'info', 2500);
                        requestUserLocation(false);
                        return;
                    }

                    setRadiusBtnActive(rVal);
                    applyRadiusFilter(radiusKm);
                });
            });
        }

        // Hàm loại bỏ dấu tiếng Việt
        function removeAccents(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
        }

        // Xử lý tìm kiếm gợi ý
        searchInput.addEventListener('input', function () {
            const val = this.value.toLowerCase().trim();
            const valNoAccent = removeAccents(val);
            suggestionsBox.innerHTML = '';

            if (val.length === 0) {
                suggestionsBox.classList.remove('active');
                return;
            }

            const results = locations.filter(loc => {
                const nameNoAccent = removeAccents(loc.name.toLowerCase());
                return nameNoAccent.includes(valNoAccent) && locationMatchesActiveCategory(loc);
            });

            if (results.length > 0) {
                results.slice(0, 10).forEach(loc => {
                    const item = document.createElement('div');
                    item.className = 'suggestion-item';

                    item.innerHTML = `
                        <div class="suggestion-info">
                            <div class="suggestion-name">${loc.name}</div>
                            <div class="suggestion-cat">${loc.category?.name || 'Chưa phân loại'}</div>
                        </div>
                    `;

                    const handleSelectLoc = (e) => {
                        if (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        suggestionsBox.classList.remove('active');
                        searchInput.value = loc.name;

                        // Đảm bảo marker đang hiển thị (nếu đang filter)
                        if (!markers.hasLayer(loc.marker)) {
                            markers.addLayer(loc.marker);
                        }

                        const openLocPopup = () => {
                            setTimeout(() => {
                                if (loc.marker) loc.marker.openPopup();
                            }, 100);
                        };

                        // Zoom từng cấp cụm một (step-by-step) thay vì nhảy vọt
                        stepZoomToMarker(loc, () => {
                            let targetZoom = Math.max(18, map.getZoom());
                            let dist = map.getCenter().distanceTo([loc.lat, loc.lng]);

                            if (dist > 80) {
                                map.once('moveend', openLocPopup);
                                map.flyTo([loc.lat, loc.lng], targetZoom, { duration: 1.1 });
                            } else {
                                map.setView([loc.lat, loc.lng], targetZoom, { animate: true });
                                openLocPopup();
                            }
                        });
                    };

                    item.addEventListener('click', handleSelectLoc);
                    item.addEventListener('mousedown', handleSelectLoc);

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

            // Bỏ emoji / icon trang trí trong thông báo
            const clean = String(message || '').replace(/[\u{1F300}-\u{1FAFF}\u{2600}-\u{27BF}\u{FE0F}]/gu, '').replace(/\s{2,}/g, ' ').trim();

            if (type === 'loading') {
                toast.innerHTML = `<div class="toast-spinner"></div><div class="toast-content"></div>`;
            } else {
                toast.innerHTML = `<div class="toast-content"></div>`;
            }
            toast.querySelector('.toast-content').textContent = clean;

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

                    // Lọc bán kính đang chờ vị trí
                    if (pendingRadiusKm != null) {
                        const km = pendingRadiusKm;
                        pendingRadiusKm = null;
                        pendingFlyTo = false;
                        applyRadiusFilter(km);
                        return;
                    }

                    if (pendingFlyTo) {
                        pendingFlyTo = false;
                        updateUserMarker(latitude, longitude);
                        flyToUserLocation();
                    } else if (!silent) {
                        updateUserMarker(latitude, longitude);
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
                    const wasRadius = pendingRadiusKm != null;
                    pendingFlyTo = false;
                    pendingRadiusKm = null;

                    if (wasRadius) {
                        setRadiusBtnActive('all');
                        clearRadiusFilter();
                    }

                    console.warn('Geolocation error:', error.message);
                    if (!silent || wasPending || wasRadius) {
                        let msg = 'Không thể lấy vị trí của bạn.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Vui lòng cấp quyền vị trí trong cài đặt trình duyệt để dùng bán kính gần bạn.';
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
                    showToast('Bạn đang ở ngoài khu vực Ninh Bình.', 'warning');
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
                weatherToggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    newsWidgetWrapper.classList.toggle('weather-active');
                });
            }

            // Ngăn sự kiện click bên trong các banner truyền ra ngoài
            const dropdownBanner = newsWidgetWrapper.querySelector('.news-dropdown-banner');
            if (dropdownBanner) {
                dropdownBanner.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
                L.DomEvent.disableClickPropagation(dropdownBanner);
                L.DomEvent.disableScrollPropagation(dropdownBanner);
            }

            const weatherBanner = newsWidgetWrapper.querySelector('.weather-dropdown-banner');
            if (weatherBanner) {
                weatherBanner.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
                L.DomEvent.disableClickPropagation(weatherBanner);
                L.DomEvent.disableScrollPropagation(weatherBanner);
            }

            // Ngăn chặn click vào thanh mini truyền qua bản đồ
            if (newsWidgetToggle) {
                L.DomEvent.disableClickPropagation(newsWidgetToggle);
                L.DomEvent.disableScrollPropagation(newsWidgetToggle);
            }
        }

        // Click ra ngoài bản đồ để đóng bảng thời tiết (nhưng giữ nguyên bảng tin tức)
        document.addEventListener('click', function (e) {
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
            if (featuredDrawer.classList.contains('open')) {
                document.body.classList.add('drawer-open');
            }

            drawerToggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                featuredDrawer.classList.toggle('open');
                document.body.classList.toggle('drawer-open', featuredDrawer.classList.contains('open'));
            });
        }

        // Prevent map click when clicking drawer
        if (featuredDrawer) {
            featuredDrawer.addEventListener('click', function (e) {
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

        let flyToSeq = 0;

        function openLocationPopup(loc) {
            if (!loc || !loc.marker) return;

            const tryOpen = () => {
                if (typeof markers !== 'undefined' && markers.getVisibleParent) {
                    const parent = markers.getVisibleParent(loc.marker);
                    if (parent && parent.getChildCount) {
                        parent.spiderfy();
                        setTimeout(() => {
                            try { loc.marker.openPopup(); } catch (e) {}
                        }, 200);
                        return;
                    }
                }
                try { loc.marker.openPopup(); } catch (e) {}
            };

            setTimeout(tryOpen, 80);
        }

        // Helper function to fly to a location from the featured drawer
        function flyToLocation(id) {
            const loc = locations.find(l => l.id === id);
            if (!loc || !loc.marker) return;

            const seq = ++flyToSeq;

            // Giữ thanh Khám phá mở — chỉ đổi địa điểm / popup
            map.closePopup();

            const openIfCurrent = () => {
                if (seq !== flyToSeq) return;
                openLocationPopup(loc);
            };

            // Zoom từng lớp cụm một y hệt như tìm kiếm
            stepZoomToMarker(loc, () => {
                if (seq !== flyToSeq) return;

                let targetZoom = Math.max(18, map.getZoom());
                let dist = map.getCenter().distanceTo([loc.lat, loc.lng]);
                const zoomDiff = Math.abs(map.getZoom() - targetZoom);

                if (dist > 80 || zoomDiff > 0.2) {
                    map.flyTo([loc.lat, loc.lng], targetZoom, { duration: 1.1 });
                    map.once('moveend', openIfCurrent);
                    setTimeout(() => {
                        if (seq !== flyToSeq) return;
                        if (loc.marker && !loc.marker.isPopupOpen()) openLocationPopup(loc);
                    }, 1600);
                } else {
                    map.setView([loc.lat, loc.lng], targetZoom, { animate: true });
                    openIfCurrent();
                }
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
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('userSidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggle');

        if (sidebar && sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');

                if (sidebar.classList.contains('collapsed')) {
                    document.body.classList.remove('sidebar-expanded');
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    document.body.classList.add('sidebar-expanded');
                    document.body.classList.remove('sidebar-collapsed');
                }
            });

            // Tự động thu gọn trên màn hình nhỏ
            if (window.innerWidth <= 1024) {
                sidebar.classList.add('collapsed');
                document.body.classList.remove('sidebar-expanded');
                document.body.classList.add('sidebar-collapsed');
            }
        }

        // Claim Daily Bonus from map widget
        @auth
        const widgetClaimDailyBtn = document.getElementById("widgetClaimDailyBtn");
        if (widgetClaimDailyBtn) {
            widgetClaimDailyBtn.addEventListener("click", function() {
                widgetClaimDailyBtn.disabled = true;
                widgetClaimDailyBtn.innerHTML = "Đang nhận...";

                fetch("{{ route('client.profile.claim_daily') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const parentContainer = widgetClaimDailyBtn.parentElement;
                        if (parentContainer) {
                            parentContainer.innerHTML = '<div style="display: flex; align-items: center; gap: 2px; font-weight: 700; color: #334155; font-size: 0.6rem;">+' + (data.coins || 10) + ' <img src="{{ asset("images/xu.png") }}" alt="xu" style="width: 11px; height: 11px; object-fit: contain;"></div><span style="font-weight: 600; color: #475569; font-size: 0.56rem; white-space: nowrap;">Đã điểm danh</span>';
                        }
                        
                        // Update points displays
                        const widgetPoints = document.getElementById("widgetPoints");
                        if (widgetPoints) {
                            widgetPoints.textContent = data.points;
                        }
                        const headerPoints = document.getElementById("navbarUserPoints");
                        if (headerPoints) {
                            headerPoints.textContent = data.points + " xu";
                        }
                        alert(data.message);
                    } else {
                        widgetClaimDailyBtn.disabled = false;
                        widgetClaimDailyBtn.innerHTML = "Nhận";
                        alert(data.message);
                    }
                })
                .catch(error => {
                    widgetClaimDailyBtn.disabled = false;
                    widgetClaimDailyBtn.innerHTML = "Nhận";
                    console.error("Error claiming daily bonus:", error);
                    alert("Có lỗi xảy ra, vui lòng thử lại sau.");
                });
            });
        }
        @endauth

        // Toggle Mission Widget — nút tròn ↔ panel
        const missionWidget = document.getElementById("mission-widget-wrapper");
        const missionHeader = document.getElementById("mission-widget-header");
        const missionFabBtn = document.getElementById("mission-fab-btn");

        function setMissionExpanded(expanded) {
            if (!missionWidget) return;
            missionWidget.classList.toggle("collapsed", !expanded);
            if (missionFabBtn) {
                missionFabBtn.setAttribute("aria-expanded", expanded ? "true" : "false");
            }
        }

        if (missionWidget) {
            if (missionFabBtn) {
                missionFabBtn.addEventListener("click", function (e) {
                    e.stopPropagation();
                    setMissionExpanded(true);
                });
            }
            if (missionHeader) {
                missionHeader.addEventListener("click", function () {
                    setMissionExpanded(false);
                });
                missionHeader.addEventListener("keydown", function (e) {
                    if (e.key === "Enter" || e.key === " ") {
                        e.preventDefault();
                        setMissionExpanded(false);
                    }
                });
            }

            L.DomEvent.disableClickPropagation(missionWidget);
            L.DomEvent.disableScrollPropagation(missionWidget);
        }

        // VR Header Menu Actions
        function toggleFeaturedDrawer() {
            const drawer = document.getElementById('featured-drawer');
            if (drawer) {
                drawer.classList.toggle('open');
            }
        }

        function resetMapView() {
            if (typeof map !== 'undefined' && map) {
                map.setView([20.25, 105.97], 11);
            } else {
                window.location.href = "{{ url('/') }}";
            }
        }

        const vrFloatingMenuEl = document.querySelector('.vr-floating-hex-menu');
        if (vrFloatingMenuEl && typeof L !== 'undefined' && L.DomEvent) {
            L.DomEvent.disableClickPropagation(vrFloatingMenuEl);
            L.DomEvent.disableScrollPropagation(vrFloatingMenuEl);
        }

        // ========== Monuments Modal JS ==========
        function openMonumentsModal() {
            var modal = document.getElementById('monumentsModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                filterMonumentsModal();
            }
        }
        function closeMonumentsModal() {
            var modal = document.getElementById('monumentsModal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        function filterMonumentsModal() {
            var catSelect = document.getElementById('monumentsCatSelect');
            var selectedCat = catSelect ? catSelect.value : 'all';
            var cards = document.querySelectorAll('#monumentsGrid .monument-card');
            var visibleCount = 0;
            cards.forEach(function(card) {
                var catId = card.getAttribute('data-cat');
                var matchCat = (selectedCat === 'all' || catId === selectedCat);
                if (matchCat) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            var countEl = document.getElementById('monumentsCurrentCount');
            if (countEl) countEl.textContent = visibleCount;
        }
        function selectMonumentFromModal(locId) {
            closeMonumentsModal();
            if (typeof locations !== 'undefined' && Array.isArray(locations)) {
                var loc = locations.find(function(l) { return l.id === locId; });
                if (loc && loc.lat && loc.lng && typeof map !== 'undefined' && map) {
                    map.flyTo([loc.lat, loc.lng], 16, { animate: true, duration: 1.2 });
                    setTimeout(function() {
                        if (loc.marker) loc.marker.openPopup();
                    }, 1200);
                }
            }
        }

        // Bắt sự kiện Leaflet cho nút Hex 2
        (function() {
            var btn = document.getElementById('vrBtnCategory');
            if (btn && typeof L !== 'undefined' && L.DomEvent) {
                L.DomEvent.on(btn, 'click', function(e) {
                    L.DomEvent.stopPropagation(e);
                    openMonumentsModal();
                });
            }
        })();

        // Đóng modal bằng ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMonumentsModal();
        });
    </script>
    
    @include('client.components.contribution-modals')

    <!-- AI Chatbot & Trip Planner Floating Widgets -->
    <x-chatbot-widget />
    <x-trip-planner-widget />
</body>
</html>