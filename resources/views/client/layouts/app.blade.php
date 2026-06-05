<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cổng Thông Tin Du Lịch Hà Nam')</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0066ff;
            --primary-hover: #0052cc;
            --bg-body: #f4f7fb;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-dark); 
            -webkit-font-smoothing: antialiased;
        }
        /* Navbar */
        .navbar { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(16px); 
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05); 
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            padding: 15px 0;
            transition: all 0.3s ease;
        }
        .navbar-brand { 
            font-weight: 800; 
            font-size: 1.5rem;
            background: linear-gradient(135deg, #0066ff, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        .nav-link { 
            font-weight: 600; 
            color: var(--text-muted); 
            padding: 8px 16px !important;
            margin: 0 4px;
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
            border-radius: 20px; 
            background: #fff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04); 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
            overflow: hidden; 
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-modern:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); 
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
            transform: scale(1.05);
        }
        
        /* Badges */
        .badge-glass { 
            position: absolute; 
            top: 16px; 
            left: 16px; 
            background: rgba(255,255,255,0.85); 
            color: var(--text-dark); 
            padding: 6px 14px; 
            border-radius: 30px; 
            font-size: 0.75rem; 
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
            padding: 60px 0 30px; 
            margin-top: 80px; 
            font-size: 0.95rem;
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
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer>
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} Cổng Thông Tin Du Lịch Hà Nam. Thiết kế bằng <i class="fa-solid fa-heart text-danger"></i></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
