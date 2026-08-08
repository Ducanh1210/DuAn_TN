<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Doanh Nghiệp - {{ $businessProfile->business_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f2442;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --accent-primary: #1e3a5f;
        }

        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-body);
            font-size: 0.85rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            margin: 0;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid var(--border-light);
            padding: 1.5rem 1rem;
            transition: all 0.2s ease;
        }
        .sidebar-brand {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-heading);
            padding: 0 0.75rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            letter-spacing: -0.01em;
        }
        .sidebar-brand-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-primary);
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .sidebar-biz {
            padding: 0 0.75rem;
            margin-bottom: 1.5rem;
        }
        .sidebar-biz__name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-heading);
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .sidebar-biz__meta {
            font-size: 0.7rem;
            color: var(--text-muted);
            line-height: 1.35;
        }
        .sidebar-group-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 500;
            padding: 0 0.75rem;
            margin-top: 1.25rem;
            margin-bottom: 0.4rem;
        }
        .sidebar nav a {
            display: block;
            position: relative;
            padding: 0.5rem 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 0 6px 6px 0;
            font-weight: 400;
            font-size: 0.825rem;
            margin-bottom: 0.1rem;
            transition: all 0.15s ease;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .sidebar nav a:hover {
            color: var(--text-heading);
            background-color: #f1f5f9;
        }
        .sidebar nav a.active {
            color: var(--accent-primary);
            background-color: #f1f5f9;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--accent-primary);
        }
        .sidebar nav a .badge-count {
            float: right;
            margin-top: 1px;
        }

        .navbar-main {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 0.75rem 2rem;
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-heading);
        }
        .main-wrapper {
            width: calc(100% - 240px);
            min-width: 0;
        }
        .content-area {
            padding: 1.5rem 2rem 2rem;
        }
        .page-header-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-heading);
            margin: 0;
        }

        .metric-strip {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 1rem 0;
        }
        .metric-item {
            padding: 0 1.25rem;
            border-right: 1px solid var(--border-light);
        }
        .metric-item:last-child { border-right: none; }
        .metric-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 400;
            margin-bottom: 0.25rem;
        }
        .metric-value {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--text-heading);
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }

        .card-minimal {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }
        .card-header-minimal {
            padding: 0.85rem 1.15rem;
            border-bottom: 1px solid var(--border-light);
            font-weight: 500;
            color: var(--text-heading);
            font-size: 0.875rem;
        }

        .btn-minimal {
            font-size: 0.8rem;
            font-weight: 400;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            background: #ffffff;
            color: var(--text-body);
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .btn-minimal:hover {
            background: #f8fafc;
            color: var(--text-heading);
        }
        .btn-minimal-primary {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: #ffffff;
        }
        .btn-minimal-primary:hover {
            background: #2b4c7e;
            border-color: #2b4c7e;
            color: #ffffff;
        }
        .badge-minimal {
            font-size: 0.725rem;
            font-weight: 500;
            padding: 0.25rem 0.6rem;
            border-radius: 4px;
            background: #f1f5f9;
            color: var(--text-muted);
            display: inline-block;
        }
        .badge-minimal-success {
            background: #f0fdf4;
            color: #166534;
        }
        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            background: #e8eef5;
            color: #1e3a5f;
            border: 1px solid #cbdbe8;
            font-size: 0.65rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            line-height: 1.3;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.825rem;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row__label {
            color: var(--text-muted);
            flex-shrink: 0;
        }
        .info-row__value {
            color: var(--text-heading);
            font-weight: 500;
            text-align: right;
            word-break: break-word;
        }
        .description-box {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 12px 14px;
            color: var(--text-body);
            font-size: 0.825rem;
            line-height: 1.6;
            white-space: pre-line;
        }

        #dashboardMap {
            height: min(420px, 52vh);
            width: 100%;
            border-radius: 0 0 8px 8px;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 1rem 1.15rem;
        }
        .photo-grid-item {
            aspect-ratio: 4/3;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--border-light);
            background: #f8fafc;
            position: relative;
        }
        .photo-grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .empty-photo-box {
            margin: 1rem 1.15rem;
            background: #f8fafc;
            border: 1px dashed var(--border-light);
            border-radius: 6px;
            padding: 18px 14px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .review-card {
            border-bottom: 1px solid #f1f5f9;
            padding: 0.9rem 1.15rem;
        }
        .review-card:last-child { border-bottom: none; }

        .form-control, .form-select {
            font-size: 0.825rem;
            border-color: #cbdbe8;
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            color: var(--text-heading);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.08);
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -240px;
                z-index: 1050;
                box-shadow: 4px 0 24px rgba(15, 36, 66, 0.08);
            }
            .sidebar.show { left: 0; }
            .main-wrapper { width: 100% !important; }
            .content-area { padding: 1rem; }
            .navbar-main { padding: 0.65rem 1rem; }
            .metric-item {
                border-right: none;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
@php
    $commentCount = $comments->count();
@endphp

<div class="d-flex">
    <aside class="sidebar flex-shrink-0" id="sidebar">
        <div class="sidebar-brand">
            <span class="sidebar-brand-dot"></span>
            <span>Portal Doanh Nghiệp</span>
        </div>

        <div class="sidebar-biz">
            <div class="sidebar-biz__name">{{ $businessProfile->business_name }}</div>
            <div class="sidebar-biz__meta">
                {{ $businessProfile->category ? $businessProfile->category->name : 'Doanh nghiệp' }}
                · <span class="badge-minimal badge-minimal-success" style="padding: 0.1rem 0.4rem;">Đã kích hoạt</span>
            </div>
        </div>

        <div class="sidebar-group-title">Quản lý</div>
        <nav>
            <a href="#tab-overview" class="biz-nav-link active" data-tab="tab-overview">Tổng quan & Bản đồ</a>
            <a href="#tab-gallery" class="biz-nav-link" data-tab="tab-gallery">Hình ảnh & Thực đơn</a>
            <a href="#tab-reviews" class="biz-nav-link d-flex justify-content-between align-items-center" data-tab="tab-reviews">
                <span>Đánh giá khách hàng</span>
                @if($commentCount > 0)
                    <span class="badge-count">{{ $commentCount }}</span>
                @endif
            </a>
            <a href="{{ route('client.pano_service') }}" class="biz-nav-link d-flex justify-content-between align-items-center" target="_blank" rel="noopener">
                <span>Dịch vụ tour 360</span>
            </a>
        </nav>

        <div class="sidebar-group-title">Liên kết</div>
        <nav>
            @if($location)
                <a href="{{ route('client.locations.360', $location->slug) }}" target="_blank">Xem trên bản đồ</a>
            @endif
            <a href="{{ route('client.profile') }}">Trang cá nhân</a>
            <a href="{{ url('/') }}">Về bản đồ chính</a>
        </nav>
    </aside>

    <div class="flex-grow-1 main-wrapper">
        <div class="navbar-main d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light d-md-none me-1 border" id="toggleSidebar" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="text-muted" style="font-size: 0.8rem;">Quản trị doanh nghiệp</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="user-pill">
                    <x-user-avatar :user="Auth::user()" size="28" />
                    <span>{{ Auth::user()->display_name ?? Auth::user()->username }}</span>
                </div>
                <a href="{{ route('client.profile') }}" class="btn-minimal">Thoát portal</a>
            </div>
        </div>

        <main class="content-area">
            @if(session('success'))
                <div class="alert border-0 py-2 px-3 mb-3 bg-white border-start border-3 border-success shadow-sm" style="font-size: 0.8rem; color: #166534;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert border-0 py-2 px-3 mb-3 bg-white border-start border-3 border-danger shadow-sm" style="font-size: 0.8rem; color: #991b1b;">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert border-0 py-2 px-3 mb-3 bg-white border-start border-3 border-danger shadow-sm" style="font-size: 0.8rem; color: #991b1b;">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                <div>
                    <h1 class="page-header-title" id="pageSectionTitle">Tổng quan</h1>
                    <p class="text-muted mb-0" style="font-size:0.8rem;">
                        {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if($location)
                        <a href="{{ route('client.locations.360', $location->slug) }}" target="_blank" class="btn-minimal">
                            Xem bản đồ
                        </a>
                    @endif
                    <button type="button" class="btn-minimal btn-minimal-primary" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                        Chỉnh sửa thông tin
                    </button>
                </div>
            </div>

            <div class="metric-strip mb-3" id="bizMetricStrip">
                <div class="row g-0 align-items-center">
                    <div class="col-6 col-md-3 metric-item">
                        <div class="metric-label">Lượt xem địa điểm</div>
                        <div class="metric-value">{{ number_format($viewsCount) }}</div>
                    </div>
                    <div class="col-6 col-md-3 metric-item">
                        <div class="metric-label">Đánh giá trung bình</div>
                        <div class="metric-value">{{ number_format($averageRating, 1) }} <span style="font-size:0.8rem;color:var(--text-muted);font-weight:400;">/ 5</span></div>
                    </div>
                    <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
                        <div class="metric-label">Lượt lưu yêu thích</div>
                        <div class="metric-value">{{ number_format($favoritesCount) }}</div>
                    </div>
                    <div class="col-6 col-md-3 metric-item mt-3 mt-md-0">
                        <div class="metric-label">Tổng nhận xét</div>
                        <div class="metric-value">{{ number_format($commentCount) }}</div>
                    </div>
                </div>
            </div>

            {{-- Tổng quan --}}
            <div class="tab-pane active" id="tab-overview">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="card-minimal h-100">
                            <div class="card-header-minimal d-flex justify-content-between align-items-center">
                                <span>Thông tin doanh nghiệp</span>
                                <button type="button" class="btn-minimal" data-bs-toggle="modal" data-bs-target="#editInfoModal">Sửa</button>
                            </div>
                            <div class="px-3 py-2">
                                <div class="info-row">
                                    <span class="info-row__label">Điện thoại</span>
                                    <span class="info-row__value">{{ $businessProfile->phone }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row__label">Website</span>
                                    <span class="info-row__value">
                                        @if($businessProfile->website)
                                            <a href="{{ $businessProfile->website }}" target="_blank" style="color: var(--accent-primary); text-decoration: none;">{{ $businessProfile->website }}</a>
                                        @else
                                            <span class="text-muted" style="font-weight:400;">Chưa cập nhật</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row__label">Địa chỉ</span>
                                    <span class="info-row__value">
                                        {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                                    </span>
                                </div>
                                <div class="pt-2 pb-2">
                                    <div class="info-row__label mb-1">Mô tả</div>
                                    <div class="description-box">
                                        {{ $businessProfile->description ?? 'Chưa có mô tả nào được thêm.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card-minimal">
                            <div class="card-header-minimal">Vị trí trên bản đồ</div>
                            <div id="dashboardMap"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="tab-pane" id="tab-gallery">
                <div class="card-minimal mb-3">
                    <div class="card-header-minimal d-flex justify-content-between align-items-center">
                        <span>Ảnh mặt tiền ({{ count($businessProfile->storefront_photos ?? []) }})</span>
                        <button type="button" class="btn-minimal btn-minimal-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">Tải ảnh lên</button>
                    </div>
                    @if(!empty($businessProfile->storefront_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->storefront_photos as $index => $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Mặt tiền">
                                    </a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="storefront">
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-light border py-0 px-1" style="font-size: 0.7rem;">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-photo-box">Chưa có ảnh mặt tiền nào được tải lên.</div>
                    @endif
                </div>

                <div class="card-minimal">
                    <div class="card-header-minimal">
                        Ảnh thực đơn & bảng giá ({{ count($businessProfile->menu_photos ?? []) }})
                    </div>
                    @if(!empty($businessProfile->menu_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->menu_photos as $index => $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Thực đơn">
                                    </a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="menu">
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-light border py-0 px-1" style="font-size: 0.7rem;">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-photo-box">Chưa có ảnh thực đơn nào được tải lên.</div>
                    @endif
                </div>
            </div>

            {{-- Reviews --}}
            <div class="tab-pane" id="tab-reviews">
                <div class="card-minimal">
                    <div class="card-header-minimal">Nhận xét từ khách hàng</div>
                    @forelse($comments as $comment)
                        <div class="review-card">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <x-user-avatar :user="$comment->user" size="32" />
                                    <div>
                                        <div style="color: var(--text-heading); font-size: 0.825rem; font-weight: 500;">
                                            {{ $comment->user->display_name ?? $comment->user->username }}
                                        </div>
                                        <div style="color: var(--text-muted); font-size: 0.7rem;">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div style="color: #d97706; font-size: 0.75rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= ($comment->rating ?? 5)) ★ @else <span style="color: #e2e8f0;">★</span> @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="mb-0 mt-1" style="color: var(--text-body); font-size: 0.825rem;">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <div class="empty-photo-box">Chưa có nhận xét nào từ khách hàng.</div>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>

{{-- Modal Edit Info --}}
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('business.update_info') }}" method="POST">
            @csrf
            <div class="modal-content border-0" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--border-light);">
                <div class="modal-header px-3 py-2" style="background: #fff; border-bottom: 1px solid var(--border-light);">
                    <h5 class="modal-title" style="color: var(--text-heading); font-size: 0.95rem; font-weight: 600;">Cập nhật thông tin doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label">Tên doanh nghiệp *</label>
                        <input type="text" class="form-control" name="business_name" value="{{ $businessProfile->business_name }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ *</label>
                            <input type="text" class="form-control" name="phone" value="{{ $businessProfile->phone }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trang web (Website)</label>
                            <input type="url" class="form-control" name="website" value="{{ $businessProfile->website }}" placeholder="https://">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Đường / Số nhà *</label>
                            <input type="text" class="form-control" name="address_street" value="{{ $businessProfile->address_street }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thành phố / Huyện *</label>
                            <input type="text" class="form-control" name="address_city" value="{{ $businessProfile->address_city }}" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Mô tả doanh nghiệp</label>
                        <textarea class="form-control" name="description" rows="4">{{ $businessProfile->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer px-3 py-2" style="background: #fff; border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Upload Photo --}}
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('business.upload_photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--border-light);">
                <div class="modal-header px-3 py-2" style="background: #fff; border-bottom: 1px solid var(--border-light);">
                    <h5 class="modal-title" style="color: var(--text-heading); font-size: 0.95rem; font-weight: 600;">Tải ảnh mới lên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label">Loại hình ảnh *</label>
                        <select class="form-select" name="type" required>
                            <option value="storefront">Mặt tiền cửa hàng</option>
                            <option value="menu">Thực đơn / Bảng giá / Dịch vụ</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Chọn hình ảnh *</label>
                        <input type="file" class="form-control" name="photo" accept="image/*" required>
                        <div class="form-text" style="font-size:0.75rem;color:var(--text-muted);">Tối đa 5MB. PNG, JPG, JPEG, WEBP.</div>
                    </div>
                </div>
                <div class="modal-footer px-3 py-2" style="background: #fff; border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Tải lên</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titles = {
        'tab-overview': 'Tổng quan',
        'tab-gallery': 'Hình ảnh & Thực đơn',
        'tab-reviews': 'Đánh giá khách hàng'
    };
    const titleEl = document.getElementById('pageSectionTitle');
    let dashboardMap = null;

    function showTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(function (pane) {
            pane.classList.toggle('active', pane.id === tabId);
        });
        document.querySelectorAll('.biz-nav-link').forEach(function (link) {
            link.classList.toggle('active', link.getAttribute('data-tab') === tabId);
        });
        if (titleEl && titles[tabId]) titleEl.textContent = titles[tabId];
        if (tabId === 'tab-overview' && dashboardMap) {
            setTimeout(function () { dashboardMap.invalidateSize(); }, 150);
        }
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.remove('show');
        }
        if (history.replaceState) {
            history.replaceState(null, '', '#' + tabId);
        }
    }

    document.querySelectorAll('.biz-nav-link[data-tab]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            showTab(link.getAttribute('data-tab'));
        });
    });

    // Mở tab từ hash (#tab-pano)
    const initialHash = (window.location.hash || '').replace(/^#/, '');
    if (initialHash && titles[initialHash]) {
        showTab(initialHash);
    }

    const toggleBtn = document.getElementById('toggleSidebar');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });
    }

    function initDashboardMap() {
        const mapEl = document.getElementById('dashboardMap');
        if (!mapEl || dashboardMap) return;
        const lat = parseFloat("{{ $businessProfile->lat }}");
        const lng = parseFloat("{{ $businessProfile->lng }}");
        if (isNaN(lat) || isNaN(lng)) return;

        dashboardMap = L.map('dashboardMap', {
            zoomControl: true,
            attributionControl: false
        }).setView([lat, lng], 15);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(dashboardMap);

        fetch('{{ asset('geo/ha-nam-old.geojson') }}')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                L.geoJSON(data, {
                    style: {
                        color: '#cbdbe8',
                        weight: 2,
                        opacity: 0.55,
                        fillColor: '#f8fafc',
                        fillOpacity: 0.04
                    }
                }).addTo(dashboardMap);
            })
            .catch(function () {});

        L.marker([lat, lng]).addTo(dashboardMap).bindPopup(
            '<div style="font-family:inherit;font-size:0.85rem;"><strong style="color:#1e3a5f;">{{ $businessProfile->business_name }}</strong><br><span style="color:#64748b;">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}</span></div>'
        ).openPopup();
    }

    initDashboardMap();

    const hash = (location.hash || '').replace('#', '');
    if (hash && titles[hash]) showTab(hash);
});
</script>
</body>
</html>
