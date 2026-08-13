@extends('client.layouts.app')

@php
    $tamChucPath = public_path('images/tam_chuc.png');
    $brainImgPath = 'C:/Users/admin/.gemini/antigravity-ide/brain/f933e8c2-b2d8-4fc9-a5ac-e95113e6f2a8/tam_chuc_background_1786629761917.png';
    if (!file_exists($tamChucPath) && file_exists($brainImgPath)) {
        @copy($brainImgPath, $tamChucPath);
    }
@endphp

@section('title', 'Trang chủ — Cổng Thông Tin Du Lịch Ninh Bình 360°')

@section('content')
<div class="landing-vr360-container">
    {{-- Full-screen Background Panorama Image (Tam Chúc Pagoda) --}}
    <img src="{{ asset('images/tam_chuc.png') }}" 
         alt="Ninh Bình VR360 - Cảnh đẹp Chùa Tam Chúc" 
         class="landing-bg-img"
         onerror="this.src='{{ asset('images/trag.png') }}'">

    {{-- Ambient Overlay Mask for Contrast --}}
    <div class="landing-overlay-mask"></div>

    {{-- Center Hero Content Box --}}
    <div class="landing-hero-center">
        <h1 class="landing-title">
            <span class="title-white">MỘT CHẠM ĐẾN</span>
            <span class="title-highlight">NINH BÌNH</span>
        </h1>

        <a href="{{ route('home') }}" class="btn-start-tour" title="Bắt đầu tham quan bản đồ 360°">
            <span class="btn-text">BẮT ĐẦU THAM QUAN</span>
            <span class="btn-icon"><i class="fa-solid fa-compass"></i></span>
        </a>
    </div>
</div>

@push('styles')
<style>
    /* Reset & Fullscreen Enforcements */
    .site-header, footer {
        display: none !important;
    }
    html, body {
        overflow: hidden !important;
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        background-color: #0f172a !important;
        font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .landing-vr360-container {
        position: relative;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* High Quality Panoramic Image Coverage */
    .landing-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        object-fit: cover;
        object-position: center center;
        z-index: 1;
        transform: scale(1.02);
        transition: transform 10s ease-out;
    }

    /* Ambient Vignette & Gradient Mask */
    .landing-overlay-mask {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: radial-gradient(circle at center, rgba(15, 23, 42, 0.12) 0%, rgba(15, 23, 42, 0.4) 70%, rgba(15, 23, 42, 0.65) 100%);
        z-index: 2;
        pointer-events: none;
    }

    /* Center Hero Text and Button Section */
    .landing-hero-center {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 20px;
        max-width: 1000px;
        margin-top: -2vh;
    }

    /* Title Styling: "MỘT CHẠM ĐẾN NINH BÌNH" (Tăng kích thước chữ to hơn) */
    .landing-title {
        margin: 0 0 1.8rem 0;
        font-size: clamp(3rem, 7.5vw, 6.2rem);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: 1px;
        text-transform: uppercase;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.3em;
        user-select: none;
    }

    .title-white {
        color: #ffffff;
        text-shadow: 
            0 4px 20px rgba(0, 0, 0, 0.9),
            0 2px 6px rgba(0, 0, 0, 0.95);
    }

    .title-highlight {
        color: #facc15; /* Golden Yellow matching reference */
        text-shadow: 
            0 4px 20px rgba(0, 0, 0, 0.9),
            0 0 35px rgba(250, 204, 21, 0.5);
    }

    /* Cyan Gradient Action Button ("BẮT ĐẦU THAM QUAN" - Thu nhỏ gọn gàng) */
    .btn-start-tour {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 32px;
        background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 50%, #0284c7 100%);
        color: #ffffff !important;
        font-size: clamp(0.875rem, 1.25vw, 1.05rem);
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-decoration: none !important;
        border-radius: 9999px;
        box-shadow: 
            0 6px 20px rgba(6, 182, 212, 0.45),
            0 0 0 0 rgba(34, 211, 238, 0.4);
        border: 1.5px solid rgba(255, 255, 255, 0.7);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: pulse-glow 2.5s infinite;
        position: relative;
    }

    .btn-start-tour .btn-icon {
        font-size: 1.2em;
        transition: transform 0.3s ease;
    }

    .btn-start-tour:hover {
        transform: translateY(-3px) scale(1.05);
        background: linear-gradient(135deg, #38bdf8 0%, #06b6d4 50%, #0369a1 100%);
        box-shadow: 
            0 14px 40px rgba(6, 182, 212, 0.7),
            0 0 30px rgba(56, 189, 248, 0.6);
        border-color: #ffffff;
    }

    .btn-start-tour:hover .btn-icon {
        transform: rotate(45deg) scale(1.15);
    }

    .btn-start-tour:active {
        transform: translateY(1px) scale(0.98);
        box-shadow: 0 4px 20px rgba(6, 182, 212, 0.4);
    }

    /* Pulse Glow Keyframes */
    @keyframes pulse-glow {
        0% {
            box-shadow: 
                0 8px 30px rgba(6, 182, 212, 0.5),
                0 0 0 0 rgba(34, 211, 238, 0.6);
        }
        70% {
            box-shadow: 
                0 8px 30px rgba(6, 182, 212, 0.5),
                0 0 0 18px rgba(34, 211, 238, 0);
        }
        100% {
            box-shadow: 
                0 8px 30px rgba(6, 182, 212, 0.5),
                0 0 0 0 rgba(34, 211, 238, 0);
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .btn-start-tour {
            padding: 14px 32px;
        }
        .landing-title {
            gap: 0.2em;
        }
    }
</style>
@endpush
@endsection

