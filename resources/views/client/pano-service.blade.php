@extends('client.layouts.app')

@section('title', 'Dịch vụ tour 360 | Ninh Bình Travel Hub')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/heritage.css') }}?v={{ @filemtime(public_path('css/heritage.css')) }}">
<style>
    .ps-form label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--nb-ink);
        margin-bottom: 6px;
    }
    .ps-form .ps-field {
        width: 100%;
        border: 1px solid var(--nb-line);
        border-radius: 2px;
        background: #fff;
        padding: 10px 12px;
        font-size: 0.88rem;
        color: var(--nb-ink);
        outline: none;
        font-family: inherit;
    }
    .ps-form .ps-field:focus {
        border-color: var(--nb-ink);
        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.06);
    }
    .ps-form select.ps-field {
        appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #76777d 50%), linear-gradient(135deg, #76777d 50%, transparent 50%);
        background-position: calc(100% - 16px) calc(50% + 2px), calc(100% - 11px) calc(50% + 2px);
        background-size: 5px 5px;
        background-repeat: no-repeat;
        padding-right: 28px;
        cursor: pointer;
    }
    .ps-form textarea.ps-field { min-height: 88px; resize: vertical; }
    .ps-alert {
        padding: 12px 14px;
        border-radius: 2px;
        font-size: 0.82rem;
        margin-bottom: 1rem;
        border-left: 2px solid var(--nb-accent);
        background: var(--nb-surface-mist);
    }
    .ps-alert-ok { border-left-color: #15803d; }
    .ps-alert-err { border-left-color: #b91c1c; }
    #panoRequestModal .modal-content { border-radius: 2px; border: 1px solid var(--nb-line-soft); }
</style>
@endpush

@php
    $panoTypeLabels = \App\Models\PanoramaServiceRequest::placeTypeLabels();
    $panoSceneLabels = \App\Models\PanoramaServiceRequest::sceneEstimateLabels();
    $fallbackImage = asset('images/tam_chuc.png');
    $sideImage = $demoImg ?: asset('images/trangtri3.jpg');
@endphp

@section('content')
<div class="nb">

    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-split">
                <div>
                    <span class="nb-eyebrow">Dịch vụ sản xuất</span>
                    <h1 class="nb-h2">Tour 360° cho địa điểm của bạn</h1>
                    <p class="nb-lead" style="margin-top: 20px;">
                        Quay panorama tại chỗ, dựng tour và gắn lên bản đồ du lịch Ninh Bình Travel Hub.
                        Khách có thể nhìn quanh từng góc như đang đứng tại chỗ, thay vì chỉ xem ảnh tĩnh.
                    </p>
                    <p class="nb-text" style="margin-top: 16px;">
                        Không cần tài khoản, không cần làm web riêng. Chúng tôi quay tại địa điểm, dựng các điểm chuyển cảnh
                        và đưa tour lên nền tảng đang có sẵn lượt truy cập. Bạn nhận link để chia sẻ qua Zalo, Facebook hoặc gửi trực tiếp cho khách.
                    </p>

                    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:28px;">
                        <button type="button" class="nb-btn nb-btn--solid" data-bs-toggle="modal" data-bs-target="#panoRequestModal">
                            Gửi yêu cầu tư vấn
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </button>
                        @if($demoUrl)
                            <a href="{{ $demoUrl }}" target="_blank" rel="noopener" class="nb-btn nb-btn--outline">
                                Xem demo
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>

                    <div class="nb-stats">
                        <div>
                            <span class="nb-stats__value">4</span>
                            <span class="nb-stats__label">Bước triển khai</span>
                        </div>
                        <div>
                            <span class="nb-stats__value">360°</span>
                            <span class="nb-stats__label">Trải nghiệm thật</span>
                        </div>
                        <div>
                            <span class="nb-stats__value">Map</span>
                            <span class="nb-stats__label">Gắn sẵn nền tảng</span>
                        </div>
                    </div>

                    <p class="nb-meta" style="margin-top: 8px;">Báo giá theo quy mô thực tế, không ép gói cố định trên web.</p>
                </div>

                <div class="nb-split__figure">
                    <img src="{{ $sideImage }}"
                         alt="{{ $demoLocation->name ?? 'Demo tour 360' }}"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                </div>
            </div>
        </div>
    </section>

    {{-- Quy trình --}}
    <section class="nb-section nb-section--mist">
        <div class="nb-wrap">
            <div class="nb-section__head nb-section__head--center">
                <span class="nb-eyebrow">Quy trình</span>
                <h2 class="nb-h2">Từ nhu cầu đến link dùng được</h2>
                <p class="nb-text" style="margin-top: 16px;">
                    Bốn bước cố định, rõ ràng. Bạn biết mình đang trả tiền cho phần nào ở từng chặng.
                </p>
            </div>

            <div class="nb-wrap nb-wrap--narrow">
                <ul class="nb-values">
                    <li>
                        <span class="nb-values__mark">01</span>
                        <div>
                            <h4 class="nb-values__title">Nhận nhu cầu</h4>
                            <p class="nb-values__text">Bạn gửi thông tin địa điểm, phạm vi quay và cách muốn bàn giao.</p>
                        </div>
                    </li>
                    <li>
                        <span class="nb-values__mark">02</span>
                        <div>
                            <h4 class="nb-values__title">Quay tại chỗ</h4>
                            <p class="nb-values__text">Chụp panorama theo không gian thực tế và lộ trình đã thống nhất.</p>
                        </div>
                    </li>
                    <li>
                        <span class="nb-values__mark">03</span>
                        <div>
                            <h4 class="nb-values__title">Dựng tour</h4>
                            <p class="nb-values__text">Nối các scene, thêm điểm chuyển cảnh và tối ưu trải nghiệm xem.</p>
                        </div>
                    </li>
                    <li>
                        <span class="nb-values__mark">04</span>
                        <div>
                            <h4 class="nb-values__title">Bàn giao link</h4>
                            <p class="nb-values__text">Tour được gắn lên nền tảng, có link chia sẻ ngay sau khi hoàn tất.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Vì sao trên nền tảng --}}
    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-split nb-split--reverse">
                <div>
                    <span class="nb-eyebrow">Lợi ích</span>
                    <h2 class="nb-h2">Không chỉ là một link tour rời</h2>
                    <blockquote class="nb-quote" style="margin-top: 20px;">
                        Tour trở thành một phần của trang địa điểm trên bản đồ, nơi khách đang tìm kiếm và ra quyết định ghé thăm.
                    </blockquote>
                    <p class="nb-text">
                        Viewer 360, hotspot và điều hướng đã có sẵn. Bạn không cần thuê thêm web chỉ để hiển thị tour.
                        Phù hợp homestay, nhà hàng, quán cafe, khu tham quan và showroom.
                    </p>
                    @if($demoUrl)
                        <a href="{{ $demoUrl }}" target="_blank" rel="noopener" class="nb-link" style="margin-top: 28px;">
                            Xem demo {{ \Illuminate\Support\Str::limit($demoLocation->name ?? 'tour 360', 24) }}
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>

                <div class="nb-split__figure">
                    <img src="{{ asset('images/trangtri2.jpg') }}"
                         alt="Khám phá địa điểm trên bản đồ"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="nb-section nb-section--tight">
        <div class="nb-wrap">
            @if(session('success'))
                <div class="ps-alert ps-alert-ok mb-4" style="max-width:640px;margin-left:auto;margin-right:auto;text-align:center;">{{ session('success') }}</div>
            @endif

            <div class="nb-section__head nb-section__head--center" style="margin-bottom: 32px;">
                <h2 class="nb-h2">Sẵn sàng nhận tư vấn?</h2>
                <p class="nb-text" style="margin-top: 16px;">
                    Điền thông tin liên hệ. Chúng tôi sẽ gọi hoặc nhắn Zalo để báo giá theo quy mô thực tế của bạn.
                </p>
            </div>

            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 12px;">
                <button type="button" class="nb-btn nb-btn--solid" data-bs-toggle="modal" data-bs-target="#panoRequestModal">
                    Gửi yêu cầu tư vấn
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>
                <a href="{{ route('home') }}" class="nb-btn nb-btn--outline">
                    Về bản đồ
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

</div>

{{-- Modal form --}}
<div class="modal fade" id="panoRequestModal" tabindex="-1" aria-labelledby="panoRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-semibold mb-1" id="panoRequestModalLabel" style="color:var(--nb-ink);font-size:1.05rem;">Gửi yêu cầu tư vấn</h5>
                    <p class="mb-0 nb-meta">Không cần tài khoản</p>
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
                        <input type="text" name="contact_name" class="ps-field" required maxlength="120" value="{{ $defaultContact }}">
                    </div>
                    <div class="col-md-6">
                        <label>SĐT / Zalo</label>
                        <input type="text" name="phone" class="ps-field" required maxlength="30" value="{{ $defaultPhone }}" placeholder="09xx...">
                    </div>
                    <div class="col-md-6">
                        <label>Tên địa điểm</label>
                        <input type="text" name="place_name" class="ps-field" required maxlength="180" value="{{ $defaultPlace }}" placeholder="VD: Homestay Hoa Lư...">
                    </div>
                    <div class="col-md-6">
                        <label>Loại địa điểm</label>
                        <select name="place_type" class="ps-field">
                            <option value="">Chọn loại</option>
                            @foreach($panoTypeLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('place_type') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Số góc / scene ước lượng</label>
                        <select name="scene_estimate" class="ps-field">
                            <option value="">Chọn mức</option>
                            @foreach($panoSceneLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('scene_estimate') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Ghi chú thêm (tuỳ chọn)</label>
                        <textarea name="note" class="ps-field" rows="3" maxlength="800" placeholder="VD: muốn quay sảnh + 2 phòng...">{{ old('note') }}</textarea>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center pt-1">
                        <button type="submit" class="nb-btn nb-btn--solid">Gửi yêu cầu</button>
                        <button type="button" class="nb-btn nb-btn--outline" data-bs-dismiss="modal">Đóng</button>
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
    const modalEl = document.getElementById('panoRequestModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    @if($errors->any() || session('error'))
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    @endif
});
</script>
@endpush
