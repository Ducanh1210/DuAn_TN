@extends('client.layouts.app')

@section('title', 'Trung Tâm Nhiệm Vụ & Đổi Thưởng - Ninh Bình Travel Hub')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">
<style>
    :root {
        --q-bg: #f8fafc;
        --q-card-bg: #ffffff;
        --q-primary: #1e3a5f;
        --q-primary-hover: #2b4c7e;
        --q-primary-light: #f1f5f9;
        --q-accent-orange: #f59e0b;
        --q-text-main: #0f2442;
        --q-text-sub: #64748b;
        --q-border: #e2e8f0;
    }

    footer {
        display: none !important;
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
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.16);
        border: 1px solid #e2e8f0;
        padding: 24px;
        text-align: center;
    }

    .reward-modal-header-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
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
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
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
        accent-color: #6366f1;
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
        background: #fef9c3;
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
        color: #334155;
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
        background: #6366f1;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 11px 20px;
        border-radius: 14px;
        border: none;
        transition: all 0.2s;
    }

    .btn-reward-equip:hover {
        background: #4f46e5;
    }

    .btn-reward-close {
        flex: 1;
        background: #f1f5f9;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 11px 20px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .btn-reward-close:hover {
        background: #e2e8f0;
        color: #0f172a !important;
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
        gap: 11px;
        text-decoration: none;
    }

    .reward-brand-icon {
        font-size: 1.8rem;
        color: #1e3a5f;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reward-brand-text {
        font-size: 1.02rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
        line-height: 1.25;
        transition: color 0.2s ease;
    }

    .reward-brand-sub {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
        line-height: 1.2;
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
        color: #1e3a5f !important;
    }

    .reward-top-menu .nav-link.active {
        color: #1e3a5f !important;
        font-weight: 700 !important;
        border-bottom: 3px solid #1e3a5f !important;
    }

    .user-point-capsule {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: none;
        padding: 5px 14px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        color: #0f172a;
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
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .sidebar-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        padding: 4px 10px 10px;
        margin-bottom: 4px;
        border-bottom: 1px solid #f1f5f9;
    }

    .reward-cat-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 9px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.84rem;
        color: #475569 !important;
        background: transparent !important;
        border: none !important;
        text-align: left;
        transition: all 0.2s ease;
        margin-bottom: 3px;
        cursor: pointer !important;
        outline: none !important;
    }

    .reward-cat-btn:hover {
        background: #f1f5f9 !important;
        color: #1e3a5f !important;
    }

    .reward-cat-btn.active {
        background: #f1f5f9 !important;
        color: #1e3a5f !important;
        font-weight: 700 !important;
        box-shadow: none !important;
    }

    .reward-cat-btn.active i,
    .reward-cat-btn.active span {
        color: #1e3a5f !important;
    }

    /* Hero Banner (Shop Tab) */
    .reward-hero-compact {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px 28px;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .hero-title-compact {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 6px;
    }

    .hero-sub-compact {
        font-size: 0.84rem;
        color: #64748b;
        margin-bottom: 16px;
    }

    .btn-hero-compact {
        background: #0284c7;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .btn-hero-compact:hover {
        background: #0369a1;
        color: #ffffff;
    }

    /* Top Assurance Bar */
    .top-assurance-compact {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
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
        background: #f0f9ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        border: 1px solid #bae6fd;
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
        border-radius: 12px;
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
        border-color: #cbd5e1;
    }

    .item-img-compact {
        height: 90px;
        background: #f8fafc;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        padding: 8px;
        border: 1px solid #f1f5f9;
    }

    .item-name-compact {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .item-desc-compact {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .item-price-compact {
        font-size: 0.82rem;
        font-weight: 800;
        color: #0284c7;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }

    .btn-action-compact {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 8px;
        padding: 7px;
        border: none;
        width: 100%;
        transition: all 0.2s;
        box-shadow: none;
    }

    .btn-action-compact:hover {
        background: #2b4c7e;
        color: #ffffff;
    }

    .btn-indigo {
        background: #1e3a5f !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }

    .btn-indigo:hover {
        background: #2b4c7e !important;
        box-shadow: none !important;
        color: #ffffff !important;
    }

    .text-indigo {
        color: #1e3a5f !important;
    }

    .btn-outline-compact {
        border: 1px solid #1e3a5f;
        color: #1e3a5f;
        background: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 8px;
        padding: 7px;
        width: 100%;
        transition: all 0.2s;
    }

    .btn-outline-compact:hover {
        background: #1e3a5f;
        color: #ffffff;
    }

    /* Quests Tab Layout Specific Styles */
    .main-section-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .quest-filter-pill {
        border-radius: 8px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .quest-filter-pill.active {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: 700;
        border-color: #1e3a5f;
        box-shadow: none;
    }

    .quest-item-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .quest-item-card:hover {
        border-color: #cbd5e1;
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
        background: #f8fafc;
        color: #1e3a5f;
        border: 1px solid #e2e8f0;
    }

    .quest-reward-tag {
        position: absolute;
        top: 10px;
        right: 14px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #1e3a5f;
        background: transparent;
        border: none;
        padding: 0;
    }

    .btn-quest-action {
        background: #ffffff;
        color: #1e3a5f;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 8px;
        padding: 5px 14px;
        border: 1px solid #1e3a5f;
        transition: all 0.2s ease;
    }

    .btn-quest-action:hover {
        background: #1e3a5f;
        color: #ffffff;
    }

    .btn-quest-claim {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 8px;
        padding: 6px 16px;
        border: none;
        box-shadow: none;
    }

    .btn-quest-claim:hover {
        background: #2b4c7e;
        color: #ffffff;
    }

    .widget-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    .streak-day-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 2px;
        text-align: center;
        flex: 1;
        min-width: 0;
    }

    .streak-day-box.completed {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        opacity: 0.7 !important;
    }

    .streak-day-box.current {
        border: 1.5px solid #1e3a5f !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }

    .streak-day-box.special-day {
        background: #f1f5f9 !important;
        border: 1px solid #cbdbe8 !important;
        color: #1e3a5f !important;
    }

    .streak-day-box.special-day .special-day-title {
        color: #1e3a5f !important;
        font-weight: 700 !important;
    }

    /* Muted disabled check-in button */
    #btnDailyCheckinSide:disabled,
    #btnDailyCheckinShop:disabled,
    .btn-indigo:disabled {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.85;
    }

    .circle-progress-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        color: #0284c7;
        flex-shrink: 0;
        background: #f0f9ff;
    }

    .leaderboard-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .leaderboard-row.highlight {
        background: #f0f9ff;
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

    .rank-1 { background: #0f172a; }
    .rank-2 { background: #475569; }
    .rank-3 { background: #64748b; }
    .rank-other { background: #f1f5f9; color: #64748b; }

    .promo-banner-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    /* TIẾN ĐỘ NHIỆM VỤ Widget (Minimalist & Clean Theme) */
    .quest-progress-card-cream {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
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
        background: #e2e8f0;
        border-radius: 4px;
        transform: translateY(-50%);
        z-index: 1;
    }

    .quest-milestone-line-fill {
        height: 100%;
        background: #0284c7;
        border-radius: 4px;
        transition: width 0.4s ease;
    }

    .quest-coin-badge {
        width: 40px;
        height: 40px;
        background: #0284c7;
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
        color: #94a3b8;
        margin-bottom: 3px;
    }

    .node-icon-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        box-shadow: none;
    }

    .node-icon-gift {
        font-size: 18px;
        color: #94a3b8;
    }

    .node-icon-gift-big {
        font-size: 24px;
        color: #0284c7;
        filter: none;
    }

    .node-bottom-val {
        position: absolute;
        top: 100%;
        margin-top: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        white-space: nowrap;
    }

    .milestone-node .milestone-tooltip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(4px);
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.2), 0 3px 8px rgba(0, 0, 0, 0.06);
        border-radius: 10px;
        padding: 4px;
        white-space: nowrap;
        text-align: center;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tooltip-mini-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        width: 44px;
        height: 44px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2px;
        box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.9);
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
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
        border-color: #bfdbfe;
    }

    .btn-outline-purple {
        border: 1.5px solid #3b82f6;
        color: #2563eb;
        background: transparent;
        border-radius: 20px;
        padding: 4px 16px;
        font-weight: 700;
        font-size: 0.78rem;
        transition: all 0.2s ease;
    }

    .btn-outline-purple:hover {
        background: #2563eb;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<!-- Full Width Header Navigation Bar -->
<div class="reward-top-bar-full">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a href="{{ url('/') }}" class="reward-brand" title="Quay về trang chủ Bản đồ">
            <i class="fa-solid fa-gift reward-brand-icon"></i>
            <div>
                <div class="reward-brand-text" id="headerBrandText">Nhiệm Vụ & Tích Xu</div>
                <div class="reward-brand-sub" id="headerBrandSub">Hoàn thành nhiệm vụ nhận xu hàng ngày</div>
            </div>
        </a>

        <!-- Menu Tabs -->
        <ul class="reward-top-menu nav" id="rewardTopNav" role="tablist">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
            </li>
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
                    Tủ quà của tôi
                </button>
            </li>
        </ul>

        <!-- Points Capsule & Avatar -->
        <div class="d-flex align-items-center gap-3">
            @if($user)
                <div class="user-point-capsule">
                    <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 20px; height: 20px; object-fit: contain; vertical-align: -3px;" class="me-1">
                    <span id="headerUserPoints">{{ number_format($user->points) }}</span>
                </div>

                <div class="position-relative">
                    <i class="fa-solid fa-bell text-muted fs-5 cursor-pointer"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" style="font-size: 0.6rem;">3</span>
                </div>

                <x-user-avatar :user="$user" size="34" />
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm fw-bold px-3" style="border-radius: 8px;">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Đăng nhập
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="reward-full-container">
    <div class="tab-content" id="mainTabContent">
        
        <!-- TAB 1: ĐỔI THƯỞNG (SHOP PANE - MATCHED INDIGO COLOR SCHEME) -->
        <div class="tab-pane fade" id="shop-pane" role="tabpanel">
            <div class="row g-3">
                <!-- LEFT COLUMN: Danh Mục Phần Thưởng Sidebar -->
                <div class="col-lg-3 col-xl-2">
                    <div class="reward-sidebar-compact">
                        <div class="sidebar-title">Danh mục phần thưởng</div>
                        <div class="d-flex flex-column" id="rewardCatList">
                            <button class="reward-cat-btn active" onclick="filterCategory('all', this)" type="button">
                                <span>Tất cả quà</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('voucher', this)" type="button">
                                <span>Voucher &amp; Thẻ nạp</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('badge', this)" type="button">
                                <span>Huy hiệu &amp; Vật phẩm</span>
                            </button>
                            <button class="reward-cat-btn" onclick="filterCategory('exclusive', this)" type="button">
                                <span>Quà độc quyền</span>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Card -->
                    <div class="reward-sidebar-compact mt-2 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px 10px;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">Giới thiệu bạn bè</h6>
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">Nhận ngay <strong>2.000 xu</strong> cho mỗi lượt mời!</p>
                        <button class="btn btn-indigo btn-sm w-100 fw-bold" onclick="copyReferralLink()" style="font-size: 0.78rem; padding: 7px 10px;">
                            Giới thiệu ngay
                        </button>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Shop Banner, Assurance & Gift Grid -->
                <div class="col-lg-9 col-xl-10">




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
                                        <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain; vertical-align: -2px;" class="me-0.5">
                                        <span>5.000 xu</span>
                                    </div>
                                    <button class="btn btn-action-compact" onclick="showShopNotice('Tính năng sắp ra mắt', 'Tính năng đổi voucher sẽ ra mắt trong phiên bản sắp tới!', 'voucher')">
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
                                        <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain; vertical-align: -2px;" class="me-0.5">
                                        <span>3.000 xu</span>
                                    </div>
                                    <button class="btn btn-action-compact" onclick="showShopNotice('Thông tin huy hiệu', 'Huy hiệu sẽ được tự động mở khóa khi đạt mốc thành tựu!', 'badge')">
                                        Nhận huy hiệu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- TAB 2: NHIỆM VỤ (QUESTS PANE - REFERENCED QUEST LAYOUT) -->
        <div class="tab-pane fade show active" id="quests-pane" role="tabpanel">
            <div class="row g-3">
                <!-- LEFT COLUMN: Menu Sidebar -->
                <div class="col-lg-3 col-xl-2">
                    <div class="reward-sidebar-compact">
                        <div class="sidebar-title">Menu Nhiệm vụ</div>
                        <div class="d-flex flex-column">
                            <button class="reward-cat-btn active" type="button">
                                <span>Nhiệm vụ</span>
                            </button>
                            <button class="reward-cat-btn" onclick="switchNavTab('shop-pane')" type="button">
                                <span>Đổi thưởng</span>
                            </button>
                            <button class="reward-cat-btn" onclick="switchNavTab('inventory-pane')" type="button">
                                <span>Tủ quà của tôi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Referral Widget -->
                    <div class="reward-sidebar-compact mt-2 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 14px 10px;">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">Mời bạn bè</h6>
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">Nhận ngay <strong>2.000 xu</strong> cho mỗi lượt mời!</p>
                        <button class="btn btn-indigo btn-sm w-100 fw-bold" onclick="copyReferralLink()" style="font-size: 0.78rem; padding: 7px 10px;">
                            Mời ngay
                        </button>
                    </div>
                </div>

                <!-- CENTER COLUMN: Quest List -->
                <div class="col-lg-5 col-xl-6">
                    @php
                        $totalPointsEarned = \App\Models\PointTransaction::where('user_id', Auth::id())
                            ->where('amount', '>', 0)
                            ->where('action', 'not like', 'daily_milestone_%')
                            ->sum('amount');

                        $claimedMilestones = \App\Models\PointTransaction::where('user_id', Auth::id())
                            ->where('action', 'like', 'daily_milestone_%')
                            ->pluck('action')
                            ->toArray();

                        // Progress percentage for 3 nodes (100, 200, 500)
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

                        $frame100 = \App\Models\AvatarFrame::find(1);
                        $frame200 = \App\Models\AvatarFrame::find(2);
                        $frame500 = \App\Models\AvatarFrame::find(4);
                    @endphp

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
                                        <div class="tooltip-mini-card">
                                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain;" class="mb-0.5">
                                            <div class="fw-extrabold text-dark" style="font-size: 0.58rem;">+10 xu</div>
                                        </div>
                                        @if($frame100)
                                            <div class="tooltip-mini-card">
                                                <img src="{{ asset($frame100->image_url) }}" style="width: 20px; height: 20px; object-fit: contain;" class="mb-0.5" alt="{{ $frame100->name }}">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.55rem; max-width: 38px;">{{ $frame100->name }}</div>
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
                                        <div class="tooltip-mini-card">
                                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain;" class="mb-0.5">
                                            <div class="fw-extrabold text-dark" style="font-size: 0.58rem;">+20 xu</div>
                                        </div>
                                        @if($frame200)
                                            <div class="tooltip-mini-card">
                                                <img src="{{ asset($frame200->image_url) }}" style="width: 20px; height: 20px; object-fit: contain;" class="mb-0.5" alt="{{ $frame200->name }}">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.55rem; max-width: 38px;">{{ $frame200->name }}</div>
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
                                        <div class="tooltip-mini-card">
                                            <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 16px; height: 16px; object-fit: contain;" class="mb-0.5">
                                            <div class="fw-extrabold text-dark" style="font-size: 0.58rem;">+30 xu</div>
                                        </div>
                                        @if($frame500)
                                            <div class="tooltip-mini-card">
                                                <img src="{{ asset($frame500->image_url) }}" style="width: 20px; height: 20px; object-fit: contain;" class="mb-0.5" alt="{{ $frame500->name }}">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.55rem; max-width: 38px;">{{ $frame500->name }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($claimed500)
                                    <div style="width: 22px; height: 22px; border-radius: 50%; background: #0284c7; display: flex; align-items: center; justify-content: center;" title="Đã nhận quà mốc 500 xu">
                                        <svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 10.2L8.8 13L14 7.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @elseif($totalPointsEarned >= 500)
                                    <button type="button" class="btn p-0 border-0 bg-transparent btn-claim-milestone-box" data-milestone="500" title="Nhấn để nhận Hộp Quà Mốc 500 Xu Tối Đa!" style="cursor: pointer; animation: pulse 1.2s infinite;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="10" width="18" height="11" rx="2" fill="#0284c7" stroke="#0284c7" stroke-width="1.6"/>
                                            <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#38bdf8" stroke="#0284c7" stroke-width="1.6"/>
                                            <path d="M12 6V21" stroke="#ffffff" stroke-width="1.8"/>
                                        </svg>
                                    </button>
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" title="Đạt 500 xu để mở quà lớn!">
                                        <rect x="3" y="10" width="18" height="11" rx="2" fill="#E0F2FE" stroke="#0284c7" stroke-width="1.6"/>
                                        <rect x="2" y="6" width="20" height="4" rx="1.5" fill="#BAE6FD" stroke="#0284c7" stroke-width="1.6"/>
                                        <path d="M12 6V21" stroke="#0284c7" stroke-width="1.6"/>
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
                                $todayActiveMinutes = \App\Models\PointTransaction::where('user_id', Auth::id())
                                    ->where('action', 'active_session')
                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                    ->sum('amount');
                                $activePercent = min(100, round(($todayActiveMinutes / 60) * 100));
                            @endphp
                            <!-- Online Active Session Mission Card -->
                            <div class="quest-item-card quest-unit" data-type="daily">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.86rem;">Hoạt động trên trang</h6>
                                    <div class="text-muted mb-1" style="font-size: 0.74rem;">Nhận 1 xu cho mỗi 1 phút online (tối đa 60 xu/ngày)</div>
                                    <div class="d-flex align-items-center gap-2" style="width: 140px;">
                                        <div class="progress flex-grow-1 rounded-pill" style="height: 4px; background: #e2e8f0;">
                                            <div class="progress-bar bg-primary" id="missionSessionProgressBar" style="width: {{ $activePercent }}%;"></div>
                                        </div>
                                        <span class="text-muted fw-semibold" id="missionSessionProgressText" style="font-size: 0.68rem;">{{ $todayActiveMinutes }}/60 phút</span>
                                    </div>
                                </div>
                                <div class="quest-reward-tag">
                                    +60 <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                </div>
                                <div class="text-end pt-3">
                                    @if($todayActiveMinutes >= 60)
                                        <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 8px; padding: 5px 14px;">Đã hoàn thành</button>
                                    @else
                                        <button class="btn-quest-action" disabled style="opacity: 0.7;">Đang tích lũy</button>
                                    @endif
                                </div>
                            </div>

                            @forelse($dailyMissions as $mission)
                                @if(in_array($mission->action_key, ['daily_login', 'active_session']) || str_contains(mb_strtolower($mission->title), 'đăng nhập') || str_contains(mb_strtolower($mission->title), 'điểm danh') || str_contains(mb_strtolower($mission->title), 'truy cập online') || str_contains(mb_strtolower($mission->title), 'thời gian truy cập'))
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
                                        +{{ $mission->reward_points }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                    </div>
                                    <div class="text-end pt-3">
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 8px; padding: 5px 14px;">Đã nhận</button>
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
                                @if(in_array($mission->action_key, ['daily_login', 'active_session']) || str_contains(mb_strtolower($mission->title), 'đăng nhập') || str_contains(mb_strtolower($mission->title), 'điểm danh') || str_contains(mb_strtolower($mission->title), 'truy cập online') || str_contains(mb_strtolower($mission->title), 'thời gian truy cập'))
                                    @continue
                                @endif
                                @php
                                    $um = $userMissions->get($mission->id);
                                    $currentProgress = $um ? $um->current_count : 0;
                                    $isCompleted = $um && ($um->status === 'completed' || $um->status === 'claimed');
                                    $isClaimed = $um && $um->status === 'claimed';
                                    $percent = min(100, round(($currentProgress / $mission->target_count) * 100));
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
                                        +{{ $mission->reward_points }} <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5">
                                    </div>
                                    <div class="text-end pt-3">
                                        @if($isClaimed)
                                            <button class="btn btn-light text-muted fw-bold border" disabled style="font-size: 0.78rem; border-radius: 8px; padding: 5px 14px;">Đã nhận</button>
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

                <!-- RIGHT COLUMN: Multi-Widgets -->
                <div class="col-lg-4 col-xl-4">
                    <!-- Widget 1: Điểm danh mỗi ngày -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Điểm danh mỗi ngày</h6>
                        </div>
                        <div class="text-muted mb-3" style="font-size: 0.74rem;">Điểm danh liên tục để nhận thưởng lớn!</div>

                        <div class="d-flex align-items-center gap-1 mb-3">
                            @php
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

                    <!-- Widget 2: Nhiệm vụ hàng ngày Circular Gauge -->
                    <div class="widget-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;">Nhiệm vụ hàng ngày</h6>
                            <span class="badge bg-light text-muted fw-semibold" style="font-size: 0.68rem;">12:45:30</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="circle-progress-box">
                                <span>2/5</span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.82rem;">Hoàn thành 5 nhiệm vụ</div>
                                <div class="text-muted mb-1.5" style="font-size: 0.74rem;">Nhận thưởng <strong class="text-dark">100 xu <img src="{{ asset('images/xu.png') }}" alt="xu" style="width: 15px; height: 15px; object-fit: contain; vertical-align: -2px;" class="ms-0.5"></strong></div>
                                <div class="progress rounded-pill" style="height: 5px; width: 130px; background: #e2e8f0;">
                                    <div class="progress-bar" style="width: 40%; background: #0284c7;"></div>
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
                            @if($user && $user->equipped_frame_id)
                                <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold btn-unequip-frame" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-xmark me-1"></i> Tháo khung
                                </button>
                            @endif
                        </div>

                        <div class="row g-3">
                            @php $myFrames = $allFrames->whereIn('id', $unlockedFrameIds); @endphp
                            @forelse($myFrames as $frame)
                                @php $isEquipped = ($user && $user->equipped_frame_id == $frame->id); @endphp
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="p-3 border rounded-3 text-center bg-white">
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
                showRewardModal({
                    title: data.success ? '🎉 ĐỔI KHUNG THÀNH CÔNG!' : 'THÔNG BÁO',
                    message: data.message,
                    isError: !data.success,
                    icon: 'fa-solid fa-store',
                    onConfirm: function() {
                        if (data.success) location.reload();
                    }
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
    const brandMap = {
        'quests-pane': {
            title: 'Nhiệm Vụ & Tích Xu',
            sub: 'Hoàn thành nhiệm vụ nhận xu hàng ngày'
        },
        'shop-pane': {
            title: 'Cửa Hàng Đổi Thưởng',
            sub: 'Dùng xu đổi quà & khung avatar độc quyền'
        },
        'inventory-pane': {
            title: 'Tủ Khung Cá Nhân',
            sub: 'Bộ sưu tập khung & vật phẩm đã sở hữu'
        }
    };

    if (brandMap[paneId]) {
        const textEl = document.getElementById('headerBrandText');
        const subEl = document.getElementById('headerBrandSub');

        if (textEl) textEl.textContent = brandMap[paneId].title;
        if (subEl) subEl.textContent = brandMap[paneId].sub;
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

window.showShopNotice = function(title, message, iconType = 'info') {
    const modalEl = document.getElementById('shopNoticeModal');
    const titleEl = document.getElementById('shopNoticeTitle');
    const messageEl = document.getElementById('shopNoticeMessage');
    const iconEl = document.getElementById('shopNoticeIcon');

    if (!modalEl) return;

    if (titleEl) titleEl.innerText = title;
    if (messageEl) messageEl.innerText = message;

    if (iconEl) {
        if (iconType === 'voucher') {
            iconEl.style.backgroundColor = '#fef9c3';
            iconEl.style.borderColor = '#fef08a';
            iconEl.style.color = '#d97706';
            iconEl.innerHTML = '<i class="fa-solid fa-ticket"></i>';
        } else if (iconType === 'badge') {
            iconEl.style.backgroundColor = '#fee2e2';
            iconEl.style.borderColor = '#fecdd3';
            iconEl.style.color = '#dc2626';
            iconEl.innerHTML = '<i class="fa-solid fa-medal"></i>';
        } else {
            iconEl.style.backgroundColor = '#eff6ff';
            iconEl.style.borderColor = '#dbeafe';
            iconEl.style.color = '#3b82f6';
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
                <h5 class="fw-bold mb-2" id="shopNoticeTitle" style="color: #1e3a5f; font-size: 1.1rem; font-family: 'Plus Jakarta Sans', sans-serif;">
                    Thông báo
                </h5>
                <p class="text-secondary small mb-4" id="shopNoticeMessage" style="color: #64748b; line-height: 1.5; font-size: 0.875rem;">
                    Nội dung thông báo...
                </p>
                <button type="button" class="btn px-4 py-2 w-100" data-bs-dismiss="modal" style="background: #1e3a5f; border-color: #1e3a5f; color: white; font-weight: 600; border-radius: 12px; font-size: 0.85rem;">
                    Đã hiểu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Shop Toast Container -->
<div id="shopToastNotification" style="position: fixed; top: 24px; left: 50%; transform: translateX(-50%) translateY(-30px); background: rgba(15, 23, 42, 0.92); backdrop-filter: blur(16px); color: #ffffff; padding: 12px 24px; border-radius: 50px; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 10px; box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3); opacity: 0; pointer-events: none; transition: all 0.35s ease; z-index: 10000; font-family: 'Be Vietnam Pro', sans-serif;">
    <i class="fa-solid fa-circle-check text-success fs-5"></i>
    <span id="shopToastText">Nội dung thông báo</span>
</div>
@endpush
