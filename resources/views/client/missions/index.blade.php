@extends('client.layouts.app')

@section('title', 'Trung Tâm Nhiệm Vụ & Đổi Thưởng - Ninh Bình Travel Hub')

@section('body_class', 'missions-shell')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
<style>
    :root {
        --q-bg: #f7f9fb;
        --q-card-bg: #ffffff;
        --q-primary: #000000;
        --q-primary-hover: #565e74;
        --q-primary-light: #f2f4f6;
        --q-accent-orange: #735c00;
        --q-text-main: #191c1e;
        --q-text-sub: #76777d;
        --q-border: #e0e3e5;
    }


    /* Tab Panes Visibility Control */
    .tab-content > .tab-pane {
        display: none !important;
    }
    .tab-content > .tab-pane.active,
    .tab-content > .tab-pane.show.active {
        display: block !important;
    }

    /* Reward Item Cards Modal */
    .reward-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reward-modal-dialog {
        position: relative;
        width: 90%;
        max-width: 400px;
    }

    .reward-modal-card {
        background: #ffffff;
        border-radius: 2px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.16);
        border: 1px solid #e0e3e5;
        padding: 24px;
        text-align: center;
    }

    .reward-modal-header-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #000000;
        margin-bottom: 20px;
    }

    .reward-cards-container {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-bottom: 24px;
    }

    .reward-item-card {
        position: relative;
        background: #f7f9fb;
        border: 1.5px solid #e0e3e5;
        border-radius: 2px;
        padding: 14px;
        width: 130px;
        height: 130px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease;
    }

    .reward-card-checkbox {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #000000;
        z-index: 2;
        margin: 0;
    }

    .reward-item-card:hover {
        transform: translateY(-3px);
    }

    .reward-card-preview {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .reward-avatar-preview-box {
        position: relative;
        width: 64px;
        height: 64px;
    }

    .reward-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .reward-frame-overlay-img {
        position: absolute;
        top: -24%;
        left: -24%;
        width: 148%;
        height: 148%;
        pointer-events: none;
        object-fit: contain;
    }

    .coin-card-icon-box {
        width: 58px;
        height: 58px;
        background: #f2f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.8), 0 4px 10px rgba(234, 179, 8, 0.2);
    }

    .reward-card-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #45464d;
        text-align: center;
        white-space: nowrap;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .reward-modal-actions-row {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-reward-equip {
        flex: 1;
        background: #ffffff;
        color: #191c1e !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 11px 20px;
        border-radius: 2px;
        border: 1px solid #7d8791;
        box-shadow: 0 2px 0 #c6cbd1;
        transition: all 0.18s ease;
    }

    .btn-reward-equip:hover {
        background: #f7f9fb;
        border-color: #5f6973;
        box-shadow: 0 3px 0 #bcc3ca;
        transform: translateY(-1px);
    }

    .btn-reward-close {
        flex: 1;
        background: #f2f4f6;
        color: #45464d !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 11px 20px;
        border-radius: 2px;
        border: 1px solid #e0e3e5;
        transition: all 0.2s;
    }

    .btn-reward-close:hover {
        background: #e0e3e5;
        color: #000000 !important;
    }
    /* Ẩn header/footer site — trang nhiệm vụ là shell riêng */
    body.missions-shell > header.site-header,
    body.missions-shell > footer,
    body.missions-shell > .container.mt-3 {
        display: none !important;
    }

    body.missions-shell {
        background: var(--q-bg);
        min-height: 100vh;
    }

    .ms-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .ms-top {
        background: #ffffff;
        color: #191c1e;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: sticky;
        top: 0;
        z-index: 110;
        border-bottom: 1px solid var(--q-border);
    }

    .ms-top__left,
    .ms-top__center,
    .ms-top__right {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .ms-top__center {
        flex: 1;
        justify-content: center;
    }

    .ms-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #45464d;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
        transition: color 0.2s ease;
    }

    .ms-back:hover {
        color: #000000;
    }

    .ms-brand {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #000000;
    }

    .ms-brand__logo {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    .ms-brand__name {
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .ms-xu-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: #f7f9fb;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        font-weight: 700;
        font-size: 0.82rem;
        color: #191c1e;
    }

    .ms-nav__track {
        display: inline-flex;
        gap: 4px;
        background: #f2f4f6;
        padding: 4px;
        border-radius: 2px;
        list-style: none;
        margin: 0;
        flex-wrap: wrap;
    }

    .ms-nav__track .nav-link {
        padding: 8px 18px !important;
        font-size: 0.84rem !important;
        font-weight: 600 !important;
        color: #45464d !important;
        border-radius: 2px !important;
        border: none !important;
        border-bottom: 2px solid transparent !important;
        background: transparent !important;
        box-shadow: none !important;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .ms-nav__track .nav-link:hover:not(.active) {
        color: #735c00 !important;
    }

    .ms-nav__track .nav-link.active {
        background: transparent !important;
        color: #191c1e !important;
        box-shadow: none !important;
        border-bottom: 2px solid #735c00 !important;
    }

    .ms-nav__hint {
        font-size: 0.76rem;
        color: #76777d;
        margin: 0;
    }

    .reward-full-container {
        width: 100%;
        max-width: 1140px;
        margin: 0 auto;
        padding: 24px 20px 48px;
        flex: 1;
    }

    .ms-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
        gap: 24px;
        align-items: start;
        background: #f2f4f6;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 22px 24px;
        margin-bottom: 20px;
    }

    .ms-hero__eyebrow {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #735c00;
        margin-bottom: 8px;
    }

    .ms-hero__title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #000000;
        margin: 0 0 6px;
        line-height: 1.3;
    }

    .ms-hero__sub {
        font-size: 0.8rem;
        color: #76777d;
        margin: 0 0 14px;
    }

    .ms-hero__points {
        font-size: 1.75rem;
        font-weight: 700;
        color: #000000;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }

    .ms-hero__checkin-title {
        font-size: 0.86rem;
        font-weight: 600;
        color: #000000;
        margin-bottom: 4px;
    }

    .ms-hero__checkin-sub {
        font-size: 0.74rem;
        color: #76777d;
        margin-bottom: 12px;
    }

    .ms-cat-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .ms-cat-chip {
        padding: 7px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid #e0e3e5;
        background: #ffffff;
        color: #45464d;
        border-radius: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ms-cat-chip:hover {
        border-color: #000000;
        color: #000000;
    }

    .ms-cat-chip.active {
        background: #dfe4e8;
        color: #191c1e;
        border-color: #aeb4ba;
    }

    .ms-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .ms-section-head h2 {
        font-size: 1.05rem;
        font-weight: 600;
        color: #000000;
        margin: 0;
    }

    .ms-section-head p {
        font-size: 0.78rem;
        color: #76777d;
        margin: 4px 0 0;
    }

    @media (max-width: 991px) {
        .ms-hero {
            grid-template-columns: 1fr;
        }

        .ms-top {
            flex-wrap: wrap;
        }

        .ms-top__center {
            order: 3;
            width: 100%;
            justify-content: flex-start;
        }

        .ms-nav__hint {
            display: none;
        }
    }

    @media (max-width: 575px) {
        .ms-back span {
            display: none;
        }

        .ms-brand__name {
            display: none;
        }

        .ms-nav__track .nav-link {
            padding: 8px 12px !important;
            font-size: 0.78rem !important;
        }
    }

    /* Left Sidebar Cards */
    .reward-sidebar-compact {
        background: #ffffff;
        border-radius: 2px;
        padding: 12px;
        border: 1px solid #e0e3e5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .sidebar-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #76777d;
        letter-spacing: 0.5px;
        padding: 4px 10px 10px;
        margin-bottom: 4px;
        border-bottom: 1px solid #f2f4f6;
    }

    .reward-cat-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 9px 12px;
        border-radius: 2px;
        font-weight: 600;
        font-size: 0.84rem;
        color: #45464d !important;
        background: transparent !important;
        border: none !important;
        text-align: left;
        transition: all 0.2s ease;
        margin-bottom: 3px;
        cursor: pointer !important;
        outline: none !important;
    }

    .reward-cat-btn:hover {
        background: #f2f4f6 !important;
        color: #000000 !important;
    }

    .reward-cat-btn.active {
        background: #f2f4f6 !important;
        color: #000000 !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    .reward-cat-btn.active i,
    .reward-cat-btn.active span {
        color: #000000 !important;
    }

    /* Hero Banner (Shop Tab) */
    .reward-hero-compact {
        background: #f7f9fb;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 22px 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .hero-title-compact {
        font-size: 1.3rem;
        font-weight: 800;
        color: #000000;
        line-height: 1.3;
        margin-bottom: 6px;
    }

    .hero-sub-compact {
        font-size: 0.84rem;
        color: #76777d;
        margin-bottom: 16px;
    }

    .btn-hero-compact {
        background: #d9dee3;
        color: #191c1e;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 8px 20px;
        border-radius: 2px;
        border: 1px solid #aeb4ba;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .btn-hero-compact:hover {
        background: #c9d0d7;
        color: #191c1e;
    }

    /* Top Assurance Bar */
    .top-assurance-compact {
        background: #ffffff;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 12px 18px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.01);
    }

    .assurance-unit {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .assurance-icon-compact {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f2f4f6;
        color: #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        border: 1px solid #e0e3e5;
    }

    .assurance-title-compact {
        font-weight: 700;
        font-size: 0.82rem;
        color: #000000;
    }

    .assurance-sub-compact {
        font-size: 0.72rem;
        color: #76777d;
    }

    /* Gift / Frame Card Grid */
    .item-card-compact {
        background: #ffffff;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 14px;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .item-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border-color: #c6c6cd;
    }

    .item-img-compact {
        height: 90px;
        background: #f7f9fb;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        padding: 8px;
        border: 1px solid #f2f4f6;
    }

    .item-name-compact {
        font-size: 0.85rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 2px;
    }

    .item-desc-compact {
        font-size: 0.75rem;
        color: #76777d;
        margin-bottom: 8px;
    }

    .item-price-compact {
        font-size: 0.82rem;
        font-weight: 800;
        color: #000000;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .btn-action-compact {
        background: #ffffff;
        color: #191c1e;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 2px;
        padding: 8px 12px;
        border: 1px solid #7d8791;
        width: 100%;
        transition: all 0.18s ease;
        box-shadow: 0 2px 0 #c6cbd1;
        min-height: 36px;
    }

    .btn-action-compact:hover {
        background: #f7f9fb;
        border-color: #5f6973;
        box-shadow: 0 3px 0 #bcc3ca;
        transform: translateY(-1px);
        color: #191c1e;
    }

    .btn-indigo {
        background: #ffffff !important;
        border: 1px solid #7d8791 !important;
        color: #191c1e !important;
        font-weight: 700 !important;
        border-radius: 2px !important;
        box-shadow: 0 2px 0 #c6cbd1 !important;
        transition: all 0.18s ease !important;
        min-height: 38px;
    }

    .btn-indigo:hover {
        background: #f7f9fb !important;
        border-color: #5f6973 !important;
        box-shadow: 0 3px 0 #bcc3ca !important;
        transform: translateY(-1px);
        color: #191c1e !important;
    }

    .text-indigo {
        color: #000000 !important;
    }

    .btn-outline-compact {
        border: 1px solid #7d8791;
        color: #191c1e;
        background: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 2px;
        padding: 7px;
        width: 100%;
        transition: all 0.18s ease;
        box-shadow: 0 2px 0 #c6cbd1;
    }

    .btn-outline-compact:hover {
        background: #f7f9fb;
        border-color: #5f6973;
        box-shadow: 0 3px 0 #bcc3ca;
        transform: translateY(-1px);
        color: #191c1e;
    }

    /* Quests Tab Layout Specific Styles */
    .main-section-card {
        background: #ffffff;
        border-radius: 2px;
        padding: 20px 24px;
        border: 1px solid #e0e3e5;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .quest-filter-pill {
        border-radius: 2px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #76777d;
        background: #ffffff;
        border: 1px solid #e0e3e5;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quest-filter-pill.active {
        background: transparent;
        color: #191c1e;
        font-weight: 700;
        border-color: #aeb4ba;
        border-bottom: 2px solid #735c00;
        box-shadow: none;
    }

    .quest-item-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 16px 18px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto auto;
        gap: 16px;
        align-items: center;
        border-left: 3px solid transparent;
    }

    .quest-item-card:has(.btn-claim-mission) {
        border-left-color: #735c00;
    }

    .quest-item-card:has(.btn-claim-milestone-box) {
        border-left-color: #735c00;
    }

    .quest-item-card:hover {
        border-color: #c6c6cd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .quest-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .bg-icon-purple,
    .bg-icon-green,
    .bg-icon-blue,
    .bg-icon-orange {
        background: #f7f9fb;
        color: #000000;
        border: 1px solid #e0e3e5;
    }

    .quest-reward-tag {
        position: static;
        font-size: 0.82rem;
        font-weight: 700;
        color: #000000;
        background: #f2f4f6;
        border: 1px solid #e0e3e5;
        padding: 4px 10px;
        border-radius: 2px;
        white-space: nowrap;
        align-self: center;
    }

    .btn-quest-action {
        background: #ffffff;
        color: #191c1e;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 2px;
        padding: 7px 14px;
        border: 1px solid #7d8791;
        transition: all 0.18s ease;
        box-shadow: 0 2px 0 #c6cbd1;
    }

    .btn-quest-action:hover {
        background: #f7f9fb;
        border-color: #5f6973;
        box-shadow: 0 3px 0 #bcc3ca;
        transform: translateY(-1px);
        color: #191c1e;
    }

    .btn-quest-claim {
        background: #ffffff;
        color: #191c1e;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 2px;
        padding: 7px 16px;
        border: 1px solid #7d8791;
        box-shadow: 0 2px 0 #c6cbd1;
    }

    .btn-quest-claim:hover {
        background: #f7f9fb;
        border-color: #5f6973;
        box-shadow: 0 3px 0 #bcc3ca;
        transform: translateY(-1px);
        color: #191c1e;
    }

    .widget-card {
        background: #ffffff;
        border-radius: 2px;
        padding: 16px;
        border: 1px solid #e0e3e5;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .streak-day-box {
        background: #ffffff;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 6px 2px;
        text-align: center;
        flex: 1;
        min-width: 0;
    }

    .streak-day-box.completed {
        background: #f7f9fb !important;
        border-color: #e0e3e5 !important;
        color: #76777d !important;
        opacity: 0.7 !important;
    }

    .streak-day-box.current {
        border: 1.5px solid #000000 !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .streak-day-box.special-day {
        background: #f2f4f6 !important;
        border: 1px solid #c6c6cd !important;
        color: #000000 !important;
    }

    .streak-day-box.special-day .special-day-title {
        color: #000000 !important;
        font-weight: 700 !important;
    }

    /* Muted disabled check-in button */
    #btnDailyCheckinSide:disabled,
    #btnDailyCheckinShop:disabled,
    .btn-indigo:disabled {
        background: #f2f4f6 !important;
        border-color: #e0e3e5 !important;
        color: #76777d !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.85;
    }

    .circle-progress-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid #000000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 700;
        color: #000000;
        flex-shrink: 0;
        background: #f2f4f6;
    }

    .leaderboard-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 2px;
        transition: all 0.2s ease;
    }

    .leaderboard-row.highlight {
        background: #f2f4f6;
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

    .rank-1 { background: #000000; }
    .rank-2 { background: #45464d; }
    .rank-3 { background: #76777d; }
    .rank-other { background: #f2f4f6; color: #76777d; }

    .promo-banner-card {
        background: #f7f9fb;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 16px;
    }

    /* TIẾN ĐỘ NHIỆM VỤ Widget (Minimalist & Clean Theme) */
    .quest-progress-card-cream {
        background: #ffffff;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
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
        background: #e0e3e5;
        border-radius: 4px;
        transform: translateY(-50%);
        z-index: 1;
    }

    .quest-milestone-line-fill {
        height: 100%;
        background: #000000;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .quest-coin-badge {
        width: 40px;
        height: 40px;
        background: #000000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        box-shadow: none;
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
        cursor: pointer;
    }

    .node-top-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #76777d;
        margin-bottom: 3px;
    }

    .node-icon-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #e0e3e5;
        color: #76777d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        box-shadow: none;
    }

    .node-icon-gift {
        font-size: 18px;
        color: #76777d;
    }

    .node-icon-gift-big {
        font-size: 24px;
        color: #000000;
        filter: none;
    }

    .node-bottom-val {
        position: absolute;
        top: 100%;
        margin-top: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #45464d;
        white-space: nowrap;
    }

    .milestone-node .milestone-tooltip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(4px);
        background: #ffffff;
        border: 1px solid #e0e3e5;
        box-shadow: 0 10px 24px -6px rgba(15, 23, 42, 0.18), 0 2px 6px rgba(0, 0, 0, 0.05);
        border-radius: 2px;
        padding: 8px;
        white-space: normal;
        text-align: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tooltip-mini-card {
        background: #f7f9fb;
        border: 1px solid #e0e3e5;
        border-radius: 2px;
        min-width: 100px;
        max-width: 128px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px 10px;
    }

    .tooltip-mini-card img {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .tooltip-frame-caption {
        font-size: 0.55rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #76777d;
    }

    .tooltip-frame-name {
        font-size: 0.68rem;
        font-weight: 700;
        color: #191c1e;
        line-height: 1.25;
        white-space: normal;
        word-break: break-word;
    }

    .milestone-node .milestone-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 6px 6px 0 6px;
        border-style: solid;
        border-color: #ffffff transparent transparent transparent;
    }

    .milestone-node:hover .milestone-tooltip,
    .milestone-node.active .milestone-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .quest-sub-item-card {
        background: #ffffff;
        border-radius: 2px;
        padding: 14px 16px;
        margin-bottom: 10px;
        border: 1px solid #f2f4f6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }

    .quest-sub-item-card:hover {
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
        border-color: #c6c6cd;
    }

    .btn-outline-purple {
        border: 1.5px solid #735c00;
        color: #735c00;
        background: transparent;
        border-radius: 2px;
        padding: 4px 16px;
        font-weight: 700;
        font-size: 0.78rem;
        transition: all 0.2s ease;
    }

    .progress {
        border-radius: 2px !important;
        background: #e0e3e5 !important;
    }
    .progress-bar,
    .progress-bar.bg-primary {
        background: #000000 !important;
        border-radius: 2px !important;
    }

    @media (max-width: 767px) {
        .quest-item-card {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .quest-reward-tag {
            justify-self: start;
        }
    }

    .btn-outline-purple:hover {
        background: #735c00;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="ms-shell">
    <header class="ms-top">
        <div class="ms-top__left">
            <a href="{{ route('home') }}" class="ms-back">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                <span>Bản đồ</span>
            </a>
            <a href="{{ route('client.missions') }}" class="ms-brand">
                <img class="ms-brand__logo" src="{{ asset('images/logo.png') }}" alt="">
                <span class="ms-brand__name">Ninh Bình Travel Hub</span>
            </a>
        </div>
        <div class="ms-top__center">
            <ul class="ms-nav__track nav" id="rewardTopNav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="nav-quests-tab" data-bs-toggle="pill" data-bs-target="#quests-pane" type="button" role="tab" onclick="switchNavTab('quests-pane')">
                        Nhiệm vụ
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="nav-shop-tab" data-bs-toggle="pill" data-bs-target="#shop-pane" type="button" role="tab" onclick="switchNavTab('shop-pane')">
                        Đổi thưởng
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="nav-inventory-tab" data-bs-toggle="pill" data-bs-target="#inventory-pane" type="button" role="tab" onclick="switchNavTab('inventory-pane')">
                        Tủ quà
                    </button>
                </li>
            </ul>
        </div>
        <div class="ms-top__right">
            <p class="ms-nav__hint mb-0" id="headerBrandSub">Hoàn thành nhiệm vụ để nhận xu mỗi ngày</p>
            @if($user)
                <div class="ms-xu-pill">
                    <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 18px; height: 18px; object-fit: contain;">
                    <span id="headerUserPoints">{{ number_format($user->points) }}</span>
                </div>
                <x-user-avatar :user="$user" size="32" />
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-light" style="border-radius: 2px; font-weight: 600;">Đăng nhập</a>
            @endif
        </div>
    </header>

<!-- Main Container -->
<div class="reward-full-container">
    <div class="tab-content" id="mainTabContent">
        
        <!-- TAB 1: ĐỔI THƯỞNG -->
        <div class="tab-pane fade" id="shop-pane" role="tabpanel">
            <div class="ms-section-head">
                <div>
                    <h2 id="shopSectionTitle">Đổi thưởng</h2>
                    <p>Dùng xu tích lũy để đổi khung avatar</p>
                </div>
                <button class="btn btn-outline-compact" type="button" onclick="copyReferralLink()" style="width: auto; padding: 7px 16px;">
                    Giới thiệu bạn bè
                </button>
            </div>

            <div class="ms-cat-chips" id="rewardCatList">
                <button class="ms-cat-chip active" onclick="filterCategory('all', this)" type="button">Tất cả khung</button>
                <button class="ms-cat-chip" onclick="filterCategory('exclusive', this)" type="button">Khung avatar</button>
            </div>

            <div id="emptyCatMessage" class="text-center py-5 bg-white border d-none" style="border-radius: 2px;">
                <i class="fa-solid fa-box-open text-muted fs-1 mb-2"></i>
                <h6 class="fw-bold text-dark mb-1">Chưa có phần thưởng trong mục này</h6>
                <p class="text-muted small mb-0">Các phần quà mới sẽ sớm được cập nhật.</p>
            </div>

                    <div class="row g-3" id="shopItemGrid">
                        @forelse($shopFrames as $frame)
                            @php
                                $canAfford = auth()->check() && auth()->user()->points >= (int) $frame->required_points;
                                $avatarPreview = $user?->avatar_formatted_url ?? 'https://ui-avatars.com/api/?name=Guest&background=eceff1&color=111827';
                            @endphp
                            <div class="col-6 col-md-4 col-lg-3 reward-item-wrapper" data-category="exclusive">
                                <div class="item-card-compact">
                                    <div>
                                        <div class="item-img-compact bg-light d-flex align-items-center justify-content-center">
                                            @if($frame->image_url)
                                                <div class="avatar-frame-wrapper has-png-frame" style="width: 58px; height: 58px;">
                                                    <img src="{{ $avatarPreview }}" alt="Avatar preview" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                    <img src="{{ asset($frame->image_url) }}" class="avatar-frame-png-overlay" alt="{{ $frame->name }}">
                                                </div>
                                            @else
                                                <div class="avatar-frame-wrapper {{ $frame->css_style }}" style="width: 58px; height: 58px;">
                                                    <img src="{{ $avatarPreview }}" alt="Avatar preview" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                                </div>
                                            @endif
                                        </div>
                                        <h6 class="item-name-compact text-truncate">{{ $frame->name }}</h6>
                                        <div class="item-desc-compact text-truncate">{{ $frame->description ?: 'Khung avatar có thể đổi bằng xu.' }}</div>
                                    </div>
                                    <div>
                                        <div class="item-price-compact">
                                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain; vertical-align: -2px;" class="me-0.5">
                                            <span>{{ number_format((int) $frame->required_points) }} xu</span>
                                        </div>
                                        @guest
                                            <a href="{{ route('login') }}" class="btn btn-action-compact text-decoration-none text-center d-block">Đăng nhập để đổi</a>
                                        @else
                                            <button class="btn btn-action-compact btn-buy-frame" type="button" data-id="{{ $frame->id }}" data-name="{{ $frame->name }}" data-points="{{ (int) $frame->required_points }}" {{ $canAfford ? '' : 'disabled' }}>
                                                {{ $canAfford ? 'Đổi khung' : 'Không đủ xu' }}
                                            </button>
                                        @endguest
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 bg-white rounded-3 border">
                                    <p class="text-muted small mb-0">Cửa hàng đổi thưởng đang được cập nhật.</p>
                                </div>
                            </div>
                        @endforelse
            </div>
        </div>

        <!-- TAB 2: NHIỆM VỤ -->
        <div class="tab-pane fade show active" id="quests-pane" role="tabpanel">
            @php
                $totalPointsEarned = \App\Models\PointTransaction::where('user_id', Auth::id())
                    ->where('amount', '>', 0)
                    ->where('action', 'not like', 'daily_milestone_%')
                    ->sum('amount');

                $claimedMilestones = \App\Models\PointTransaction::where('user_id', Auth::id())
                    ->where('action', 'like', 'daily_milestone_%')
                    ->pluck('action')
                    ->toArray();

                if ($totalPointsEarned <= 100) {
                    $milestonePercent = ($totalPointsEarned / 100) * 33.33;
                } elseif ($totalPointsEarned <= 200) {
                    $milestonePercent = 33.33 + (($totalPointsEarned - 100) / 100) * 33.33;
                } else {
                    $milestonePercent = 66.66 + (min(300, $totalPointsEarned - 200) / 300) * 33.34;
                }
                $milestonePercent = min(100, max(0, round($milestonePercent)));

                $claimed100 = in_array('daily_milestone_100', $claimedMilestones);
                $claimed200 = in_array('daily_milestone_200', $claimedMilestones);
                $claimed500 = in_array('daily_milestone_500', $claimedMilestones);
                $allClaimed = $claimed100 && $claimed200 && $claimed500;

                $frame100 = \App\Models\AvatarFrame::where('code', 'frame-bronze')->first();
                $frame200 = \App\Models\AvatarFrame::where('code', 'frame-silver')->first();
                $frame500 = \App\Models\AvatarFrame::where('code', 'frame-diamond')->first();

                $hasClaimedToday = false;
                $effectiveStreak = 0;
                if ($user) {
                    $hasClaimedToday = $user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday();
                    $lastStreakAt = $user->last_streak_at ? \Carbon\Carbon::parse($user->last_streak_at) : null;

                    if ($hasClaimedToday) {
                        $rawStreak = (int)($user->streak_count ?? 1);
                        $effectiveStreak = (($rawStreak - 1) % 7) + 1;
                    } else {
                        if ($lastStreakAt && $lastStreakAt->isYesterday()) {
                            $prevStreak = (int)($user->streak_count ?? 0);
                            $effectiveStreak = (($prevStreak - 1) % 7) + 1;
                        } else {
                            $effectiveStreak = 0;
                        }
                    }
                }

                $day7Frame = \App\Models\AvatarFrame::where('code', 'frame-streak')->first()
                    ?? \App\Models\AvatarFrame::where('name', 'like', '%Duy Trì%')->first()
                    ?? \App\Models\AvatarFrame::first();
            @endphp

            <div class="ms-hero">
                <div>
                    <div class="ms-hero__eyebrow">Trung tâm tích xu</div>
                    <h1 class="ms-hero__title">
                        @auth
                            Xin chào, {{ $user->display_name ?? $user->username }}
                        @else
                            Bắt đầu tích xu ngay
                        @endauth
                    </h1>
                    <p class="ms-hero__sub">Hoàn thành nhiệm vụ, điểm danh mỗi ngày và đổi quà hấp dẫn.</p>
                    <div class="ms-hero__points">
                        {{ $user ? number_format($user->points) : '0' }}
                        <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 26px; height: 26px; object-fit: contain; margin-left: 4px;">
                    </div>
                </div>
                <div>
                    <div class="ms-hero__checkin-title">Điểm danh 7 ngày</div>
                    <div class="ms-hero__checkin-sub">Điểm danh liên tục để nhận thưởng lớn vào ngày thứ 7.</div>
                    <div class="d-flex align-items-center gap-1 mb-3">
                        @for($day = 1; $day <= 7; $day++)
                            @php
                                $isDone = ($hasClaimedToday && $day <= $effectiveStreak) || (!$hasClaimedToday && $day <= $effectiveStreak);
                                $isCurrent = (!$hasClaimedToday && $day == ($effectiveStreak + 1));
                                $isFrameDay = ($day == 7);
                            @endphp
                            <div class="streak-day-box {{ $isDone ? 'completed' : ($isCurrent ? 'current' : '') }} {{ $isFrameDay && !$isDone && !$isCurrent ? 'special-day' : '' }}">
                                <div style="font-size: 0.64rem; font-weight: 600;">Ngày {{ $day }}</div>
                                <div class="my-1 d-flex align-items-center justify-content-center" style="height: 28px;">
                                    @if($isDone)
                                        <i class="fa-solid fa-check text-muted" style="font-size: 0.8rem;"></i>
                                    @elseif($isFrameDay)
                                        @if($day7Frame && $day7Frame->image_url)
                                            <img src="{{ asset($day7Frame->image_url) }}" alt="{{ $day7Frame->name }}" style="width: 24px; height: 24px; object-fit: contain;" title="Phần thưởng: Khung {{ $day7Frame->name }}">
                                        @else
                                            <span class="text-dark fw-bold" style="font-size: 0.68rem;">Khung</span>
                                        @endif
                                    @else
                                        <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain;">
                                    @endif
                                </div>
                                <div class="fw-bold text-truncate" style="font-size: 0.65rem;">
                                    @if($isFrameDay)
                                        <span class="{{ $isDone ? 'text-muted' : 'special-day-title' }}">{{ $day7Frame->name ?? 'Khung' }}</span>
                                    @else
                                        +{{ $day * 10 }}
                                    @endif
                                </div>
                            </div>
                        @endfor
                    </div>
                    <button id="btnDailyCheckinSide" class="btn btn-indigo w-100 fw-bold" style="padding: 9px; font-size: 0.84rem;" @if($user && $user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday()) disabled @endif>
                        @if($user && $user->last_daily_bonus_at && \Carbon\Carbon::parse($user->last_daily_bonus_at)->isToday())
                            Đã điểm danh hôm nay
                        @else
                            Điểm danh ngay
                        @endif
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8 col-xl-8">
                    <!-- TIẾN ĐỘ NHIỆM VỤ Widget (3 Mốc 100, 200, 500 Xu) -->
                    <div class="quest-progress-card-cream">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-extrabold text-dark mb-0" style="font-size: 0.88rem;">
                                Tiến độ tích xu: <span class="text-indigo fw-black">{{ number_format($totalPointsEarned) }} xu</span>
                            </h6>
                            @if($allClaimed)
                                <span class="badge bg-light text-success fw-bold px-2.5 py-1 border" style="font-size: 0.7rem;">
                                    Đã hoàn thành tất cả mốc!
                                </span>
                            @else
                                <span class="badge bg-light text-muted fw-bold px-2.5 py-1 border" style="font-size: 0.7rem;">
                                    Mốc 100 - 200 - 500 Xu
                                </span>
                            @endif
                        </div>

                        <div class="quest-milestone-track-wrapper">
                            <div class="quest-milestone-line-bg">
                                <div class="quest-milestone-line-fill" style="width: {{ $milestonePercent }}%; background: #475569;"></div>
                            </div>

                            <!-- Start Flag -->
                            <div class="quest-coin-badge border-0 shadow-none bg-transparent" title="Vạch xuất phát" style="width: 22px; height: 22px; display: flex; align-items: flex-end; justify-content: center; transform: translateY(-1px);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 21V3" stroke="#475569" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M5 4H19L14.5 9L19 14H5" fill="#475569" stroke="#475569" stroke-width="1.2" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <!-- Mốc 1: 100 Xu -->
                            <div class="milestone-node">
                                <div class="milestone-tooltip">
                                    <div class="d-flex align-items-center gap-1">
                                        @if($frame100)
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <img src="{{ asset($frame100->image_url) }}" alt="{{ $frame100->name }}">
                                                <div class="tooltip-frame-name">Khung {{ $frame100->name }}</div>
                                            </div>
                                        @else
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <div class="tooltip-frame-name">Mở khung avatar</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($claimed100)
                                    <div style="width: 22px; height: 22px; border-radius: 4px; background: #475569; display: flex; align-items: center; justify-content: center;" title="Đã nhận quà mốc 100 xu">
                                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 9.2L7.8 12L13 6.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @elseif($totalPointsEarned >= 100)
                                    <button type="button" class="btn p-0 border-0 bg-transparent btn-claim-milestone-box" data-milestone="100" title="Nhấn để nhận Hộp Quà Mốc 100 Xu!" style="cursor: pointer; animation: pulse 1.2s infinite;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="10" width="18" height="11" rx="2" fill="#475569" stroke="#475569" stroke-width="1.6"/>
                                            <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#64748b" stroke="#475569" stroke-width="1.6"/>
                                            <path d="M12 6V21" stroke="#ffffff" stroke-width="1.8"/>
                                        </svg>
                                    </button>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Cần 100 xu để mở">
                                        <rect x="3" y="10" width="18" height="11" rx="2" fill="#F8FAFC" stroke="#64748b" stroke-width="1.6"/>
                                        <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#E2E8F0" stroke="#64748b" stroke-width="1.6"/>
                                        <path d="M12 6V21" stroke="#64748b" stroke-width="1.6"/>
                                    </svg>
                                @endif
                                <div class="node-bottom-val {{ $totalPointsEarned >= 100 ? 'text-dark fw-extrabold' : 'text-muted' }}">100</div>
                            </div>

                            <!-- Mốc 2: 200 Xu -->
                            <div class="milestone-node">
                                <div class="milestone-tooltip">
                                    <div class="d-flex align-items-center gap-1">
                                        @if($frame200)
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <img src="{{ asset($frame200->image_url) }}" alt="{{ $frame200->name }}">
                                                <div class="tooltip-frame-name">Khung {{ $frame200->name }}</div>
                                            </div>
                                        @else
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <div class="tooltip-frame-name">Mở khung avatar</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($claimed200)
                                    <div style="width: 22px; height: 22px; border-radius: 4px; background: #475569; display: flex; align-items: center; justify-content: center;" title="Đã nhận quà mốc 200 xu">
                                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 9.2L7.8 12L13 6.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @elseif($totalPointsEarned >= 200)
                                    <button type="button" class="btn p-0 border-0 bg-transparent btn-claim-milestone-box" data-milestone="200" title="Nhấn để nhận Hộp Quà Mốc 200 Xu!" style="cursor: pointer; animation: pulse 1.2s infinite;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="10" width="18" height="11" rx="2" fill="#475569" stroke="#475569" stroke-width="1.6"/>
                                            <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#64748b" stroke="#475569" stroke-width="1.6"/>
                                            <path d="M12 6V21" stroke="#ffffff" stroke-width="1.8"/>
                                        </svg>
                                    </button>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Cần 200 xu để mở">
                                        <rect x="3" y="10" width="18" height="11" rx="2" fill="#F8FAFC" stroke="#64748b" stroke-width="1.6"/>
                                        <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#E2E8F0" stroke="#64748b" stroke-width="1.6"/>
                                        <path d="M12 6V21" stroke="#64748b" stroke-width="1.6"/>
                                    </svg>
                                @endif
                                <div class="node-bottom-val {{ $totalPointsEarned >= 200 ? 'text-dark fw-extrabold' : 'text-muted' }}">200</div>
                            </div>

                            <!-- Mốc 3: 500 Xu (Mốc Lớn Tối Đa) -->
                            <div class="milestone-node">
                                <div class="milestone-tooltip">
                                    <div class="d-flex align-items-center gap-1">
                                        @if($frame500)
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <img src="{{ asset($frame500->image_url) }}" alt="{{ $frame500->name }}">
                                                <div class="tooltip-frame-name">Khung {{ $frame500->name }}</div>
                                            </div>
                                        @else
                                            <div class="tooltip-mini-card">
                                                <span class="tooltip-frame-caption">Phần thưởng</span>
                                                <div class="tooltip-frame-name">Mở khung avatar</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($claimed500)
                                    <div style="width: 22px; height: 22px; border-radius: 2px; background: #000000; display: flex; align-items: center; justify-content: center;" title="Đã nhận quà mốc 500 xu">
                                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10.2L8.8 13L14 7.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @elseif($totalPointsEarned >= 500)
                                    <button type="button" class="btn p-0 border-0 bg-transparent btn-claim-milestone-box" data-milestone="500" title="Nhấn để nhận Hộp Quà Mốc 500 Xu Tối Đa!" style="cursor: pointer; animation: pulse 1.2s infinite;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="10" width="18" height="11" rx="2" fill="#000000" stroke="#000000" stroke-width="1.6"/>
                                            <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#565e74" stroke="#000000" stroke-width="1.6"/>
                                            <path d="M12 6V21" stroke="#ffffff" stroke-width="1.8"/>
                                        </svg>
                                    </button>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Đạt 500 xu để mở quà lớn!">
                                        <rect x="3" y="10" width="18" height="11" rx="2" fill="#f2f4f6" stroke="#000000" stroke-width="1.6"/>
                                        <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#e0e3e5" stroke="#000000" stroke-width="1.6"/>
                                        <path d="M12 6V21" stroke="#000000" stroke-width="1.6"/>
                                    </svg>
                                @endif
                                <div class="node-bottom-val {{ $totalPointsEarned >= 500 ? 'text-indigo fw-extrabold' : 'text-muted' }}">500</div>
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
                            @php
                                $sessionMission = $dailyMissions->firstWhere('action_key', 'active_session');
                                $sessionUm = $sessionMission ? $userMissions->get($sessionMission->id) : null;
                                $sessionTarget = $sessionMission ? (int) $sessionMission->target_count : 15;
                                $sessionMinutes = $sessionUm ? (int) $sessionUm->current_count : 0;
                                $sessionPercent = min(100, round(($sessionMinutes / max(1, $sessionTarget)) * 100));
                                $sessionClaimed = $sessionUm && $sessionUm->status === 'claimed';
                                $sessionCompleted = $sessionUm && $sessionUm->status === 'completed';
                            @endphp
                            @if($sessionMission)
                            <div class="quest-item-card quest-unit" data-type="daily">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">{{ $sessionMission->title }}</h6>
                                    <div class="text-muted mb-1" style="font-size: 0.74rem;">{{ $sessionMission->description ?: 'Online đủ thời gian rồi nhận thưởng nhiệm vụ.' }}</div>
                                    <div class="d-flex align-items-center gap-2" style="width: 140px;">
                                        <div class="progress flex-grow-1 rounded-pill" style="height: 4px; background: #e2e8f0;">
                                            <div class="progress-bar bg-primary" id="missionSessionProgressBar" style="width: {{ $sessionPercent }}%;" aria-valuenow="{{ $sessionMinutes }}"></div>
                                        </div>
                                        <span class="text-muted fw-semibold" id="missionSessionProgressText" style="font-size: 0.68rem;">{{ min($sessionMinutes, $sessionTarget) }}/{{ $sessionTarget }} phút</span>
                                    </div>
                                </div>
                                <div class="quest-reward-tag">
                                    @if($sessionMission->reward_points > 0)
                                        +{{ $sessionMission->reward_points }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                    @else
                                        <span class="text-muted" style="font-size:0.72rem;">Theo dõi</span>
                                    @endif
                                </div>
                                    <div>
                                        @if($sessionClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 2px; padding: 5px 14px;">Đã nhận</button>
                                    @elseif($sessionCompleted)
                                        <button class="btn-quest-claim btn-claim-mission" data-id="{{ $sessionMission->id }}">Nhận thưởng</button>
                                    @else
                                        <button class="btn-quest-action" disabled style="opacity: 0.7;">Đang tích lũy</button>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @forelse($dailyMissions as $mission)
                                @if($mission->action_key === 'daily_login' || $mission->action_key === 'active_session' || str_contains(mb_strtolower($mission->title), 'điểm danh'))
                                    @continue
                                @endif
                                @php
                                    $um = $userMissions->get($mission->id);
                                    $currentProgress = $um ? $um->current_count : 0;
                                    $isCompleted = $um && ($um->status === 'completed' || $um->status === 'claimed');
                                    $isClaimed = $um && $um->status === 'claimed';
                                    $percent = min(100, round(($currentProgress / max(1, $mission->target_count)) * 100));
                                @endphp
                                <div class="quest-item-card quest-unit" data-type="daily">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">{{ $mission->title }}</h6>
                                        <div class="text-muted mb-1" style="font-size: 0.74rem;">{{ $mission->description }}</div>
                                        <div class="d-flex align-items-center gap-2" style="width: 130px;">
                                            <div class="progress flex-grow-1 rounded-pill" style="height: 4px; background: #e2e8f0;">
                                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%;"></div>
                                            </div>
                                            <span class="text-muted fw-semibold" style="font-size: 0.68rem;">{{ $currentProgress }}/{{ $mission->target_count }}</span>
                                        </div>
                                    </div>
                                    <div class="quest-reward-tag">
                                        @if($mission->reward_points > 0)
                                            +{{ $mission->reward_points }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                        @else
                                            <span class="text-muted" style="font-size:0.72rem;">Xu tức thì</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 2px; padding: 5px 14px;">Hoàn thành</button>
                                        @elseif($isCompleted)
                                            <button class="btn-quest-claim btn-claim-mission" data-id="{{ $mission->id }}">Nhận thưởng</button>
                                        @else
                                            <a href="{{ url('/') }}" class="btn-quest-action text-decoration-none d-inline-block">Đến ngay</a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                            @endforelse

                            @php $otherMissions = $weeklyMissions->concat($achievementMissions); @endphp
                            @foreach($otherMissions as $mission)
                                @if($mission->action_key === 'daily_login' || $mission->action_key === 'active_session')
                                    @continue
                                @endif
                                @php
                                    $um = $userMissions->get($mission->id);
                                    $currentProgress = $um ? $um->current_count : 0;
                                    $isCompleted = $um && ($um->status === 'completed' || $um->status === 'claimed');
                                    $isClaimed = $um && $um->status === 'claimed';
                                    $percent = min(100, round(($currentProgress / max(1, $mission->target_count)) * 100));
                                @endphp
                                <div class="quest-item-card quest-unit" data-type="{{ $mission->type }}">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">{{ $mission->title }}</h6>
                                        <div class="text-muted mb-1" style="font-size: 0.74rem;">{{ $mission->description }}</div>
                                        <span class="badge px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; background: #f1f5f9; color: #475569;">
                                            {{ $mission->type === 'weekly' ? 'Hàng tuần' : 'Thành tựu' }}
                                        </span>
                                    </div>
                                    <div class="quest-reward-tag">
                                        @if($mission->reward_points > 0)
                                            +{{ $mission->reward_points }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                        @else
                                            <span class="text-muted" style="font-size:0.72rem;">Thành tích</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 2px; padding: 5px 14px;">Đã nhận</button>
                                        @elseif($isCompleted)
                                            <button class="btn-quest-claim btn-claim-mission" data-id="{{ $mission->id }}">Nhận thưởng</button>
                                        @else
                                            <a href="{{ url('/') }}" class="btn-quest-action text-decoration-none d-inline-block">Đến ngay</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-4 col-xl-4">
                    <!-- Widget: Nhiệm vụ hàng ngày -->
                    @php
                        $dailyTrack = $dailyMissions ?? collect();
                        $dailyTotal = $dailyTrack->count();
                        $dailyDone = 0;
                        $dailyRewardSum = 0;
                        $dailyClaimable = 0;
                        foreach ($dailyTrack as $dm) {
                            $dailyRewardSum += (int) $dm->reward_points;
                            $dum = $userMissions->get($dm->id);
                            if ($dum && in_array($dum->status, ['completed', 'claimed'], true)) {
                                $dailyDone++;
                            }
                            if ($dum && $dum->status === 'completed') {
                                $dailyClaimable++;
                            }
                        }
                        $dailyPercent = $dailyTotal > 0 ? (int) round(($dailyDone / $dailyTotal) * 100) : 0;
                        $secondsToMidnight = max(0, (int) now()->diffInSeconds(now()->endOfDay(), false));
                        if ($secondsToMidnight < 0) {
                            $secondsToMidnight = 0;
                        }
                    @endphp
                    <div class="widget-card" id="dailySummaryWidget">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;">Nhiệm vụ hàng ngày</h6>
                            <span class="badge bg-light text-muted fw-semibold" style="font-size: 0.68rem;" title="Thời gian còn lại đến khi reset nhiệm vụ ngày">
                                <span id="dailyResetCountdown" data-seconds="{{ $secondsToMidnight }}">--:--:--</span>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="circle-progress-box" style="{{ $dailyPercent >= 100 ? 'border-color:#166534;color:#166534;background:#f0fdf4;' : '' }}">
                                <span id="dailySummaryCount">{{ $dailyDone }}/{{ max(1, $dailyTotal) }}</span>
                            </div>
                            <div class="flex-grow-1">
                                @if($dailyTotal === 0)
                                    <div class="fw-bold text-dark" style="font-size: 0.82rem;">Chưa có nhiệm vụ ngày</div>
                                    <div class="text-muted" style="font-size: 0.74rem;">Quay lại sau khi hệ thống cập nhật.</div>
                                @elseif($dailyDone >= $dailyTotal)
                                    <div class="fw-bold text-dark" style="font-size: 0.82rem;">Đã hoàn thành hôm nay</div>
                                    <div class="text-muted mb-1.5" style="font-size: 0.74rem;">
                                        @if($dailyClaimable > 0)
                                            Còn {{ $dailyClaimable }} phần thưởng chưa nhận — bấm “Nhận thưởng” bên trái.
                                        @else
                                            Reset sau <span class="daily-reset-hint">00:00</span> ngày mai.
                                        @endif
                                    </div>
                                @else
                                    <div class="fw-bold text-dark" style="font-size: 0.82rem;">Hoàn thành {{ $dailyTotal }} nhiệm vụ ngày</div>
                                    <div class="text-muted mb-1.5" style="font-size: 0.74rem;">
                                        Tổng thưởng ngày tới
                                        <strong class="text-dark">{{ number_format($dailyRewardSum) }} xu
                                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                        </strong>
                                    </div>
                                @endif
                                <div class="progress rounded-pill" style="height: 5px; width: 100%; max-width: 160px; background: #e2e8f0;">
                                    <div class="progress-bar" id="dailySummaryBar" style="width: {{ $dailyPercent }}%; background: {{ $dailyPercent >= 100 ? '#000000' : '#000000' }};"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widget 3: Bảng xếp hạng -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;">Bảng xếp hạng</h6>
                            <a href="#" class="text-muted fw-semibold" style="font-size: 0.74rem; text-decoration: none;" onclick="return false;">Xem tất cả &gt;</a>
                        </div>

                        <div class="d-flex flex-column gap-1">
                            @forelse($leaderboard as $index => $topUser)
                                @php $rank = $index + 1; @endphp
                                <div class="leaderboard-row {{ $user && $topUser->id == $user->id ? 'highlight' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rank-badge {{ $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : 'rank-other')) }}">
                                            {{ $rank }}
                                        </div>
                                        <x-user-avatar :user="$topUser" size="26" />
                                        <span class="text-dark small text-truncate" style="max-width: 110px;">{{ $topUser->display_name ?? $topUser->username }}</span>
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">
                                        {{ number_format($topUser->points) }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 14px; height: 14px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- TAB 3: TỦ QUÀ -->
        <div class="tab-pane fade" id="inventory-pane" role="tabpanel">
            <div class="ms-section-head">
                <div>
                    <h2>Tủ khung cá nhân</h2>
                    <p>Khung avatar và vật phẩm bạn đã sở hữu</p>
                </div>
                @if($user && $user->equipped_frame_id)
                    <button class="btn btn-outline-compact btn-unequip-frame" type="button" style="width: auto; padding: 7px 16px;">
                        Tháo khung
                    </button>
                @endif
            </div>

            <div class="main-section-card">
                <div class="row g-3">
                            @php $myFrames = $allFrames->whereIn('id', $unlockedFrameIds); @endphp
                            @forelse($myFrames as $frame)
                                @php $isEquipped = ($user && $user->equipped_frame_id == $frame->id); @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="p-3 border text-center bg-white" style="border-radius: 2px;">
                                        <div class="avatar-frame-wrapper {{ $frame->image_url ? 'has-png-frame' : $frame->css_style }} mx-auto mb-2" style="width: 56px; height: 56px;">
                                            <img src="{{ $user ? $user->avatar_formatted_url : 'https://ui-avatars.com/api/?name=Guest&background=6366f1&color=fff' }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Guest&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            @if($frame->image_url)
                                                <img src="{{ asset($frame->image_url) }}" class="avatar-frame-png-overlay">
                                            @endif
                                        </div>
                                        <h6 class="fw-bold text-dark text-truncate mb-1" style="font-size: 0.86rem;">{{ $frame->name }}</h6>
                                        <div class="text-muted text-truncate small mb-2" style="font-size: 0.75rem;">{{ $frame->description }}</div>
                                        @if($isEquipped)
                                            <button class="btn btn-secondary btn-sm w-100 fw-bold rounded-pill" disabled style="font-size: 0.78rem;">Đang đeo</button>
                                        @else
                                            <button class="btn btn-indigo btn-sm w-100 btn-equip-frame" data-id="{{ $frame->id }}" style="font-size: 0.78rem;">Trang bị</button>
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

<!-- Custom Reward Item Cards Modal -->
<div id="rewardCustomModal" class="reward-modal-backdrop d-none">
    <div class="reward-modal-dialog">
        <div class="reward-modal-card">
            <h5 class="reward-modal-header-title" id="rewardModalTitle">Phần thưởng đạt được</h5>

            <!-- Reward Item Cards Grid -->
            <div class="reward-cards-container" id="rewardCardsContainer">
                
                <!-- Card 1: Avatar Frame Card -->
                <div class="reward-item-card d-none" id="cardAvatarFrame">
                    <input type="checkbox" class="reward-card-checkbox" id="chkEquipFrame" checked title="Tích chọn để trang bị khung này">
                    <div class="reward-card-preview">
                        <div class="reward-avatar-preview-box">
                            <img src="{{ $user ? $user->avatar_formatted_url : 'https://ui-avatars.com/api/?name=Guest&background=6366f1&color=fff' }}" class="reward-avatar-img">
                            <img src="" id="rewardFrameOverlay" class="reward-frame-overlay-img">
                        </div>
                    </div>
                    <div class="reward-card-label" id="rewardFrameName">Khung Avatar</div>
                </div>

                <!-- Card 2: Coins Reward Card -->
                <div class="reward-item-card" id="cardCoinsReward">
                    <div class="reward-card-preview">
                        <div class="coin-card-icon-box">
                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                    </div>
                    <div class="reward-card-label text-warning fw-extrabold fs-6" id="rewardCoinsVal">+100 Xu</div>
                </div>

            </div>

            <div class="reward-modal-actions-row">
                <button type="button" class="btn btn-reward-equip d-none" id="rewardModalBtnEquip" onclick="equipRewardFrame()">Trang bị</button>
                <button type="button" class="btn btn-reward-close" id="rewardModalBtnClose" onclick="closeRewardModal()">Đóng</button>
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
    document.querySelectorAll('.ms-cat-chip').forEach(b => {
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
            'all': 'Tất cả khung',
            'avatar_frame': 'Khung Avatar độc quyền',
            'exclusive': 'Khung avatar'
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
            q.style.setProperty('display', 'grid', 'important');
        } else {
            q.style.setProperty('display', 'none', 'important');
        }
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = "{{ csrf_token() }}";

    // Đếm ngược đến lúc reset nhiệm vụ ngày (00:00)
    (function initDailyResetCountdown() {
        const el = document.getElementById('dailyResetCountdown');
        if (!el) return;
        let remain = parseInt(el.getAttribute('data-seconds') || '0', 10);
        if (isNaN(remain) || remain < 0) remain = 0;

        function pad(n) { return String(n).padStart(2, '0'); }
        function render() {
            const h = Math.floor(remain / 3600);
            const m = Math.floor((remain % 3600) / 60);
            const s = remain % 60;
            el.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
            document.querySelectorAll('.daily-reset-hint').forEach(function (hint) {
                hint.textContent = el.textContent;
            });
        }
        render();
        setInterval(function () {
            if (remain <= 0) {
                el.textContent = '00:00:00';
                return;
            }
            remain -= 1;
            render();
        }, 1000);
    })();

    function updatePointsUI(points, streak) {
        document.querySelectorAll('#headerUserPoints, #navbarUserPoints').forEach(el => {
            if (el) el.textContent = new Intl.NumberFormat().format(points) + (el.id === 'navbarUserPoints' ? ' xu' : '');
        });
        if (streak !== undefined) {
            const streakEl = document.getElementById('headerStreakCount');
            if (streakEl) streakEl.textContent = streak;
        }
    }

    let currentUnlockedFrameId = null;

    window.showRewardModal = function(options) {
        const modalEl = document.getElementById('rewardCustomModal');
        const titleEl = document.getElementById('rewardModalTitle');
        const cardAvatarFrame = document.getElementById('cardAvatarFrame');
        const frameOverlay = document.getElementById('rewardFrameOverlay');
        const frameNameEl = document.getElementById('rewardFrameName');
        const cardCoinsReward = document.getElementById('cardCoinsReward');
        const coinsValEl = document.getElementById('rewardCoinsVal');
        const btnEquip = document.getElementById('rewardModalBtnEquip');

        if (!modalEl) {
            alert(options.message || options.title);
            return;
        }

        titleEl.textContent = options.title || (options.isError ? 'Thông báo' : 'Phần thưởng đạt được');

        currentUnlockedFrameId = null;

        const chkEquipFrame = document.getElementById('chkEquipFrame');
        if (chkEquipFrame) chkEquipFrame.checked = true;

        // Frame Card
        if (options.frame && options.frame.image_url) {
            currentUnlockedFrameId = options.frame.id;
            frameOverlay.src = options.frame.image_url;
            if (frameNameEl) frameNameEl.textContent = options.frame.name || 'Khung Avatar';
            if (cardAvatarFrame) cardAvatarFrame.classList.remove('d-none');
            if (btnEquip) {
                btnEquip.classList.remove('d-none');
                btnEquip.textContent = 'Trang bị';
            }
        } else {
            if (cardAvatarFrame) cardAvatarFrame.classList.add('d-none');
            if (btnEquip) btnEquip.classList.add('d-none');
        }

        // Coins Card
        let coinsAmount = options.coins || null;
        if (!coinsAmount && options.message) {
            const match = options.message.match(/\+(\d+)\s*xu/i);
            if (match) coinsAmount = match[1];
        }

        if (coinsAmount && cardCoinsReward) {
            if (coinsValEl) coinsValEl.textContent = '+' + coinsAmount + ' Xu';
            cardCoinsReward.classList.remove('d-none');
        } else if (cardCoinsReward) {
            cardCoinsReward.classList.add('d-none');
        }

        modalEl.classList.remove('d-none');
    };

    window.equipRewardFrame = function() {
        if (!currentUnlockedFrameId) {
            closeRewardModal();
            return;
        }

        const frameIdToEquip = currentUnlockedFrameId;
        fetch("{{ route('client.avatar_frames.equip') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ frame_id: frameIdToEquip })
        })
        .then(res => res.json())
        .then(data => {
            closeRewardModal();
        });
    };

    window.closeRewardModal = function() {
        const modalEl = document.getElementById('rewardCustomModal');
        if (modalEl) modalEl.classList.add('d-none');
        location.reload();
    };

    const chkEquipFrameEl = document.getElementById('chkEquipFrame');
    if (chkEquipFrameEl) {
        chkEquipFrameEl.addEventListener('change', function() {
            const btnEquip = document.getElementById('rewardModalBtnEquip');
            if (btnEquip) {
                if (this.checked) {
                    btnEquip.classList.remove('d-none');
                } else {
                    btnEquip.classList.add('d-none');
                }
            }
        });
    }

    // Claim Milestone Gift Box (100, 200, 500)
    document.querySelectorAll('.btn-claim-milestone-box').forEach(btn => {
        btn.addEventListener('click', function() {
            const milestoneVal = this.getAttribute('data-milestone');
            fetch("{{ route('client.missions.claim_milestone') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ milestone: milestoneVal })
            })
            .then(res => res.json())
            .then(data => {
                showRewardModal({
                    title: data.success ? 'Nhận phần thưởng mốc' : 'Thông báo',
                    message: data.message,
                    coins: data.coins || null,
                    isError: !data.success,
                    frame: data.frame || null
                });
            });
        });
    });

    // Toggle milestone tooltip popovers on click / touch
    document.querySelectorAll('.milestone-node').forEach(node => {
        node.addEventListener('click', function(e) {
            if (e.target.closest('.btn-claim-milestone-box')) return;
            e.stopPropagation();
            const isActive = this.classList.contains('active');
            document.querySelectorAll('.milestone-node').forEach(n => n.classList.remove('active'));
            if (!isActive) this.classList.add('active');
        });
    });

    document.addEventListener('click', function() {
        document.querySelectorAll('.milestone-node').forEach(n => n.classList.remove('active'));
    });

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
            showRewardModal({
                title: data.success ? 'Điểm danh thành công' : 'Thông báo',
                message: data.message,
                isError: !data.success,
                frame: data.frame || null
            });
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
                showRewardModal({
                    title: data.success ? '🏆 HOÀN THÀNH NHIỆM VỤ!' : 'THÔNG BÁO',
                    message: data.message,
                    isError: !data.success,
                    icon: 'fa-solid fa-trophy',
                    onConfirm: function() {
                        if (data.success) location.reload();
                    }
                });
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
                showRewardModal({
                    title: data.success ? '✨ TRANG BỊ KHUNG THÀNH CÔNG!' : 'THÔNG BÁO',
                    message: data.message,
                    isError: !data.success,
                    icon: 'fa-solid fa-circle-user',
                    onConfirm: function() {
                        if (data.success) location.reload();
                    }
                });
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
                showRewardModal({
                    title: data.success ? ' THÁO KHUNG THÀNH CÔNG!' : 'THÔNG BÁO',
                    message: data.message,
                    isError: !data.success,
                    icon: 'fa-solid fa-circle-minus',
                    onConfirm: function() {
                        if (data.success) location.reload();
                    }
                });
            });
        });
    }

    // Buy Frame
    document.querySelectorAll('.btn-buy-frame').forEach(btn => {
        btn.addEventListener('click', function() {
            const frameId = this.dataset.id;
            const points = this.dataset.points;
            const frameName = this.dataset.name || 'Khung Avatar';

            Swal.fire({
                title: 'Xác nhận đổi khung',
                html: `Bạn có chắc chắn muốn dùng <strong style="color: #735c00;">${points} xu</strong> để đổi Khung Avatar <strong>"${frameName}"</strong> này không?`,
                icon: 'question',
                iconColor: '#1e3a5f',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý đổi',
                cancelButtonText: 'Hủy bỏ',
                reverseButtons: true,
                customClass: {
                    popup: 'custom-swal-popup',
                    title: 'custom-swal-title',
                    htmlContainer: 'custom-swal-text',
                    confirmButton: 'custom-swal-confirm-btn',
                    cancelButton: 'custom-swal-cancel-btn'
                },
                buttonsStyling: false
            }).then((result) => {
                if (!result.isConfirmed) return;

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
                        showRewardModal({
                            title: '🎉 ĐỔI KHUNG THÀNH CÔNG!',
                            message: data.message,
                            isError: false,
                            frame: data.frame || null,
                            icon: 'fa-solid fa-store'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            iconColor: '#dc2626',
                            title: 'Thông báo',
                            text: data.message || 'Không thể đổi khung avatar!',
                            confirmButtonText: 'Đóng',
                            customClass: {
                                popup: 'custom-swal-popup',
                                title: 'custom-swal-title',
                                htmlContainer: 'custom-swal-text',
                                confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        iconColor: '#dc2626',
                        title: 'Lỗi hệ thống',
                        text: 'Không thể kết nối máy chủ!',
                        confirmButtonText: 'Đóng',
                        customClass: {
                            popup: 'custom-swal-popup',
                            title: 'custom-swal-title',
                            htmlContainer: 'custom-swal-text',
                            confirmButton: 'custom-swal-confirm-btn custom-swal-confirm-danger'
                        },
                        buttonsStyling: false
                    });
                });
            });
        });
    });

    // Restore active tab from hash or localStorage
    let activeTab = 'quests-pane';
    if (window.location.hash) {
        const hash = window.location.hash.replace('#', '');
        if (['quests-pane', 'shop-pane', 'inventory-pane'].includes(hash)) {
            activeTab = hash;
        }
    } else if (localStorage.getItem('active_mission_tab')) {
        const saved = localStorage.getItem('active_mission_tab');
        if (['quests-pane', 'shop-pane', 'inventory-pane'].includes(saved)) {
            activeTab = saved;
        }
    }

    if (activeTab) {
        window.switchNavTab(activeTab);
    }
});

// Global tab switch function
window.switchNavTab = function(paneId) {
    if (!paneId) return;
    paneId = paneId.replace('#', '');
    localStorage.setItem('active_mission_tab', paneId);
    if (history.replaceState) {
        history.replaceState(null, null, '#' + paneId);
    }

    // Dynamically update Brand Header Title & Subtitle
    const hintMap = {
        'quests-pane': 'Hoàn thành nhiệm vụ để nhận xu mỗi ngày',
        'shop-pane': 'Dùng xu đổi quà và khung avatar',
        'inventory-pane': 'Khung avatar và vật phẩm đã sở hữu'
    };

    if (hintMap[paneId]) {
        const subEl = document.getElementById('headerBrandSub');
        if (subEl) subEl.textContent = hintMap[paneId];
    }

    document.querySelectorAll('#mainTabContent > .tab-pane').forEach(pane => {
        if (pane.id === paneId) {
            pane.classList.add('show', 'active');
            pane.style.setProperty('display', 'block', 'important');
        } else {
            pane.classList.remove('show', 'active');
            pane.style.setProperty('display', 'none', 'important');
        }
    });

    const tabBtn = document.querySelector(`[data-bs-target="#${paneId}"]`);
    if (tabBtn) {
        document.querySelectorAll('#rewardTopNav .nav-link').forEach(btn => btn.classList.remove('active'));
        tabBtn.classList.add('active');
    }
};

window.redeemReward = function(rewardId, btn) {
    if (!btn || btn.disabled) return;

    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Đang đổi...';

    fetch("{{ url('/rewards/redeem') }}/" + rewardId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            showShopNotice('Đổi thưởng thành công', data.message || 'Phần thưởng đã được ghi nhận.', 'voucher');
            btn.textContent = 'Đã đổi';
            const pointsEl = document.getElementById('userPointsDisplay');
            if (pointsEl && typeof data.points !== 'undefined') {
                pointsEl.textContent = new Intl.NumberFormat('vi-VN').format(data.points);
            }
            setTimeout(() => window.location.reload(), 1200);
            return;
        }

        showShopNotice('Không thể đổi thưởng', data.message || 'Vui lòng thử lại sau.', 'info');
        btn.disabled = false;
        btn.textContent = originalText;
    })
    .catch(() => {
        showShopNotice('Lỗi', 'Không thể kết nối máy chủ.', 'info');
        btn.disabled = false;
        btn.textContent = originalText;
    });
};

window.showShopNotice = function(title, message, iconType = 'info') {
    const modalEl = document.getElementById('shopNoticeModal');
    const titleEl = document.getElementById('shopNoticeTitle');
    const messageEl = document.getElementById('shopNoticeMessage');
    const iconEl = document.getElementById('shopNoticeIcon');

    if (!modalEl) return;

    if (titleEl) titleEl.innerText = title;
    if (messageEl) messageEl.innerText = message;

    if (iconEl) {
        iconEl.style.backgroundColor = '#f2f4f6';
        iconEl.style.borderColor = '#e0e3e5';
        iconEl.style.color = '#000000';
        if (iconType === 'voucher') {
            iconEl.innerHTML = '<i class="fa-solid fa-ticket"></i>';
        } else if (iconType === 'badge') {
            iconEl.innerHTML = '<i class="fa-solid fa-medal"></i>';
        } else {
            iconEl.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
        }
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
};

window.showShopToast = function(message) {
    const toast = document.getElementById('shopToastNotification');
    const toastText = document.getElementById('shopToastText');
    if (!toast) return;

    if (toastText) toastText.innerText = message;

    toast.style.opacity = '1';
    toast.style.pointerEvents = 'auto';
    toast.style.transform = 'translateX(-50%) translateY(0)';

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.pointerEvents = 'none';
        toast.style.transform = 'translateX(-50%) translateY(-30px)';
    }, 2500);
};

window.copyReferralLink = function() {
    navigator.clipboard.writeText(window.location.origin);
    showShopToast('Đã sao chép liên kết mời thành công!');
};
</script>

<!-- Custom Shop Notice Modal -->
<div class="modal fade modal-confirm-backdrop" id="shopNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" id="shopNoticeIcon" style="width: 60px; height: 60px; background-color: #eff6ff; color: #3b82f6; border-radius: 50%; font-size: 1.5rem; border: 1px solid #dbeafe;">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <h5 class="fw-bold mb-2" id="shopNoticeTitle" style="color: #000000; font-size: 1.1rem;">
                    Thông báo
                </h5>
                <p class="text-secondary small mb-4" id="shopNoticeMessage" style="color: #64748b; line-height: 1.5; font-size: 0.875rem;">
                    Nội dung thông báo...
                </p>
                <button type="button" class="btn px-4 py-2 w-100" data-bs-dismiss="modal" style="background: #000000; border-color: #000000; color: white; font-weight: 600; border-radius: 2px; font-size: 0.85rem;">
                    Đã hiểu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Shop Toast Container -->
<div id="shopToastNotification" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%) translateY(-12px); background: #ffffff; border: 1px solid #e0e3e5; color: #191c1e; padding: 10px 18px; border-radius: 10px; font-size: 0.82rem; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08); opacity: 0; pointer-events: none; transition: all 0.25s ease; z-index: 10000; font-family: 'Be Vietnam Pro', sans-serif;">
    <i class="fa-solid fa-circle-check" style="color: #166534; font-size: 1rem;"></i>
    <span id="shopToastText">Nội dung thông báo</span>
</div>
@endpush
