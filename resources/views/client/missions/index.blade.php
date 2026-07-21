@extends('client.layouts.app')

@section('title', 'Trung Tâm Nhiệm Vụ & Đổi Thưởng - Hà Nam POI')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
<style>
    :root {
        --q-bg: #f8fafc;
        --q-card-bg: #ffffff;
        --q-primary: #6366f1;
        --q-primary-hover: #4f46e5;
        --q-primary-light: #eeeffe;
        --q-accent-orange: #f59e0b;
        --q-text-main: #0f172a;
        --q-text-sub: #64748b;
        --q-border: #e2e8f0;
    }

    /* Hide global app header and footer */
    body > nav.navbar,
    body > footer {
        display: none !important;
    }

    body {
        background-color: var(--q-bg);
        font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        color: var(--q-text-main);
        margin: 0;
        padding: 0;
    }

    /* Full Width Top Header Bar */
    .reward-top-bar-full {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 10px 32px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .reward-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .reward-brand-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        box-shadow: none;
    }

    .reward-brand-text {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .reward-brand-sub {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
    }

    .reward-top-menu {
        display: flex;
        align-items: center;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .reward-top-menu .nav-link {
        font-weight: 600;
        font-size: 0.88rem;
        color: #475569 !important;
        padding: 8px 18px !important;
        border-radius: 0 !important;
        transition: all 0.2s ease;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        cursor: pointer;
    }

    .reward-top-menu .nav-link:hover {
        color: #6366f1 !important;
    }

    .reward-top-menu .nav-link.active {
        color: #6366f1 !important;
        font-weight: 700 !important;
        border-bottom: 3px solid #6366f1 !important;
    }

    .user-point-capsule {
        background: #fffbe6;
        border: 1px solid #fde68a;
        padding: 5px 14px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: #d97706;
        font-size: 0.85rem;
    }

    /* Main Container */
    .reward-full-container {
        width: 100%;
        padding: 20px 32px 40px;
    }

    /* Left Sidebar Cards */
    .reward-sidebar-compact {
        background: #ffffff;
        border-radius: 16px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }

    .sidebar-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        padding: 4px 10px 10px;
        margin-bottom: 4px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .reward-cat-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #475569 !important;
        background: transparent !important;
        border: none !important;
        text-align: left;
        transition: all 0.2s ease;
        margin-bottom: 4px;
        cursor: pointer !important;
        outline: none !important;
    }

    .reward-cat-btn:hover {
        background: #eeeffe !important;
        color: #6366f1 !important;
    }

    .reward-cat-btn.active {
        background: #6366f1 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3) !important;
    }

    .reward-cat-btn.active i,
    .reward-cat-btn.active span {
        color: #ffffff !important;
    }

    /* Hero Banner (Shop Tab) */
    .reward-hero-compact {
        background: linear-gradient(135deg, #f5f3ff 0%, #eeeffe 50%, #e0e7ff 100%);
        border: 1px solid #c7d2fe;
        border-radius: 18px;
        padding: 22px 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .hero-title-compact {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 6px;
    }

    .hero-sub-compact {
        font-size: 0.85rem;
        color: #475569;
        margin-bottom: 16px;
    }

    .btn-hero-compact {
        background: #6366f1;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.84rem;
        padding: 8px 22px;
        border-radius: 20px;
        border: none;
        box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
        transition: all 0.2s ease;
    }

    .btn-hero-compact:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    /* Top Assurance Bar */
    .top-assurance-compact {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px 18px;
        margin-bottom: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.01);
    }

    .assurance-unit {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .assurance-icon-compact {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #eeeffe;
        color: #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        border: 1px solid #c7d2fe;
    }

    .assurance-title-compact {
        font-weight: 700;
        font-size: 0.82rem;
        color: #0f172a;
    }

    .assurance-sub-compact {
        font-size: 0.72rem;
        color: #64748b;
    }

    /* Gift / Frame Card Grid */
    .item-card-compact {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .item-card-compact:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.1);
        border-color: #c7d2fe;
    }

    .item-img-compact {
        height: 90px;
        background: #f8fafc;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        padding: 8px;
        border: 1px solid #f1f5f9;
    }

    .item-name-compact {
        font-size: 0.86rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .item-desc-compact {
        font-size: 0.76rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .item-price-compact {
        font-size: 0.84rem;
        font-weight: 800;
        color: #d97706;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .btn-action-compact {
        background: #6366f1;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 10px;
        padding: 7px;
        border: none;
        width: 100%;
        transition: all 0.2s;
        box-shadow: 0 3px 8px rgba(99, 102, 241, 0.25);
    }

    .btn-action-compact:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    .btn-outline-compact {
        border: 1px solid #6366f1;
        color: #6366f1;
        background: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 10px;
        padding: 7px;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-outline-compact:hover {
        background: #6366f1;
        color: #ffffff;
    }

    /* Quests Tab Layout Specific Styles */
    .main-section-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .quest-filter-pill {
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quest-filter-pill.active {
        background: #6366f1;
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
    }

    .quest-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .quest-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .bg-icon-purple { background: #eeeffe; color: #6366f1; }
    .bg-icon-green { background: #dcfce7; color: #10b981; }
    .bg-icon-blue { background: #e0f2fe; color: #0284c7; }
    .bg-icon-orange { background: #ffedd5; color: #f97316; }

    .quest-reward-tag {
        font-size: 0.82rem;
        font-weight: 700;
        color: #d97706;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-quest-action {
        background: #eeeffe;
        color: #6366f1;
        font-weight: 700;
        font-size: 0.82rem;
        border-radius: 10px;
        padding: 7px 18px;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-quest-action:hover {
        background: #6366f1;
        color: #ffffff;
    }

    .btn-quest-claim {
        background: #6366f1;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        border-radius: 10px;
        padding: 7px 18px;
        border: none;
        box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
    }

    .btn-quest-claim:hover {
        background: #4f46e5;
        color: #ffffff;
    }

    .widget-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
    }

    .streak-day-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 6px 2px;
        text-align: center;
        flex: 1;
        min-width: 0;
    }

    .streak-day-box.completed {
        background: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
        opacity: 0.55 !important;
    }

    .streak-day-box.current {
        background: #fffbe6;
        border-color: #fde68a;
        color: #d97706;
        box-shadow: 0 0 0 2px #f59e0b;
    }

    /* Muted disabled check-in button */
    #btnDailyCheckinSide:disabled,
    #btnDailyCheckinShop:disabled,
    .btn-indigo:disabled {
        background: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
        color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.85;
    }

    .circle-progress-box {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 4px solid #6366f1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        color: #6366f1;
        flex-shrink: 0;
    }

    .leaderboard-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .leaderboard-row.highlight {
        background: #eeeffe;
        font-weight: 700;
    }

    .rank-badge {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
        color: #fff;
    }

    .rank-1 { background: #f59e0b; }
    .rank-2 { background: #94a3b8; }
    .rank-3 { background: #d97706; }
    .rank-other { background: #e2e8f0; color: #64748b; }

    .promo-banner-card {
        background: linear-gradient(135deg, #fce7f3, #fae8ff);
        border: 1px solid #f5d0fe;
        border-radius: 16px;
        padding: 16px;
    }

    /* TIẾN ĐỘ NHIỆM VỤ Widget (Cream Theme matching image) */
    .quest-progress-card-cream {
        background: #fffdfa;
        border: 1px solid #fef3c7;
        border-radius: 22px;
        padding: 20px;
        box-shadow: 0 4px 18px rgba(217, 119, 6, 0.05);
        margin-bottom: 20px;
    }

    .quest-milestone-track-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 10px 28px 10px;
        margin-bottom: 18px;
    }

    .quest-milestone-line-bg {
        position: absolute;
        top: 32px;
        left: 45px;
        right: 40px;
        height: 4px;
        background: #e2e8f0;
        border-radius: 4px;
        transform: translateY(-50%);
        z-index: 1;
    }

    .quest-milestone-line-fill {
        height: 100%;
        background: linear-gradient(90deg, #7c3aed, #9333ea);
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .quest-coin-badge {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #fef08a, #f59e0b, #d97706);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        z-index: 2;
        border: 2px solid #ffffff;
    }

    .milestone-node {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 44px;
    }

    .node-top-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 3px;
    }

    .node-icon-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #7c3aed;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
    }

    .node-icon-gift {
        font-size: 20px;
        color: #c4b5fd;
    }

    .node-icon-gift-big {
        font-size: 28px;
        color: #ef4444;
        filter: drop-shadow(0 4px 8px rgba(239, 68, 68, 0.3));
    }

    .node-bottom-val {
        position: absolute;
        top: 100%;
        margin-top: 4px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #1e1b4b;
        white-space: nowrap;
    }

    .quest-sub-item-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 10px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .quest-sub-item-card:hover {
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.08);
        border-color: #ddd6fe;
    }

    .btn-outline-purple {
        border: 1.5px solid #8b5cf6;
        color: #7c3aed;
        background: transparent;
        border-radius: 20px;
        padding: 4px 16px;
        font-weight: 700;
        font-size: 0.78rem;
        transition: all 0.2s ease;
    }

    .btn-outline-purple:hover {
        background: #7c3aed;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<!-- Full Width Header Navigation Bar -->
<div class="reward-top-bar-full">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a href="{{ url('/') }}" class="reward-brand" title="Quay về trang chủ Bản đồ">
            <div class="reward-brand-icon">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <div class="reward-brand-text">ĐỔI THƯỞNG</div>
                <div class="reward-brand-sub">Nhận quà cực dễ</div>
            </div>
        </a>

        <!-- Menu Tabs -->
        <ul class="reward-top-menu nav" id="rewardTopNav" role="tablist">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
            </li>
            <li class="nav-item">
                <button class="nav-link active" id="nav-shop-tab" data-bs-toggle="pill" data-bs-target="#shop-pane" type="button" role="tab" onclick="switchNavTab('shop-pane')">
                    Đổi thưởng
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="nav-quests-tab" data-bs-toggle="pill" data-bs-target="#quests-pane" type="button" role="tab" onclick="switchNavTab('quests-pane')">
                    Nhiệm vụ
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="nav-inventory-tab" data-bs-toggle="pill" data-bs-target="#inventory-pane" type="button" role="tab" onclick="switchNavTab('inventory-pane')">
                    Tủ quà của tôi
                </button>
            </li>
        </ul>

        <!-- Points Capsule & Avatar -->
        <div class="d-flex align-items-center gap-3">
            <div class="user-point-capsule">
                <i class="fa-solid fa-coins text-warning fs-6"></i>
                <span id="headerUserPoints">{{ number_format($user->points) }}</span> xu
            </div>

            <div class="position-relative">
                <i class="fa-solid fa-bell text-muted fs-5 cursor-pointer"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">3</span>
            </div>

            <div class="avatar-frame-wrapper {{ $user->equippedFrame ? $user->equippedFrame->css_style : '' }}" style="width: 34px; height: 34px;">
                <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="reward-full-container">
    <div class="tab-content" id="mainTabContent">
        
        <!-- TAB 1: ĐỔI THƯỞNG (SHOP PANE - MATCHED INDIGO COLOR SCHEME) -->
        <div class="tab-pane fade show active" id="shop-pane" role="tabpanel">
            <div class="row g-3">
                <!-- LEFT COLUMN: Danh Mục Phần Thưởng Sidebar -->
                <div class="col-lg-3 col-xl-2">
                    <div class="reward-sidebar-compact">
                        <div class="sidebar-title">Danh mục phần thưởng</div>
                        <div class="d-flex flex-column" id="rewardCatList">
                            <button class="reward-cat-btn active" onclick="filterCategory('all', this)" type="button">
                                <i class="fa-solid fa-gift"></i>
                                <span>Tất cả quà</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('voucher', this)" type="button">
                                <i class="fa-solid fa-ticket"></i>
                                <span>Voucher &amp; Thẻ nạp</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('badge', this)" type="button">
                                <i class="fa-solid fa-medal"></i>
                                <span>Huy hiệu &amp; Vật phẩm</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('exclusive', this)" type="button">
                                <i class="fa-solid fa-crown"></i>
                                <span>Quà độc quyền</span>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Card -->
                    <div class="reward-sidebar-compact mt-2 text-center" style="background: linear-gradient(135deg, #fff7ed, #ffedd5); padding: 14px 10px;">
                        <div class="fs-4 text-warning mb-1"><i class="fa-solid fa-gift"></i></div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">Giới thiệu bạn bè</h6>
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">Nhận ngay <strong>2.000 xu</strong> cho mỗi lượt mời!</p>
                        <button class="btn btn-indigo btn-sm w-100 fw-bold rounded-pill text-white shadow-sm" onclick="navigator.clipboard.writeText(window.location.origin); alert('Đã sao chép liên kết!');" style="background: #6366f1; font-size: 0.75rem; padding: 4px 10px;">
                            Giới thiệu ngay
                        </button>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Shop Banner, Assurance & Gift Grid -->
                <div class="col-lg-9 col-xl-10">
                    <!-- 1. Hero Highlight Banner -->
                    <div class="reward-hero-compact">
                        <div class="row align-items-center">
                            <div class="col-lg-8 mb-2 mb-lg-0">
                                <h2 class="hero-title-compact">Đổi xu nhận quà<br>Đơn giản &amp; nhanh chóng</h2>
                                <p class="hero-sub-compact">Hàng ngàn phần quà hấp dẫn đang chờ bạn</p>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="btnDailyCheckinShop" class="btn btn-hero-compact" @if($user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday()) disabled @endif>
                                        @if($user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday())
                                            Đã xu danh hôm nay
                                        @else
                                            Đổi quà ngay (+10 xu)
                                        @endif
                                    </button>
                                    <span class="badge bg-white text-indigo fw-bold px-2.5 py-1.5 rounded-pill shadow-sm border border-indigo border-opacity-25" style="color: #6366f1; font-size: 0.75rem;">
                                        Chuỗi <span id="headerStreakCount">{{ $user->streak_count ?? 0 }}</span> ngày
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-4 text-center text-lg-end">
                                <div class="p-2 bg-white bg-opacity-80 rounded-3 d-inline-block border border-indigo border-opacity-25 shadow-sm text-center">
                                    <div class="avatar-frame-wrapper {{ $user->equippedFrame ? $user->equippedFrame->css_style : '' }} mx-auto mb-1" style="width: 60px; height: 60px;">
                                        <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 0.78rem;">{{ $user->display_name ?? $user->username }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        {{ $user->equippedFrame ? $user->equippedFrame->name : 'Khung mặc định' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Top Assurance Row -->
                    <div class="top-assurance-compact">
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">100% Chính hãng</div>
                                        <div class="assurance-sub-compact">Xác thực nguồn gốc</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Đổi nhanh trong 1 phút</div>
                                        <div class="assurance-sub-compact">Xử lý tự động</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-user-shield"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">An toàn bảo mật</div>
                                        <div class="assurance-sub-compact">Tuyệt đối bảo mật</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-headset"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Hỗ trợ 24/7</div>
                                        <div class="assurance-sub-compact">Sẵn sàng hỗ trợ</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fw-bold text-dark fs-5" id="shopSectionTitle">Gợi ý cho bạn</div>
                        <a href="#" class="text-muted small fw-semibold text-decoration-none" onclick="return false;">Xem tất cả <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.7rem;"></i></a>
                    </div>

                    <!-- Empty Category Message -->
                    <div id="emptyCatMessage" class="col-12 text-center py-5 bg-white rounded-3 border d-none">
                        <i class="fa-solid fa-box-open text-muted fs-1 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Chưa có phần thưởng trong mục này</h6>
                        <p class="text-muted small mb-0">Các phần quà mới sẽ sớm được cập nhật!</p>
                    </div>

                    <!-- Gift Cards Grid -->
                    <div class="row g-3" id="shopItemGrid">

                        <!-- Demo Voucher Card -->
                        <div class="col-6 col-md-4 col-lg-3 reward-item-wrapper" data-category="voucher">
                            <div class="item-card-compact">
                                <div>
                                    <div class="item-img-compact bg-light">
                                        <i class="fa-solid fa-ticket text-warning fs-1"></i>
                                    </div>
                                    <h6 class="item-name-compact text-truncate">Voucher Giảm 50K</h6>
                                    <div class="item-desc-compact text-truncate">Áp dụng cho mọi địa xu ăn uống</div>
                                </div>
                                <div>
                                    <div class="item-price-compact">
                                        <i class="fa-solid fa-coins text-warning"></i>
                                        <span>5.000 xu</span>
                                    </div>
                                    <button class="btn btn-action-compact" onclick="alert('Tính năng đổi voucher sẽ ra mắt trong phiên bản sắp tới!');">
                                        Đổi ngay
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Badge Item -->
                        <div class="col-6 col-md-4 col-lg-3 reward-item-wrapper" data-category="badge">
                            <div class="item-card-compact">
                                <div>
                                    <div class="item-img-compact bg-light">
                                        <i class="fa-solid fa-medal text-danger fs-1"></i>
                                    </div>
                                    <h6 class="item-name-compact text-truncate">Huy Hiệu Nhà Khám Phá</h6>
                                    <div class="item-desc-compact text-truncate">Huy hiệu VIP trên trang cá nhân</div>
                                </div>
                                <div>
                                    <div class="item-price-compact">
                                        <i class="fa-solid fa-coins text-warning"></i>
                                        <span>3.000 xu</span>
                                    </div>
                                    <button class="btn btn-action-compact" onclick="alert('Huy hiệu sẽ được tự động mở khóa khi đạt mốc thành tựu!');">
                                        Nhận huy hiệu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Assurance Row -->
                    <div class="bottom-assurance-compact">
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-gift"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Nhiều quà hấp dẫn</div>
                                        <div class="assurance-sub-compact">Cập nhật thường xuyên</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-clipboard-check"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Đổi thưởng dễ dàng</div>
                                        <div class="assurance-sub-compact">Thao tác đơn giản</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-truck-fast"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Giao quà nhanh chóng</div>
                                        <div class="assurance-sub-compact">Nhận quà tức thì</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="assurance-unit">
                                    <div class="assurance-icon-compact"><i class="fa-solid fa-shield-check"></i></div>
                                    <div>
                                        <div class="assurance-title-compact">Cam kết uy tín</div>
                                        <div class="assurance-sub-compact">Tuyệt đối bảo mật</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: NHIỆM VỤ (QUESTS PANE - REFERENCED QUEST LAYOUT) -->
        <div class="tab-pane fade" id="quests-pane" role="tabpanel">
            <div class="row g-3">
                <!-- LEFT COLUMN: Menu Sidebar -->
                <div class="col-lg-3 col-xl-2">
                    <div class="reward-sidebar-compact">
                        <div class="sidebar-title">Menu Nhiệm vụ</div>
                        <div class="d-flex flex-column">
                            <button class="reward-cat-btn active" type="button">
                                <i class="fa-solid fa-bullseye text-warning"></i>
                                <span>Nhiệm vụ</span>
                            </button>
                            <button class="reward-cat-btn" onclick="switchNavTab('shop-pane')" type="button">
                                <i class="fa-solid fa-gift text-indigo"></i>
                                <span>Đổi thưởng</span>
                            </button>
                            <button class="reward-cat-btn" onclick="switchNavTab('inventory-pane')" type="button">
                                <i class="fa-solid fa-box-archive text-primary"></i>
                                <span>Tủ quà của tôi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Widget -->
                    <div class="reward-sidebar-compact mt-2 text-center" style="background: linear-gradient(135deg, #fff7ed, #ffedd5); padding: 14px 10px;">
                        <div class="fs-4 text-warning mb-1"><i class="fa-solid fa-gift"></i></div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">Mời bạn bè</h6>
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">Nhận ngay <strong>2.000 xu</strong> cho mỗi lượt mời!</p>
                        <button class="btn btn-indigo btn-sm w-100 fw-bold rounded-pill text-white shadow-sm" onclick="navigator.clipboard.writeText(window.location.origin); alert('Đã sao chép liên kết mời!');" style="background: #6366f1; font-size: 0.75rem; padding: 4px 10px;">
                            Mời ngay
                        </button>
                    </div>
                </div>

                <!-- CENTER COLUMN: Quest List -->
                <div class="col-lg-5 col-xl-6">
                    @php
                        $todayPointsEarned = \App\Models\PointTransaction::where('user_id', Auth::id())
                            ->where('amount', '>', 0)
                            ->whereDate('created_at', \Carbon\Carbon::today())
                            ->sum('amount');
                        $milestonePercent = min(100, max(0, $todayPointsEarned));
                    @endphp

                    <!-- TIẾN ĐỘ NHIỆM VỤ Widget (Cream Theme) -->
                    <div class="quest-progress-card-cream">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-extrabold text-dark mb-0" style="font-size: 0.88rem;">
                                <i class="fa-solid fa-trophy text-warning me-1.5"></i> Tiến độ tích xu hôm nay: <span class="text-indigo fw-black">{{ $todayPointsEarned }}/100 xu</span>
                            </h6>
                            <span class="badge bg-warning-subtle text-warning fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-bolt me-1"></i> Mốc 100 xu nhận Hộp Quà
                            </span>
                        </div>

                        <div class="quest-milestone-track-wrapper">
                            <div class="quest-milestone-line-bg">
                                <div class="quest-milestone-line-fill" style="width: {{ $milestonePercent }}%;"></div>
                            </div>

                            <!-- Start Coin -->
                            <div class="quest-coin-badge" title="Tích xu hôm nay">
                                <i class="fa-solid fa-star"></i>
                            </div>

                            <!-- Node 25 -->
                            <div class="milestone-node">
                                <div class="node-icon-circle {{ $todayPointsEarned >= 25 ? 'bg-indigo text-white' : 'bg-secondary bg-opacity-25 text-muted' }}">
                                    @if($todayPointsEarned >= 25)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        <span style="font-size: 0.65rem;">25</span>
                                    @endif
                                </div>
                                <div class="node-bottom-val {{ $todayPointsEarned >= 25 ? 'text-indigo' : 'text-muted' }}">25</div>
                            </div>

                            <!-- Node 50 -->
                            <div class="milestone-node">
                                <div class="node-icon-circle {{ $todayPointsEarned >= 50 ? 'bg-indigo text-white' : 'bg-secondary bg-opacity-25 text-muted' }}">
                                    @if($todayPointsEarned >= 50)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        <span style="font-size: 0.65rem;">50</span>
                                    @endif
                                </div>
                                <div class="node-bottom-val {{ $todayPointsEarned >= 50 ? 'text-indigo' : 'text-muted' }}">50</div>
                            </div>

                            <!-- Node 75 -->
                            <div class="milestone-node">
                                <div class="node-icon-circle {{ $todayPointsEarned >= 75 ? 'bg-indigo text-white' : 'bg-secondary bg-opacity-25 text-muted' }}">
                                    @if($todayPointsEarned >= 75)
                                        <i class="fa-solid fa-check"></i>
                                    @else
                                        <span style="font-size: 0.65rem;">75</span>
                                    @endif
                                </div>
                                <div class="node-bottom-val {{ $todayPointsEarned >= 75 ? 'text-indigo' : 'text-muted' }}">75</div>
                            </div>

                            <!-- Node 100 (Gift Box) -->
                            <div class="milestone-node">
                                @php
                                    $hasClaimed100Gift = \App\Models\PointTransaction::where('user_id', Auth::id())
                                        ->where('action', 'daily_milestone_100')
                                        ->whereDate('created_at', \Carbon\Carbon::today())
                                        ->exists();
                                @endphp
                                @if($hasClaimed100Gift)
                                    <div class="node-icon-circle bg-success text-white" title="Đã nhận quà mốc 100 xu">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @elseif($todayPointsEarned >= 100)
                                    <button type="button" id="btnClaimMilestone100" class="btn p-0 border-0 bg-transparent node-icon-gift-big text-danger" title="Nhấn để nhận Hộp Quà Mốc 100 Xu!" style="cursor: pointer; animation: pulse 1.2s infinite;">
                                        <i class="fa-solid fa-gift"></i>
                                    </button>
                                @else
                                    <div class="node-icon-gift-big opacity-50" title="Đạt 100 xu để mở quà!">
                                        <i class="fa-solid fa-gift text-danger"></i>
                                    </div>
                                @endif
                                <div class="node-bottom-val {{ $todayPointsEarned >= 100 ? 'text-danger fw-extrabold' : 'text-muted' }}">100</div>
                            </div>
                        </div>
                    </div>

                    <div class="main-section-card">
                        <div class="mb-3">
                            <h4 class="fw-extrabold text-dark mb-1" style="font-size: 1.2rem;">Danh sách nhiệm vụ</h4>
                            <p class="text-muted small mb-0">Hoàn thành nhiệm vụ để kiếm xu dễ dàng</p>
                        </div>

                        <!-- Filter Pills Bar -->
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-4">
                            <button class="quest-filter-pill active" onclick="filterQuestList('all', this)">
                                Tất cả
                            </button>
                            <button class="quest-filter-pill" onclick="filterQuestList('daily', this)">
                                Hàng ngày
                            </button>
                            <button class="quest-filter-pill" onclick="filterQuestList('weekly', this)">
                                Hàng tuần
                            </button>
                            <button class="quest-filter-pill" onclick="filterQuestList('achievement', this)">
                                Thành tựu
                            </button>
                        </div>

                        <!-- Quest Items List -->
                        <div id="questListContainer">
                            @forelse($dailyMissions as $mission)
                                @if($mission->action_key === 'daily_login' || str_contains(mb_strtolower($mission->title), 'đăng nhập') || str_contains(mb_strtolower($mission->title), 'điểm danh'))
                                    @continue
                                @endif
                                @php
                                    $um = $userMissions->get($mission->id);
                                    $currentProgress = $um ? $um->current_count : 0;
                                    $isCompleted = $um && ($um->status === 'completed' || $um->status === 'claimed');
                                    $isClaimed = $um && $um->status === 'claimed';
                                    $percent = min(100, round(($currentProgress / $mission->target_count) * 100));
                                @endphp
                                <div class="quest-item-card quest-unit" data-type="daily">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="quest-icon-box bg-icon-green">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">{{ $mission->title }}</h6>
                                            <div class="text-muted mb-1" style="font-size: 0.74rem;">{{ $mission->description }}</div>
                                            <div class="d-flex align-items-center gap-2" style="width: 130px;">
                                                <div class="progress flex-grow-1 rounded-pill" style="height: 4px; background: #e2e8f0;">
                                                    <div class="progress-bar bg-success" style="width: {{ $percent }}%;"></div>
                                                </div>
                                                <span class="text-muted fw-semibold" style="font-size: 0.68rem;">{{ $currentProgress }}/{{ $mission->target_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="quest-reward-tag mb-2">
                                            <i class="fa-solid fa-coins text-warning"></i> +{{ $mission->reward_points }} xu
                                        </div>
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 10px; padding: 6px 14px;">Đã nhận</button>
                                        @elseif($isCompleted)
                                            <button class="btn-quest-claim btn-claim-mission" data-id="{{ $mission->id }}">Nhận thưởng</button>
                                        @else
                                            <button class="btn-quest-action" onclick="alert('Hãy hoàn thành các hoạt động tương ứng trên bản đồ!');">Đến ngay</button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                            @endforelse

                            @php $otherMissions = $weeklyMissions->concat($achievementMissions); @endphp
                            @foreach($otherMissions as $mission)
                                @php
                                    $um = $userMissions->get($mission->id);
                                    $currentProgress = $um ? $um->current_count : 0;
                                    $isCompleted = $um && ($um->status === 'completed' || $um->status === 'claimed');
                                    $isClaimed = $um && $um->status === 'claimed';
                                    $percent = min(100, round(($currentProgress / $mission->target_count) * 100));
                                @endphp
                                <div class="quest-item-card quest-unit" data-type="{{ $mission->type }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="quest-icon-box {{ $mission->type === 'weekly' ? 'bg-icon-blue' : 'bg-icon-orange' }}">
                                            <i class="fa-solid {{ $mission->type === 'weekly' ? 'fa-share-nodes' : 'fa-award' }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">{{ $mission->title }}</h6>
                                            <div class="text-muted mb-1" style="font-size: 0.74rem;">{{ $mission->description }}</div>
                                            <span class="badge px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; background: #f1f5f9; color: #475569;">
                                                {{ $mission->type === 'weekly' ? 'Hàng tuần' : 'Thành tựu' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="quest-reward-tag mb-2">
                                            <i class="fa-solid fa-coins text-warning"></i> +{{ $mission->reward_points }} xu
                                        </div>
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 10px; padding: 6px 14px;">Đã nhận</button>
                                        @elseif($isCompleted)
                                            <button class="btn-quest-claim btn-claim-mission" data-id="{{ $mission->id }}">Nhận thưởng</button>
                                        @else
                                            <button class="btn-quest-action" onclick="alert('Đang làm nhiệm vụ...');">Thực hiện</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Multi-Widgets -->
                <div class="col-lg-4 col-xl-4">
                    <!-- Widget 1: Điểm danh mỗi ngày -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Điểm danh mỗi ngày <i class="fa-solid fa-circle-info text-muted fs-6" title="Chuỗi xu danh"></i></h6>
                        </div>
                        <div class="text-muted mb-3" style="font-size: 0.74rem;">Điểm danh liên tục để nhận thưởng lớn!</div>

                        <div class="d-flex align-items-center gap-1 mb-3">
                            @php
                                $hasClaimedToday = $user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday();
                                $rawStreak = (int)($user->streak_count ?? 0);
                                $effectiveStreak = $hasClaimedToday ? max(1, $rawStreak) : $rawStreak;
                            @endphp
                            @for($day = 1; $day <= 7; $day++)
                                @php
                                    $isDone = ($day <= $effectiveStreak);
                                    // Current active day only highlights if NOT claimed today yet
                                    $isCurrent = (!$hasClaimedToday && $day == ($effectiveStreak + 1));
                                    $isFrameDay = ($day == 7);
                                @endphp
                                <div class="streak-day-box {{ $isDone ? 'completed' : ($isCurrent ? 'current' : '') }} {{ $isFrameDay && !$isDone && !$isCurrent ? 'border-warning bg-warning-subtle' : '' }}">
                                    <div style="font-size: 0.64rem; font-weight: 600;">Ngày {{ $day }}</div>
                                    <div class="my-1">
                                        @if($isDone)
                                            <i class="fa-solid fa-check fs-6 text-success fw-bold"></i>
                                        @elseif($isFrameDay)
                                            <i class="fa-solid fa-id-badge fs-6 text-warning" title="Phần thưởng Khung Avatar!"></i>
                                        @else
                                            <i class="fa-solid fa-gift fs-6 text-warning"></i>
                                        @endif
                                    </div>
                                    <div class="fw-bold text-truncate" style="font-size: 0.65rem;">
                                        @if($isFrameDay)
                                            <span class="{{ $isDone ? 'text-muted' : 'text-warning fw-extrabold' }}">Khung</span>
                                        @else
                                            +{{ $day * 10 }}
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <button id="btnDailyCheckinSide" class="btn btn-indigo w-100 fw-bold rounded-pill text-white shadow-sm" style="background: #6366f1; padding: 8px; font-size: 0.84rem;" @if($user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday()) disabled @endif>
                            @if($user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday())
                                Đã xu danh hôm nay
                            @else
                                Điểm danh ngay
                            @endif
                        </button>
                    </div>

                    <!-- Widget 2: Nhiệm vụ hàng ngày Circular Gauge -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;">Nhiệm vụ hàng ngày</h6>
                            <span class="badge bg-light text-muted fw-semibold" style="font-size: 0.68rem;"><i class="fa-regular fa-clock me-1"></i> 12:45:30</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="circle-progress-box">
                                <span>2/5</span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.82rem;">Hoàn thành 5 nhiệm vụ</div>
                                <div class="text-muted mb-1.5" style="font-size: 0.74rem;">Nhận thưởng <strong class="text-warning">100 xu</strong></div>
                                <div class="progress rounded-pill" style="height: 5px; width: 130px; background: #e2e8f0;">
                                    <div class="progress-bar" style="width: 40%; background: #6366f1;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widget 3: Bảng xếp hạng -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;"><i class="fa-solid fa-trophy text-warning me-1"></i> Bảng xếp hạng</h6>
                            <a href="#" class="text-indigo fw-semibold" style="font-size: 0.74rem; text-decoration: none;" onclick="return false;">Xem tất cả &gt;</a>
                        </div>

                        <div class="d-flex flex-column gap-1">
                            @forelse($leaderboard as $index => $topUser)
                                @php $rank = $index + 1; @endphp
                                <div class="leaderboard-row {{ $topUser->id == $user->id ? 'highlight' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rank-badge {{ $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : 'rank-other')) }}">
                                            {{ $rank }}
                                        </div>
                                        <div class="avatar-frame-wrapper {{ $topUser->equippedFrame ? $topUser->equippedFrame->css_style : '' }}" style="width: 26px; height: 26px;">
                                            <img src="{{ $topUser->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($topUser->display_name ?? $topUser->username) }}&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                        </div>
                                        <span class="text-dark small text-truncate" style="max-width: 110px;">{{ $topUser->display_name ?? $topUser->username }}</span>
                                    </div>
                                    <div class="fw-bold text-indigo" style="color: #6366f1; font-size: 0.8rem;">
                                        {{ number_format($topUser->points) }} <span class="text-muted fw-normal" style="font-size: 0.7rem;">đ</span>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>

                    <!-- Widget 4: Săn nhiệm vụ đặc biệt Promo -->
                    <div class="promo-banner-card">
                        <h6 class="fw-extrabold text-dark mb-1" style="font-size: 0.86rem;">Săn nhiệm vụ đặc biệt</h6>
                        <p class="text-muted mb-2" style="font-size: 0.74rem;">Nhiệm vụ đặc biệt với phần thưởng siêu hấp dẫn đang chờ bạn!</p>
                        <button class="btn btn-dark btn-sm rounded-pill fw-bold" style="font-size: 0.75rem; padding: 5px 14px;" onclick="switchNavTab('shop-pane')">
                            Khám phá ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: TỦ KHUNG CÁ NHÂN (INVENTORY PANE) -->
        <div class="tab-pane fade" id="inventory-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-lg-3 col-xl-2">
                    <div class="reward-sidebar-compact">
                        <div class="sidebar-title">Bộ sưu tập</div>
                        <div class="d-flex flex-column">
                            <button class="reward-cat-btn active" type="button">
                                <i class="fa-solid fa-box-archive text-indigo"></i>
                                <span>Tủ khung cá nhân</span>
                            </button>
                            <button class="reward-cat-btn" onclick="switchNavTab('shop-pane')" type="button">
                                <i class="fa-solid fa-gift text-indigo"></i>
                                <span>Đổi thưởng</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-xl-10">
                    <div class="main-section-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="fw-extrabold text-dark mb-0" style="font-size: 1.2rem;">Tủ khung cá nhân</h4>
                            @if($user->equipped_frame_id)
                                <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold btn-unequip-frame" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-xmark me-1"></i> Tháo khung
                                </button>
                            @endif
                        </div>

                        <div class="row g-3">
                            @php $myFrames = $allFrames->whereIn('id', $unlockedFrameIds); @endphp
                            @forelse($myFrames as $frame)
                                @php $isEquipped = ($user->equipped_frame_id == $frame->id); @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="p-3 border rounded-3 text-center bg-white">
                                        <div class="avatar-frame-wrapper {{ $frame->css_style }} mx-auto mb-2" style="width: 56px; height: 56px;">
                                            <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                        </div>
                                        <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.86rem;">{{ $frame->name }}</h6>
                                        <div class="text-muted text-truncate small mb-2" style="font-size: 0.75rem;">{{ $frame->description }}</div>
                                        @if($isEquipped)
                                            <button class="btn btn-secondary btn-sm w-100 fw-bold rounded-pill" disabled style="font-size: 0.78rem;">Đang đeo</button>
                                        @else
                                            <button class="btn btn-indigo btn-sm w-100 fw-bold rounded-pill text-white btn-equip-frame" data-id="{{ $frame->id }}" style="background: #6366f1; font-size: 0.78rem;">Trang bị</button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2"></i>
                                    <p class="mb-0">Bạn chưa sở hữu khung avatar nào.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
window.switchNavTab = function(tabId) {
    // Sync header navigation tabs
    document.querySelectorAll('#rewardTopNav .nav-link').forEach(btn => {
        if (btn.getAttribute('data-bs-target') === '#' + tabId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Show selected tab pane
    const targetEl = document.getElementById(tabId);
    if (targetEl) {
        document.querySelectorAll('#mainTabContent .tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        targetEl.classList.add('show', 'active');
    }
};

window.filterCategory = function(cat, btn) {
    // Highlight sidebar category button
    document.querySelectorAll('.reward-cat-btn').forEach(b => {
        b.classList.remove('active');
    });
    if (btn) {
        btn.classList.add('active');
    }

    // Switch to Shop tab if not currently active
    switchNavTab('shop-pane');

    // Update section title
    const shopSectionTitle = document.getElementById('shopSectionTitle');
    if (shopSectionTitle) {
        const textMap = {
            'all': 'Gợi ý cho bạn',
            'avatar_frame': 'Khung Avatar độc quyền',
            'voucher': 'Voucher & Thẻ nạp',
            'badge': 'Huy hiệu & Vật phẩm',
            'exclusive': 'Quà tặng độc quyền'
        };
        shopSectionTitle.textContent = textMap[cat] || 'Danh sách phần thưởng';
    }

    // Filter reward items
    let visibleCount = 0;
    const rewardItems = document.querySelectorAll('.reward-item-wrapper');
    rewardItems.forEach(item => {
        const itemCat = item.getAttribute('data-category');
        if (cat === 'all' || itemCat === cat) {
            item.style.setProperty('display', 'block', 'important');
            visibleCount++;
        } else {
            item.style.setProperty('display', 'none', 'important');
        }
    });

    // Toggle empty category message
    const emptyMsg = document.getElementById('emptyCatMessage');
    if (emptyMsg) {
        if (visibleCount === 0) {
            emptyMsg.classList.remove('d-none');
        } else {
            emptyMsg.classList.add('d-none');
        }
    }
};

window.filterQuestList = function(type, btn) {
    document.querySelectorAll('.quest-filter-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const quests = document.querySelectorAll('#questListContainer .quest-unit');
    quests.forEach(q => {
        const qType = q.getAttribute('data-type');
        if (type === 'all' || qType === type) {
            q.style.setProperty('display', 'flex', 'important');
        } else {
            q.style.setProperty('display', 'none', 'important');
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = "{{ csrf_token() }}";

    function updatePointsUI(points, streak) {
        document.querySelectorAll('#headerUserPoints, #navbarUserPoints').forEach(el => {
            if (el) el.textContent = new Intl.NumberFormat().format(points) + (el.id === 'navbarUserPoints' ? ' xu' : '');
        });
        if (streak !== undefined) {
            const streakEl = document.getElementById('headerStreakCount');
            if (streakEl) streakEl.textContent = streak;
        }
    }

    // Claim 100 Milestone Gift Box
    const btnMilestone100 = document.getElementById('btnClaimMilestone100');
    if (btnMilestone100) {
        btnMilestone100.addEventListener('click', function() {
            fetch("{{ route('client.missions.claim_milestone') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    }

    // Daily checkin handler
    const handleDailyCheckin = function() {
        fetch("{{ route('client.profile.claim_daily') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                updatePointsUI(data.points, data.streak);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    };

    const btnShopCheckin = document.getElementById('btnDailyCheckinShop');
    if (btnShopCheckin) btnShopCheckin.addEventListener('click', handleDailyCheckin);
    const btnDailyQuest = document.getElementById('btnDailyCheckinQuest');
    if (btnDailyQuest) btnDailyQuest.addEventListener('click', handleDailyCheckin);
    const btnDailySide = document.getElementById('btnDailyCheckinSide');
    if (btnDailySide) btnDailySide.addEventListener('click', handleDailyCheckin);

    // Claim Mission Reward
    document.querySelectorAll('.btn-claim-mission').forEach(btn => {
        btn.addEventListener('click', function() {
            const missionId = this.dataset.id;
            fetch(`/missions/claim/${missionId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    });

    // Equip Frame
    document.querySelectorAll('.btn-equip-frame').forEach(btn => {
        btn.addEventListener('click', function() {
            const frameId = this.dataset.id;
            fetch("{{ route('client.avatar_frames.equip') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ frame_id: frameId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    });

    // Unequip Frame
    const btnUnequip = document.querySelector('.btn-unequip-frame');
    if (btnUnequip) {
        btnUnequip.addEventListener('click', function() {
            fetch("{{ route('client.avatar_frames.equip') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ frame_id: null })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    }

    // Buy Frame
    document.querySelectorAll('.btn-buy-frame').forEach(btn => {
        btn.addEventListener('click', function() {
            const frameId = this.dataset.id;
            const points = this.dataset.points;
            if (!confirm(`Xác nhận dùng ${points} xu để đổi Khung Avatar này?`)) return;

            fetch(`/avatar-frames/buy/${frameId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });
    });
});
</script>
@endpush
