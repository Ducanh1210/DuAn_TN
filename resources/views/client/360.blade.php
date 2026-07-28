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

        /* Back Button */
        .btn-back-map {
            position: absolute; top: 20px; left: 20px; z-index: 1000;
            display: flex; align-items: center; gap: 8px;
            background: rgba(0,0,0,0.6); color: white; border: 1px solid rgba(255,255,255,0.2);
            padding: 8px 16px; border-radius: 30px; text-decoration: none; font-weight: 500;
            transition: all 0.2s; backdrop-filter: blur(5px);
        }
        .btn-back-map:hover { background: rgba(0,0,0,0.8); color: white; transform: translateY(-1px); border-color: rgba(255,255,255,0.4); }

        /* Hide all default Marzipano UI elements */
        #titleBar { display: none !important; }
        #sceneList { display: none !important; }
        #sceneListToggle { display: none !important; }
        #autorotateToggle { display: none !important; }
        #fullscreenToggle { display: none !important; }
        .viewControlButton { display: none !important; }

        /* Audio Player */
        .audio-player {
            position: absolute; bottom: 24px; right: 24px; z-index: 1000;
            display: none; /* Hidden by default, shown when audio available */
            align-items: center; gap: 10px;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(12px);
            padding: 10px 16px; border-radius: 40px;
            border: 1px solid rgba(255,255,255,0.15);
            color: white; font-size: 13px;
            transition: all 0.3s ease;
            min-width: 200px;
        }
        .audio-player.visible { display: flex; }
        .audio-player:hover { background: rgba(0,0,0,0.8); border-color: rgba(255,255,255,0.3); }

        .audio-play-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.15); border: none; color: white;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 14px; flex-shrink: 0;
            transition: all 0.2s;
        }
        .audio-play-btn:hover { background: rgba(255,255,255,0.3); transform: scale(1.1); }

        .audio-info { flex: 1; min-width: 0; }
        .audio-label {
            font-size: 11px; color: rgba(255,255,255,0.7);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 4px;
        }
        .audio-progress-bar {
            width: 100%; height: 4px; background: rgba(255,255,255,0.2);
            border-radius: 2px; cursor: pointer; position: relative;
            overflow: hidden;
        }
        .audio-progress-fill {
            height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            border-radius: 2px; width: 0%; transition: width 0.1s linear;
        }
        .audio-time {
            font-size: 11px; color: rgba(255,255,255,0.5);
            white-space: nowrap; flex-shrink: 0;
        }
        .audio-volume-btn {
            background: none; border: none; color: rgba(255,255,255,0.6);
            cursor: pointer; font-size: 14px; padding: 4px;
            transition: color 0.2s;
        }
        .audio-volume-btn:hover { color: white; }

        /* Floating Toolbar */
        .interaction-toolbar {
            position: absolute; right: 24px; top: 50%; transform: translateY(-50%);
            z-index: 1000; display: flex; flex-direction: column; gap: 16px;
        }
        .interaction-btn {
            width: 50px; height: 50px; border-radius: 50%;
            background: rgba(255,255,255,0.15); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.3); color: white;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); position: relative;
        }
        .interaction-btn:hover {
            background: rgba(255,255,255,0.3); transform: scale(1.1);
        }
        .interaction-btn.active .fa-heart {
            color: #ef4444; font-weight: 900;
        }
        
        .interaction-badge {
            position: absolute; top: -5px; right: -5px; background: #ef4444; color: white;
            font-size: 11px; font-weight: bold; width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: 2px solid rgba(0,0,0,0.5);
        }

        /* Side Drawer */
        .comments-drawer {
            position: absolute; right: -420px; top: 0; bottom: 0; width: 400px;
            background: rgba(24, 24, 27, 0.75); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            z-index: 1001; transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column;
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
        }
        .comments-drawer.open { right: 0; }
        .drawer-header {
            padding: 24px; border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: space-between; color: white;
        }
        .drawer-header h3 { margin: 0; font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 10px;}
        .btn-close-drawer {
            background: rgba(255,255,255,0.1); border: none; color: white; font-size: 16px; cursor: pointer;
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .btn-close-drawer:hover { background: rgba(255,255,255,0.2); transform: scale(1.1); }
        
        .comments-list {
            flex: 1; overflow-y: auto; padding: 24px;
        }
        .comments-list::-webkit-scrollbar { width: 6px; }
        .comments-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }

        .comment-item {
            display: flex; gap: 16px; margin-bottom: 24px; animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .comment-avatar {
            width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(255,255,255,0.1); flex-shrink: 0;
        }
        .comment-body {
            flex: 1; background: rgba(255,255,255,0.06); padding: 14px 16px; border-radius: 0 16px 16px 16px;
            border: 1px solid rgba(255,255,255,0.05); position: relative;
        }
        .comment-author { color: white; font-weight: 600; font-size: 15px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;}
        .comment-time { font-size: 12px; color: rgba(255,255,255,0.4); font-weight: 400;}
        .comment-text { color: rgba(255,255,255,0.85); font-size: 14px; line-height: 1.6; margin: 0; word-break: break-word;}
        
        .comment-form {
            padding: 24px; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.3);
        }
        .comment-form textarea {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; color: white; padding: 14px; font-size: 14px; resize: none; outline: none;
            transition: all 0.3s; box-sizing: border-box; font-family: inherit;
        }
        .comment-form textarea:focus { border-color: var(--hotspot-color); background: rgba(255,255,255,0.1); }
        .comment-form textarea::placeholder { color: rgba(255,255,255,0.4); }
        .btn-submit-comment {
            width: 100%; margin-top: 12px; background: var(--hotspot-color); color: white;
            border: none; padding: 12px; border-radius: 12px; font-weight: 600; cursor: pointer;
            transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 15px;
        }
        .btn-submit-comment:hover { filter: brightness(1.15); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(var(--hotspot-color-rgb, 255,81,47), 0.4); }
        .btn-submit-comment:active { transform: translateY(0); }

        .star-rating-picker {
            display: flex; align-items: center; gap: 4px; color: #f59e0b; cursor: pointer; font-size: 1.15rem;
        }
        .star-rating-picker i { transition: transform 0.15s ease, color 0.15s ease; }
        .star-rating-picker i:hover { transform: scale(1.2); }
        .comment-stars-display {
            color: #f59e0b; font-size: 11px; display: inline-flex; gap: 2px; vertical-align: middle;
        }
        
        .auth-prompt { text-align: center; color: rgba(255,255,255,0.7); font-size: 14px; }
        .auth-prompt a { color: var(--hotspot-color); text-decoration: none; font-weight: 600; }
        .auth-prompt a:hover { text-decoration: underline; }

        /* Report Modal Styles */
        .report-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.7); z-index: 10000;
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(5px);
        }
        .report-modal-overlay.active { display: flex; }
        .report-modal {
            background: #1e293b; border-radius: 16px; width: 400px; max-width: 90%;
            padding: 24px; color: white; border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            animation: modalPopIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes modalPopIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .report-modal h4 { margin-top: 0; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 18px; color: #f87171;}
        .report-modal label { display: block; margin-bottom: 8px; font-size: 13px; color: rgba(255,255,255,0.7); }
        .report-modal select, .report-modal textarea {
            width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px; padding: 10px 12px; color: white; margin-bottom: 16px; font-family: inherit; font-size: 14px;
        }
        .report-modal select:focus, .report-modal textarea:focus { border-color: #ef4444; outline: none; }
        .report-modal select option { background: #1e293b; color: white; }
        .report-modal textarea { resize: none; }
        .report-modal-actions { display: flex; gap: 12px; justify-content: flex-end; }
        .btn-report-cancel { background: rgba(255,255,255,0.1); border: none; padding: 10px 16px; border-radius: 8px; color: white; cursor: pointer; transition: 0.2s;}
        .btn-report-cancel:hover { background: rgba(255,255,255,0.2); }
        .btn-report-submit { background: #ef4444; border: none; padding: 10px 16px; border-radius: 8px; color: white; cursor: pointer; font-weight: 600; transition: 0.2s;}
        .btn-report-submit:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);}
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
        <button class="interaction-btn" id="btnToggleComments" title="Bình luận">
            <i class="fa-regular fa-comment-dots"></i>
            <span class="interaction-badge" id="commentsCountBadge">{{ $location->comments->count() }}</span>
        </button>
    </div>

    <!-- Comments Drawer -->
    <div class="comments-drawer" id="commentsDrawer">
        <div class="drawer-header">
            <h3><i class="fa-regular fa-comments"></i> Bình luận</h3>
            <button class="btn-close-drawer" id="btnCloseComments"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="comments-list" id="commentsList">
            @forelse($location->comments as $comment)
                <div class="comment-item" id="comment-{{ $comment->id }}">
                    <x-user-avatar :user="$comment->user" size="38" />
                    <div class="comment-body">
                        <div class="comment-author">
                            <div class="d-flex align-items-center gap-2">
                                <span>{{ $comment->user->display_name ?? $comment->user->username }}</span>
                                @if($comment->rating && $comment->rating > 0)
                                    <span class="comment-stars-display" title="{{ $comment->rating }}/5 sao">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $comment->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                        @endfor
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                <button class="btn btn-sm text-danger p-0 border-0 bg-transparent" title="Báo cáo bình luận" onclick="openReportModal({{ $comment->id }}, 'Comment')"><i class="fa-solid fa-flag" style="font-size: 12px;"></i></button>
                            </div>
                        </div>
                        <p class="comment-text">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center" style="color: rgba(255,255,255,0.5); padding: 20px;" id="noCommentsMsg">
                    Chưa có bình luận nào. Hãy là người đầu tiên!
                </div>
            @endforelse
        </div>
        <div class="comment-form">
            @auth
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="color: rgba(255,255,255,0.75); font-size: 13px; font-weight: 500;">Đánh giá của bạn:</span>
                    <div class="star-rating-picker" id="starRatingPicker">
                        <i class="fa-solid fa-star star-btn" data-value="1"></i>
                        <i class="fa-solid fa-star star-btn" data-value="2"></i>
                        <i class="fa-solid fa-star star-btn" data-value="3"></i>
                        <i class="fa-solid fa-star star-btn" data-value="4"></i>
                        <i class="fa-solid fa-star star-btn" data-value="5"></i>
                    </div>
                    <input type="hidden" id="selectedCommentRating" value="5">
                </div>
                <textarea id="commentContent" rows="3" placeholder="Chia sẻ cảm nhận của bạn về địa điểm này..."></textarea>
                <button class="btn-submit-comment" id="btnSubmitComment">
                    <i class="fa-regular fa-paper-plane"></i> Gửi bình luận
                </button>
            @else
                <div class="auth-prompt">
                    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để để lại bình luận và lưu địa điểm yêu thích.
                </div>
            @endauth
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

        <!-- Audio Player -->
        <div class="audio-player" id="audioPlayer">
            <button class="audio-play-btn" id="audioPlayBtn" onclick="toggleAudio()">
                <i class="fa-solid fa-play" id="audioPlayIcon"></i>
            </button>
            <div class="audio-info">
                <div class="audio-label" id="audioLabel">Âm thanh thuyết minh</div>
                <div class="audio-progress-bar" id="audioProgressBar" onclick="seekAudio(event)">
                    <div class="audio-progress-fill" id="audioProgressFill"></div>
                </div>
            </div>
            <span class="audio-time" id="audioTime">0:00</span>
            <button class="audio-volume-btn" id="audioVolumeBtn" onclick="toggleMute()">
                <i class="fa-solid fa-volume-high" id="audioVolumeIcon"></i>
            </button>
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

    <!-- Audio Player Logic -->
    <script>
        // Location-level audio (single audio for entire 360° tour)
        @if($location->audio_url)
        (function() {
            const audioUrl = @json(asset('storage/' . $location->audio_url));
            const locationName = @json($location->name);

            const audioEl = new Audio(audioUrl);
            const playerEl = document.getElementById('audioPlayer');
            const playIcon = document.getElementById('audioPlayIcon');
            const progressFill = document.getElementById('audioProgressFill');
            const timeEl = document.getElementById('audioTime');
            const labelEl = document.getElementById('audioLabel');
            const volumeIcon = document.getElementById('audioVolumeIcon');
            let isMuted = false;

            // Show player
            playerEl.classList.add('visible');
            labelEl.textContent = 'Thuyết minh: ' + locationName;

            function formatTime(sec) {
                if (isNaN(sec)) return '0:00';
                let m = Math.floor(sec / 60);
                let s = Math.floor(sec % 60);
                return m + ':' + (s < 10 ? '0' : '') + s;
            }

            audioEl.addEventListener('timeupdate', function() {
                if (audioEl.duration) {
                    progressFill.style.width = (audioEl.currentTime / audioEl.duration * 100) + '%';
                    timeEl.textContent = formatTime(audioEl.currentTime) + ' / ' + formatTime(audioEl.duration);
                }
            });

            audioEl.addEventListener('ended', function() {
                playIcon.className = 'fa-solid fa-play';
                progressFill.style.width = '0%';
            });

            audioEl.addEventListener('play', function() {
                playIcon.className = 'fa-solid fa-pause';
            });

            audioEl.addEventListener('pause', function() {
                playIcon.className = 'fa-solid fa-play';
            });

            window.toggleAudio = function() {
                if (audioEl.paused) {
                    audioEl.play().catch(() => {});
                } else {
                    audioEl.pause();
                }
            };

            window.seekAudio = function(e) {
                if (!audioEl.duration) return;
                let rect = e.currentTarget.getBoundingClientRect();
                let ratio = (e.clientX - rect.left) / rect.width;
                audioEl.currentTime = ratio * audioEl.duration;
            };

            window.toggleMute = function() {
                isMuted = !isMuted;
                audioEl.muted = isMuted;
                volumeIcon.className = isMuted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high';
            };
        })();
        @endif
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

        // Interactive Star Rating Picker
        const starRatingPicker = document.getElementById('starRatingPicker');
        const selectedCommentRating = document.getElementById('selectedCommentRating');

        if (starRatingPicker && selectedCommentRating) {
            const starBtns = starRatingPicker.querySelectorAll('.star-btn');

            function setStars(val) {
                starBtns.forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-value'));
                    if (sVal <= val) {
                        star.classList.remove('fa-regular');
                        star.classList.add('fa-solid');
                    } else {
                        star.classList.remove('fa-solid');
                        star.classList.add('fa-regular');
                    }
                });
            }

            setStars(5);

            starBtns.forEach(star => {
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    selectedCommentRating.value = val;
                    setStars(val);
                });

                star.addEventListener('mouseenter', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    setStars(val);
                });
            });

            starRatingPicker.addEventListener('mouseleave', function() {
                const currentVal = parseInt(selectedCommentRating.value) || 5;
                setStars(currentVal);
            });
        }

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
                    btnSubmitComment.innerHTML = '<i class="fa-regular fa-paper-plane"></i> Gửi bình luận';
                    
                    if (data.success) {
                        commentContent.value = '';
                        if (noCommentsMsg) noCommentsMsg.style.display = 'none';
                        
                        // Add new comment to list
                        const c = data.comment;
                        const ratingVal = c.rating || rating;
                        let starsHtml = '';
                        if (ratingVal > 0) {
                            starsHtml = `<span class="comment-stars-display" title="${ratingVal}/5 sao">`;
                            for (let i = 1; i <= 5; i++) {
                                starsHtml += `<i class="${i <= ratingVal ? 'fa-solid' : 'fa-regular'} fa-star"></i>`;
                            }
                            starsHtml += `</span>`;
                        }

                        const html = `
                            <div class="comment-item" id="comment-${c.id}">
                                <img src="${c.user.avatar_url}" alt="${c.user.display_name}" class="comment-avatar">
                                <div class="comment-body">
                                    <div class="comment-author">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>${c.user.display_name}</span>
                                            ${starsHtml}
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="comment-time">Vừa xong</span>
                                            <button class="btn btn-sm text-danger p-0 border-0 bg-transparent" title="Báo cáo bình luận" onclick="openReportModal(${c.id}, 'Comment')"><i class="fa-solid fa-flag" style="font-size: 12px;"></i></button>
                                        </div>
                                    </div>
                                    <p class="comment-text">${c.content}</p>
                                </div>
                            </div>
                        `;
                        commentsList.insertAdjacentHTML('afterbegin', html);
                        commentsList.scrollTop = 0;
                        
                        // Update badge count
                        let currentCount = parseInt(commentsCountBadge.innerText) || 0;
                        commentsCountBadge.innerText = currentCount + 1;

                        showToast360('Đã gửi bình luận thành công! (+5 điểm)', 'comment');
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

</body>
</html>
