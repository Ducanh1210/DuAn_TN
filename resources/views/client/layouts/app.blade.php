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
    <!-- SweetAlert2 JS & Theme -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Avatar Frames CSS -->
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    @stack('styles')
    <style>
        /* Custom SweetAlert2 Theme for Ninh Bình POI Client */
        .swal2-container {
            z-index: 20000 !important;
        }
        .custom-swal-popup {
            border-radius: 16px !important;
            padding: 1.5rem 1.75rem !important;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            border: 1px solid #cbdbe8 !important;
            box-shadow: 0 20px 25px -5px rgba(30, 58, 95, 0.12), 0 8px 10px -6px rgba(30, 58, 95, 0.06) !important;
            background: #ffffff !important;
        }
        .custom-swal-title {
            color: #1e3a5f !important;
            font-size: 1.15rem !important;
            font-weight: 600 !important;
            padding-top: 0.25rem !important;
        }
        .custom-swal-text {
            color: #334155 !important;
            font-size: 0.875rem !important;
            margin-top: 0.4rem !important;
            line-height: 1.5 !important;
        }
        .custom-swal-confirm-btn {
            background-color: #1e3a5f !important;
            color: #ffffff !important;
            font-size: 0.825rem !important;
            font-weight: 500 !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px !important;
            border: none !important;
            margin: 0.25rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: none !important;
        }
        .custom-swal-confirm-btn:hover {
            background-color: #0f2442 !important;
            color: #ffffff !important;
        }
        .custom-swal-confirm-danger {
            background-color: #dc2626 !important;
            color: #ffffff !important;
        }
        .custom-swal-confirm-danger:hover {
            background-color: #b91c1c !important;
            color: #ffffff !important;
        }
        .custom-swal-cancel-btn {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            font-size: 0.825rem !important;
            font-weight: 500 !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 8px !important;
            border: 1px solid #cbdbe8 !important;
            margin: 0.25rem !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            box-shadow: none !important;
        }
        .custom-swal-cancel-btn:hover {
            background-color: #e2e8f0 !important;
            color: #1e3a5f !important;
        }
        .custom-swal-toast {
            border-radius: 10px !important;
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, sans-serif !important;
        }

        :root {
            /* Bảng màu lấy từ mẫu "Trang chủ Khám phá Ninh Bình Premium" */
            --primary: #000000;
            --primary-hover: #565e74;
            --bg-body: #f7f9fb;
            --text-dark: #191c1e;
            --text-body: #45464d;
            --text-muted: #76777d;

            /* Vàng đồng là màu nhấn DUY NHẤT. Chỉ dùng cho chữ, icon và viền —
               không bao giờ tô nền, mọi mảng nền đều giữ xám trung tính. */
            --accent: #735c00;
            --accent-bright: #cba72f;

            --line: #c6c6cd;
            --line-soft: #e0e3e5;
            --surface-mist: #f2f4f6;
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
            color: var(--primary) !important;
        }
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
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
            border-bottom: 1px solid var(--line);
            box-shadow: 0 1px 2px rgba(25, 28, 30, 0.05);
        }
        .site-header__inner {
            display: flex;
            align-items: center;
            gap: 28px;
            min-height: 56px;
        }
        .site-brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            flex-shrink: 0;
            color: var(--primary) !important;
            text-decoration: none;
        }
        .site-brand__logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .site-brand__name {
            display: inline-flex;
            align-items: baseline;
            gap: 6px;
        }
        .site-brand__main {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
            color: var(--primary);
        }
        .site-brand__sub {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-muted);
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
            color: var(--text-body);
            transition: color 0.15s ease;
            position: relative;
            white-space: nowrap;
        }
        .site-nav__link:hover {
            color: var(--accent);
        }
        .site-nav__link.is-active {
            color: var(--primary);
            font-weight: 600;
            box-shadow: inset 0 -2px 0 var(--accent);
        }
        .site-header .navbar-toggler {
            border-color: var(--line);
            padding: 4px 8px;
        }
        .site-header .navbar-toggler:focus {
            box-shadow: 0 0 0 2px rgba(115, 92, 0, 0.25);
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
                box-shadow: inset 0 -2px 0 var(--accent);
            }
        }

        /* Legacy — bootstrap navbar expand helper */
        .site-header.navbar {
            padding: 0;
        }

        /* Modern Card */
        .card-modern { 
            border: none; 
            border-radius: 2px; 
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
            box-shadow: 0 14px 30px rgba(25, 28, 30, 0.12); 
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
        }
        
        /* Badges */
        .badge-glass { 
            position: absolute; 
            top: 12px; 
            left: 12px; 
            background: rgba(255,255,255,0.85); 
            color: var(--text-dark); 
            padding: 4px 10px; 
            border-radius: 2px; 
            font-size: 0.725rem; 
            font-weight: 700; 
            backdrop-filter: blur(8px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Footer — mẫu dùng footer nền sáng, tách khỏi nội dung bằng viền mảnh */
        footer {
            background: var(--bg-body);
            color: var(--text-body);
            border-top: 1px solid var(--line);
            padding: 64px 0 28px;
            margin-top: 80px;
            font-size: 0.85rem;
        }
        footer a {
            color: var(--text-body);
            transition: color 0.15s ease;
        }
        footer a:hover { color: var(--accent); }
        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 1rem;
            color: var(--primary);
            letter-spacing: -0.02em;
        }
        .footer-brand__logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }
        .footer-tagline {
            color: var(--text-muted);
            font-size: 0.825rem;
            line-height: 1.55;
            max-width: 280px;
        }
        .footer-heading {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li + li { margin-top: 8px; }
        .footer-bottom {
            border-top: 1px solid var(--line-soft);
            margin-top: 32px;
            padding-top: 20px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Cover image */
        .cover-image {
            position: relative;
            overflow: hidden;
            border-radius: 2px;
            background: var(--line-soft);
        }
        .cover-image__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 0.2s ease;
        }
        .cover-image__placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e3e5 0%, #f2f4f6 100%);
            color: var(--text-muted);
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
            position: relative;
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--line-soft);
        }
        .page-header::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 56px;
            height: 2px;
            background: var(--accent);
        }
        .page-header__title {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .page-header__subtitle {
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 400;
        }
        .section-label {
            color: var(--accent);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .editorial-link:hover .editorial-link__title {
            color: var(--accent);
            text-decoration: underline;
        }
        .editorial-link:hover .cover-image__img {
            opacity: 0.92;
        }
        .meta-text {
            color: var(--text-muted);
            font-size: 0.775rem;
        }
        .tab-filter {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            background: var(--surface-mist);
            border: 1px solid var(--line);
            border-radius: 2px;
        }
        .tab-filter__item {
            padding: 6px 14px;
            font-size: 0.825rem;
            font-weight: 500;
            color: var(--text-body);
            border-radius: 2px;
            transition: all 0.15s ease;
        }
        .tab-filter__item:hover {
            color: var(--accent);
            background: #ffffff;
        }
        .tab-filter__item.is-active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(25, 28, 30, 0.24);
        }
        .event-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 2px;
            overflow: hidden;
            height: 100%;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .event-card:hover {
            border-color: var(--accent);
            box-shadow: 0 8px 24px rgba(25, 28, 30, 0.10);
        }
        .event-card__body { padding: 16px; }
        .event-card__title {
            color: var(--primary);
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
            color: var(--text-muted);
            font-size: 0.775rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .event-card__excerpt {
            color: var(--text-body);
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
            border: 1px dashed var(--line);
            border-radius: 2px;
            background: var(--surface-mist);
        }
        .empty-state__title {
            color: var(--primary);
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .empty-state__text {
            color: var(--text-muted);
            font-size: 0.875rem;
            max-width: 420px;
            margin: 0 auto 20px;
            line-height: 1.55;
        }
        .custom-pagination .pagination { gap: 4px; }
        .custom-pagination .page-link {
            color: var(--text-body);
            border: 1px solid var(--line);
            border-radius: 2px;
            padding: 5px 11px;
            font-size: 0.825rem;
            background: #ffffff;
        }
        .custom-pagination .page-link:hover {
            color: var(--accent);
            border-color: var(--accent);
            background: var(--surface-mist);
        }
        .custom-pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
        }
        
        /* Utilities */
        a { text-decoration: none; }
        .text-primary { color: var(--primary) !important; }
    </style>

</head>
<body class="@yield('body_class')">
    <header class="site-header navbar navbar-expand-lg @yield('header_variant')">
        <div class="container">
            <div class="site-header__inner">
                <a class="site-brand" href="{{ route('client.landing') }}">
                    <img class="site-brand__logo" src="{{ asset('images/logo.png') }}" alt="Logo Du lịch Ninh Bình">
                    <span class="site-brand__name">
                        <span class="site-brand__main">Ninh Bình</span>
                        <span class="site-brand__sub">Travel Hub</span>
                    </span>
                </a>

                <button class="navbar-toggler ms-auto d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavCollapse" aria-controls="siteNavCollapse" aria-expanded="false" aria-label="Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse flex-grow-1" id="siteNavCollapse">
                    <nav class="site-nav" aria-label="Điều hướng chính">
                        <a class="site-nav__link {{ request()->routeIs('client.landing') ? 'is-active' : '' }}" href="{{ route('client.landing') }}">Trang chủ</a>
                        <a class="site-nav__link {{ request()->routeIs('client.about') ? 'is-active' : '' }}" href="{{ route('client.about') }}">Giới thiệu</a>
                        <a class="site-nav__link {{ request()->routeIs('client.events.*') || request()->routeIs('client.news.*') ? 'is-active' : '' }}" href="{{ route('client.events.index') }}">Tin tức</a>
                        <a class="site-nav__link {{ request()->routeIs('client.pano_service') ? 'is-active' : '' }}" href="{{ route('client.pano_service') }}">Dịch vụ</a>
                        <a class="site-nav__link {{ request()->routeIs('home') ? 'is-active' : '' }}" href="{{ route('home') }}">Bản đồ</a>
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
                    <a class="footer-brand mb-2" href="{{ route('client.landing') }}">
                        <img class="footer-brand__logo" src="{{ asset('images/logo.png') }}" alt="" aria-hidden="true">
                        <span>Ninh Bình Travel Hub</span>
                    </a>
                    <p class="footer-tagline mb-0">Cổng thông tin du lịch — khám phá điểm đến, tin tức và sự kiện tại Ninh Bình.</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="footer-heading">Khám phá</div>
                    <ul class="footer-links">
                        <li><a href="{{ route('client.landing') }}">Trang chủ</a></li>
                        <li><a href="{{ route('home') }}">Bản đồ du lịch</a></li>
                        <li><a href="{{ route('client.about') }}">Giới thiệu</a></li>
                        <li><a href="{{ route('client.events.index') }}">Tin tức</a></li>
                        <li><a href="{{ route('client.pano_service') }}">Dịch vụ tour 360</a></li>
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

    <!-- Trip Planner (chatbot chỉ hiện ở trang chủ / bản đồ) -->
    <x-trip-planner-widget />
    <script>
        document.querySelectorAll('form#logout-form, form[action*="logout"]').forEach(function (form) {
            form.addEventListener('submit', function () {
                try {
                    Object.keys(localStorage).forEach(function (k) {
                        if (k === 'biz_wizard_state' || k.indexOf('biz_wizard_state_') === 0) {
                            localStorage.removeItem(k);
                        }
                    });
                } catch (e) {}
                try { indexedDB.deleteDatabase('biz_wizard_db'); } catch (e) {}
            });
        });
    </script>
</body>
</html>
