<!DOCTYPE html>
<html>
<head>
    <title>Khám phá 360° - {{ $location->name }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, minimal-ui" />
    <style> @-ms-viewport { width: device-width; } </style>
    
    <!-- Marzipano Original CSS -->
    <link rel="stylesheet" href="{{ asset('marzipano/vendor/reset.min.css') }}">
    <link rel="stylesheet" href="{{ asset('marzipano/style.css') }}">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        /* Hide all default Marzipano UI elements */
        #titleBar { display: none !important; }
        #sceneList { display: none !important; }
        #sceneListToggle { display: none !important; }
        #autorotateToggle { display: none !important; }
        #fullscreenToggle { display: none !important; }
        .viewControlButton { display: none !important; }

        /* Audio Mascot Player */
        .audio-player {
            position: absolute; bottom: 24px; right: 24px; z-index: 1000;
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

        /* Side Drawer (Misty Ice-Blue & Slate Ink Theme per DESIGN_GUIDE.md) */
        .comments-drawer {
            position: absolute; right: -420px; top: 0; bottom: 0; width: 380px; max-width: 100vw;
            background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            z-index: 1001; transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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
            padding: 14px 0;
            margin-bottom: 0;
            position: relative;
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
        }
        .gmaps-user-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .gmaps-user-block img, .gmaps-user-block .avatar {
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

        /* Floating Curved Review Bubbles (Mini Compact Arc Loop Shifted Down) */
        .floating-comment-container {
            position: fixed;
            top: 80px;
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
            padding: 5px 10px 5px 7px;
            box-shadow: 0 6px 18px rgba(30, 58, 95, 0.12), 0 2px 4px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 7px;
            max-width: 210px;
            opacity: 0;
            backface-visibility: hidden;
            will-change: transform, opacity;
            animation: floatUCurve 1.8s linear forwards;
        }

        .floating-comment-card .user-avatar {
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
            background: rgba(15, 23, 42, 0.45); z-index: 10000;
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
            top: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(-30px);
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.15);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
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
@endphp

<body>

@if($scenes->isEmpty())
    <div style="display:flex; justify-content:center; align-items:center; height:100vh; background:#111; color:#fff; flex-direction:column;">
        <h3>Địa điểm này chưa có không gian 360°</h3>
        <a href="{{ url('/') }}" class="btn btn-outline-light mt-3">Quay lại Bản đồ</a>
    </div>
@else
    <a href="{{ url('/') }}" class="btn-back-map">
        <i class="fa-solid fa-arrow-left"></i> Quay lại Bản đồ
    </a>

    <!-- Interaction Toolbar -->
    <div class="interaction-toolbar">
        @php
            $isFavorited = Auth::check() && Auth::user()->favoriteLocations()->where('location_id', $location->id)->exists();
        @endphp
        <button class="interaction-btn {{ $isFavorited ? 'active' : '' }}" id="btnToggleFavorite" title="Yêu thích">
            <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
        </button>
        <button class="interaction-btn" title="Báo cáo địa điểm" onclick="openReportModal({{ $location->id }}, 'Location')">
            <i class="fa-solid fa-flag"></i>
        </button>
        <button class="interaction-btn" id="btnToggleComments" title="Đánh giá">
            <i class="fa-regular fa-star"></i>
            <span class="interaction-badge" id="commentsCountBadge">{{ $location->comments->count() }}</span>
        </button>
    </div>

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
                        $userCommentsCount = optional($comment->user)->comments ? $comment->user->comments->count() : 1;
                        $content = $comment->content;
                        $isLong = mb_strlen($content) > 150;
                    @endphp
                    <div class="gmaps-review-card" id="comment-{{ $comment->id }}" data-rating="{{ $comment->rating ?? 5 }}" data-timestamp="{{ $comment->created_at->timestamp }}">
                        <!-- Header -->
                        <div class="gmaps-review-header">
                            <div class="gmaps-user-block">
                                <x-user-avatar :user="$comment->user" size="38" />
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
                                        <div class="gmaps-dropdown-item" onclick="openEditReviewModal({{ $comment->id }}, {{ $comment->rating }}, '{{ addslashes($comment->content) }}')">
                                            Chỉnh sửa
                                        </div>
                                        <div class="gmaps-dropdown-item text-danger" onclick="deleteUserComment({{ $comment->id }})">
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

    <div class="viewer-area">
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

        <!-- Audio Mascot Player -->
        <div class="audio-player" id="audioPlayer">
            <!-- Small Popover right above mascot -->
            <div class="audio-info-popover" id="audioInfoPopover">
                <div class="audio-popover-header">
                    <span>{{ $location->name }}</span>
                    <button class="btn-close-popover" onclick="toggleAudioInfoPopover(event)"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="audio-popover-body">
                    @php
                        $audioNarrationText = data_get($location->attributes, 'tts_text') 
                                ?: ($location->description 
                                ?: ($location->short_description 
                                ?: 'Chưa có nội dung thuyết minh.'));
                    @endphp
                    {!! nl2br(e($audioNarrationText)) !!}
                </div>
            </div>

            <div class="audio-mascot-wrapper">
                <button class="audio-info-btn" id="btnAudioInfo" onclick="toggleAudioInfoPopover(event)" title="Xem nội dung thuyết minh">
                    i
                </button>
                <img src="{{ asset('images/loax.png') }}" 
                     alt="Thuyết minh 360" 
                     class="audio-mascot-btn" 
                     id="audioMascotImg" 
                     onclick="toggleAudio()" 
                     title="Nhấp để Bật/Tắt thuyết minh">
            </div>
            <div class="audio-progress-bar" id="audioProgressBar" onclick="seekAudio(event)" title="Thanh tiến trình thuyết minh">
                <div class="audio-progress-fill" id="audioProgressFill"></div>
            </div>
        </div>
    </div>

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

    <!-- Audio Player & Autoplay Logic -->
    <script>
    (function() {
        const playerEl = document.getElementById('audioPlayer');
        const mascotImg = document.getElementById('audioMascotImg');
        const progressFill = document.getElementById('audioProgressFill');

        const loaOnSrc = @json(asset('images/loa.png'));
        const loaOffSrc = @json(asset('images/loax.png'));
        const audioUrl = @json($location->audio_url ? asset('storage/' . $location->audio_url) : null);

        @php
            $rawNarrationText = data_get($location->attributes, 'tts_text') 
                    ?: ($location->description 
                    ?: ($location->short_description 
                    ?: ''));
        @endphp
        const narrationText = @json(trim(strip_tags($rawNarrationText)));

        if (playerEl) playerEl.classList.add('visible');

        let audioEl = null;
        let isTtsMode = false;
        let ttsUtterance = null;
        let isPlaying = false;

        if (audioUrl) {
            audioEl = new Audio(audioUrl);
            audioEl.addEventListener('timeupdate', function() {
                if (audioEl.duration && progressFill) {
                    progressFill.style.width = (audioEl.currentTime / audioEl.duration * 100) + '%';
                }
            });
            audioEl.addEventListener('ended', function() {
                isPlaying = false;
                if (mascotImg) mascotImg.src = loaOffSrc;
                if (progressFill) progressFill.style.width = '0%';
            });
            audioEl.addEventListener('play', function() {
                isPlaying = true;
                if (mascotImg) mascotImg.src = loaOnSrc;
            });
            audioEl.addEventListener('pause', function() {
                isPlaying = false;
                if (mascotImg) mascotImg.src = loaOffSrc;
            });
        } else if ('speechSynthesis' in window && narrationText) {
            isTtsMode = true;
        }

        function playNarration() {
            if (audioEl) {
                audioEl.play().then(() => {
                    isPlaying = true;
                    if (mascotImg) mascotImg.src = loaOnSrc;
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
                    if (mascotImg) mascotImg.src = loaOnSrc;
                };
                ttsUtterance.onend = function() {
                    isPlaying = false;
                    if (mascotImg) mascotImg.src = loaOffSrc;
                };
                ttsUtterance.onerror = function() {
                    isPlaying = false;
                    if (mascotImg) mascotImg.src = loaOffSrc;
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
            if (mascotImg) mascotImg.src = loaOffSrc;
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
@endif

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

        // Toggle Drawer
        if (btnToggleComments) {
            btnToggleComments.addEventListener('click', () => {
                commentsDrawer.classList.toggle('open');
            });
        }
        if (btnCloseComments) {
            btnCloseComments.addEventListener('click', () => {
                commentsDrawer.classList.remove('open');
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
                toastIcon.innerHTML = '<i class="fa-solid fa-heart" style="color: #ef4444; font-size: 1.2rem;"></i>';
            } else if (status === 'removed') {
                toastIcon.innerHTML = '<i class="fa-regular fa-heart" style="color: #94a3b8; font-size: 1.2rem;"></i>';
            } else if (status === 'comment') {
                toastIcon.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #22c55e; font-size: 1.2rem;"></i>';
            } else {
                toastIcon.innerHTML = '<i class="fa-solid fa-circle-info" style="color: #38bdf8; font-size: 1.2rem;"></i>';
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
                        btnToggleFavorite.classList.add('active');
                        btnToggleFavorite.innerHTML = '<i class="fa-solid fa-heart" style="color: #ef4444;"></i>';
                        showToast360(data.message || 'Đã thêm vào danh sách yêu thích (+2 điểm)', 'added');
                    } else {
                        btnToggleFavorite.classList.remove('active');
                        btnToggleFavorite.innerHTML = '<i class="fa-regular fa-heart"></i>';
                        showToast360(data.message || 'Đã xóa khỏi danh sách yêu thích', 'removed');
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
                                        <img src="${c.user.avatar_url}" alt="${c.user.display_name}" class="comment-avatar">
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
                                            <div class="gmaps-dropdown-item" onclick="openEditReviewModal(${c.id}, ${ratingVal}, '${c.content.replace(/'/g, "\\'")}')">
                                                Chỉnh sửa
                                            </div>
                                            <div class="gmaps-dropdown-item text-danger" onclick="deleteUserComment(${c.id})">
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
                                        <img src="${c.user.avatar_url}" alt="${c.user.display_name}" class="comment-avatar">
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
                return [
                    'name' => optional($c->user)->display_name ?? optional($c->user)->username ?? 'Khách du lịch',
                    'avatar' => optional($c->user)->avatar_formatted_url ?? asset('images/default-avatar.png'),
                    'rating' => $c->rating ?? 5,
                    'text' => $c->content
                ];
            })->values();

        if ($floatingComments->isEmpty()) {
            $floatingComments = collect([
                ['name' => 'Nguyễn Văn An', 'avatar' => asset('images/default-avatar.png'), 'rating' => 5, 'text' => 'Không gian 360° tuyệt đẹp! Cảnh quan rất hùng vĩ.'],
                ['name' => 'Trần Thị Mai', 'avatar' => asset('images/default-avatar.png'), 'rating' => 5, 'text' => 'Trải nghiệm rất thực tế và sinh động.'],
                ['name' => 'Phạm Quốc Tuấn', 'avatar' => asset('images/default-avatar.png'), 'rating' => 5, 'text' => 'Điểm đến ấn tượng tuyệt vời tại Ninh Bình!']
            ]);
        }
    @endphp

    const commentsData = @json($floatingComments);
    if (!commentsData || commentsData.length === 0) return;

    let commentIdx = 0;
    let spawnTimer = null;
    const container = document.createElement('div');
    container.className = 'floating-comment-container';
    document.body.appendChild(container);

    function renderStarsSvg(count) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= count) {
                html += `<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26"/></svg>`;
            }
        }
        return html;
    }

    function spawnFloatingComment() {
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
            <img src="${item.avatar}" alt="${item.name}" class="user-avatar" onerror="this.src='{{ asset('images/default-avatar.png') }}'">
            <div class="comment-content-box">
                <div class="user-name-row">
                    <span class="user-name">${item.name}</span>
                    <div class="stars-row">${renderStarsSvg(item.rating)}</div>
                </div>
                <div class="comment-text">${item.text}</div>
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
