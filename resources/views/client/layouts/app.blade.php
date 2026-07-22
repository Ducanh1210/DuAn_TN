<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cổng Thông Tin Du Lịch Hà Nam')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Avatar Frames CSS -->
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    @stack('styles')
    <style>
        :root {
            --primary: #0066ff;
            --primary-hover: #0052cc;
            --bg-body: #f4f7fb;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        body { 
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-dark); 
            font-size: 0.925rem;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }

        /* Proportional, compact typography matching homepage scale */
        h1, .h1 { font-size: 1.4rem !important; line-height: 1.3; }
        h2, .h2 { font-size: 1.2rem !important; line-height: 1.35; }
        h3, .h3 { font-size: 1.05rem !important; line-height: 1.4; }
        h4, .h4 { font-size: 0.95rem !important; line-height: 1.4; }
        h5, .h5 { font-size: 0.9rem !important; line-height: 1.4; }
        h6, .h6 { font-size: 0.85rem !important; line-height: 1.4; }

        .breadcrumb { font-size: 0.825rem; }
        .badge { font-size: 0.75rem; font-weight: 600; }
        .form-control, .form-select { font-size: 0.875rem; }
        .btn { font-size: 0.875rem; }
        .btn-sm { font-size: 0.775rem; }

        /* Navbar */
        .navbar { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05); 
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            padding: 10px 0 !important;
            transition: all 0.3s ease;
        }
        .navbar-brand { 
            font-weight: 800; 
            font-size: 1.25rem !important;
            background: linear-gradient(135deg, #0066ff, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        .nav-link { 
            font-weight: 600; 
            font-size: 0.875rem !important;
            color: var(--text-muted); 
            padding: 6px 14px !important;
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active { 
            color: var(--primary); 
            background: rgba(0, 102, 255, 0.08);
        }

        /* Modern Card */
        .card-modern { 
            border: none; 
            border-radius: 16px; 
            background: #fff;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04); 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            overflow: hidden; 
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-modern:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 14px 30px rgba(0,0,0,0.07); 
        }
        .card-modern .img-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16/10;
            overflow: hidden;
        }
        .card-modern .img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .card-modern:hover .img-wrapper img {
            transform: scale(1.04);
        }
        
        /* Badges */
        .badge-glass { 
            position: absolute; 
            top: 12px; 
            left: 12px; 
            background: rgba(255,255,255,0.85); 
            color: var(--text-dark); 
            padding: 4px 10px; 
            border-radius: 20px; 
            font-size: 0.725rem; 
            font-weight: 700; 
            backdrop-filter: blur(8px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Footer */
        footer { 
            background: #0f172a; 
            color: #94a3b8; 
            padding: 40px 0 20px; 
            margin-top: 50px; 
            font-size: 0.85rem;
        }
        
        /* Utilities */
        a { text-decoration: none; }
        .text-primary { color: var(--primary) !important; }
        .text-gradient {
            background: linear-gradient(135deg, #0066ff, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}"><i class="fa-solid fa-map-location-dot"></i> Hà Nam POI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Bản đồ</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('client.news.*') ? 'active' : '' }}" href="{{ route('client.news.index') }}">Tin tức & Cẩm nang</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('client.events.*') ? 'active' : '' }}" href="{{ route('client.events.index') }}">Sự kiện nổi bật</a></li>
                    
                    @auth
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--primary); background: rgba(0, 102, 255, 0.08);">
                                <x-user-avatar :user="Auth::user()" size="26" />
                                <span>{{ Auth::user()->display_name ?? Auth::user()->username }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3 mt-2" aria-labelledby="navbarUserDropdown">
                                <li><span class="dropdown-item-text py-2 fw-bold text-muted small"><i class="fa-solid fa-coins me-2 text-warning"></i><span id="navbarUserPoints">{{ Auth::user()->points }} xu</span></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('client.missions') }}"><i class="fa-solid fa-bullseye me-2 text-warning"></i>Nhiệm vụ & Đổi thưởng</a></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('client.profile') }}"><i class="fa-solid fa-user me-2 text-primary"></i>Trang cá nhân</a></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('client.favorites.index') }}"><i class="fa-solid fa-heart me-2 text-danger"></i>Địa điểm yêu thích</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 fw-semibold text-danger"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3"><a class="nav-link btn text-white px-4 py-2" style="background: var(--primary); border-radius: 8px; font-weight: 600;" href="{{ route('login') }}">Đăng nhập</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success_points'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm d-flex align-items-center gap-2" role="alert" style="background-color: #d1e7dd; color: #0f5132; padding: 12px 20px;">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>{{ session('success_points') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @yield('content')

    <footer>
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} Cổng Thông Tin Du Lịch Hà Nam. Thiết kế bằng <i class="fa-solid fa-heart text-danger"></i></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    @auth
    <script>
        // Track session duration activity
        (function() {
            // Send heartbeat every 60 seconds (1 minute)
            const intervalTime = 60000; 

            // Function to send heartbeat
            function sendHeartbeat() {
                fetch("{{ route('client.profile.heartbeat') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Heartbeat sent: " + data.message + " Current points: " + data.points);
                        const pointsElement = document.getElementById("navbarUserPoints");
                        if (pointsElement) {
                            pointsElement.textContent = data.points + " điểm";
                        }
                        const sidebarPoints = document.getElementById("sidebarMissionPoints");
                        if (sidebarPoints) {
                            sidebarPoints.textContent = data.points + " điểm";
                        }
                        const widgetPoints = document.getElementById("widgetPoints");
                        if (widgetPoints) {
                            widgetPoints.textContent = data.points + " điểm";
                        }
                        const progressText = document.getElementById("missionSessionProgressText");
                        const progressBar = document.getElementById("missionSessionProgressBar");
                        if (progressText && progressBar) {
                            let currentVal = parseInt(progressText.textContent) || 0;
                            if (currentVal < 60) {
                                currentVal += 1;
                                progressText.textContent = currentVal + "/60 phút";
                                progressBar.style.width = ((currentVal / 60) * 100) + "%";
                                progressBar.setAttribute("aria-valuenow", currentVal);
                            }
                        }
                        const widgetSessionText = document.getElementById("widgetSessionText");
                        const widgetSessionBar = document.getElementById("widgetSessionBar");
                        if (widgetSessionText && widgetSessionBar) {
                            let currentVal = parseInt(widgetSessionText.textContent) || 0;
                            if (currentVal < 60) {
                                currentVal += 1;
                                widgetSessionText.textContent = currentVal + "/60 phút";
                                widgetSessionBar.style.width = ((currentVal / 60) * 100) + "%";
                                widgetSessionBar.setAttribute("aria-valuenow", currentVal);
                            }
                        }
                    }
                })
                .catch(error => console.error("Error sending heartbeat:", error));
            }

            // Start heartbeat tracker
            setInterval(sendHeartbeat, intervalTime);
        })();
    </script>
    @endauth
</body>
</html>
