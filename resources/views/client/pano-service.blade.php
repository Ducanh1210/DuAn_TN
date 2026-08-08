@extends('client.layouts.app')

@section('title', 'Dịch vụ tour 360 — Ninh Bình Travel Hub')

@push('styles')
<style>
    .ps-page {
        --ps-heading: #1e3a5f;
        --ps-ink: #27272a;
        --ps-body: #3b5980;
        --ps-muted: #6482a6;
        --ps-line: #cbdbe8;
        --ps-soft: #e5e7eb;
        --ps-mist: #f1f5f9;
        color: var(--ps-body);
    }
    .ps-page .ps-kicker {
        font-size: 0.72rem;
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--ps-muted);
        margin-bottom: 8px;
    }
    .ps-page h1 {
        font-size: 1.45rem !important;
        font-weight: 600 !important;
        color: var(--ps-heading);
        letter-spacing: -0.02em;
        line-height: 1.35 !important;
        margin-bottom: 12px;
    }
    .ps-page .ps-lead {
        font-size: 0.95rem;
        font-weight: 400;
        line-height: 1.65;
        color: var(--ps-body);
        max-width: 46ch;
        margin-bottom: 20px;
    }
    .ps-hero {
        background: var(--ps-mist);
        border-bottom: 1px solid var(--ps-soft);
    }
    .ps-hero-grid {
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        gap: 28px;
        align-items: center;
    }
    .ps-visual {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--ps-soft);
        background: #e8eef4;
        min-height: 260px;
        aspect-ratio: 16 / 10;
    }
    .ps-visual img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .ps-visual::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 64px;
        background: linear-gradient(to right, #f1f5f9 0%, rgba(241,245,249,0) 100%);
        pointer-events: none;
        z-index: 1;
    }
    .ps-demo-link {
        position: absolute;
        left: 14px;
        bottom: 14px;
        z-index: 2;
        padding: 7px 12px;
        border-radius: 8px;
        background: rgba(255,255,255,0.94);
        border: 1px solid rgba(203,219,232,0.95);
        color: var(--ps-heading);
        font-size: 0.75rem;
        font-weight: 500;
        text-decoration: none;
    }
    .ps-demo-link:hover { color: #2b4c7e; }
    .ps-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .ps-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none !important;
        border: 1px solid transparent;
        transition: background .15s ease, border-color .15s ease;
    }
    .ps-btn-primary {
        background: #1e3a5f;
        border-color: #1e3a5f;
        color: #fff !important;
    }
    .ps-btn-primary:hover { background: #2b4c7e; border-color: #2b4c7e; color: #fff !important; }
    .ps-btn-ghost {
        background: #fff;
        border-color: var(--ps-line);
        color: var(--ps-heading) !important;
    }
    .ps-btn-ghost:hover { background: var(--ps-mist); }
    .ps-note {
        margin-top: 14px;
        font-size: 0.78rem;
        color: var(--ps-muted);
    }
    .ps-section-title {
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        color: var(--ps-heading);
        margin-bottom: 6px;
    }
    .ps-section-sub {
        font-size: 0.82rem;
        color: var(--ps-muted);
        margin-bottom: 18px;
    }
    .ps-steps {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    .ps-step {
        background: #fff;
        border: 1px solid var(--ps-soft);
        border-radius: 8px;
        padding: 16px 14px;
    }
    .ps-step__num {
        font-size: 0.7rem;
        font-weight: 500;
        color: var(--ps-muted);
        margin-bottom: 6px;
    }
    .ps-step__title {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--ps-heading);
        margin-bottom: 4px;
    }
    .ps-step p {
        margin: 0;
        font-size: 0.78rem;
        line-height: 1.5;
        color: var(--ps-body);
    }
    .ps-why {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .ps-why-item {
        background: #fff;
        border: 1px solid var(--ps-soft);
        border-radius: 8px;
        padding: 16px;
    }
    .ps-why-item strong {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--ps-heading);
        margin-bottom: 4px;
    }
    .ps-why-item p {
        margin: 0;
        font-size: 0.8rem;
        line-height: 1.55;
        color: var(--ps-body);
    }
    .ps-cta {
        background: #fff;
        border: 1px solid var(--ps-soft);
        border-radius: 8px;
        padding: 22px 20px;
    }
    .ps-cta h2 {
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        color: var(--ps-heading);
        margin: 0 0 4px;
    }
    .ps-cta > p {
        margin: 0 0 16px;
        font-size: 0.82rem;
        color: var(--ps-body);
        max-width: 56ch;
    }
    .ps-form label {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--ps-heading);
        margin-bottom: 6px;
    }
    .ps-form .ps-field {
        width: 100%;
        border: 1px solid var(--ps-line);
        border-radius: 8px;
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 0.88rem;
        color: var(--ps-ink);
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    }
    .ps-form .ps-field::placeholder {
        color: #94a3b8;
    }
    .ps-form .ps-field:focus {
        border-color: #1e3a5f;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
    }
    .ps-form select.ps-field {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #6482a6 50%), linear-gradient(135deg, #6482a6 50%, transparent 50%);
        background-position: calc(100% - 16px) calc(50% + 2px), calc(100% - 11px) calc(50% + 2px);
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
        padding-right: 28px;
        cursor: pointer;
    }
    .ps-form textarea.ps-field {
        min-height: 88px;
        resize: vertical;
    }
    #panoRequestModal .modal-content {
        box-shadow: 0 12px 40px rgba(15, 36, 66, 0.14);
    }
    #panoRequestModal .modal-body {
        background: #fff;
    }
    .ps-alert {
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        margin-bottom: 14px;
        border-left: 2.5px solid #1e3a5f;
        background: #fafafa;
        color: var(--ps-body);
    }
    .ps-alert-ok {
        border-left-color: #15803d;
        background: #f0fdf4;
        color: #166534;
    }
    .ps-alert-err {
        border-left-color: #b91c1c;
        background: #fef2f2;
        color: #991b1b;
    }
    @media (max-width: 900px) {
        .ps-hero-grid,
        .ps-steps,
        .ps-why { grid-template-columns: 1fr 1fr; }
        .ps-hero-grid { grid-template-columns: 1fr; }
        .ps-visual::before { display: none; }
    }
    @media (max-width: 560px) {
        .ps-steps,
        .ps-why { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php
    $panoTypeLabels = \App\Models\PanoramaServiceRequest::placeTypeLabels();
    $panoSceneLabels = \App\Models\PanoramaServiceRequest::sceneEstimateLabels();
@endphp
<div class="ps-page">
    <section class="ps-hero">
        <div class="container py-4 py-md-5">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('client.landing') }}" style="color: var(--ps-muted); text-decoration: none;">Trang chủ</a></li>
                    <li class="breadcrumb-item active" style="color: var(--ps-heading);">Dịch vụ tour 360</li>
                </ol>
            </nav>
            <div class="ps-hero-grid">
                <div>
                    <p class="ps-kicker">Dịch vụ sản xuất</p>
                    <h1>Tour 360° khiến khách “đứng trong” địa điểm của bạn</h1>
                    <p class="ps-lead">
                        Quay panorama tại chỗ, dựng điểm chuyển cảnh và gắn lên bản đồ du lịch Ninh Bình Travel Hub.
                        Không cần tài khoản — nhấn gửi yêu cầu, điền form nhanh, chúng tôi liên hệ báo giá theo nhu cầu.
                    </p>
                    <div class="ps-actions">
                        <button type="button" class="ps-btn ps-btn-primary" data-bs-toggle="modal" data-bs-target="#panoRequestModal">
                            Gửi yêu cầu tư vấn
                        </button>
                        @if($demoUrl)
                            <a href="{{ $demoUrl }}" target="_blank" rel="noopener" class="ps-btn ps-btn-ghost">Xem demo 360</a>
                        @endif
                    </div>
                    <p class="ps-note">Giá theo nhu cầu — không ép gói cố định trên web.</p>
                </div>
                <div class="ps-visual">
                    @if($demoImg)
                        <img src="{{ $demoImg }}" alt="{{ $demoLocation->name ?? 'Demo tour 360' }}">
                        @if($demoUrl)
                            <a href="{{ $demoUrl }}" target="_blank" rel="noopener" class="ps-demo-link">
                                Demo · {{ \Illuminate\Support\Str::limit($demoLocation->name ?? 'Tour 360', 28) }}
                            </a>
                        @endif
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100" style="color: var(--ps-muted); font-weight: 500;">
                            Sắp có demo tour 360
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4 py-md-5">
        <h2 class="ps-section-title">Quy trình làm việc</h2>
        <p class="ps-section-sub">Bốn bước gọn — từ nhu cầu đến link bàn giao.</p>
        <div class="ps-steps">
            <div class="ps-step">
                <div class="ps-step__num">01</div>
                <span class="ps-step__title">Khảo sát nhu cầu</span>
                <p>Bạn mô tả không gian, số góc và yêu cầu thêm (nếu có).</p>
            </div>
            <div class="ps-step">
                <div class="ps-step__num">02</div>
                <span class="ps-step__title">Quay tại chỗ</span>
                <p>Chụp panorama theo hiện trạng thực tế của địa điểm.</p>
            </div>
            <div class="ps-step">
                <div class="ps-step__num">03</div>
                <span class="ps-step__title">Dựng & gắn map</span>
                <p>Nối các góc nhìn và đưa lên bản đồ du lịch có sẵn khách.</p>
            </div>
            <div class="ps-step">
                <div class="ps-step__num">04</div>
                <span class="ps-step__title">Bàn giao link</span>
                <p>Nhận link để đăng Zalo / Facebook ngay sau khi hoàn tất.</p>
            </div>
        </div>
    </section>

    <section class="container pb-4 pb-md-5">
        <h2 class="ps-section-title">Vì sao nên làm trên nền tảng này?</h2>
        <p class="ps-section-sub">Khác với chỉ thuê quay 360 rồi để một link đơn lẻ.</p>
        <div class="ps-why mb-4">
            <div class="ps-why-item">
                <strong>Đã có khách trên map</strong>
                <p>Tour nằm trong cổng du lịch — người đang tìm điểm đến có thể khám phá địa điểm của bạn.</p>
            </div>
            <div class="ps-why-item">
                <strong>Viewer 360 sẵn sàng</strong>
                <p>Hotspot, tên scene, thuyết minh — hệ thống hiển thị đã có sẵn, không cần dựng web riêng.</p>
            </div>
            <div class="ps-why-item">
                <strong>Giá linh hoạt</strong>
                <p>Báo giá sau khi nắm số scene / phạm vi — đúng nhu cầu, không ép gói cứng.</p>
            </div>
            <div class="ps-why-item">
                <strong>Bàn giao nhanh</strong>
                <p>Có link dùng ngay cho Zalo, Facebook sau khi dựng xong.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="ps-alert ps-alert-ok mb-4">{{ session('success') }}</div>
        @endif

        <div class="ps-cta">
            <h2>Sẵn sàng thuê làm tour 360?</h2>
            <p>Không cần đăng nhập. Điền thông tin liên hệ — chúng tôi gọi / nhắn Zalo để tư vấn và báo giá.</p>
            <div class="ps-actions">
                <button type="button" class="ps-btn ps-btn-primary" data-bs-toggle="modal" data-bs-target="#panoRequestModal">
                    Gửi yêu cầu tư vấn
                </button>
                <a href="{{ route('home') }}" class="ps-btn ps-btn-ghost">Về bản đồ</a>
            </div>
        </div>
    </section>
</div>

{{-- Popup form --}}
<div class="modal fade" id="panoRequestModal" tabindex="-1" aria-labelledby="panoRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 10px; border: 1px solid #e5e7eb;">
            <div class="modal-header border-0 pb-0 px-4 pt-3">
                <div>
                    <h5 class="modal-title fw-semibold mb-1" id="panoRequestModalLabel" style="color:#1e3a5f;font-size:1.05rem;">Gửi yêu cầu tư vấn</h5>
                    <p class="mb-0" style="font-size:0.78rem;color:#6482a6;">Không cần tài khoản · phản hồi qua Zalo / SĐT</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-3">
                @if(session('error'))
                    <div class="ps-alert ps-alert-err">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="ps-alert ps-alert-err">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('client.pano_service.submit') }}" method="POST" class="ps-form row g-3">
                    @csrf
                    <input type="hidden" name="from" value="public">
                    <div class="col-md-6">
                        <label>Tên liên hệ</label>
                        <input type="text" name="contact_name" class="ps-field" required maxlength="120"
                               value="{{ $defaultContact }}">
                    </div>
                    <div class="col-md-6">
                        <label>SĐT / Zalo</label>
                        <input type="text" name="phone" class="ps-field" required maxlength="30"
                               value="{{ $defaultPhone }}" placeholder="09xx...">
                    </div>
                    <div class="col-md-6">
                        <label>Tên địa điểm</label>
                        <input type="text" name="place_name" class="ps-field" required maxlength="180"
                               value="{{ $defaultPlace }}" placeholder="VD: Homestay Hoa Lư...">
                    </div>
                    <div class="col-md-6">
                        <label>Loại địa điểm</label>
                        <select name="place_type" class="ps-field">
                            <option value="">— Chọn —</option>
                            @foreach($panoTypeLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('place_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Số góc / scene ước lượng</label>
                        <select name="scene_estimate" class="ps-field">
                            <option value="">— Chọn —</option>
                            @foreach($panoSceneLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('scene_estimate') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Ghi chú thêm (tuỳ chọn)</label>
                        <textarea name="note" class="ps-field" rows="3" maxlength="800"
                                  placeholder="Ví dụ: muốn quay sảnh + 2 phòng...">{{ old('note') }}</textarea>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center pt-1">
                        <button type="submit" class="ps-btn ps-btn-primary">Gửi yêu cầu</button>
                        <button type="button" class="ps-btn ps-btn-ghost" data-bs-dismiss="modal">Đóng</button>
                        <span style="font-size:0.72rem;color:#6482a6;">Miễn phí tư vấn · không ràng buộc</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('panoRequestModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    @if($errors->any() || session('error'))
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    @endif
});
</script>
@endpush
