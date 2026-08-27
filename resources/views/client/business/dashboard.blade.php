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
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f2442;
            --text-body: #475569;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --accent-primary: #1e3a5f;
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg-main);
            color: var(--text-body);
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 0;
        }

        .biz-shell { min-height: 100vh; }

        .main-wrapper { min-width: 0; display: flex; flex-direction: column; min-height: 100vh; }
        .page-wrap {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        .biz-header {
            background: #fff;
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .biz-header__inner {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 0.7rem 1.5rem;
        }
        .biz-header__back {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.78rem;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .biz-header__back:hover { color: var(--text-heading); }
        .biz-header__nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex: 1;
            min-width: 0;
            overflow-x: auto;
        }
        .biz-header__user {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .user-pill { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-heading); }
        .biz-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.42rem 0.75rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 999px;
            font-size: 0.8rem;
            white-space: nowrap;
            transition: background 0.12s, color 0.12s;
        }
        .biz-nav-link:hover { color: var(--text-heading); background: #f8fafc; }
        .biz-nav-link.active {
            color: var(--accent-primary);
            background: #f1f5f9;
            font-weight: 600;
        }
        .biz-nav-link--ghost {
            border: 1px solid var(--border-light);
            background: #fff;
        }
        .biz-nav-link .badge-count {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        .content-area { padding: 1.25rem 0 2rem; flex: 1; }

        /* Header gọn */
        .biz-hero {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .biz-hero__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 1.15rem;
            border-bottom: 1px solid var(--border-light);
            flex-wrap: wrap;
        }
        .biz-hero__name {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-heading);
            margin: 0 0 4px;
        }
        .biz-hero__meta { font-size: 0.78rem; color: var(--text-muted); }
        .biz-hero__actions { display: flex; flex-wrap: wrap; gap: 6px; }
        .biz-hero__stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .biz-stat {
            padding: 0.75rem 1rem;
            border-right: 1px solid var(--border-light);
        }
        .biz-stat:last-child { border-right: none; }
        .biz-stat__val {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-heading);
            font-variant-numeric: tabular-nums;
        }
        .biz-stat__val small { font-size: 0.72rem; font-weight: 400; color: var(--text-muted); }
        .biz-stat__lbl { font-size: 0.7rem; color: var(--text-muted); margin-top: 2px; }

        .btn-minimal {
            font-size: 0.78rem;
            font-weight: 500;
            padding: 0.38rem 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border-light);
            background: #fff;
            color: var(--text-body);
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .btn-minimal:hover { background: #f8fafc; color: var(--text-heading); }
        .btn-minimal[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
            pointer-events: none;
        }
        .btn-minimal-primary {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: #fff;
        }
        .btn-minimal-primary:hover { background: #2b4c7e; color: #fff; }
        .btn-minimal-link {
            background: none;
            border: none;
            color: var(--accent-primary);
            padding: 0;
            font-size: 0.78rem;
            cursor: pointer;
            text-decoration: underline;
        }

        .biz-grid { display: grid; gap: 1rem; }
        .biz-grid--2 { grid-template-columns: 1fr 1fr; }
        .biz-grid--3 { grid-template-columns: 1.4fr 1fr; }
        @media (max-width: 992px) {
            .biz-grid--2, .biz-grid--3 { grid-template-columns: 1fr; }
            .biz-hero__stats { grid-template-columns: repeat(2, 1fr); }
            .biz-stat:nth-child(2) { border-right: none; }
            .biz-stat:nth-child(1), .biz-stat:nth-child(2) { border-bottom: 1px solid var(--border-light); }
        }

        .card-minimal {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }
        .card-header-minimal {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-light);
            font-weight: 600;
            color: var(--text-heading);
            font-size: 0.84rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-body-pad { padding: 0.85rem 1rem; }

        .checklist-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.45rem 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.78rem;
        }
        .checklist-item:last-child { border-bottom: none; }
        .checklist-item__mark { width: 14px; color: var(--text-muted); flex-shrink: 0; }
        .checklist-item__mark.done { color: var(--accent-primary); }
        .checklist-item__text { flex: 1; }
        .checklist-item__link {
            font-size: 0.72rem;
            color: var(--accent-primary);
            text-decoration: none;
        }
        .profile-progress {
            height: 4px;
            background: #f1f5f9;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.65rem;
        }
        .profile-progress__bar {
            height: 100%;
            background: var(--accent-primary);
            border-radius: 2px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.8rem;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row__label { color: var(--text-muted); }
        .info-row__value { color: var(--text-heading); font-weight: 500; text-align: right; word-break: break-word; }
        .description-box {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 0.8rem;
            line-height: 1.55;
            white-space: pre-line;
        }

        #dashboardMap { height: 260px; width: 100%; }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 8px;
            padding: 0.85rem 1rem 1rem;
        }
        .photo-grid-item {
            aspect-ratio: 4/3;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--border-light);
            position: relative;
            background: #f8fafc;
        }
        .photo-grid-item img { width: 100%; height: 100%; object-fit: cover; }
        .photo-grid-item form { position: absolute; top: 4px; right: 4px; margin: 0; }
        .photo-grid-item .del-btn {
            width: 20px; height: 20px;
            border: 1px solid var(--border-light);
            background: #fff;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
            color: var(--text-muted);
            line-height: 1;
        }

        .empty-state {
            text-align: center;
            padding: 1.75rem 1rem;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .empty-state__title { font-weight: 600; color: var(--text-heading); margin-bottom: 4px; }
        .empty-state__desc { margin-bottom: 0.75rem; }

        .tip-text {
            margin: 0 1rem 1rem;
            font-size: 0.74rem;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .contact-preview .info-row { padding: 0.6rem 0; }

        .review-summary {
            display: flex;
            align-items: baseline;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.8rem;
        }
        .review-summary__score { font-size: 1.5rem; font-weight: 600; color: var(--text-heading); }
        .review-stars { color: #c4a574; letter-spacing: 1px; font-size: 0.78rem; }
        .review-stars .is-empty { color: #e2e8f0; }
        .review-summary__stars { font-size: 0.85rem; }
        .review-summary__meta { color: var(--text-muted); }
        .review-card { padding: 0.85rem 1rem; border-bottom: 1px solid #f8fafc; }
        .review-card:last-child { border-bottom: none; }
        .review-reply {
            margin-top: 0.55rem;
            padding: 0.6rem 0.75rem;
            background: #f8fafc;
            border-left: 2px solid var(--border-light);
            font-size: 0.78rem;
        }
        .review-reply__label { font-size: 0.68rem; color: var(--text-muted); margin-bottom: 2px; }
        .review-reply-form { margin-top: 0.55rem; }
        .review-reply-form.is-hidden { display: none; }
        .review-reply-form textarea { min-height: 56px; font-size: 0.8rem; }
        .review-reply-form .btn-row { display: flex; justify-content: flex-end; gap: 6px; margin-top: 6px; }
        .review-card__actions { margin-top: 0.45rem; }

        .form-control {
            font-size: 0.82rem;
            border-color: #cbdbe8;
            border-radius: 6px;
        }
        .form-control:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(30,58,95,0.06);
        }
        .form-label { font-size: 0.78rem; font-weight: 500; color: var(--text-heading); }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        .mini-preview-row {
            display: flex;
            gap: 6px;
            padding: 0 1rem 0.85rem;
            overflow-x: auto;
        }
        .mini-preview-row img {
            width: 52px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--border-light);
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .page-wrap { padding-left: 1rem; padding-right: 1rem; }
            .biz-header__inner {
                flex-wrap: wrap;
                gap: 0.75rem;
                padding: 0.65rem 1rem;
            }
            .biz-header__nav {
                order: 3;
                flex: 1 1 100%;
                padding-bottom: 0.15rem;
            }
            .content-area { padding: 1rem 0 1.5rem; }
        }
    </style>
</head>
<body>
@php
    $commentCount = $comments->count();
    $menuPhotos = $businessProfile->menu_photos ?? [];
    $legacyStorefrontPhotos = $businessProfile->storefront_photos ?? [];
    $galleryPhotos = array_merge($legacyStorefrontPhotos, $menuPhotos);
    $galleryTotal = count($galleryPhotos);
    $heroImage = $location->resolveThumbnailUrl();
    if (!$heroImage && !empty($businessProfile->avatar_photo)) {
        $heroImage = asset('storage/' . ltrim($businessProfile->avatar_photo, '/'));
    }
    if (!$heroImage && $galleryTotal > 0) {
        $heroImage = asset('storage/' . ltrim($galleryPhotos[0], '/'));
    }
    $hasDescription = !empty(trim((string) $businessProfile->description));
    $hasPublicContact = !empty($businessProfile->public_phone) || !empty($businessProfile->zalo) || !empty($businessProfile->facebook);
    $checklistDone = 0;
    $checklistTotal = 4;
    if ($hasDescription) $checklistDone++;
    if ($galleryTotal > 0) $checklistDone++;
    if ($hasPublicContact) $checklistDone++;
    if ($commentCount > 0) $checklistDone++;
    $profilePercent = (int) round(($checklistDone / $checklistTotal) * 100);
@endphp

<div class="biz-shell">
    <div class="main-wrapper">
        <header class="biz-header">
            <div class="biz-header__inner">
                <a href="{{ route('client.profile') }}" class="biz-header__back">← Trang cá nhân</a>
                <nav class="biz-header__nav">
                    <a href="#tab-overview" class="biz-nav-link active" data-tab="tab-overview">Tổng quan</a>
                    <a href="#tab-gallery" class="biz-nav-link" data-tab="tab-gallery">Hình ảnh</a>
                    <a href="#tab-reviews" class="biz-nav-link" data-tab="tab-reviews">
                        Đánh giá@if($commentCount > 0) <span class="badge-count">({{ $commentCount }})</span>@endif
                    </a>
                    <a href="#tab-contact" class="biz-nav-link" data-tab="tab-contact">Liên hệ</a>
                    <a href="{{ route('client.pano_service') }}" class="biz-nav-link biz-nav-link--ghost" target="_blank" rel="noopener">Tour 360</a>
                </nav>
                <div class="biz-header__user">
                    <div class="user-pill">
                        <x-user-avatar :user="Auth::user()" size="28" />
                        <span>{{ Auth::user()->display_name ?? Auth::user()->username }}</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <div class="page-wrap">
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
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Hero --}}
            <div class="biz-hero">
                <div class="biz-hero__top">
                    <div>
                        <h1 class="biz-hero__name">{{ $businessProfile->business_name }}</h1>
                        <div class="biz-hero__meta">
                            {{ $businessProfile->category?->name ?? 'Doanh nghiệp' }}
                            · {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}
                        </div>
                    </div>
                    <div class="biz-hero__actions">
                        @if($location)
                            <a href="{{ route('client.locations.360', $location->slug) }}" target="_blank" class="btn-minimal">Xem trang công khai</a>
                        @endif
                        <button type="button" class="btn-minimal btn-minimal-primary" data-bs-toggle="modal" data-bs-target="#editInfoModal">Sửa mô tả</button>
                        <button type="button" class="btn-minimal" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal" {{ $galleryTotal >= 20 ? 'disabled' : '' }}>Tải ảnh</button>
                    </div>
                </div>
                <div class="biz-hero__stats">
                    <div class="biz-stat">
                        <div class="biz-stat__val">{{ number_format($viewsCount) }}</div>
                        <div class="biz-stat__lbl">Lượt xem</div>
                    </div>
                    <div class="biz-stat">
                        <div class="biz-stat__val">{{ number_format($averageRating, 1) }}<small> /5</small></div>
                        <div class="biz-stat__lbl">Đánh giá TB</div>
                    </div>
                    <div class="biz-stat">
                        <div class="biz-stat__val">{{ number_format($favoritesCount) }}</div>
                        <div class="biz-stat__lbl">Yêu thích</div>
                    </div>
                    <div class="biz-stat">
                        <div class="biz-stat__val">{{ number_format($commentCount) }}</div>
                        <div class="biz-stat__lbl">Nhận xét</div>
                    </div>
                </div>
            </div>

            {{-- Tổng quan --}}
            <div class="tab-pane active" id="tab-overview">
                <div class="biz-grid biz-grid--3">
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        <div class="card-minimal">
                            <div class="card-header-minimal">
                                <span>Thông tin doanh nghiệp</span>
                                <button type="button" class="btn-minimal-link" data-bs-toggle="modal" data-bs-target="#editInfoModal">Sửa</button>
                            </div>
                            <div class="card-body-pad">
                                <div class="info-row">
                                    <span class="info-row__label">SĐT hồ sơ</span>
                                    <span class="info-row__value">{{ $businessProfile->phone ?: '—' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row__label">Website</span>
                                    <span class="info-row__value">
                                        @if($businessProfile->website)
                                            <a href="{{ $businessProfile->website }}" target="_blank" style="color:var(--accent-primary);text-decoration:none;">{{ $businessProfile->website }}</a>
                                        @else <span style="color:#94a3b8;font-weight:400;">Chưa cập nhật</span> @endif
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row__label">Địa chỉ</span>
                                    <span class="info-row__value">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}</span>
                                </div>
                                <div class="pt-2">
                                    <div class="info-row__label mb-1" style="font-size:0.76rem;">Mô tả</div>
                                    <div class="description-box">{{ $businessProfile->description ?? 'Chưa có mô tả. Thêm mô tả giúp khách hiểu rõ hơn về bạn.' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($galleryTotal > 0)
                        <div class="card-minimal">
                            <div class="card-header-minimal">
                                <span>Hình ảnh ({{ $galleryTotal }})</span>
                                <a href="#tab-gallery" class="btn-minimal-link biz-nav-link" data-tab="tab-gallery">Xem tất cả</a>
                            </div>
                            <div class="mini-preview-row">
                                @foreach(array_slice($galleryPhotos, 0, 6) as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="">
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        <div class="card-minimal">
                            <div class="card-header-minimal">
                                <span>Hoàn thiện hồ sơ</span>
                                <span style="font-size:0.72rem;color:var(--text-muted);">{{ $profilePercent }}%</span>
                            </div>
                            <div class="card-body-pad">
                                <div class="profile-progress"><div class="profile-progress__bar" style="width:{{ $profilePercent }}%"></div></div>
                                <div class="checklist-item">
                                    <span class="checklist-item__mark {{ $hasDescription ? 'done' : '' }}">{{ $hasDescription ? '✓' : '○' }}</span>
                                    <span class="checklist-item__text">Viết mô tả doanh nghiệp</span>
                                    @unless($hasDescription)<a href="#" class="checklist-item__link" data-bs-toggle="modal" data-bs-target="#editInfoModal">Thêm</a>@endunless
                                </div>
                                <div class="checklist-item">
                                    <span class="checklist-item__mark {{ $galleryTotal > 0 ? 'done' : '' }}">{{ $galleryTotal > 0 ? '✓' : '○' }}</span>
                                    <span class="checklist-item__text">Tải ít nhất 1 hình ảnh</span>
                                    @if($galleryTotal === 0)<button type="button" class="checklist-item__link" style="border:none;background:none;cursor:pointer;padding:0;" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">Tải</button>@endif
                                </div>
                                <div class="checklist-item">
                                    <span class="checklist-item__mark {{ $hasPublicContact ? 'done' : '' }}">{{ $hasPublicContact ? '✓' : '○' }}</span>
                                    <span class="checklist-item__text">Thêm liên hệ cho khách</span>
                                    @unless($hasPublicContact)<a href="#tab-contact" class="checklist-item__link biz-nav-link" data-tab="tab-contact">Cập nhật</a>@endunless
                                </div>
                                <div class="checklist-item">
                                    <span class="checklist-item__mark {{ $commentCount > 0 ? 'done' : '' }}">{{ $commentCount > 0 ? '✓' : '○' }}</span>
                                    <span class="checklist-item__text">Theo dõi đánh giá khách</span>
                                    @if($commentCount > 0)<a href="#tab-reviews" class="checklist-item__link biz-nav-link" data-tab="tab-reviews">Mở</a>@endif
                                </div>
                            </div>
                        </div>

                        <div class="card-minimal">
                            <div class="card-header-minimal">Vị trí trên bản đồ</div>
                            <div id="dashboardMap"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="tab-pane" id="tab-gallery">
                <div class="card-minimal">
                    <div class="card-header-minimal">
                        <span>Hình ảnh địa điểm ({{ $galleryTotal }})</span>
                        <button type="button" class="btn-minimal btn-minimal-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal" {{ $galleryTotal >= 20 ? 'disabled' : '' }}>Tải ảnh</button>
                    </div>
                    @if($galleryTotal > 0)
                        <div class="photo-grid">
                            @foreach($legacyStorefrontPhotos as $index => $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank"><img src="{{ asset('storage/' . $photo) }}" alt=""></a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="type" value="storefront"><input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="del-btn">×</button>
                                    </form>
                                </div>
                            @endforeach
                            @foreach($menuPhotos as $index => $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank"><img src="{{ asset('storage/' . $photo) }}" alt=""></a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="type" value="menu"><input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="del-btn">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                        <p class="tip-text">Ảnh rõ nét (không gian, món ăn, bảng giá) giúp khách quyết định ghé thăm nhanh hơn.</p>
                    @else
                        <div class="empty-state">
                            <div class="empty-state__title">Chưa có hình ảnh</div>
                            <div class="empty-state__desc">Tải ảnh không gian, món ăn hoặc bảng giá — khách sẽ thấy trên trang địa điểm của bạn.</div>
                            <button type="button" class="btn-minimal btn-minimal-primary mt-2" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal" {{ $galleryTotal >= 20 ? 'disabled' : '' }}>Tải ảnh đầu tiên</button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contact --}}
            <div class="tab-pane" id="tab-contact">
                <div class="biz-grid biz-grid--2">
                    <div class="card-minimal">
                        <div class="card-header-minimal">Cập nhật liên hệ</div>
                        <form action="{{ route('business.update_contact') }}" method="POST" class="card-body-pad">
                            @csrf
                            <p style="font-size:0.76rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.45;">
                                Ba kênh này hiện khi khách mở trang địa điểm trên bản đồ. Khác với SĐT dùng lúc đăng ký duyệt hồ sơ.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control @error('public_phone') is-invalid @enderror" name="public_phone" value="{{ old('public_phone', $businessProfile->public_phone) }}" placeholder="VD: 0912345678" maxlength="30">
                                @error('public_phone')<div class="text-danger mt-1" style="font-size:0.72rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Zalo</label>
                                <input type="text" class="form-control @error('zalo') is-invalid @enderror" name="zalo" value="{{ old('zalo', $businessProfile->zalo) }}" placeholder="Số Zalo hoặc https://zalo.me/..." maxlength="255">
                                @error('zalo')<div class="text-danger mt-1" style="font-size:0.72rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" class="form-control @error('facebook') is-invalid @enderror" name="facebook" value="{{ old('facebook', $businessProfile->facebook) }}" placeholder="https://facebook.com/ten-trang" maxlength="255">
                                @error('facebook')<div class="text-danger mt-1" style="font-size:0.72rem;">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn-minimal btn-minimal-primary">Lưu liên hệ</button>
                        </form>
                    </div>
                    <div class="card-minimal">
                        <div class="card-header-minimal">Khách sẽ thấy</div>
                        <div class="card-body-pad contact-preview">
                            <div class="info-row">
                                <span class="info-row__label">Điện thoại</span>
                                <span class="info-row__value" style="{{ $businessProfile->public_phone ? '' : 'color:#94a3b8;font-weight:400;' }}">{{ $businessProfile->public_phone ?: 'Chưa có' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-row__label">Zalo</span>
                                <span class="info-row__value" style="{{ $businessProfile->zalo ? '' : 'color:#94a3b8;font-weight:400;' }}">{{ $businessProfile->zalo ?: 'Chưa có' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-row__label">Facebook</span>
                                <span class="info-row__value" style="{{ $businessProfile->facebook ? '' : 'color:#94a3b8;font-weight:400;' }}">{{ $businessProfile->facebook ?: 'Chưa có' }}</span>
                            </div>
                            <p class="tip-text" style="margin:0.75rem 0 0;">Facebook chỉ nhận link dạng facebook.com.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reviews --}}
            <div class="tab-pane" id="tab-reviews">
                <div class="card-minimal">
                    <div class="card-header-minimal">Nhận xét từ khách hàng</div>
                    @if($commentCount > 0)
                        <div class="review-summary">
                            <div>
                                <div class="review-summary__score">{{ number_format($averageRating, 1) }}</div>
                                <div class="review-stars review-summary__stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))★@else<span class="is-empty">★</span>@endif
                                    @endfor
                                </div>
                                <div class="review-summary__meta">{{ $commentCount }} nhận xét · {{ $favoritesCount }} lượt lưu</div>
                            </div>
                        </div>
                    @endif
                    @forelse($comments as $comment)
                        @php
                            $bizReply = $comment->replies->firstWhere('user_id', Auth::id()) ?? $comment->replies->first();
                        @endphp
                        <div class="review-card">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <x-user-avatar :user="$comment->user" size="34" />
                                    <div>
                                        <div style="font-size:0.82rem;font-weight:600;color:var(--text-heading);">{{ $comment->user->display_name ?? $comment->user->username }}</div>
                                        <div style="color:var(--text-muted);font-size:0.68rem;">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                                <div class="review-stars">
                                    @for($i = 1; $i <= 5; $i++)@if($i <= ($comment->rating ?? 5))★@else<span class="is-empty">★</span>@endif @endfor
                                </div>
                            </div>
                            <p class="mb-0" style="font-size:0.82rem;color:var(--text-body);">{{ $comment->content }}</p>
                            @if($bizReply)
                                <div class="review-reply">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="review-reply__label">Phản hồi của bạn</div>
                                            <p class="review-reply__text mb-0">{{ $bizReply->content }}</p>
                                        </div>
                                        <div class="d-flex gap-2 flex-shrink-0">
                                            <button type="button" class="btn-minimal-link review-reply-toggle">Sửa</button>
                                            <form action="{{ route('business.delete_reply', $comment) }}" method="POST" onsubmit="return confirm('Thu hồi câu trả lời này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-minimal" style="color:#b91c1c;border-color:#fecaca;font-size:0.72rem;">Thu hồi</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="review-card__actions">
                                    <button type="button" class="btn-minimal-link review-reply-toggle">Trả lời</button>
                                </div>
                            @endif
                            <form action="{{ route('business.reply_comment', $comment) }}" method="POST" class="review-reply-form is-hidden">
                                @csrf
                                <textarea class="form-control" name="content" rows="2" maxlength="1000" placeholder="{{ $bizReply ? 'Sửa câu trả lời...' : 'Viết trả lời cho khách...' }}" required>{{ $bizReply?->content }}</textarea>
                                <div class="btn-row">
                                    <button type="button" class="btn-minimal review-reply-cancel">Hủy</button>
                                    <button type="submit" class="btn-minimal btn-minimal-primary">{{ $bizReply ? 'Cập nhật' : 'Gửi trả lời' }}</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state__title">Chưa có đánh giá</div>
                            <div class="empty-state__desc">Khi khách để lại nhận xét trên trang địa điểm, bạn sẽ thấy và trả lời tại đây.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            </div>
        </main>
    </div>
</div>

{{-- Modal Edit Description --}}
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('business.update_info') }}" method="POST">
            @csrf
            <div class="modal-content border-0" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--border-light);">
                <div class="modal-header px-3 py-2" style="background: #fff; border-bottom: 1px solid var(--border-light);">
                    <h5 class="modal-title" style="color: var(--text-heading); font-size: 0.95rem; font-weight: 600;">Sửa mô tả</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-0">
                        <label class="form-label">Mô tả doanh nghiệp</label>
                        <textarea class="form-control" name="description" rows="5" maxlength="1000" placeholder="Giới thiệu ngắn về cửa hàng của bạn...">{{ $businessProfile->description }}</textarea>
                        <div class="form-text" style="font-size:0.75rem;color:var(--text-muted);">Tối đa 1000 ký tự. Tên, địa chỉ và SĐT hồ sơ không đổi tại đây.</div>
                    </div>
                </div>
                <div class="modal-footer px-3 py-2" style="background: #fff; border-top: 1px solid var(--border-light);">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Lưu mô tả</button>
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
                    <input type="hidden" name="type" value="menu">
                    <div class="mb-0">
                        <label class="form-label">Chọn hình ảnh *</label>
                        <input type="file" class="form-control" name="photo" accept="image/*" required>
                        <div class="form-text" style="font-size:0.75rem;color:var(--text-muted);">Ảnh không gian, món ăn, bảng giá... Tối đa 20 ảnh, mỗi ảnh 20MB. PNG, JPG, JPEG, WEBP.</div>
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
        'tab-gallery': 'Hình ảnh',
        'tab-contact': 'Liên hệ',
        'tab-reviews': 'Đánh giá'
    };
    let dashboardMap = null;

    function showTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(function (pane) {
            pane.classList.toggle('active', pane.id === tabId);
        });
        document.querySelectorAll('.biz-nav-link[data-tab]').forEach(function (link) {
            link.classList.toggle('active', link.getAttribute('data-tab') === tabId);
        });
        if (tabId === 'tab-overview' && dashboardMap) {
            setTimeout(function () { dashboardMap.invalidateSize(); }, 150);
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
    } else if (document.querySelector('.content-area .alert ul')) {
        showTab('tab-contact');
    }

    function openReplyForm(card) {
        const form = card.querySelector('.review-reply-form');
        const reply = card.querySelector('.review-reply');
        const actions = card.querySelector('.review-card__actions');
        if (!form) return;

        form.classList.remove('is-hidden');
        if (reply) reply.style.display = 'none';
        if (actions) actions.style.display = 'none';
        const textarea = form.querySelector('textarea');
        if (textarea) textarea.focus();
    }

    function closeReplyForm(card) {
        const form = card.querySelector('.review-reply-form');
        const reply = card.querySelector('.review-reply');
        const actions = card.querySelector('.review-card__actions');
        if (!form) return;

        form.classList.add('is-hidden');
        if (reply) reply.style.display = '';
        if (actions) actions.style.display = '';
    }

    document.querySelectorAll('.review-reply-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openReplyForm(btn.closest('.review-card'));
        });
    });

    document.querySelectorAll('.review-reply-cancel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeReplyForm(btn.closest('.review-card'));
        });
    });

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

        L.tileLayer(@json(config('services.carto.tile_url')), {
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
