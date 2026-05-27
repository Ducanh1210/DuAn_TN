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

</body>
</html>
