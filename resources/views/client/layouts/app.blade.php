<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cổng Thông Tin Du Lịch Ninh Bình')</title>
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
            --primary: #1e3a5f;
            --primary-hover: #2b4c7e;
            --bg-body: #f8fafc;
            --text-dark: #0f2442;
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
        .text-primary {
            color: #1e3a5f !important;
        }
        .btn-primary {
            background-color: #1e3a5f !important;
            border-color: #1e3a5f !important;
        }
        .btn-primary:hover {
            background-color: #2b4c7e !important;
            border-color: #2b4c7e !important;
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

        /* Site header */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 36, 66, 0.04);
        }
        .site-header__inner {
            display: flex;
            align-items: center;
            gap: 28px;
            min-height: 56px;
        }
        .site-brand {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
            flex-shrink: 0;
            color: #0f172a !important;
            text-decoration: none;
        }
        .site-brand__main {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
            color: #1e3a5f;
        }
        .site-brand__sub {
            font-weight: 500;
            font-size: 0.9rem;
            color: #64748b;
            letter-spacing: -0.01em;
        }
        .site-nav {
            display: flex;
            align-items: stretch;
            gap: 2px;
            margin-left: auto;
            align-self: stretch;
        }
        .site-nav__link {
            display: inline-flex;
            align-items: center;
            padding: 0 14px;
            min-height: 56px;
            font-size: 0.825rem;
            font-weight: 500;
            color: #64748b;
            transition: color 0.15s ease;
            position: relative;
            white-space: nowrap;
        }
        .site-nav__link:hover {
            color: #1e3a5f;
        }
        .site-nav__link.is-active {
            color: #1e3a5f;
            font-weight: 600;
            box-shadow: inset 0 -2px 0 #1e3a5f;
        }
        .site-header .navbar-toggler {
            border-color: #e2e8f0;
            padding: 4px 8px;
        }
        .site-header .navbar-toggler:focus {
            box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.12);
        }
        @media (min-width: 992px) {
            .site-header .navbar-collapse {
                display: flex !important;
                align-items: center;
            }
        }
        @media (max-width: 991.98px) {
            .site-header__inner {
                flex-wrap: wrap;
                align-items: stretch;
                padding: 10px 0 12px;
                gap: 12px;
            }
            .site-nav {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                margin-left: 0;
                gap: 2px;
            }
            .site-nav__link {
                width: 100%;
                padding: 10px 14px;
            }
            .site-nav__link.is-active {
                box-shadow: inset 0 -2px 0 #1e3a5f;
            }
        }

        /* Legacy — bootstrap navbar expand helper */
        .site-header.navbar {
            padding: 0;
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
            padding: 48px 0 24px;
            margin-top: 64px;
            font-size: 0.85rem;
        }
        footer a {
            color: #cbd5e1;
            transition: color 0.15s ease;
        }
        footer a:hover { color: #ffffff; }
        .footer-brand {
            font-weight: 600;
            font-size: 1rem;
            color: #f8fafc;
            letter-spacing: -0.02em;
        }
        .footer-tagline {
            color: #64748b;
            font-size: 0.825rem;
            line-height: 1.55;
            max-width: 280px;
        }
        .footer-heading {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 12px;
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li + li { margin-top: 8px; }
        .footer-bottom {
            border-top: 1px solid rgba(148, 163, 184, 0.15);
            margin-top: 32px;
            padding-top: 20px;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Cover image */
        .cover-image {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            background: #f1f5f9;
        }
        .cover-image__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.2s ease, transform 0.35s ease;
        }
        .cover-image__placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e8eef5 0%, #f1f5f9 100%);
            color: #6482a6;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        /* Editorial pages */
        .page-shell {
            background: #ffffff;
            min-height: calc(100vh - 120px);
            padding-bottom: 48px;
        }
        .page-header {
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
        }
        .page-header__title {
            color: #1e3a5f;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .page-header__subtitle {
            color: #6482a6;
            font-size: 0.875rem;
            font-weight: 400;
        }
        .section-label {
            color: #3f3f46;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .editorial-link:hover .editorial-link__title {
            color: #1e3a5f;
            text-decoration: underline;
        }
        .editorial-link:hover .cover-image__img {
            opacity: 0.92;
            transform: scale(1.02);
        }
        .meta-text {
            color: #a1a1aa;
            font-size: 0.775rem;
        }
        .tab-filter {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .tab-filter__item {
            padding: 6px 14px;
            font-size: 0.825rem;
            font-weight: 500;
            color: #64748b;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .tab-filter__item:hover {
            color: #1e3a5f;
            background: #ffffff;
        }
        .tab-filter__item.is-active {
            background: #ffffff;
            color: #1e3a5f;
            box-shadow: 0 1px 2px rgba(15, 36, 66, 0.06);
        }
        .event-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .event-card:hover {
            border-color: #cbdbe8;
            box-shadow: 0 8px 24px rgba(15, 36, 66, 0.06);
        }
        .event-card__body { padding: 16px; }
        .event-card__title {
            color: #27272a;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .event-card__date {
            color: #1e3a5f;
            font-size: 0.775rem;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .event-card__excerpt {
            color: #52525b;
            font-size: 0.825rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .empty-state {
            text-align: center;
            padding: 64px 24px;
            border: 1px dashed #e2e8f0;
            border-radius: 12px;
            background: #fafbfc;
        }
        .empty-state__title {
            color: #1e3a5f;
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .empty-state__text {
            color: #6482a6;
            font-size: 0.875rem;
            max-width: 420px;
            margin: 0 auto 20px;
            line-height: 1.55;
        }
        .custom-pagination .pagination { gap: 4px; }
        .custom-pagination .page-link {
            color: #3f3f46;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 5px 11px;
            font-size: 0.825rem;
            background: #ffffff;
        }
        .custom-pagination .page-item.active .page-link {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
            color: #ffffff;
        }
        
        /* Utilities */
        a { text-decoration: none; }
        .text-primary { color: var(--primary) !important; }
    </style>

</head>
<body>
    <header class="site-header navbar navbar-expand-lg">
        <div class="container">
            <div class="site-header__inner">
                <a class="site-brand" href="{{ url('/') }}">
                    <span class="site-brand__main">Ninh Bình</span>
                    <span class="site-brand__sub">Travel Hub</span>
                </a>

                <button class="navbar-toggler ms-auto d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavCollapse" aria-controls="siteNavCollapse" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse flex-grow-1" id="siteNavCollapse">
                    <nav class="site-nav" aria-label="Điều hướng chính">
                        <a class="site-nav__link {{ request()->routeIs('client.landing') ? 'is-active' : '' }}" href="{{ route('client.landing') }}">Trang chủ</a>
                        <a class="site-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Bản đồ</a>
                        <a class="site-nav__link {{ request()->routeIs('client.news.*') ? 'is-active' : '' }}" href="{{ route('client.news.index') }}">Tin tức & Cẩm nang</a>
                        <a class="site-nav__link {{ request()->routeIs('client.events.*') ? 'is-active' : '' }}" href="{{ route('client.events.index') }}">Sự kiện nổi bật</a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

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
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5">
                    <div class="footer-brand mb-2">Ninh Bình Travel Hub</div>
                    <p class="footer-tagline mb-0">Cổng thông tin du lịch — khám phá điểm đến, tin tức và sự kiện tại Ninh Bình.</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="footer-heading">Khám phá</div>
                    <ul class="footer-links">
                        <li><a href="{{ route('client.landing') }}">Trang chủ</a></li>
                        <li><a href="{{ route('home') }}">Bản đồ du lịch</a></li>
                        <li><a href="{{ route('client.news.index') }}">Tin tức & Cẩm nang</a></li>
                        <li><a href="{{ route('client.events.index') }}">Sự kiện nổi bật</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <div class="footer-heading">Tài khoản</div>
                    <ul class="footer-links">
                        @auth
                            <li><a href="{{ route('client.profile') }}">Trang cá nhân</a></li>
                            <li><a href="{{ route('client.favorites.index') }}">Địa điểm yêu thích</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                            <li><a href="{{ route('register') }}">Đăng ký</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center text-md-start">
                &copy; {{ date('Y') }} Cổng Thông Tin Du Lịch Ninh Bình
            </div>
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

            // Function to send heartbeat (tracks online minutes for mission — no per-minute xu)
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
                    if (typeof data.minutes === 'undefined') return;
                    const minutes = parseInt(data.minutes, 10) || 0;
                    const target = parseInt(data.target, 10) || 15;
                    const pct = Math.min(100, Math.round((minutes / target) * 100));

                    const progressText = document.getElementById("missionSessionProgressText");
                    const progressBar = document.getElementById("missionSessionProgressBar");
                    if (progressText && progressBar) {
                        progressText.textContent = minutes + "/" + target + " phút";
                        progressBar.style.width = pct + "%";
                        progressBar.setAttribute("aria-valuenow", minutes);
                    }
                    const widgetSessionText = document.getElementById("widgetSessionText");
                    const widgetSessionBar = document.getElementById("widgetSessionBar");
                    if (widgetSessionText && widgetSessionBar) {
                        widgetSessionText.textContent = minutes + "/" + target + " phút";
                        widgetSessionBar.style.width = pct + "%";
                        widgetSessionBar.setAttribute("aria-valuenow", minutes);
                    }
                })
                .catch(error => console.error("Error sending heartbeat:", error));
            }

            // Start heartbeat tracker
            setInterval(sendHeartbeat, intervalTime);
        })();
    </script>
    @endauth

    <!-- AI Chatbot & Trip Planner Floating Widgets -->
    <x-chatbot-widget />
    <x-trip-planner-widget />
</body>
</html>
