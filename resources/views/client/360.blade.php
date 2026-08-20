<!DOCTYPE html>
<html>
@php $hasPanorama = $location->panoramas()->exists(); @endphp
<head>
    <title>{{ ($hasPanorama ? 'Khám phá 360°' : 'Khám phá') }} - {{ $location->name }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
    <style> @-ms-viewport { width: device-width; } </style>
    
    <!-- Marzipano Original CSS -->
    <link rel="stylesheet" href="{{ asset('marzipano/vendor/reset.min.css') }}">
    <link rel="stylesheet" href="{{ asset('marzipano/style.css') }}">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">

    <style>
        :root {
            --hotspot-color: {{ $location->category->icon_color ?? '#FF512F' }};
        }
        body, html { margin: 0; padding: 0; height: 100vh; overflow: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .viewer-area { width: 100%; height: 100vh; position: relative; background: #000; overflow: hidden; }
        #pano { position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; }

        /* Back Button (Clean white text + arrow only) */
        .btn-back-map {
            position: absolute; top: 20px; left: 20px; z-index: 1000;
            display: flex; align-items: center; gap: 8px;
            background: none;
            border: none;
            padding: 4px 8px;
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: 600;
            font-size: 15px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
            transition: transform 0.2s ease, opacity 0.2s ease;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }
        .btn-back-map:hover, .btn-back-map:focus, .btn-back-map:active {
            color: #ffffff !important;
            transform: translateX(-3px);
            opacity: 0.85;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.6);
        }
        .btn-back-map i {
            font-size: 15px;
            color: #ffffff !important;
            transition: transform 0.2s ease;
        }
        .btn-back-map:hover i {
            transform: translateX(-2px);
        }

        /* Top-right more menu (share, report) */
        .viewer-more-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10050;
        }
        .viewer-more-btn {
            width: 36px;
            height: 36px;
            border: 1.5px solid #ffffff;
            border-radius: 50%;
            background: transparent;
            color: #ffffff;
            padding: 0;
            font-size: 16px;
            line-height: 1;
            letter-spacing: 0;
            cursor: pointer;
            text-shadow: none;
            box-shadow: none;
            filter: none;
            opacity: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .viewer-more-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: #ffffff;
            color: #ffffff;
            opacity: 1;
        }
        .viewer-more-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            min-width: 120px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 6px;
            padding: 4px 0;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.12s ease;
        }
        .viewer-more-dropdown.is-open {
            opacity: 1;
            visibility: visible;
        }
        .viewer-more-item {
            width: 100%;
            border: none;
            background: transparent;
            color: #334155;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
            padding: 8px 14px;
            cursor: pointer;
            transition: color 0.12s ease;
        }
        .viewer-more-item:hover {
            color: #1e3a5f;
        }
        body.reviews-drawer-open .viewer-more-menu {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        body.reviews-drawer-open .floating-comment-container {
            display: none !important;
        }

        /* Tên scene 360 — giữa top (giữa nút Quay lại và ···) */
        #titleBar,
        body.fullscreen-enabled #titleBar,
        body.multiple-scenes #titleBar,
        body.fullscreen-enabled.mobile #titleBar,
        body.multiple-scenes.mobile #titleBar,
        .mobile #titleBar {
            display: flex !important;
            position: absolute;
            top: 18px !important;
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%);
            width: min(52vw, 420px) !important;
            height: auto !important;
            z-index: 1000;
            pointer-events: none;
            text-align: center;
            justify-content: center;
            align-items: center;
        }
        #titleBar .sceneName,
        .mobile #titleBar .sceneName {
            width: 100%;
            height: auto;
            margin: 0;
            padding: 0 8px;
            line-height: 1.25 !important;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            background: none !important;
            background-color: transparent !important;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.55);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            pointer-events: auto;
        }
        body.viewer-guide-open #titleBar,
        body.reviews-drawer-open #titleBar,
        body.pano-photo-open #titleBar {
            opacity: 0;
            visibility: hidden;
        }
        @media (max-width: 640px) {
            #titleBar {
                top: 16px;
                width: min(46vw, 200px);
            }
            #titleBar .sceneName {
                font-size: 13px;
            }
        }

        #autorotateToggle { display: none !important; }
        #fullscreenToggle { display: none !important; }
        #sceneList { display: none !important; }
        #sceneListToggle { display: none !important; }
        .viewControlButton { display: none !important; }

        /* Audio Mascot Player */
        .audio-player {
            position: absolute; bottom: 24px; right: 24px; z-index: 10049;
            display: none; /* Hidden by default, shown when audio available */
            flex-direction: column; align-items: center; gap: 6px;
            user-select: none;
            transition: all 0.3s ease;
        }
        .audio-player.visible { display: flex; }

        .audio-mascot-btn {
            width: 70px; height: auto;
            cursor: pointer;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.35));
            transition: transform 0.2s ease, filter 0.2s ease;
            -webkit-user-drag: none;
        }
        .audio-mascot-btn:hover {
            transform: scale(1.04);
            filter: drop-shadow(0 6px 16px rgba(0,0,0,0.45));
        }
        .audio-mascot-btn:active {
            transform: scale(0.98);
        }

        /* Audio Mascot Wrapper & Small Info Button "i" */
        .audio-mascot-wrapper {
            position: relative;
            display: inline-block;
        }
        .audio-info-btn {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
            transition: transform 0.2s ease, background 0.2s ease;
            z-index: 10;
            padding: 0;
            line-height: 1;
        }
        .audio-info-btn:hover {
            transform: scale(1.08);
            background: #3b82f6;
            border-color: #60a5fa;
            color: #ffffff;
        }

        /* Small Audio Info Popover Card (Light Theme) */
        .audio-info-popover {
            position: absolute;
            bottom: calc(100% + 10px);
            right: 0;
            width: 230px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 12px;
            padding: 9px 11px;
            color: #0f172a;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
            opacity: 0;
            transform: scale(0.92) translateY(6px);
            transform-origin: bottom right;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1005;
        }
        .audio-info-popover.active {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }

        /* Popover Arrow pointing down to mascot */
        .audio-info-popover::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 18px;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.94);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transform: rotate(45deg);
        }

        .audio-popover-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
            font-weight: 700;
            color: #0f172a;
            padding-bottom: 5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .audio-popover-header span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 6px;
        }
        .btn-close-popover {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 11px;
            padding: 0 2px;
            line-height: 1;
            border-radius: 3px;
            transition: color 0.15s;
        }
        .btn-close-popover:hover {
            color: #0f172a;
        }

        .audio-popover-body {
            font-size: 11.5px;
            line-height: 1.45;
            color: #334155;
            max-height: 120px;
            overflow-y: auto;
            padding-right: 3px;
            font-weight: 400;
        }
        .audio-popover-body::-webkit-scrollbar {
            width: 3px;
        }
        .audio-popover-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .audio-progress-bar {
            width: 70px; height: 6px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            cursor: pointer;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            transition: width 0.2s ease, height 0.2s ease;
        }
        .audio-progress-bar:hover {
            height: 8px;
            border-color: rgba(255, 255, 255, 0.5);
        }
        .audio-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #FF512F, #F09819);
            border-radius: 6px;
            width: 0%;
            transition: width 0.1s linear;
        }

        /* Floating Toolbar (Clean icons only, light shadow & subtle hover) */
        .interaction-toolbar {
            position: absolute; right: 24px; top: 50%; transform: translateY(-50%);
            z-index: 1000; display: flex; flex-direction: column; gap: 16px;
            align-items: center;
        }
        .interaction-btn {
            width: 36px; height: 36px;
            background: none !important;
            border: none !important;
            color: #ffffff !important;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 20px;
            transition: transform 0.2s ease, opacity 0.2s ease;
            position: relative;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        .interaction-btn i {
            filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.5));
            transition: filter 0.2s ease;
        }
        .interaction-btn:hover {
            transform: scale(1.06);
            opacity: 0.9;
        }
        .interaction-btn:hover i {
            filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.65));
        }
        .interaction-btn:active {
            transform: scale(0.98);
        }

        /* Tooltip label on hover */
        .interaction-btn::before {
            content: attr(title);
            position: absolute;
            right: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(6px);
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
        }
        .interaction-btn:hover::before {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        .interaction-btn.active .fa-heart {
            color: #f43f5e !important;
            filter: drop-shadow(0 0 10px rgba(244, 63, 94, 0.8)) drop-shadow(0 2px 6px rgba(0, 0, 0, 0.6)) !important;
        }
        
        .interaction-badge {
            position: absolute; top: -4px; right: -4px;
            background: #ef4444; color: white;
            font-size: 10px; font-weight: 700; min-width: 17px; height: 17px; border-radius: 9px;
            padding: 0 4px;
            display: flex; align-items: center; justify-content: center;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.5);
        }

        /* Side Drawer */
        .comments-drawer {
            position: fixed; right: -420px; top: 0; bottom: 0; width: 380px; max-width: 100vw;
            background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            z-index: 10060; transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            border-left: 1px solid #cbdbe8; display: flex; flex-direction: column;
            box-shadow: -8px 0 25px rgba(0, 0, 0, 0.06);
        }
        .comments-drawer.open { right: 0; }
        
        .drawer-header {
            padding: 16px 20px; border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: space-between; color: #1e3a5f;
            background: #f8fafc;
        }
        .drawer-header h3 { margin: 0; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #1e3a5f; }
        .drawer-header h3 i { color: #1e3a5f; font-size: 15px; }
        .btn-close-drawer {
            background: #ffffff; border: 1px solid #cbdbe8; color: #6482a6; font-size: 13px; cursor: pointer;
            width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-close-drawer:hover { background: #f1f5f9; color: #1e3a5f; }
        
        .comments-list {
            flex: 1; overflow-y: auto; padding: 16px;
        }
        .comments-list::-webkit-scrollbar { width: 4px; }
        .comments-list::-webkit-scrollbar-thumb { background: #cbdbe8; border-radius: 2px; }

        /* Google Maps Style Review List (Unboxed Clean Layout) */
        .gmaps-review-card {
            background: transparent;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 0;
            padding: 16px 4px 14px;
            margin-bottom: 0;
            position: relative;
            overflow: visible;
            animation: fadeIn 0.3s ease;
        }
        .gmaps-review-card:last-child {
            border-bottom: none;
        }

        .gmaps-review-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            overflow: visible;
        }
        .gmaps-user-block {
            display: flex;
            align-items: center;
            gap: 12px;
            overflow: visible;
            min-width: 0;
        }
        .gmaps-user-block .avatar-frame-wrapper {
            width: 42px !important;
            height: 42px !important;
            flex-shrink: 0;
            overflow: visible;
        }
        .gmaps-user-block .avatar-frame-wrapper img:not(.avatar-frame-png-overlay) {
            width: 100% !important;
            height: 100% !important;
            border-radius: 50%;
            object-fit: cover;
            border: none;
        }
        .gmaps-user-block .avatar-frame-png-overlay {
            width: 124% !important;
            height: 124% !important;
            border: none !important;
            border-radius: 0 !important;
            object-fit: contain !important;
        }
        /* Legacy plain img fallback (no frame wrapper) */
        .gmaps-user-block > img.comment-avatar {
            width: 38px; height: 38px; border-radius: 50%; object-fit: cover;
            border: 1px solid #cbdbe8; flex-shrink: 0;
        }
        .gmaps-username {
            color: #1e3a5f;
            font-weight: 600;
            font-size: 13.5px;
            line-height: 1.25;
        }
        .gmaps-user-subtitle {
            color: #6482a6;
            font-size: 11px;
            margin-top: 1px;
            font-weight: 400;
        }

        /* 3-Dots Dropdown Menu */
        .gmaps-more-wrapper {
            position: relative;
        }
        .gmaps-btn-more {
            background: transparent;
            border: none;
            color: #94a3b8;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .gmaps-btn-more:hover {
            background: #f1f5f9;
            color: #1e3a5f;
        }
        .gmaps-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 120px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 4px 0;
            z-index: 100;
            display: none;
        }
        .gmaps-dropdown-menu.show {
            display: block;
        }
        .gmaps-dropdown-item {
            padding: 7px 12px;
            font-size: 12.5px;
            color: #3b5980;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
            font-weight: 500;
        }
        .gmaps-dropdown-item:hover {
            background: #f8fafc;
            color: #1e3a5f;
        }
        .gmaps-dropdown-item.text-danger {
            color: #dc2626;
        }
        .gmaps-dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Rating Stars & Date Line */
        .gmaps-rating-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }
        .gmaps-stars {
            color: #fbbc04;
            font-size: 10.5px;
            display: flex;
            gap: 2px;
        }
        .gmaps-date {
            color: #6482a6;
            font-size: 11px;
            font-weight: 400;
        }

        /* Review Content */
        .gmaps-review-body {
            color: #3b5980;
            font-size: 13px;
            line-height: 1.5;
            word-break: break-word;
            margin-bottom: 10px;
            font-weight: 400;
        }
        .gmaps-owner-reply {
            margin: 0 0 12px;
            padding: 8px 10px;
            background: #f8fafc;
            border-left: 2px solid #cbd5e1;
            border-radius: 0 6px 6px 0;
            opacity: 0.72;
        }
        .gmaps-owner-reply__label {
            font-size: 11px;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .gmaps-owner-reply__text {
            margin: 0;
            font-size: 12.5px;
            color: #64748b;
            line-height: 1.45;
            white-space: pre-wrap;
        }
        .gmaps-btn-see-more {
            background: none;
            border: none;
            color: #1e3a5f;
            font-weight: 600;
            font-size: 12.5px;
            cursor: pointer;
            padding: 0;
            margin-left: 4px;
        }
        .gmaps-btn-see-more:hover {
            text-decoration: underline;
        }

        /* Footer Action Bar */
        .gmaps-review-footer {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-top: 4px;
        }
        .gmaps-action-btn {
            background: transparent;
            border: none;
            color: #6482a6;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            padding: 3px 6px;
            border-radius: 4px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .gmaps-action-btn:hover {
            background: #f8fafc;
            color: #1e3a5f;
        }
        .gmaps-action-btn.liked {
            color: #1e3a5f;
        }
        .gmaps-action-btn.liked i {
            font-weight: 900;
        }
        
        /* Razor-Sharp Geometric SVG Stars */
        .sharp-star-btn {
            width: 20px;
            height: 20px;
            cursor: pointer;
            stroke-linejoin: miter;
            stroke-miterlimit: 10;
            transition: transform 0.15s ease, fill 0.15s ease, stroke 0.15s ease;
        }
        .sharp-star-btn.filled {
            fill: #fbbc04;
            stroke: #fbbc04;
            stroke-width: 1;
        }
        .sharp-star-btn.empty {
            fill: none;
            stroke: #cbd5e1;
            stroke-width: 1.8;
        }
        .sharp-star-btn:hover {
            transform: scale(1.08);
        }

        .star-rating-picker {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .comment-form {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .btn-goto-my-review {
            background: #f1f5f9;
            border: 1px solid #cbdbe8;
            color: #1e3a5f;
            font-size: 11.5px;
            font-weight: 500;
            padding: 2px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }
        .btn-goto-my-review:hover {
            background: #e2e8f0;
            border-color: #1e3a5f;
        }

        @keyframes highlightFlash {
            0% { background-color: #fef3c7; }
            100% { background-color: transparent; }
        }
        .my-review-highlight {
            animation: highlightFlash 1.5s ease-out;
        }

        /* Remove white square block & icon ONLY from Info Hotspot tags */
        .info-hotspot .hotspot-badge-icon {
            display: none !important;
        }
        .info-hotspot .hotspot-badge-title {
            margin-left: 0 !important;
            border-radius: 8px !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            padding: 0 14px !important;
        }

        /* Floating Curved Review Bubbles (Mini Compact Arc Loop Shifted Down) */
        .floating-comment-container {
            position: fixed;
            top: 110px;
            right: 25px;
            z-index: 9999;
            pointer-events: none;
            width: 230px;
            height: 120px;
            overflow: visible;
        }

        .floating-comment-card {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.85);
            border-radius: 14px;
            padding: 6px 10px 6px 8px;
            box-shadow: 0 6px 18px rgba(30, 58, 95, 0.12), 0 2px 4px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 230px;
            opacity: 0;
            overflow: visible;
            backface-visibility: hidden;
            will-change: transform, opacity;
            animation: floatUCurve 1.8s linear forwards;
        }

        .floating-comment-card .avatar-frame-wrapper {
            width: 30px !important;
            height: 30px !important;
            flex-shrink: 0;
            overflow: visible;
        }
        .floating-comment-card .avatar-frame-wrapper img:not(.avatar-frame-png-overlay) {
            width: 100% !important;
            height: 100% !important;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .floating-comment-card .avatar-frame-png-overlay {
            width: 128% !important;
            height: 128% !important;
            border: none !important;
            border-radius: 0 !important;
            object-fit: contain !important;
            box-shadow: none !important;
        }
        .floating-comment-card > img.user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .floating-comment-card .comment-content-box {
            display: flex;
            flex-direction: column;
            gap: 1px;
            overflow: hidden;
        }

        .floating-comment-card .user-name-row {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .floating-comment-card .user-name {
            font-size: 11px;
            font-weight: 600;
            color: #1e3a5f;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .floating-comment-card .stars-row {
            display: flex;
            align-items: center;
            gap: 1.5px;
        }
        .floating-comment-card .stars-row svg {
            width: 9.5px;
            height: 9.5px;
            fill: #fbbc04;
            stroke: #fbbc04;
        }

        .floating-comment-card .comment-text {
            font-size: 10.5px;
            color: #3b5980;
            font-weight: 400;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        /* Mini Compact Arc Loop (Fast 1.8s Sweep Shifted Down) */
        @keyframes floatUCurve {
            0% {
                opacity: 0;
                transform: translate3d(30px, 0, 0) scale(0.9);
            }
            35% {
                opacity: 0.95;
                transform: translate3d(-15px, -15px, 0) scale(0.98);
            }
            65% {
                opacity: 1;
                transform: translate3d(-45px, -40px, 0) scale(1.02);
            }
            100% {
                opacity: 0;
                transform: translate3d(-80px, -70px, 0) scale(0.9);
            }
        }

        /* Write Review Modal (Light Theme Google Maps Style) */
        .write-review-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.45); z-index: 10100;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        }
        .write-review-overlay.active { display: flex; }
        .write-review-modal {
            background: #ffffff;
            border-radius: 8px;
            width: 460px;
            max-width: 92%;
            border: 1px solid #e5e7eb;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
            animation: modalPopIn 0.25s ease-out;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .write-review-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .location-name-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e3a5f;
            margin: 0;
        }
        .btn-close-write-modal {
            background: transparent;
            border: none;
            color: #6482a6;
            font-size: 16px;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s ease;
        }
        .btn-close-write-modal:hover {
            background: #f1f5f9;
            color: #1e3a5f;
        }

        .write-review-body {
            padding: 18px 20px;
            overflow-y: auto;
            max-height: 70vh;
        }

        /* User Info Section */
        .review-user-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            overflow: visible;
        }
        .review-user-box .avatar-frame-wrapper {
            overflow: visible;
            flex-shrink: 0;
        }
        .user-display-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e3a5f;
        }
        .user-privacy-tag {
            font-size: 11.5px;
            color: #6482a6;
            margin-top: 1px;
        }

        /* Stars inside modal */
        .review-modal-stars-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
            padding: 10px 0 16px 0;
            margin-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .modal-stars-picker {
            gap: 8px;
        }
        .modal-stars-picker .modal-star-btn.fa-solid {
            color: #fbbc04;
        }
        .modal-stars-picker .modal-star-btn {
            width: 28px;
            height: 28px;
            cursor: pointer;
        }
        .modal-rating-hint {
            font-size: 13px;
            font-weight: 500;
            color: #3b5980;
            min-height: 20px;
        }

        /* Review Input & Media Button */
        .review-input-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .review-input-box textarea {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #cbdbe8;
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 13px;
            color: #1e3a5f;
            resize: none;
            outline: none;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .review-input-box textarea:focus {
            border-color: #1e3a5f;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1);
        }
        .review-input-box textarea::placeholder {
            color: #94a3b8;
        }

        .review-media-upload-area {
            display: flex;
            align-items: center;
        }
        .btn-add-media-dummy {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #cbdbe8;
            border-radius: 6px;
            padding: 9px;
            font-size: 13px;
            font-weight: 500;
            color: #1e3a5f;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s ease;
        }
        .btn-add-media-dummy:hover {
            background: #f1f5f9;
        }

        /* Modal Footer */
        .write-review-footer {
            padding: 12px 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: #ffffff;
        }
        .btn-review-cancel {
            background: #f1f5f9;
            border: 1px solid #cbdbe8;
            padding: 8px 18px;
            border-radius: 6px;
            color: #52525b;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-review-cancel:hover {
            background: #e5e7eb;
            color: #1e3a5f;
        }
        .btn-review-submit {
            background: #1e3a5f;
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
            box-shadow: 0 2px 6px rgba(30, 58, 95, 0.2);
        }
        .btn-review-submit:hover {
            background: #2b4c7e;
        }
        .btn-review-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Rating Overview Header Card (Unboxed) */
        .rating-overview-card {
            background: transparent;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 0;
            padding: 0 0 16px 0;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: none;
        }
        .rating-score-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-right: 14px;
            border-right: 1px solid #f1f5f9;
            min-width: 90px;
        }
        .rating-score-num {
            font-size: 32px;
            font-weight: 600;
            color: #1e3a5f;
            line-height: 1;
            margin-bottom: 4px;
        }
        .rating-score-stars {
            color: #fbbc04;
            font-size: 11px;
            display: flex;
            gap: 2px;
            margin-bottom: 4px;
        }
        .rating-score-count {
            font-size: 11px;
            color: #6482a6;
            font-weight: 400;
            white-space: nowrap;
        }
        .rating-bars-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
        }
        .star-num {
            color: #52525b;
            width: 26px;
            display: flex;
            align-items: center;
            gap: 3px;
            font-weight: 500;
        }
        .star-num i {
            color: #fbbc04;
            font-size: 9px;
        }
        .rating-bar-bg {
            flex: 1;
            height: 6px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }
        .rating-bar-fill {
            height: 100%;
            background: #fbbc04;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        .star-count {
            color: #a1a1aa;
            min-width: 14px;
            text-align: right;
            font-size: 10.5px;
        }
        
        /* Search & Sort Filter Bar */
        .gmaps-filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .gmaps-search-box {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
        }
        .gmaps-search-box .search-icon {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            font-size: 12px;
            pointer-events: none;
        }
        .gmaps-search-box input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #cbdbe8;
            border-radius: 8px;
            padding: 7.5px 28px 7.5px 30px;
            font-size: 12px;
            color: #1e3a5f;
            outline: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .gmaps-search-box input:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1);
        }
        .gmaps-search-box input::placeholder {
            color: #a1a1aa;
        }
        .btn-clear-search {
            position: absolute;
            right: 8px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 2px 4px;
            font-size: 11px;
        }
        .btn-clear-search:hover {
            color: #1e3a5f;
        }

        .gmaps-sort-box {
            position: relative;
            min-width: 135px;
        }
        .gmaps-sort-box select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            background: #ffffff;
            border: 1px solid #cbdbe8;
            border-radius: 8px;
            padding: 7.5px 24px 7.5px 10px;
            font-size: 12px;
            color: #1e3a5f;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            transition: border-color 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .gmaps-sort-box select:focus {
            border-color: #1e3a5f;
        }
        .gmaps-sort-box select option {
            background: #ffffff;
            color: #1e3a5f;
            padding: 6px;
        }
        .select-caret {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #6482a6;
            font-size: 11px;
            pointer-events: none;
        }

        .star-rating-picker {
            display: flex; align-items: center; gap: 3px; color: #f59e0b; cursor: pointer; font-size: 1rem;
        }
        .star-rating-picker i { transition: transform 0.15s ease; }
        .star-rating-picker i:hover { transform: scale(1.15); }
        .comment-stars-display {
            color: #f59e0b; font-size: 10.5px; display: inline-flex; gap: 2px; vertical-align: middle;
        }
        
        .auth-prompt { text-align: center; color: #52525b; font-size: 13px; }
        .auth-prompt a { color: #1e3a5f; text-decoration: none; font-weight: 500; }
        .auth-prompt a:hover { text-decoration: underline; }

        /* Report Modal Styles (Refined per DESIGN_GUIDE.md) */
        .report-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4); z-index: 10000;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        }
        .report-modal-overlay.active { display: flex; }
        .report-modal {
            background: #ffffff; border-radius: 10px; width: 390px; max-width: 90%;
            padding: 20px; color: #1e3a5f; border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            animation: modalPopIn 0.25s ease-out;
        }
        .report-modal h4 { margin-top: 0; margin-bottom: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1e3a5f; }
        .report-modal label { display: block; margin-bottom: 5px; font-size: 12.5px; color: #3b5980; font-weight: 500; }
        .report-modal select, .report-modal textarea {
            width: 100%; background: #f8fafc; border: 1px solid #cbdbe8;
            border-radius: 8px; padding: 9px 12px; color: #1e3a5f; margin-bottom: 14px; font-family: inherit; font-size: 13px;
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .report-modal select:focus, .report-modal textarea:focus { border-color: #1e3a5f; outline: none; background: #ffffff; box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1); }
        .report-modal select option { background: #ffffff; color: #1e3a5f; }
        .report-modal textarea { resize: none; }
        .report-modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 4px; }

        .btn-report-cancel { background: #f1f5f9; border: 1px solid #cbdbe8; padding: 8px 16px; border-radius: 8px; color: #52525b; cursor: pointer; transition: all 0.2s ease; font-size: 13px; font-weight: 500; }
        .btn-report-cancel:hover { background: #e5e7eb; color: #1e3a5f; }
        .btn-report-submit { background: #dc2626; border: none; padding: 8px 18px; border-radius: 8px; color: white; cursor: pointer; font-weight: 500; transition: background 0.2s ease; font-size: 13px; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.2); }
        .btn-report-submit:hover { background: #b91c1c; }
        .btn-report-submit:active { transform: translateY(0); }
        .btn-report-submit:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Floating Toast Notification 360 */
        .toast-notification-360 {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-12px);
            background: #ffffff;
            border: 1px solid #e0e3e5;
            color: #191c1e;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
            z-index: 10000;
            font-family: 'Be Vietnam Pro', sans-serif;
        }
        .toast-notification-360.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        /* Heart Pop Animation */
        @keyframes heartPop {
            0% { transform: scale(1); }
            50% { transform: scale(1.38); }
            100% { transform: scale(1); }
        }
        .heart-pop-anim {
            animation: heartPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .interaction-btn.active {
            background-color: rgba(255, 228, 230, 0.25) !important;
            border-color: #fecdd3 !important;
        }
        .interaction-btn.active i {
            color: #ef4444 !important;
        }

        /* Photo viewer (no 360) — nền ảnh blur full, ảnh nét contain giữa */
        .photo-viewer-stage {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            overflow: hidden;
            cursor: default;
        }
        .photo-viewer-stage__blur {
            position: absolute;
            inset: -24px;
            width: calc(100% + 48px);
            height: calc(100% + 48px);
            object-fit: cover;
            object-position: center;
            filter: blur(28px) saturate(1.05) brightness(0.72);
            transform: scale(1.08);
            display: block;
            pointer-events: none;
            user-select: none;
            -webkit-user-drag: none;
        }
        .photo-viewer-stage__img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
            pointer-events: none;
            z-index: 1;
        }
        .photo-viewer-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: auto;
            height: auto;
            padding: 12px 10px;
            border: none;
            border-radius: 0;
            background: none;
            box-shadow: none;
            color: #ffffff;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            opacity: 0.9;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }
        .photo-viewer-nav:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.06);
        }
        .photo-viewer-nav:active {
            transform: translateY(-50%) scale(0.98);
        }
        .photo-viewer-nav--prev { left: 12px; }
        .photo-viewer-nav--next { right: 12px; }
        .photo-viewer-nav i {
            filter: drop-shadow(0 1px 0.5px rgba(0, 0, 0, 0.3));
        }
        .photo-viewer-stage__empty {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            padding: 24px;
            text-align: center;
            background: #000;
        }

        /* Bottom dock (Lưu / Bình luận / Ảnh) */
        .viewer-dock {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10050;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 20px 12px 14px;
            flex-wrap: nowrap;
            background: none;
            pointer-events: none;
            transition: padding 0.22s ease;
        }
        .viewer-dock__tools {
            display: contents;
        }
        .viewer-dock__btn {
            pointer-events: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 6px;
            border: none;
            background: none !important;
            box-shadow: none !important;
            outline: none;
            color: #fff;
            cursor: pointer;
            padding: 0;
            box-sizing: border-box;
            width: 72px;
            min-width: 72px;
            max-width: 72px;
            flex: 0 0 72px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.28);
            filter: none !important;
            transform: none !important;
            -webkit-font-smoothing: antialiased;
            transition: opacity 0.15s ease;
        }
        .viewer-dock__btn:hover,
        .viewer-dock__btn:focus,
        .viewer-dock__btn:active {
            opacity: 0.88;
            background: none !important;
            box-shadow: none !important;
            outline: none;
            transform: none !important;
        }
        .viewer-dock__btn > i,
        .viewer-dock__btn .viewer-dock__icon-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            font-size: 20px;
            line-height: 1;
            filter: drop-shadow(0 1px 0.5px rgba(0, 0, 0, 0.3));
            text-shadow: none !important;
        }
        .viewer-dock__icon-wrap {
            position: relative;
        }
        .viewer-dock__label {
            display: block;
            width: 100%;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0;
            line-height: 1.15;
            text-align: center;
            white-space: normal;
            overflow: hidden;
            opacity: 1;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.28);
            filter: none !important;
        }
        .viewer-dock__badge {
            position: absolute;
            top: -5px;
            right: -9px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            border-radius: 8px;
            background: #ef4444;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            text-shadow: none;
            box-shadow: none;
            pointer-events: none;
        }
        .viewer-dock__btn.is-active i.fa-heart {
            color: #f43f5e;
        }
        .viewer-dock__btn.is-active {
            opacity: 1;
        }
        #viewerContactWrap {
            pointer-events: auto;
            position: fixed;
            left: 16px;
            bottom: 16px;
            z-index: 10051;
            width: auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            overflow: visible;
        }
        .viewer-contact-fab {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: none;
            padding: 0;
            color: #fff;
            cursor: pointer;
            outline: none;
            overflow: visible;
        }
        .viewer-contact-fab__icon {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(18, 16, 14, 0.48);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow:
                0 10px 28px rgba(0, 0, 0, 0.32),
                inset 0 1px 0 rgba(255, 255, 255, 0.22);
            transition: transform 0.28s cubic-bezier(0.32, 0.72, 0, 1),
                        background 0.28s cubic-bezier(0.32, 0.72, 0, 1),
                        border-color 0.28s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .viewer-contact-fab__icon i {
            position: absolute;
            font-size: 17px;
            line-height: 1;
            transition: opacity 0.2s cubic-bezier(0.32, 0.72, 0, 1),
                        transform 0.28s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .viewer-contact-fab__close {
            opacity: 0;
            transform: rotate(-75deg) scale(0.6);
        }
        .viewer-contact.is-open .viewer-contact-fab__open {
            opacity: 0;
            transform: rotate(75deg) scale(0.6);
        }
        .viewer-contact.is-open .viewer-contact-fab__close {
            opacity: 1;
            transform: rotate(0) scale(1);
        }
        .viewer-contact.is-open .viewer-contact-fab__icon {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.38);
        }
        .viewer-contact-fab:hover .viewer-contact-fab__icon,
        .viewer-contact-fab:focus-visible .viewer-contact-fab__icon {
            transform: scale(1.04);
        }
        .viewer-contact-fab:active .viewer-contact-fab__icon {
            transform: scale(0.96);
        }
        .viewer-contact-bar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            overflow: visible;
        }
        .viewer-contact-tip {
            position: absolute;
            top: -42px;
            left: 4px;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: #ffffff;
            color: #1f2937;
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: -0.01em;
            white-space: nowrap;
            line-height: 1;
            border: 1px solid rgba(15, 23, 42, 0.06);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.16);
            pointer-events: none;
            opacity: 0;
            transform: translateY(4px) scale(0.96);
            animation: viewerContactTipPulse 14s ease-in-out infinite;
        }
        .viewer-contact-tip::after {
            content: '';
            position: absolute;
            left: 12px;
            bottom: -6px;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 7px solid #ffffff;
            filter: drop-shadow(0 2px 1px rgba(15, 23, 42, 0.08));
        }
        .viewer-contact.is-open .viewer-contact-tip {
            animation: none;
            opacity: 0;
        }
        /* Hiện nhanh ~2s rồi ẩn lâu ~12s */
        @keyframes viewerContactTipPulse {
            0%, 1% {
                opacity: 0;
                transform: translateY(6px) scale(0.96);
            }
            3%, 14% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            16%, 100% {
                opacity: 0;
                transform: translateY(4px) scale(0.96);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .viewer-contact-tip {
                animation: viewerContactTipPulse 20s ease-in-out infinite;
            }
        }
        .viewer-contact-sheet {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 188px;
            padding: 6px;
            border-radius: 22px;
            background: rgba(16, 14, 12, 0.62);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow:
                0 18px 40px rgba(0, 0, 0, 0.38),
                inset 0 1px 0 rgba(255, 255, 255, 0.14);
            transform-origin: bottom left;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(10px) scale(0.96);
            transition: opacity 0.28s cubic-bezier(0.32, 0.72, 0, 1),
                        transform 0.28s cubic-bezier(0.32, 0.72, 0, 1),
                        visibility 0.28s;
        }
        .viewer-contact.is-open .viewer-contact-sheet,
        .viewer-contact-sheet:not([hidden]) {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .viewer-contact-sheet[hidden] {
            display: flex !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        .viewer-contact-sheet__item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 7px 12px 7px 7px;
            border-radius: 16px;
            color: #fff !important;
            text-decoration: none !important;
            transition: background 0.22s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .viewer-contact-sheet__item:hover,
        .viewer-contact-sheet__item:focus-visible {
            background: rgba(255, 255, 255, 0.1);
            color: #fff !important;
        }
        .viewer-contact-sheet__icon {
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28);
        }
        .viewer-contact-sheet__item--call .viewer-contact-sheet__icon {
            background: linear-gradient(180deg, #4ade80, #16a34a);
        }
        .viewer-contact-sheet__item--zalo .viewer-contact-sheet__icon {
            background: linear-gradient(180deg, #3b8aff, #0057d9);
        }
        .viewer-contact-sheet__item--fb .viewer-contact-sheet__icon {
            background: linear-gradient(180deg, #4b92f7, #166fe5);
        }
        .viewer-contact-sheet__copy {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }
        .viewer-contact-sheet__name {
            font-size: 13px;
            font-weight: 650;
            letter-spacing: -0.01em;
            line-height: 1.2;
            color: #fff !important;
        }
        .viewer-contact-sheet__hint {
            font-size: 10px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.62) !important;
            line-height: 1.2;
        }
        /* Khi dải ảnh mở: ẩn hẳn thanh thao tác */
        body.viewer-photo-strip-open .viewer-dock,
        body.viewer-photo-strip-open #viewerContactWrap {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        body.viewer-photo-strip-open .viewer-photo-strip {
            bottom: 16px;
        }
        body.reviews-drawer-open .viewer-dock,
        body.reviews-drawer-open #viewerContactWrap,
        body.reviews-drawer-open .viewer-photo-strip {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Góc dưới trái: Toàn màn hình / Hướng dẫn / Ngừng quay */
        .viewer-tools {
            position: absolute;
            left: 50%;
            right: auto;
            bottom: 78px;
            transform: translateX(-50%);
            z-index: 10051;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 14px;
            pointer-events: none;
        }
        .viewer-tools__btn {
            pointer-events: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 5px;
            border: none;
            background: none;
            color: #fff;
            cursor: pointer;
            padding: 0;
            min-width: 56px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.28);
            transition: opacity 0.15s ease;
        }
        .viewer-tools__btn:hover,
        .viewer-tools__btn:focus {
            opacity: 0.88;
            outline: none;
        }
        .viewer-tools__btn > i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            font-size: 18px;
            line-height: 1;
            filter: drop-shadow(0 1px 0.5px rgba(0, 0, 0, 0.3));
        }
        .viewer-tools__label {
            font-size: 10px;
            font-weight: 500;
            line-height: 1.15;
            text-align: center;
            white-space: nowrap;
        }
        .viewer-tools__btn.is-active {
            opacity: 1;
        }
        body.viewer-photo-strip-open .viewer-tools,
        body.reviews-drawer-open .viewer-tools,
        body.viewer-guide-open .viewer-tools,
        body.viewer-guide-open .viewer-dock,
        body.viewer-guide-open #viewerContactWrap,
        body.viewer-guide-open .audio-player {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Overlay hướng dẫn sử dụng 360° */
        .viewer-guide {
            position: fixed;
            inset: 0;
            z-index: 10090;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background:
                radial-gradient(ellipse 80% 60% at 50% 0%, rgba(56, 120, 180, 0.35), transparent 55%),
                linear-gradient(165deg, #0f2744 0%, #1e3a5f 48%, #152a45 100%);
            color: #fff;
            cursor: pointer;
        }
        .viewer-guide.is-open {
            display: flex;
        }
        .viewer-guide__inner {
            position: relative;
            width: min(520px, 100%);
            max-height: 100%;
            overflow: auto;
            cursor: default;
            padding: 8px 4px 16px;
        }
        .viewer-guide__close {
            position: absolute;
            top: 0;
            right: 0;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            z-index: 2;
        }
        .viewer-guide__close:hover {
            background: rgba(255, 255, 255, 0.22);
        }
        .viewer-guide__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px 24px;
            padding: 36px 8px 4px;
        }
        .viewer-guide__row {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .viewer-guide__icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.25));
        }
        .viewer-guide__icon--hotspot {
            position: relative;
        }
        .viewer-guide__icon--hotspot::before {
            content: '';
            position: absolute;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.12) 45%, transparent 70%);
        }
        .viewer-guide__icon--hotspot::after {
            content: '';
            position: relative;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.35);
            z-index: 1;
        }
        .viewer-guide__text {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.2;
            opacity: 0.92;
        }
        .viewer-guide__hint {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            opacity: 0.55;
        }
        .viewer-guide__crosshair {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 18px;
            height: 18px;
            margin: -9px 0 0 -9px;
            opacity: 0.25;
            pointer-events: none;
        }
        .viewer-guide__crosshair::before,
        .viewer-guide__crosshair::after {
            content: '';
            position: absolute;
            background: #fff;
        }
        .viewer-guide__crosshair::before {
            left: 8px; top: 0; width: 2px; height: 18px;
        }
        .viewer-guide__crosshair::after {
            left: 0; top: 8px; width: 18px; height: 2px;
        }

        /* Horizontal photo strip (mở bằng nút Ảnh) */
        .viewer-photo-strip {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 72px;
            z-index: 10048;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 16px 4px;
            background: none;
            pointer-events: none;
            animation: viewerStripIn 0.22s ease;
            transition: bottom 0.22s ease;
        }
        .viewer-photo-strip[hidden] {
            display: none !important;
        }
        @keyframes viewerStripIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .viewer-photo-strip__inner {
            pointer-events: auto;
            position: relative;
            width: max-content;
            max-width: min(920px, 94vw);
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 10px;
        }
        .viewer-photo-strip__collapse {
            flex: 0 0 auto;
            order: 2;
            margin-top: 16px;
            align-self: flex-start;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .viewer-photo-strip__collapse:hover {
            background: rgba(30, 58, 95, 0.8);
        }
        .viewer-photo-strip__scroller {
            order: 1;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: nowrap;
            gap: 10px;
            max-width: min(860px, calc(94vw - 48px));
            overflow-x: auto;
            padding: 4px 0 6px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .viewer-photo-strip__scroller::-webkit-scrollbar { display: none; }
        .viewer-photo-strip__item {
            flex: 0 0 96px;
            width: 96px;
            height: 64px;
            border: none;
            background: #111;
            padding: 0;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-sizing: border-box;
            outline: 2px solid rgba(255,255,255,0.18);
            outline-offset: -2px;
            transition: transform 0.15s ease, outline-color 0.15s ease;
        }
        .viewer-photo-strip__item:hover {
            transform: translateY(-2px);
        }
        .viewer-photo-strip__item img {
            width: 96px !important;
            height: 64px !important;
            object-fit: cover !important;
            object-position: center;
            display: block;
            border: none;
            pointer-events: none;
        }
        .viewer-photo-strip__item.is-active {
            outline-color: #fff;
        }
        .viewer-photo-strip__check {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            font-size: 9px;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
        .viewer-photo-strip__item.is-active .viewer-photo-strip__check {
            display: flex;
        }
        .viewer-photo-strip__caption {
            display: none !important;
        }

        /* Photo peek overlay — nền blur từ chính ảnh, ảnh nét ở giữa */
        .pano-photo-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10055;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 48px 24px 120px;
            border: 0;
            box-sizing: border-box;
            background: #0a0a0a;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .pano-photo-overlay[hidden] {
            display: none !important;
        }
        .pano-photo-overlay__blur {
            position: absolute;
            inset: -32px;
            width: calc(100% + 64px);
            height: calc(100% + 64px);
            object-fit: cover;
            object-position: center;
            filter: blur(32px) saturate(1.05) brightness(0.7);
            transform: scale(1.1);
            pointer-events: none;
            user-select: none;
            -webkit-user-drag: none;
            z-index: 0;
        }
        .pano-photo-overlay__img {
            position: relative;
            z-index: 1;
            max-width: min(90%, 960px);
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            user-select: none;
            -webkit-user-drag: none;
        }
        .pano-photo-overlay .photo-viewer-nav {
            position: fixed;
            z-index: 10059;
        }
        .pano-photo-overlay__close {
            position: fixed;
            top: 18px;
            right: 58px; /* tránh đè lên nút ··· */
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.55);
            color: #fff;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            z-index: 10059;
        }
        .pano-photo-overlay__close:hover {
            background: rgba(30, 58, 95, 0.85);
        }
        body.pano-photo-open .viewer-dock { z-index: 10058; }
        body.pano-photo-open .viewer-photo-strip { z-index: 10057; }
        body.pano-photo-open .audio-player { z-index: 10057; }
        body.pano-photo-open .viewer-more-menu { z-index: 10058; }

        @media (max-width: 640px) {
            .viewer-dock {
                gap: 2px;
                padding: 18px 8px 10px;
            }
            .viewer-dock__btn {
                width: 56px;
                min-width: 56px;
                max-width: 56px;
                flex-basis: 56px;
            }
            .viewer-dock__label { font-size: 10px; }
            #viewerContactWrap {
                left: 12px;
                bottom: 12px;
            }
            .viewer-contact-fab__icon {
                width: 44px;
                height: 44px;
            }
            .viewer-contact-sheet {
                min-width: 176px;
            }
            .viewer-tools {
                left: 50%;
                bottom: 72px;
                gap: 10px;
            }
            .viewer-tools__btn { min-width: 48px; }
            .viewer-tools__btn > i { font-size: 16px; }
            .viewer-tools__label { font-size: 9px; }
            .viewer-guide__grid {
                grid-template-columns: 1fr;
                gap: 22px;
                padding-top: 40px;
            }
            .viewer-guide__title { font-size: 12px; }
            .viewer-guide__text { font-size: 13px; }
            .viewer-photo-strip { bottom: 16px; padding: 8px 10px; }
            .viewer-photo-strip__item {
                flex-basis: 80px;
                width: 80px;
                height: 54px;
            }
            .viewer-photo-strip__item img {
                width: 80px !important;
                height: 54px !important;
            }
            .pano-photo-overlay {
                padding: 56px 12px 108px;
            }
            .pano-photo-overlay__close {
                right: 52px;
                top: 64px;
            }
        }
    </style>
</head>

@php
    $scenes = $location->panoramas()->orderByDesc('is_default')->orderBy('sort_order')->get();
    
    $appData = [
        'name' => $location->name,
        'settings' => [
            'mouseViewMode' => 'drag',
            'autorotateEnabled' => true,
            'fullscreenButton' => true,
            'viewControlButtons' => true
        ],
        'scenes' => $scenes->map(function($p) {
            return [
                'id' => (string)$p->id,
                'name' => $p->scene_name,
                'url' => asset('storage/' . ltrim($p->image_url, '/')),
                'initialViewParameters' => [
                    'yaw' => $p->initial_yaw * pi() / 180,
                    'pitch' => $p->initial_pitch * pi() / 180,
                    'fov' => $p->initial_fov ? ($p->initial_fov * pi() / 180) : 1.5707963267948966
                ],
                'linkHotspots' => $p->hotspots->where('hotspot_type', 'link')->map(function($h) {
                    return [
                        'id' => $h->id,
                        'yaw' => $h->yaw * pi() / 180,
                        'pitch' => $h->pitch * pi() / 180,
                        'rotation' => 0,
                        'target' => (string)$h->target_panorama_id,
                        'target_yaw' => $h->target_yaw !== null ? $h->target_yaw * pi() / 180 : null,
                        'target_pitch' => $h->target_pitch !== null ? $h->target_pitch * pi() / 180 : null,
                        'scale' => $h->scale ?? 1.0
                    ];
                })->values(),
                'infoHotspots' => $p->hotspots->where('hotspot_type', 'info')->map(function($h) {
                    return [
                        'id' => $h->id,
                        'yaw' => $h->yaw * pi() / 180,
                        'pitch' => $h->pitch * pi() / 180,
                        'title' => $h->title,
                        'text' => $h->content,
                        'scale' => $h->scale ?? 1.0
                    ];
                })->values()
            ];
        })->values()
    ];

    $photoMode = $scenes->isEmpty();
    $photoSlides = [];
    $hero = $heroImage ?? $location->resolveThumbnailUrl();
    $gallery = $galleryImages ?? $location->resolveImageUrls();
    $seen = [];
    if ($hero) {
        $photoSlides[] = ['url' => $hero, 'caption' => $location->name];
        $seen[$hero] = true;
    }
    foreach ($gallery as $img) {
        $url = $img['url'] ?? null;
        if ($url && !isset($seen[$url])) {
            $photoSlides[] = ['url' => $url, 'caption' => $img['caption'] ?? $location->name];
            $seen[$url] = true;
        }
    }
    $hasNarrationAudio = filled($location->audio_url);
    $hasNarrationText = filled(data_get($location->attributes, 'tts_text'));
    $hasNarration = $hasNarrationAudio || $hasNarrationText;
    $audioNarrationText = data_get($location->attributes, 'tts_text') ?: 'Chưa có nội dung thuyết minh.';
@endphp

<body>

    <a href="{{ route('home') }}" class="btn-back-map">
        <i class="fa-solid fa-arrow-left"></i> Quay lại Bản đồ
    </a>

    <div class="viewer-more-menu" id="viewerMoreMenu">
        <button type="button" class="viewer-more-btn" id="viewerMoreBtn" aria-label="Tùy chọn" onclick="toggleViewerMoreMenu(event)">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
        <div class="viewer-more-dropdown" id="viewerMoreDropdown">
            <button type="button" class="viewer-more-item" onclick="shareLocation()">Chia sẻ</button>
            <button type="button" class="viewer-more-item" onclick="openReportModal({{ $location->id }}, 'Location'); closeViewerMoreMenu();">Báo cáo</button>
            <button type="button" class="viewer-more-item" onclick="openFeedbackModal({{ $location->id }}); closeViewerMoreMenu();">Góp ý / báo lỗi</button>
        </div>
    </div>

    <!-- Bottom dock: Lưu / Bình luận / Ảnh / Âm thanh -->
    @include('client.partials.location-viewer-dock', [
        'location' => $location,
        'photoSlides' => $photoSlides,
        'photoMode' => $photoMode,
        'hasNarration' => $hasNarration,
    ])

    @if(!$photoMode)
    <div class="viewer-guide" id="viewerGuideOverlay" role="dialog" aria-modal="true" aria-label="Hướng dẫn sử dụng" hidden>
        <div class="viewer-guide__crosshair" aria-hidden="true"></div>
        <div class="viewer-guide__inner" id="viewerGuideInner">
            <button type="button" class="viewer-guide__close" id="btnCloseViewerGuide" aria-label="Đóng hướng dẫn">×</button>
            <div class="viewer-guide__grid">
                <div class="viewer-guide__row">
                    <div class="viewer-guide__icon"><i class="fa-solid fa-hand-pointer"></i></div>
                    <div class="viewer-guide__text">Thu phóng</div>
                </div>
                <div class="viewer-guide__row">
                    <div class="viewer-guide__icon"><i class="fa-solid fa-up-down-left-right"></i></div>
                    <div class="viewer-guide__text">Kéo xem</div>
                </div>
                <div class="viewer-guide__row">
                    <div class="viewer-guide__icon"><i class="fa-solid fa-computer-mouse"></i></div>
                    <div class="viewer-guide__text">Cuộn chuột</div>
                </div>
                <div class="viewer-guide__row">
                    <div class="viewer-guide__icon viewer-guide__icon--hotspot" aria-hidden="true"></div>
                    <div class="viewer-guide__text">Đổi chỗ</div>
                </div>
            </div>
            <p class="viewer-guide__hint">Chạm để đóng</p>
        </div>
    </div>
    @endif

    <!-- Comments Drawer -->
    <div class="comments-drawer" id="commentsDrawer">
        <div class="drawer-header">
            <h3><i class="fa-regular fa-star"></i> Đánh giá & Bình luận</h3>
            <button class="btn-close-drawer" id="btnCloseComments"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="comments-list" id="commentsList">
            @php
                $totalComments = $location->comments->count();
                $avgRating = $totalComments > 0 ? round($location->comments->avg('rating'), 1) : 5.0;
                if ($avgRating <= 0) $avgRating = 5.0;
            @endphp

            <!-- Rating Overview Header Card -->
            <div class="rating-overview-card">
                <div class="rating-score-box">
                    <div class="rating-score-num">{{ number_format($avgRating, 1) }}</div>
                    <div class="rating-score-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= round($avgRating) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <div class="rating-score-count">{{ $totalComments }} đánh giá</div>
                </div>
                <div class="rating-bars-box">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $starCount = $location->comments->where('rating', $star)->count();
                            $percent = $totalComments > 0 ? ($starCount / $totalComments) * 100 : 0;
                        @endphp
                        <div class="rating-bar-row">
                            <span class="star-num">{{ $star }} <i class="fa-solid fa-star"></i></span>
                            <div class="rating-bar-bg">
                                <div class="rating-bar-fill" style="width: {{ $percent }}%;"></div>
                            </div>
                            <span class="star-count">{{ $starCount }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Search & Sort Filter Bar -->
            <div class="gmaps-filter-bar">
                <div class="gmaps-search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" id="reviewSearchInput" placeholder="Tìm bài đánh giá..." onkeyup="filterAndSortReviews()">
                    <button class="btn-clear-search" id="btnClearSearch" onclick="clearReviewSearch()" style="display:none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="gmaps-sort-box">
                    <select id="reviewSortSelect" onchange="filterAndSortReviews()">
                        <option value="relevant">Phù hợp nhất</option>
                        <option value="newest">Mới nhất</option>
                        <option value="highest">Xếp hạng cao nhất</option>
                        <option value="lowest">Xếp hạng thấp nhất</option>
                    </select>
                    <i class="fa-solid fa-caret-down select-caret"></i>
                </div>
            </div>

            <!-- Comment Items Container -->
            <div id="commentsItemsWrapper">
                @forelse($location->comments as $comment)
                    @php
                        $userCommentsCount = optional($comment->user)->comments_count_cached
                            ?? (optional($comment->user)->comments ? $comment->user->comments->count() : 1);
                        $content = $comment->content;
                        $isLong = mb_strlen($content) > 150;
                    @endphp
                    <div class="gmaps-review-card" id="comment-{{ $comment->id }}" data-rating="{{ $comment->rating ?? 5 }}" data-timestamp="{{ $comment->created_at->timestamp }}">
                        <!-- Header -->
                        <div class="gmaps-review-header">
                            <div class="gmaps-user-block">
                                <x-user-avatar :user="$comment->user" size="42" />
                                <div>
                                    <div class="gmaps-username">{{ optional($comment->user)->display_name ?? optional($comment->user)->username ?? 'Thành viên' }}</div>
                                    <div class="gmaps-user-subtitle">Thành viên · {{ $userCommentsCount }} đánh giá</div>
                                </div>
                            </div>
                            
                            <!-- 3-Dots Menu -->
                            <div class="gmaps-more-wrapper">
                                <button class="gmaps-btn-more" onclick="toggleGmapsMenu(event, {{ $comment->id }})" title="Tùy chọn">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="gmaps-dropdown-menu" id="gmapsMenu-{{ $comment->id }}">
                                    @if(Auth::check() && Auth::id() === $comment->user_id)
                                        <div class="gmaps-dropdown-item" onclick="event.stopPropagation(); openEditReviewModal({{ $comment->id }}, {{ $comment->rating }}, {{ json_encode($comment->content) }})">
                                            Chỉnh sửa
                                        </div>
                                        <div class="gmaps-dropdown-item text-danger" onclick="event.stopPropagation(); deleteUserComment({{ $comment->id }})">
                                            Xóa
                                        </div>
                                    @else
                                        <div class="gmaps-dropdown-item" onclick="shareCommentLink(event, {{ $comment->id }})">
                                            Chia sẻ
                                        </div>
                                        <div class="gmaps-dropdown-item text-danger" onclick="openReportModal({{ $comment->id }}, 'Comment')">
                                            Báo cáo
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Rating Stars & Date -->
                        <div class="gmaps-rating-row">
                            @if($comment->rating && $comment->rating > 0)
                                <div class="gmaps-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $comment->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            @endif
                            <span class="gmaps-date">Thời gian: {{ $comment->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Body -->
                        <div class="gmaps-review-body">
                            @if($isLong)
                                <span class="gmaps-text-short">{{ mb_substr($content, 0, 150) }}...</span>
                                <span class="gmaps-text-full" style="display: none;">{{ $content }}</span>
                                <button class="gmaps-btn-see-more" onclick="toggleGmapsSeeMore(this)">Xem thêm</button>
                            @else
                                <span>{{ $content }}</span>
                            @endif
                        </div>

                        @php
                            $ownerReply = $comment->replies->first(function ($reply) use ($location) {
                                return (int) $reply->user_id === (int) $location->created_by;
                            }) ?? $comment->replies->first();
                        @endphp
                        @if($ownerReply)
                            <div class="gmaps-owner-reply">
                                <div class="gmaps-owner-reply__label">Phản hồi từ chủ địa điểm</div>
                                <p class="gmaps-owner-reply__text">{{ $ownerReply->content }}</p>
                            </div>
                        @endif

                        <!-- Footer Actions -->
                        <div class="gmaps-review-footer">
                            <button class="gmaps-action-btn btn-like-gmaps" onclick="toggleLikeGmaps(this, {{ $comment->id }})">
                                <i class="fa-regular fa-thumbs-up"></i>
                                <span class="like-label">Thích</span>
                            </button>
                            <button class="gmaps-action-btn" onclick="shareCommentLink(event, {{ $comment->id }})">
                                <i class="fa-solid fa-share-nodes"></i>
                                <span>Chia sẻ</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center" style="color: #6482a6; padding: 20px;" id="noCommentsMsg">
                        Chưa có đánh giá nào. Hãy là người đầu tiên!
                    </div>
                @endforelse
            </div>
        </div>
        @php
            $userComment = Auth::check() ? $location->comments->where('user_id', Auth::id())->first() : null;
            $hasReviewed = !is_null($userComment);
        @endphp

        <div class="comment-form" id="commentFormContainer">
            @if($hasReviewed)
                <div class="d-flex align-items-center justify-content-between py-1">
                    <span style="color: #1e3a5f; font-size: 12.5px; font-weight: 500;">
                        Bạn đã gửi đánh giá địa điểm này
                    </span>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="gmaps-stars" style="font-size: 13px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $userComment->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </span>
                        <button class="btn-goto-my-review" onclick="scrollToMyReview({{ $userComment->id }})">Xem</button>
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span style="color: #3b5980; font-size: 12.5px; font-weight: 500;">Viết đánh giá: </span>
                        <span id="ratingLabelHint" class="rating-label-hint" style="margin-left: 4px; font-size: 12px; color: #6482a6;"></span>
                    </div>
                    <div class="star-rating-picker" id="starRatingPicker">
                        <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="3"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Write Review Modal (Google Maps Light Theme) -->
    <div class="write-review-overlay" id="writeReviewModalOverlay">
        <div class="write-review-modal">
            <input type="hidden" id="editingCommentId" value="">
            <!-- Modal Header -->
            <div class="write-review-header">
                <h3 class="location-name-title" id="modalWriteReviewTitle">{{ $location->name }}</h3>
                <button class="btn-close-write-modal" onclick="closeWriteReviewModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="write-review-body">
                <!-- Red Box 1: User Info & Stars -->
                <div class="review-user-box">
                    @auth
                        <x-user-avatar :user="Auth::user()" size="40" />
                        <div>
                            <div class="user-display-name">{{ Auth::user()->display_name ?? Auth::user()->username }}</div>
                            <div class="user-privacy-tag">Đăng công khai trên hệ thống <i class="fa-solid fa-circle-info"></i></div>
                        </div>
                    @else
                        <div class="user-display-name">Khách hàng</div>
                    @endauth
                </div>

                <div class="review-modal-stars-wrapper">
                    <div class="star-rating-picker modal-stars-picker" id="modalStarRatingPicker">
                        <svg class="sharp-star-btn modal-star-btn empty" viewBox="0 0 24 24" data-value="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn modal-star-btn empty" viewBox="0 0 24 24" data-value="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn modal-star-btn empty" viewBox="0 0 24 24" data-value="3"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn modal-star-btn empty" viewBox="0 0 24 24" data-value="4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                        <svg class="sharp-star-btn modal-star-btn empty" viewBox="0 0 24 24" data-value="5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                    </div>
                    <span id="modalRatingLabelHint" class="modal-rating-hint"></span>
                    <input type="hidden" id="modalSelectedRating" value="5">
                </div>

                <!-- Red Box 2: Textarea & Camera Button -->
                <div class="review-input-box">
                    <textarea id="modalCommentContent" rows="4" placeholder="Mô tả cụ thể trải nghiệm của bạn tại địa điểm này..."></textarea>
                    
                    <div class="review-media-upload-area">
                        <button type="button" class="btn-add-media-dummy" onclick="document.getElementById('commentPhotoInput').click()">
                            <i class="fa-solid fa-camera"></i> Thêm ảnh và video
                        </button>
                        <input type="file" id="commentPhotoInput" accept="image/*" style="display:none;" onchange="handlePhotoSelected(this)">
                    </div>
                    <div id="selectedPhotoPreviewName" style="font-size: 11.5px; color: #1e3a5f; display:none; margin-top: 4px;"></div>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="write-review-footer">
                <button class="btn-review-cancel" onclick="closeWriteReviewModal()">Hủy</button>
                <button class="btn-review-submit" id="btnModalSubmitReview" onclick="submitModalReview()">Đăng</button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="report-modal-overlay" id="reportModalOverlay">
        <div class="report-modal">
            <h4><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo vi phạm</h4>
            <label>Lý do báo cáo</label>
            <select id="reportReason">
                <option value="Nội dung rác, quảng cáo">Nội dung rác, quảng cáo</option>
                <option value="Thông tin sai sự thật">Thông tin sai sự thật</option>
                <option value="Ngôn từ kích động, thù địch">Ngôn từ kích động, thù địch</option>
                <option value="Hình ảnh phản cảm">Hình ảnh phản cảm</option>
                <option value="Lừa đảo">Lừa đảo</option>
                <option value="Khác">Lý do khác...</option>
            </select>
            <label>Mô tả chi tiết (Tùy chọn)</label>
            <textarea id="reportDescription" rows="3" placeholder="Nhập thêm thông tin để quản trị viên dễ dàng xử lý..."></textarea>
            
            <div class="report-modal-actions">
                <button class="btn-report-cancel" onclick="closeReportModal()">Hủy</button>
                <button class="btn-report-submit" id="btnSubmitReport" onclick="submitReport()">Gửi báo cáo</button>
            </div>
        </div>
    </div>

    <!-- Modal Góp ý / Báo lỗi -->
    <div class="report-modal-overlay" id="feedbackModalOverlay">
        <div class="report-modal">
            <h4><i class="fa-solid fa-comment-dots"></i> Góp ý / Báo lỗi</h4>
            <label>Loại góp ý / lỗi</label>
            <select id="feedbackType">
                <option value="wrong_info">Thông tin địa điểm sai</option>
                <option value="wrong_position">Vị trí trên bản đồ sai</option>
                <option value="image_error">Ảnh bị lỗi / không hiển thị</option>
                <option value="location_closed">Địa điểm đã đóng cửa / không còn tồn tại</option>
                <option value="duplicate_location">Địa điểm bị trùng lặp</option>
                <option value="system_suggestion">Góp ý cải thiện hệ thống</option>
                <option value="other">Khác</option>
            </select>
            <label>Nội dung chi tiết</label>
            <textarea id="feedbackContent" rows="4" placeholder="Mô tả cụ thể vấn đề hoặc góp ý của bạn để chúng tôi xử lý nhanh hơn..."></textarea>

            <div class="report-modal-actions">
                <button class="btn-report-cancel" onclick="closeFeedbackModal()">Hủy</button>
                <button class="btn-report-submit" id="btnSubmitFeedback" onclick="submitFeedback()" style="background:#2563eb;box-shadow:0 2px 6px rgba(37,99,235,0.2);">Gửi góp ý</button>
            </div>
        </div>
    </div>

    <div class="viewer-area">
        @if($photoMode)
            @include('client.partials.location-photo-viewer', [
                'location' => $location,
                'photoSlides' => $photoSlides,
            ])
        @else
        <div id="pano"></div>

        <div id="titleBar">
            <h1 class="sceneName"></h1>
        </div>

        <a href="javascript:void(0)" id="autorotateToggle">
            <img class="icon off" src="{{ asset('marzipano/img/play.png') }}">
            <img class="icon on" src="{{ asset('marzipano/img/pause.png') }}">
        </a>

        <a href="javascript:void(0)" id="fullscreenToggle">
            <img class="icon off" src="{{ asset('marzipano/img/fullscreen.png') }}">
            <img class="icon on" src="{{ asset('marzipano/img/windowed.png') }}">
        </a>

        <a href="javascript:void(0)" id="sceneListToggle">
            <img class="icon off" src="{{ asset('marzipano/img/expand.png') }}">
            <img class="icon on" src="{{ asset('marzipano/img/collapse.png') }}">
        </a>

        <div id="sceneList">
            <ul class="scenes">
                @foreach($scenes as $scene)
                    <a href="javascript:void(0)" class="scene" data-id="{{ $scene->id }}">
                        <li class="text">{{ $scene->scene_name }}</li>
                    </a>
                @endforeach
            </ul>
        </div>

        <a href="javascript:void(0)" id="viewUp" class="viewControlButton viewControlButton-1">
            <img class="icon" src="{{ asset('marzipano/img/up.png') }}">
        </a>
        <a href="javascript:void(0)" id="viewDown" class="viewControlButton viewControlButton-2">
            <img class="icon" src="{{ asset('marzipano/img/down.png') }}">
        </a>
        <a href="javascript:void(0)" id="viewLeft" class="viewControlButton viewControlButton-3">
            <img class="icon" src="{{ asset('marzipano/img/left.png') }}">
        </a>
        <a href="javascript:void(0)" id="viewRight" class="viewControlButton viewControlButton-4">
            <img class="icon" src="{{ asset('marzipano/img/right.png') }}">
        </a>
        <a href="javascript:void(0)" id="viewIn" class="viewControlButton viewControlButton-5">
            <img class="icon" src="{{ asset('marzipano/img/plus.png') }}">
        </a>
        <a href="javascript:void(0)" id="viewOut" class="viewControlButton viewControlButton-6">
            <img class="icon" src="{{ asset('marzipano/img/minus.png') }}">
        </a>
        @endif

        @if($hasNarration)
        <!-- Audio Mascot Player — góc dưới bên phải -->
        <div class="audio-player visible" id="audioPlayer">
            <div class="audio-info-popover" id="audioInfoPopover">
                <div class="audio-popover-header">
                    <span>{{ $location->name }}</span>
                    <button class="btn-close-popover" onclick="toggleAudioInfoPopover(event)"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="audio-popover-body">
                    {!! nl2br(e($audioNarrationText)) !!}
                </div>
            </div>

            <div class="audio-mascot-wrapper">
                <button class="audio-info-btn" id="btnAudioInfo" onclick="toggleAudioInfoPopover(event)" title="Xem nội dung thuyết minh">
                    i
                </button>
                <img src="{{ asset('images/loax.png') }}"
                     alt="Thuyết minh"
                     class="audio-mascot-btn"
                     id="audioMascotImg"
                     onclick="toggleAudio()"
                     title="Nhấp để Bật/Tắt thuyết minh">
            </div>
            <div class="audio-progress-bar" id="audioProgressBar" onclick="seekAudio(event)" title="Thanh tiến trình thuyết minh">
                <div class="audio-progress-fill" id="audioProgressFill"></div>
            </div>
        </div>
        @endif
    </div>

    @if(!$photoMode)
    <!-- Setup APP_DATA -->
    <script>
        window.isEditorMode = false; // Disable editing mode!
        window.APP_DATA = {!! json_encode($appData) !!};
    </script>

    <script src="{{ asset('marzipano/vendor/screenfull.min.js') }}" ></script>
    <script src="{{ asset('marzipano/vendor/bowser.min.js') }}" ></script>
    <script src="{{ asset('marzipano/vendor/marzipano.js') }}" ></script>
    <!-- Use Marzipano's Original Script -->
    <script src="{{ asset('marzipano/index.js') }}"></script>

    <script>
    (function () {
        const btnFs = document.getElementById('btnViewerFullscreen');
        const btnGuide = document.getElementById('btnViewerGuide');
        const btnRotate = document.getElementById('btnViewerAutorotate');
        const iconFs = document.getElementById('iconViewerFullscreen');
        const labelFs = document.getElementById('labelViewerFullscreen');
        const iconRotate = document.getElementById('iconViewerAutorotate');
        const labelRotate = document.getElementById('labelViewerAutorotate');
        const guide = document.getElementById('viewerGuideOverlay');
        const btnCloseGuide = document.getElementById('btnCloseViewerGuide');
        const nativeFs = document.getElementById('fullscreenToggle');
        const nativeRotate = document.getElementById('autorotateToggle');

        function syncFullscreenUi() {
            const on = !!(nativeFs && nativeFs.classList.contains('enabled'))
                || !!(window.screenfull && screenfull.isFullscreen);
            if (iconFs) {
                iconFs.className = on ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
            }
            if (labelFs) {
                labelFs.textContent = on ? 'Thu nhỏ' : 'Toàn màn';
            }
            if (btnFs) {
                btnFs.title = on ? 'Thu nhỏ' : 'Toàn màn';
                btnFs.classList.toggle('is-active', on);
            }
        }

        function syncAutorotateUi() {
            const rotating = !!(nativeRotate && nativeRotate.classList.contains('enabled'));
            if (iconRotate) {
                iconRotate.className = rotating ? 'fa-solid fa-pause' : 'fa-solid fa-rotate';
            }
            if (labelRotate) {
                labelRotate.textContent = rotating ? 'Ngừng quay' : 'Tự quay';
            }
            if (btnRotate) {
                btnRotate.title = rotating ? 'Ngừng quay' : 'Bật tự quay';
                btnRotate.classList.toggle('is-active', rotating);
            }
        }

        function openGuide() {
            if (!guide) return;
            guide.hidden = false;
            guide.classList.add('is-open');
            document.body.classList.add('viewer-guide-open');
        }

        function closeGuide() {
            if (!guide) return;
            guide.classList.remove('is-open');
            guide.hidden = true;
            document.body.classList.remove('viewer-guide-open');
        }

        if (btnFs) {
            btnFs.addEventListener('click', function (e) {
                e.preventDefault();
                if (nativeFs) {
                    nativeFs.click();
                } else if (window.screenfull && screenfull.enabled) {
                    screenfull.toggle();
                }
                setTimeout(syncFullscreenUi, 50);
            });
        }

        if (btnRotate) {
            btnRotate.addEventListener('click', function (e) {
                e.preventDefault();
                if (nativeRotate) nativeRotate.click();
                setTimeout(syncAutorotateUi, 30);
            });
        }

        if (btnGuide) {
            btnGuide.addEventListener('click', function (e) {
                e.preventDefault();
                openGuide();
            });
        }

        if (btnCloseGuide) {
            btnCloseGuide.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeGuide();
            });
        }

        if (guide) {
            guide.addEventListener('click', function () {
                closeGuide();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && guide && guide.classList.contains('is-open')) {
                closeGuide();
            }
        });

        if (window.screenfull && screenfull.enabled) {
            screenfull.on('change', syncFullscreenUi);
        }

        if (nativeFs) {
            new MutationObserver(syncFullscreenUi).observe(nativeFs, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
        if (nativeRotate) {
            new MutationObserver(syncAutorotateUi).observe(nativeRotate, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Marzipano gắn class sau khi load
        setTimeout(function () {
            syncFullscreenUi();
            syncAutorotateUi();
        }, 100);
    })();
    </script>
    @endif

    @if($hasNarration)
    <!-- Audio Player & Autoplay Logic -->
    <script>
    (function() {
        const mascotImg = document.getElementById('audioMascotImg');
        const progressFill = document.getElementById('audioProgressFill');

        const loaOnSrc = @json(asset('images/loa.png'));
        const loaOffSrc = @json(asset('images/loax.png'));
        const audioUrl = @json($location->audio_url ? asset('storage/' . $location->audio_url) : null);
        const narrationText = @json(trim(strip_tags((string) data_get($location->attributes, 'tts_text', ''))));

        let audioEl = null;
        let isTtsMode = false;
        let ttsUtterance = null;
        let isPlaying = false;

        function setAudioUiPlaying(playing) {
            if (mascotImg) mascotImg.src = playing ? loaOnSrc : loaOffSrc;
        }

        if (audioUrl) {
            audioEl = new Audio(audioUrl);
            audioEl.addEventListener('timeupdate', function() {
                if (audioEl.duration && progressFill) {
                    progressFill.style.width = (audioEl.currentTime / audioEl.duration * 100) + '%';
                }
            });
            audioEl.addEventListener('ended', function() {
                isPlaying = false;
                setAudioUiPlaying(false);
                if (progressFill) progressFill.style.width = '0%';
            });
            audioEl.addEventListener('play', function() {
                isPlaying = true;
                setAudioUiPlaying(true);
            });
            audioEl.addEventListener('pause', function() {
                isPlaying = false;
                setAudioUiPlaying(false);
            });
        } else if ('speechSynthesis' in window && narrationText) {
            isTtsMode = true;
        }

        function playNarration() {
            if (audioEl) {
                audioEl.play().then(() => {
                    isPlaying = true;
                    setAudioUiPlaying(true);
                }).catch(err => {
                    console.log('Autoplay blocked by browser, waiting for user click:', err);
                    setupFirstClickAutoplay();
                });
            } else if (isTtsMode) {
                window.speechSynthesis.cancel();
                ttsUtterance = new SpeechSynthesisUtterance(narrationText);
                ttsUtterance.lang = 'vi-VN';
                ttsUtterance.rate = 1.0;
                
                ttsUtterance.onstart = function() {
                    isPlaying = true;
                    setAudioUiPlaying(true);
                };
                ttsUtterance.onend = function() {
                    isPlaying = false;
                    setAudioUiPlaying(false);
                };
                ttsUtterance.onerror = function() {
                    isPlaying = false;
                    setAudioUiPlaying(false);
                };

                window.speechSynthesis.speak(ttsUtterance);
            }
        }

        function stopNarration() {
            if (audioEl) {
                audioEl.pause();
            } else if (isTtsMode) {
                window.speechSynthesis.cancel();
            }
            isPlaying = false;
            setAudioUiPlaying(false);
        }

        function setupFirstClickAutoplay() {
            const startOnInteraction = function() {
                if (!isPlaying) {
                    playNarration();
                }
                document.removeEventListener('click', startOnInteraction);
                document.removeEventListener('touchstart', startOnInteraction);
                document.removeEventListener('pointerdown', startOnInteraction);
            };
            document.addEventListener('click', startOnInteraction, { once: true });
            document.addEventListener('touchstart', startOnInteraction, { once: true });
            document.addEventListener('pointerdown', startOnInteraction, { once: true });
        }

        window.toggleAudio = function() {
            if (isPlaying) {
                stopNarration();
            } else {
                playNarration();
            }
        };

        window.seekAudio = function(e) {
            if (audioEl && audioEl.duration) {
                let rect = e.currentTarget.getBoundingClientRect();
                let ratio = (e.clientX - rect.left) / rect.width;
                audioEl.currentTime = ratio * audioEl.duration;
            }
        };

        window.toggleAudioInfoPopover = function(e) {
            if (e) e.stopPropagation();
            const popover = document.getElementById('audioInfoPopover');
            if (popover) {
                popover.classList.toggle('active');
            }
        };

        document.addEventListener('click', function(e) {
            const popover = document.getElementById('audioInfoPopover');
            const btn = document.getElementById('btnAudioInfo');
            if (popover && popover.classList.contains('active')) {
                if (!popover.contains(e.target) && (!btn || !btn.contains(e.target))) {
                    popover.classList.remove('active');
                }
            }
        });

        // Autoplay after 1 second delay on load
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(playNarration, 1000);
        } else {
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(playNarration, 1000);
            });
        }
    })();
    </script>
    @endif

    <!-- Review Search & Sort JS Helpers -->
    <script>
        // Filter and Sort Reviews Logic
        window.filterAndSortReviews = function() {
            const searchInput = document.getElementById('reviewSearchInput');
            const clearBtn = document.getElementById('btnClearSearch');
            const sortSelect = document.getElementById('reviewSortSelect');
            const wrapper = document.getElementById('commentsItemsWrapper');
            if (!wrapper) return;

            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            if (clearBtn) {
                clearBtn.style.display = query ? 'block' : 'none';
            }

            const sortMode = sortSelect ? sortSelect.value : 'relevant';
            const cards = Array.from(wrapper.querySelectorAll('.gmaps-review-card'));
            let visibleCount = 0;

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const matches = !query || text.includes(query);
                if (matches) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            cards.sort((a, b) => {
                const ratingA = parseInt(a.dataset.rating || 5);
                const ratingB = parseInt(b.dataset.rating || 5);
                const timeA = parseInt(a.dataset.timestamp || 0);
                const timeB = parseInt(b.dataset.timestamp || 0);

                if (sortMode === 'newest') {
                    return timeB - timeA;
                } else if (sortMode === 'highest') {
                    return ratingB - ratingA || timeB - timeA;
                } else if (sortMode === 'lowest') {
                    return ratingA - ratingB || timeA - timeB;
                } else {
                    return timeB - timeA;
                }
            });

            cards.forEach(card => wrapper.appendChild(card));

            let noResultsEl = document.getElementById('noSearchMatchesMsg');
            if (visibleCount === 0 && query) {
                if (!noResultsEl) {
                    noResultsEl = document.createElement('div');
                    noResultsEl.id = 'noSearchMatchesMsg';
                    noResultsEl.className = 'text-center py-3';
                    noResultsEl.style.color = '#6482a6';
                    noResultsEl.style.fontSize = '13px';
                    noResultsEl.innerText = 'Không tìm thấy bài đánh giá phù hợp';
                    wrapper.appendChild(noResultsEl);
                } else {
                    noResultsEl.style.display = 'block';
                }
            } else if (noResultsEl) {
                noResultsEl.style.display = 'none';
            }
        };

        window.clearReviewSearch = function() {
            const searchInput = document.getElementById('reviewSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterAndSortReviews();
            }
        };

        // Google Maps Review Card Helpers
        window.closeAllGmapsMenus = function() {
            document.querySelectorAll('.gmaps-dropdown-menu').forEach(m => m.classList.remove('show'));
        };

        window.toggleGmapsMenu = function(e, id) {
            if (e) e.stopPropagation();
            const targetMenu = document.getElementById(`gmapsMenu-${id}`);
            document.querySelectorAll('.gmaps-dropdown-menu').forEach(m => {
                if (m !== targetMenu) m.classList.remove('show');
            });
            if (targetMenu) targetMenu.classList.toggle('show');
        };

        window.toggleGmapsSeeMore = function(btn) {
            const body = btn.closest('.gmaps-review-body');
            if (body) {
                const shortSpan = body.querySelector('.gmaps-text-short');
                const fullSpan = body.querySelector('.gmaps-text-full');
                if (shortSpan) shortSpan.style.display = 'none';
                if (fullSpan) fullSpan.style.display = 'inline';
                btn.style.display = 'none';
            }
        };

        window.shareCommentLink = function(e, id) {
            if (e) e.stopPropagation();
            document.querySelectorAll('.gmaps-dropdown-menu').forEach(m => m.classList.remove('show'));
            const url = window.location.origin + window.location.pathname + '#comment-' + id;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    if (typeof showToast360 === 'function') showToast360('Đã sao chép liên kết bài đánh giá!', 'info');
                }).catch(() => {
                    if (typeof showToast360 === 'function') showToast360('Liên kết: ' + url, 'info');
                });
            } else {
                if (typeof showToast360 === 'function') showToast360('Liên kết: ' + url, 'info');
            }
        };

        window.toggleLikeGmaps = function(btn, id) {
            const icon = btn.querySelector('i');
            const isLiked = btn.classList.contains('liked');
            if (isLiked) {
                btn.classList.remove('liked');
                icon.className = 'fa-regular fa-thumbs-up';
                if (typeof showToast360 === 'function') showToast360('Đã bỏ thích bài đánh giá', 'info');
            } else {
                btn.classList.add('liked');
                icon.className = 'fa-solid fa-thumbs-up';
                if (typeof showToast360 === 'function') showToast360('Cảm ơn bạn đã thích bài đánh giá!', 'comment');
            }
        };

        // Close dropdowns on document click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.gmaps-more-wrapper')) {
                document.querySelectorAll('.gmaps-dropdown-menu').forEach(m => m.classList.remove('show'));
            }
        });
    </script>

<!-- Toast Notification 360 -->
<div id="toastNotification360" class="toast-notification-360">
    <span id="toastIcon360"><i class="fa-solid fa-heart" style="color: #e11d48; font-size: 1.2rem;"></i></span>
    <span id="toastText360">Nội dung thông báo</span>
</div>

<!-- Interactions Logic -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnToggleFavorite = document.getElementById('btnToggleFavorite');
        const btnToggleComments = document.getElementById('btnToggleComments');
        const btnCloseComments = document.getElementById('btnCloseComments');
        const commentsDrawer = document.getElementById('commentsDrawer');
        const btnSubmitComment = document.getElementById('btnSubmitComment');
        const commentContent = document.getElementById('commentContent');
        const commentsList = document.getElementById('commentsList');
        const commentsCountBadge = document.getElementById('commentsCountBadge');
        const noCommentsMsg = document.getElementById('noCommentsMsg');
        
        const isAuth = {{ Auth::check() ? 'true' : 'false' }};
        const locationId = {{ $location->id }};
        const csrfToken = '{{ csrf_token() }}';

        function escapeHtmlAttr(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[s]));
        }

        function renderCommentAvatarHtml(user) {
            const name = escapeHtmlAttr(user?.display_name || 'Thành viên');
            const avatar = escapeHtmlAttr(user?.avatar_url || '');
            const frameCss = String(user?.frame_css || '').trim();
            const frameImg = String(user?.frame_image_url || '').trim();
            const frameClass = frameImg ? 'has-png-frame' : frameCss;
            const overlay = frameImg
                ? `<img src="${escapeHtmlAttr(frameImg)}" alt="Frame" class="avatar-frame-png-overlay">`
                : '';
            return `<div class="avatar-frame-wrapper ${escapeHtmlAttr(frameClass)}" style="width:42px;height:42px;flex-shrink:0;" title="${name}">
                <img src="${avatar}" alt="${name}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(user?.display_name || 'U')}&background=1e3a5f&color=fff';">
                ${overlay}
            </div>`;
        }

        // Toggle Drawer
        function setCommentsDrawerOpen(isOpen) {
            if (isOpen) {
                commentsDrawer.classList.add('open');
                document.body.classList.add('reviews-drawer-open');
                closeViewerMoreMenu();
                document.querySelectorAll('.floating-comment-card').forEach(function (card) {
                    card.remove();
                });
            } else {
                commentsDrawer.classList.remove('open');
                document.body.classList.remove('reviews-drawer-open');
            }
        }

        if (btnToggleComments) {
            btnToggleComments.addEventListener('click', () => {
                setCommentsDrawerOpen(!commentsDrawer.classList.contains('open'));
            });
        }
        if (btnCloseComments) {
            btnCloseComments.addEventListener('click', () => {
                setCommentsDrawerOpen(false);
            });
        }

        // Toast Helper Function
        let toastTimeout360 = null;
        function showToast360(message, status = 'success') {
            const toast = document.getElementById('toastNotification360');
            const toastIcon = document.getElementById('toastIcon360');
            const toastText = document.getElementById('toastText360');
            if (!toast) return;

            if (toastTimeout360) clearTimeout(toastTimeout360);

            toastText.innerText = message;
            if (status === 'added') {
                toastIcon.innerHTML = '<i class="fa-solid fa-heart" style="color: #ef4444; font-size: 1rem;"></i>';
            } else if (status === 'removed') {
                toastIcon.innerHTML = '<i class="fa-regular fa-heart" style="color: #76777d; font-size: 1rem;"></i>';
            } else if (status === 'comment') {
                toastIcon.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #166534; font-size: 1rem;"></i>';
            } else {
                toastIcon.innerHTML = '<i class="fa-solid fa-circle-info" style="color: #45464d; font-size: 1rem;"></i>';
            }

            toast.classList.add('show');

            toastTimeout360 = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Favorite Logic
        if (btnToggleFavorite) {
            btnToggleFavorite.addEventListener('click', function() {
                if (!isAuth) {
                    showToast360('Vui lòng đăng nhập để lưu địa điểm yêu thích!', 'info');
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 1500);
                    return;
                }

                // Heart Pop Animation
                btnToggleFavorite.classList.add('heart-pop-anim');
                setTimeout(() => btnToggleFavorite.classList.remove('heart-pop-anim'), 400);

                fetch(`/locations/${locationId}/favorite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'added') {
                        btnToggleFavorite.classList.add('is-active', 'active');
                        const icon = btnToggleFavorite.querySelector('i');
                        if (icon) {
                            icon.className = 'fa-solid fa-heart';
                            icon.style.color = '#ef4444';
                        }
                    } else {
                        btnToggleFavorite.classList.remove('is-active', 'active');
                        const icon = btnToggleFavorite.querySelector('i');
                        if (icon) {
                            icon.className = 'fa-regular fa-heart';
                            icon.style.color = '';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast360('Có lỗi xảy ra, vui lòng thử lại sau!', 'error');
                });
            });
        }

        // Interactive Star Rating Picker -> Triggers Google Maps Write Review Modal
        const starRatingPicker = document.getElementById('starRatingPicker');
        const ratingLabelHint = document.getElementById('ratingLabelHint');

        const ratingTexts = {
            1: 'Rất tệ',
            2: 'Không hài lòng',
            3: 'Bình thường',
            4: 'Rất tốt',
            5: 'Tuyệt vời!'
        };

        if (starRatingPicker) {
            const starBtns = starRatingPicker.querySelectorAll('.star-btn');

            function setDrawerStars(val) {
                starBtns.forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-value'));
                    if (val > 0 && sVal <= val) {
                        star.classList.remove('empty');
                        star.classList.add('filled');
                    } else {
                        star.classList.remove('filled');
                        star.classList.add('empty');
                    }
                });
                if (ratingLabelHint) {
                    ratingLabelHint.innerText = val > 0 ? ratingTexts[val] || '' : '';
                }
            }

            setDrawerStars(0);

            starBtns.forEach(star => {
                star.addEventListener('click', function() {
                    if (!isAuth) {
                        showToast360('Vui lòng đăng nhập để gửi đánh giá!', 'info');
                        setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 1200);
                        return;
                    }
                    const val = parseInt(this.getAttribute('data-value'));
                    openWriteReviewModal(val);
                });

                star.addEventListener('mouseenter', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    setDrawerStars(val);
                });
            });

            starRatingPicker.addEventListener('mouseleave', function() {
                setDrawerStars(0);
            });
        }

        // Modal Star Picker Logic
        const modalStarRatingPicker = document.getElementById('modalStarRatingPicker');
        const modalSelectedRating = document.getElementById('modalSelectedRating');
        const modalRatingLabelHint = document.getElementById('modalRatingLabelHint');

        function setModalStars(val) {
            if (!modalStarRatingPicker) return;
            const starBtns = modalStarRatingPicker.querySelectorAll('.modal-star-btn');
            starBtns.forEach(star => {
                const sVal = parseInt(star.getAttribute('data-value'));
                if (sVal <= val) {
                    star.classList.remove('empty');
                    star.classList.add('filled');
                } else {
                    star.classList.remove('filled');
                    star.classList.add('empty');
                }
            });
            if (modalRatingLabelHint) {
                modalRatingLabelHint.innerText = ratingTexts[val] || '';
            }
        }

        if (modalStarRatingPicker && modalSelectedRating) {
            const starBtns = modalStarRatingPicker.querySelectorAll('.modal-star-btn');

            starBtns.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    modalSelectedRating.value = val;
                    setModalStars(val);
                });

                star.addEventListener('mouseenter', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    setModalStars(val);
                });
            });

            modalStarRatingPicker.addEventListener('mouseleave', function() {
                const currentVal = parseInt(modalSelectedRating.value) || 5;
                setModalStars(currentVal);
            });
        }

        // Modal Open / Close Helpers
        window.openWriteReviewModal = function(initialRating = 5) {
            const modal = document.getElementById('writeReviewModalOverlay');
            const titleEl = document.getElementById('modalWriteReviewTitle');
            const submitBtn = document.getElementById('btnModalSubmitReview');
            const contentEl = document.getElementById('modalCommentContent');
            const editingIdEl = document.getElementById('editingCommentId');

            if (modal) {
                if (editingIdEl) editingIdEl.value = '';
                if (titleEl) titleEl.innerText = "{{ $location->name }}";
                if (submitBtn) submitBtn.innerText = 'Đăng';
                if (contentEl) contentEl.value = '';
                if (modalSelectedRating) modalSelectedRating.value = initialRating;
                setModalStars(initialRating);
                modal.classList.add('active');
            }
        };

        window.openEditReviewModal = function(commentId, rating, content) {
            closeAllGmapsMenus();
            const modal = document.getElementById('writeReviewModalOverlay');
            const titleEl = document.getElementById('modalWriteReviewTitle');
            const submitBtn = document.getElementById('btnModalSubmitReview');
            const contentEl = document.getElementById('modalCommentContent');
            const editingIdEl = document.getElementById('editingCommentId');

            if (modal) {
                if (editingIdEl) editingIdEl.value = commentId;
                if (titleEl) titleEl.innerText = 'Chỉnh sửa đánh giá';
                if (submitBtn) submitBtn.innerText = 'Lưu thay đổi';
                if (contentEl) contentEl.value = content;
                if (modalSelectedRating) modalSelectedRating.value = rating;
                setModalStars(rating);
                modal.classList.add('active');
            }
        };

        window.closeWriteReviewModal = function() {
            const modal = document.getElementById('writeReviewModalOverlay');
            if (modal) {
                modal.classList.remove('active');
            }
        };

        window.handlePhotoSelected = function(input) {
            const previewEl = document.getElementById('selectedPhotoPreviewName');
            if (input.files && input.files[0]) {
                if (previewEl) {
                    previewEl.style.display = 'block';
                    previewEl.innerText = 'Đã chọn ảnh: ' + input.files[0].name;
                }
            }
        };

        // Submit Modal Review Logic (New & Edit)
        window.submitModalReview = function() {
            const contentEl = document.getElementById('modalCommentContent');
            const submitBtn = document.getElementById('btnModalSubmitReview');
            const editingIdEl = document.getElementById('editingCommentId');
            const editingId = editingIdEl ? editingIdEl.value : '';
            if (!contentEl || !submitBtn) return;

            const content = contentEl.value.trim();
            if (!content) {
                showToast360('Vui lòng nhập nội dung đánh giá!', 'info');
                contentEl.focus();
                return;
            }

            const rating = modalSelectedRating ? parseInt(modalSelectedRating.value) || 5 : 5;
            const isEdit = editingId !== '';
            const url = isEdit ? `/comments/${editingId}` : `/locations/${locationId}/comment`;
            const method = isEdit ? 'PUT' : 'POST';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...';

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ content: content, rating: rating })
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = isEdit ? 'Lưu thay đổi' : 'Đăng';

                if (data.success) {
                    contentEl.value = '';
                    closeWriteReviewModal();

                    if (isEdit) {
                        // Update card in DOM
                        const card = document.getElementById(`comment-${editingId}`);
                        if (card) {
                            card.setAttribute('data-rating', rating);
                            const starsBox = card.querySelector('.gmaps-rating-row .gmaps-stars');
                            if (starsBox) {
                                let starsHtml = '';
                                for (let i = 1; i <= 5; i++) {
                                    starsHtml += `<i class="${i <= rating ? 'fa-solid' : 'fa-regular'} fa-star"></i>`;
                                }
                                starsBox.innerHTML = starsHtml;
                            }
                            const bodyBox = card.querySelector('.gmaps-review-body');
                            if (bodyBox) {
                                const isLong = content.length > 150;
                                bodyBox.innerHTML = isLong 
                                    ? `<span class="gmaps-text-short">${content.substring(0, 150)}...</span>
                                       <span class="gmaps-text-full" style="display: none;">${content}</span>
                                       <button class="gmaps-btn-see-more" onclick="toggleGmapsSeeMore(this)">Xem thêm</button>`
                                    : `<span>${content}</span>`;
                            }
                        }
                        showToast360('Đã cập nhật bài đánh giá thành công!', 'comment');
                    } else {
                        // Create card in DOM
                        if (noCommentsMsg) noCommentsMsg.style.display = 'none';

                        const c = data.comment;
                        const ratingVal = c.rating || rating;
                        let starsHtml = '';
                        if (ratingVal > 0) {
                            starsHtml = `<div class="gmaps-stars">`;
                            for (let i = 1; i <= 5; i++) {
                                starsHtml += `<i class="${i <= ratingVal ? 'fa-solid' : 'fa-regular'} fa-star"></i>`;
                            }
                            starsHtml += `</div>`;
                        }

                        const userCommentsCount = (c.user.comments_count || 1);
                        const isLong = c.content.length > 150;
                        const bodyHtml = isLong 
                            ? `<span class="gmaps-text-short">${c.content.substring(0, 150)}...</span>
                               <span class="gmaps-text-full" style="display: none;">${c.content}</span>
                               <button class="gmaps-btn-see-more" onclick="toggleGmapsSeeMore(this)">Xem thêm</button>`
                            : `<span>${c.content}</span>`;

                        const html = `
                            <div class="gmaps-review-card" id="comment-${c.id}" data-rating="${ratingVal}" data-timestamp="${Math.floor(Date.now()/1000)}">
                                <div class="gmaps-review-header">
                                    <div class="gmaps-user-block">
                                        ${renderCommentAvatarHtml(c.user)}
                                        <div>
                                            <div class="gmaps-username">${c.user.display_name}</div>
                                            <div class="gmaps-user-subtitle">Thành viên · ${userCommentsCount} đánh giá</div>
                                        </div>
                                    </div>
                                    <div class="gmaps-more-wrapper">
                                        <button class="gmaps-btn-more" onclick="toggleGmapsMenu(event, ${c.id})" title="Tùy chọn">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="gmaps-dropdown-menu" id="gmapsMenu-${c.id}">
                                            <div class="gmaps-dropdown-item" onclick="event.stopPropagation(); openEditReviewModal(${c.id}, ${ratingVal}, ${JSON.stringify(c.content || '')})">
                                                Chỉnh sửa
                                            </div>
                                            <div class="gmaps-dropdown-item text-danger" onclick="event.stopPropagation(); deleteUserComment(${c.id})">
                                                Xóa
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gmaps-rating-row">
                                    ${starsHtml}
                                    <span class="gmaps-date">Thời gian: Vừa xong</span>
                                </div>
                                <div class="gmaps-review-body">
                                    ${bodyHtml}
                                </div>
                                <div class="gmaps-review-footer">
                                    <button class="gmaps-action-btn btn-like-gmaps" onclick="toggleLikeGmaps(this, ${c.id})">
                                        <i class="fa-regular fa-thumbs-up"></i>
                                        <span class="like-label">Thích</span>
                                    </button>
                                    <button class="gmaps-action-btn" onclick="shareCommentLink(event, ${c.id})">
                                        <i class="fa-solid fa-share-nodes"></i>
                                        <span>Chia sẻ</span>
                                    </button>
                                </div>
                            </div>
                        `;
                        const wrapper = document.getElementById('commentsItemsWrapper');
                        if (wrapper) {
                            wrapper.insertAdjacentHTML('afterbegin', html);
                        } else {
                            commentsList.insertAdjacentHTML('afterbegin', html);
                        }
                        commentsList.scrollTop = 0;

                        let currentCount = parseInt(commentsCountBadge.innerText) || 0;
                        commentsCountBadge.innerText = currentCount + 1;

                        // Update drawer bottom form to show 'Already Reviewed' status with 'Xem' button
                        const formContainer = document.getElementById('commentFormContainer');
                        if (formContainer) {
                            let starsHtml = '';
                            for (let i = 1; i <= 5; i++) {
                                starsHtml += `<i class="${i <= ratingVal ? 'fa-solid' : 'fa-regular'} fa-star"></i>`;
                            }
                            formContainer.innerHTML = `
                                <div class="d-flex align-items-center justify-content-between py-1">
                                    <span style="color: #1e3a5f; font-size: 12.5px; font-weight: 500;">
                                        Bạn đã gửi đánh giá địa điểm này
                                    </span>
                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                        <span class="gmaps-stars" style="font-size: 13px;">${starsHtml}</span>
                                        <button class="btn-goto-my-review" onclick="scrollToMyReview(${c.id})">Xem</button>
                                    </div>
                                </div>
                            `;
                        }

                        showToast360('Đã đăng đánh giá thành công! (+5 điểm)', 'comment');
                    }
                } else {
                    showToast360(data.message || 'Có lỗi xảy ra.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerText = isEdit ? 'Lưu thay đổi' : 'Đăng';
                showToast360('Có lỗi kết nối, vui lòng thử lại sau.', 'error');
            });
        };

        // Delete User Review Function
        window.deleteUserComment = function(commentId) {
            closeAllGmapsMenus();
            if (!confirm('Bạn có chắc chắn muốn xóa bài đánh giá này?')) return;

            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`comment-${commentId}`);
                    if (card) {
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-10px)';
                        setTimeout(() => { card.remove(); }, 300);
                    }

                    let currentCount = parseInt(commentsCountBadge.innerText) || 0;
                    if (currentCount > 0) commentsCountBadge.innerText = currentCount - 1;

                    // Restore bottom form so user can review again
                    const formContainer = document.getElementById('commentFormContainer');
                    if (formContainer) {
                        formContainer.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span style="color: #3b5980; font-size: 12.5px; font-weight: 500;">Viết đánh giá: </span>
                                    <span id="ratingLabelHint" class="rating-label-hint" style="margin-left: 4px; font-size: 12px; color: #6482a6;"></span>
                                </div>
                                <div class="star-rating-picker" id="starRatingPicker">
                                    <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                                    <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                                    <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="3"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                                    <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                                    <svg class="sharp-star-btn star-btn empty" viewBox="0 0 24 24" data-value="5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>
                                </div>
                            </div>
                        `;
                        // Re-bind star rating picker events if starRatingPicker exists
                        const newPicker = document.getElementById('starRatingPicker');
                        if (newPicker) {
                            const starBtns = newPicker.querySelectorAll('.star-btn');
                            starBtns.forEach(star => {
                                star.addEventListener('click', function() {
                                    if (!isAuth) {
                                        showToast360('Vui lòng đăng nhập để gửi đánh giá!', 'info');
                                        setTimeout(() => { window.location.href = "{{ route('login') }}"; }, 1200);
                                        return;
                                    }
                                    const val = parseInt(this.getAttribute('data-value'));
                                    openWriteReviewModal(val);
                                });
                            });
                        }
                    }

                    showToast360('Đã xóa bài đánh giá thành công!', 'removed');
                } else {
                    showToast360(data.message || 'Không thể xóa bài đánh giá.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showToast360('Có lỗi kết nối, vui lòng thử lại sau.', 'error');
            });
        };

        // Scroll to User's Review Function
        window.scrollToMyReview = function(commentId) {
            const card = document.getElementById(`comment-${commentId}`);
            if (!card) {
                showToast360('Không tìm thấy bài đánh giá.', 'info');
                return;
            }

            // Clear search filter if active to make sure card is visible
            const searchInput = document.getElementById('gmapsSearchInput');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                filterGmapsComments();
            }

            card.style.display = 'block';
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card.classList.remove('my-review-highlight');
            void card.offsetWidth; // trigger reflow
            card.classList.add('my-review-highlight');
        };

        // Comment Logic
        if (btnSubmitComment) {
            btnSubmitComment.addEventListener('click', function() {
                const content = commentContent.value.trim();
                if (!content) return;

                const rating = selectedCommentRating ? parseInt(selectedCommentRating.value) || 5 : 5;
                
                btnSubmitComment.disabled = true;
                btnSubmitComment.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

                fetch(`/locations/${locationId}/comment`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ content: content, rating: rating })
                })
                .then(res => res.json())
                .then(data => {
                    btnSubmitComment.disabled = false;
                    btnSubmitComment.innerHTML = '<i class="fa-regular fa-paper-plane"></i> Gửi đánh giá';
                    
                    if (data.success) {
                        commentContent.value = '';
                        if (noCommentsMsg) noCommentsMsg.style.display = 'none';
                        
                        // Add new Google Maps review card to list inside wrapper
                        const c = data.comment;
                        const ratingVal = c.rating || rating;
                        let starsHtml = '';
                        if (ratingVal > 0) {
                            starsHtml = `<div class="gmaps-stars">`;
                            for (let i = 1; i <= 5; i++) {
                                starsHtml += `<i class="${i <= ratingVal ? 'fa-solid' : 'fa-regular'} fa-star"></i>`;
                            }
                            starsHtml += `</div>`;
                        }

                        const userCommentsCount = (c.user.comments_count || 1);
                        const isLong = c.content.length > 150;
                        const bodyHtml = isLong 
                            ? `<span class="gmaps-text-short">${c.content.substring(0, 150)}...</span>
                               <span class="gmaps-text-full" style="display: none;">${c.content}</span>
                               <button class="gmaps-btn-see-more" onclick="toggleGmapsSeeMore(this)">Xem thêm</button>`
                            : `<span>${c.content}</span>`;

                        const html = `
                            <div class="gmaps-review-card" id="comment-${c.id}" data-rating="${ratingVal}" data-timestamp="${Math.floor(Date.now()/1000)}">
                                <div class="gmaps-review-header">
                                    <div class="gmaps-user-block">
                                        ${renderCommentAvatarHtml(c.user)}
                                        <div>
                                            <div class="gmaps-username">${c.user.display_name}</div>
                                            <div class="gmaps-user-subtitle">Thành viên · ${userCommentsCount} đánh giá</div>
                                        </div>
                                    </div>
                                    <div class="gmaps-more-wrapper">
                                        <button class="gmaps-btn-more" onclick="toggleGmapsMenu(event, ${c.id})" title="Tùy chọn">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="gmaps-dropdown-menu" id="gmapsMenu-${c.id}">
                                            <div class="gmaps-dropdown-item" onclick="shareCommentLink(event, ${c.id})">
                                                <i class="fa-solid fa-share-nodes"></i> Chia sẻ bài đánh giá
                                            </div>
                                            <div class="gmaps-dropdown-item text-danger" onclick="openReportModal(${c.id}, 'Comment')">
                                                <i class="fa-solid fa-flag"></i> Báo bài đánh giá vi phạm
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gmaps-rating-row">
                                    ${starsHtml}
                                    <span class="gmaps-date">Thời gian: Vừa xong</span>
                                </div>
                                <div class="gmaps-review-body">
                                    ${bodyHtml}
                                </div>
                                <div class="gmaps-review-footer">
                                    <button class="gmaps-action-btn btn-like-gmaps" onclick="toggleLikeGmaps(this, ${c.id})">
                                        <i class="fa-regular fa-thumbs-up"></i>
                                        <span class="like-label">Thích</span>
                                    </button>
                                    <button class="gmaps-action-btn" onclick="shareCommentLink(event, ${c.id})">
                                        <i class="fa-solid fa-share-nodes"></i>
                                        <span>Chia sẻ</span>
                                    </button>
                                </div>
                            </div>
                        `;
                        const wrapper = document.getElementById('commentsItemsWrapper');
                        if (wrapper) {
                            wrapper.insertAdjacentHTML('afterbegin', html);
                        } else {
                            commentsList.insertAdjacentHTML('afterbegin', html);
                        }
                        commentsList.scrollTop = 0;
                        
                        // Update badge count
                        let currentCount = parseInt(commentsCountBadge.innerText) || 0;
                        commentsCountBadge.innerText = currentCount + 1;

                        showToast360('Đã gửi đánh giá thành công! (+5 điểm)', 'comment');
                    } else {
                        showToast360(data.message || 'Có lỗi xảy ra.', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnSubmitComment.disabled = false;
                    btnSubmitComment.innerHTML = '<i class="fa-regular fa-paper-plane"></i> Gửi bình luận';
                    alert('Lỗi kết nối.');
                });
            });
        }
    });

    // Report Logic
    let currentReportId = null;
    let currentReportType = null;

    function toggleViewerMoreMenu(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('viewerMoreDropdown');
        if (dropdown) dropdown.classList.toggle('is-open');
    }

    function closeViewerMoreMenu() {
        const dropdown = document.getElementById('viewerMoreDropdown');
        if (dropdown) dropdown.classList.remove('is-open');
    }

    document.addEventListener('click', function (e) {
        const menu = document.getElementById('viewerMoreMenu');
        if (menu && !menu.contains(e.target)) {
            closeViewerMoreMenu();
        }
    });

    function shareLocation() {
        const url = window.location.href;
        const title = @json($location->name);
        closeViewerMoreMenu();

        if (navigator.share) {
            navigator.share({ title, url }).catch(function () {});
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                if (typeof showToast360 === 'function') {
                    showToast360('Đã sao chép liên kết chia sẻ', 'info');
                }
            }).catch(function () {
                if (typeof showToast360 === 'function') {
                    showToast360(url, 'info');
                }
            });
            return;
        }

        if (typeof showToast360 === 'function') {
            showToast360(url, 'info');
        }
    }

    function openReportModal(id, type) {
        const checkAuth = {{ Auth::check() ? 'true' : 'false' }};
        if (!checkAuth) {
            window.location.href = "{{ route('login') }}";
            return;
        }
        currentReportId = id;
        currentReportType = type;
        document.getElementById('reportReason').value = 'Nội dung rác, quảng cáo';
        document.getElementById('reportDescription').value = '';
        document.getElementById('reportModalOverlay').classList.add('active');
    }

    function closeReportModal() {
        document.getElementById('reportModalOverlay').classList.remove('active');
        currentReportId = null;
        currentReportType = null;
    }

    function submitReport() {
        if (!currentReportId || !currentReportType) return;
        
        const btn = document.getElementById('btnSubmitReport');
        const reason = document.getElementById('reportReason').value;
        const desc = document.getElementById('reportDescription').value;
        const csrfToken = '{{ csrf_token() }}';
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

        fetch("{{ route('client.report') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reportable_id: currentReportId,
                reportable_type: currentReportType,
                reason: reason,
                description: desc
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Gửi báo cáo';
            alert(data.message);
            if (data.success) {
                closeReportModal();
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = 'Gửi báo cáo';
            alert('Có lỗi kết nối. Vui lòng thử lại sau.');
        });
    }

    // ===== Góp ý / Báo lỗi =====
    let currentFeedbackLocationId = null;

    function openFeedbackModal(locationId) {
        const checkAuth = {{ Auth::check() ? 'true' : 'false' }};
        if (!checkAuth) {
            window.location.href = "{{ route('login') }}";
            return;
        }
        currentFeedbackLocationId = locationId;
        document.getElementById('feedbackType').value = 'wrong_info';
        document.getElementById('feedbackContent').value = '';
        document.getElementById('feedbackModalOverlay').classList.add('active');
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModalOverlay').classList.remove('active');
        currentFeedbackLocationId = null;
    }

    function submitFeedback() {
        const btn = document.getElementById('btnSubmitFeedback');
        const type = document.getElementById('feedbackType').value;
        const content = document.getElementById('feedbackContent').value.trim();
        const csrfToken = '{{ csrf_token() }}';

        if (!content) {
            alert('Vui lòng nhập nội dung góp ý / báo lỗi.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

        fetch("{{ route('client.feedback.submit') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                report_type: type,
                target_type: 'location',
                target_id: currentFeedbackLocationId,
                content: content
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Gửi góp ý';
            alert(data.message || 'Đã gửi góp ý.');
            if (data.success) {
                closeFeedbackModal();
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = 'Gửi góp ý';
            alert('Có lỗi kết nối. Vui lòng thử lại sau.');
        });
    }
</script>

<!-- Floating U-Curve Review Bubbles Script -->
<script>
(function() {
    @php
        $floatingComments = $location->comments
            ->filter(function($c) {
                return ($c->rating ?? 5) >= 4;
            })
            ->map(function($c) {
                $frame = optional(optional($c->user)->equippedFrame);
                return [
                    'name' => optional($c->user)->display_name ?? optional($c->user)->username ?? 'Khách du lịch',
                    'avatar' => optional($c->user)->avatar_formatted_url ?? asset('images/default-avatar.png'),
                    'rating' => $c->rating ?? 5,
                    'text' => $c->content,
                    'frame_css' => $frame->css_style ?? '',
                    'frame_image_url' => !empty($frame->image_url) ? asset($frame->image_url) : '',
                ];
            })->values();
    @endphp

    const commentsData = @json($floatingComments);
    if (!commentsData || commentsData.length === 0) return;

    let commentIdx = 0;
    let spawnTimer = null;
    const container = document.createElement('div');
    container.className = 'floating-comment-container';
    document.body.appendChild(container);

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    function renderStarsSvg(count) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= count) {
                html += `<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>`;
            }
        }
        return html;
    }

    function renderFloatingAvatar(item) {
        const name = escapeHtml(item.name || 'Khách');
        const avatar = escapeHtml(item.avatar || '');
        const frameCss = String(item.frame_css || '').trim();
        const frameImg = String(item.frame_image_url || '').trim();
        const frameClass = frameImg ? 'has-png-frame' : frameCss;
        const overlay = frameImg
            ? `<img src="${escapeHtml(frameImg)}" alt="Frame" class="avatar-frame-png-overlay">`
            : '';
        return `<div class="avatar-frame-wrapper ${escapeHtml(frameClass)}" style="width:30px;height:30px;flex-shrink:0;" title="${name}">
            <img src="${avatar}" alt="${name}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
            ${overlay}
        </div>`;
    }

    function spawnFloatingComment() {
        if (document.body.classList.contains('reviews-drawer-open')) {
            return;
        }

        if (commentIdx >= commentsData.length) {
            // Finished 1 full cycle of all positive reviews! Stop spawning.
            if (spawnTimer) clearInterval(spawnTimer);
            setTimeout(() => { 
                if (container) container.remove(); 
            }, 2000);
            return;
        }

        const item = commentsData[commentIdx];
        commentIdx++;

        const card = document.createElement('div');
        card.className = 'floating-comment-card';
        card.innerHTML = `
            ${renderFloatingAvatar(item)}
            <div class="comment-content-box">
                <div class="user-name-row">
                    <span class="user-name">${escapeHtml(item.name)}</span>
                    <div class="stars-row">${renderStarsSvg(item.rating)}</div>
                </div>
                <div class="comment-text">${escapeHtml(item.text)}</div>
            </div>
        `;

        container.appendChild(card);

        // Remove card after animation completes (1.8s)
        setTimeout(() => {
            card.remove();
        }, 1800);
    }

    // Start floating comments 0.5s after entering 360 view (Single 1-Pass Run)
    setTimeout(() => {
        spawnFloatingComment();
        spawnTimer = setInterval(spawnFloatingComment, 2100);
    }, 500);
})();
</script>

</body>
</html>
