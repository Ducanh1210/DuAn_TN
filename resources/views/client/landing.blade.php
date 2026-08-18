@extends('client.layouts.app')

@section('title', 'Trang chủ — Cổng Thông Tin Du Lịch Ninh Bình')

{{-- Header nổi trong suốt trên ảnh hero, chuyển trắng khi cuộn xuống --}}
@section('header_variant', 'site-header--overlay')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/heritage.css') }}?v={{ @filemtime(public_path('css/heritage.css')) }}">
@endpush

@php
    $decorImages = [
        asset('images/trangtri1.jpg'),
        asset('images/trangtri2.jpg'),
        asset('images/trangtri3.jpg'),
        asset('images/trangtri4.jpg'),
    ];

    $fallbackImages = [
        $decorImages[1],
        $decorImages[0],
        asset('images/tam_chuc.png'),
        asset('images/trag.png'),
    ];

    $heroImage = asset('images/trangchu.png');
    $narrativeImage = $decorImages[2];
    $showcase = collect($featuredLocations)->take(3);
    $pickImage = fn (int $i) => $decorImages[$i % count($decorImages)];
@endphp

@section('content')
<div class="nb">

    {{-- ============ Hero ============ --}}
    <header class="nb-hero nb-hero--full">
        <img src="{{ $heroImage }}"
             alt="Thung lũng lúa và núi đá vôi Ninh Bình lúc hoàng hôn"
             class="nb-hero__img"
             onerror="this.onerror=null;this.src='{{ $fallbackImages[1] }}';">
        <div class="nb-hero__scrim"></div>

        <div class="nb-hero__inner">
            <span class="nb-eyebrow">Cổng thông tin du lịch Ninh Bình</span>
            <h1 class="nb-hero__title">Khám Phá Di Sản Thiên Niên Kỷ</h1>
            <p class="nb-hero__sub">
                Hành trình qua vẻ đẹp vượt thời gian của vùng đất cố đô — nơi lịch sử ngàn năm
                gặp gỡ những dãy núi đá vôi và dòng sông êm đềm.
            </p>
            <a href="{{ route('home') }}" class="nb-btn nb-btn--ghost-light">
                Khám phá ngay
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </header>

    {{-- ============ Điểm đến tiêu biểu ============ --}}
    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-section__head">
                <div>
                    <span class="nb-eyebrow">Tuyển chọn</span>
                    <h2 class="nb-h2">Điểm Đến Tiêu Biểu</h2>
                </div>
                <a href="{{ route('home') }}" class="nb-link">
                    Xem tất cả trên bản đồ
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            @if($showcase->isNotEmpty())
                <div class="nb-dest-grid nb-dest-grid--stagger">
                    @foreach($showcase as $index => $loc)
                        <a href="{{ route('client.locations.360', $loc->slug) }}" class="nb-dest">
                            <div class="nb-dest__media">
                                <img src="{{ $loc->display_thumbnail ?: $pickImage($index) }}"
                                     alt="{{ $loc->name }}"
                                     class="nb-dest__img"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='{{ $fallbackImages[1] }}';">
                                <div class="nb-dest__veil"></div>
                            </div>
                            <span class="nb-dest__tag">{{ $loc->category->name ?? 'Điểm đến' }}</span>
                            <h3 class="nb-dest__title">{{ $loc->name }}</h3>
                            <p class="nb-dest__desc">
                                {{ $loc->short_description ?: Str::limit(strip_tags((string) $loc->description), 120) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="nb-empty">
                    <h3 class="nb-empty__title">Chưa có điểm đến nào được xuất bản</h3>
                    <p class="nb-empty__text">
                        Dữ liệu địa điểm sẽ hiển thị tại đây ngay khi được quản trị viên đăng tải.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- ============ Hành trình khám phá ============ --}}
    <section class="nb-section nb-section--mist">
        <div class="nb-wrap">
            <div class="nb-split">
                <div>
                    <span class="nb-eyebrow">Hoàng hôn</span>
                    <h2 class="nb-h2">Thành Phố Bên Dòng Sông</h2>
                    <p class="nb-lead" style="margin-top: 20px;">
                        Khi mặt trời khuất bóng, Ninh Bình hiện ra một gương mặt khác — dòng sông soi
                        ánh trời chiều, mái ngói đỏ của khu đền cũ nằm sát những con đường vừa lên đèn.
                        Di sản và nhịp sống đô thị đứng cạnh nhau trong cùng một khung hình.
                    </p>
                    <p class="nb-text" style="margin-top: 16px;">
                        Từ trên cao, thành phố trải dài theo khúc sông, đèn vàng lần lượt bật lên giữa
                        tán cây và mái nhà. Đó là lúc vùng đất cố đô không chỉ là danh thắng, mà còn là
                        nơi người ta đang sống, đi về và giữ lửa cho những ngày tiếp theo.
                    </p>

                    <a href="{{ route('client.about') }}" class="nb-link" style="margin-top: 28px;">
                        Tìm hiểu về Ninh Bình
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="nb-split__figure">
                    <img src="{{ $narrativeImage }}"
                         alt="Hoàng hôn trên sông và thành phố Ninh Bình"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='{{ $fallbackImages[1] }}';">
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Lưu giữ khoảnh khắc ============ --}}
    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-section__head nb-section__head--center">
                <span class="nb-eyebrow">Nhật ký hình ảnh</span>
                <h2 class="nb-h2">Lưu Giữ Khoảnh Khắc</h2>
                <p class="nb-text" style="margin-top: 16px;">
                    Một lát cắt về đời sống văn hóa, về con người và những phút giây tĩnh lặng
                    làm nên tinh thần của vùng đất này.
                </p>
            </div>

            <div class="nb-bento">
                <div class="nb-bento__cell nb-bento__cell--wide">
                    <img src="{{ $decorImages[0] }}" alt="Đêm hội với đài phun nước và trống đồng Ninh Bình" class="nb-bento__img" loading="lazy">
                    <div class="nb-bento__caption">
                        <strong>Đêm hội</strong>
                        <span>Ánh đèn, đài phun nước và nhịp trống đồng giữa quảng trường.</span>
                    </div>
                </div>
                <div class="nb-bento__cell nb-bento__cell--narrow">
                    <img src="{{ $decorImages[1] }}" alt="Thuyền nan đậu dưới chân đền đài lúc chạng vạng" class="nb-bento__img" loading="lazy">
                    <div class="nb-bento__caption">
                        <strong>Chạng vạng</strong>
                        <span>Đoàn thuyền nan yên ắng dưới mái đền vàng ánh đèn.</span>
                    </div>
                </div>
                <div class="nb-bento__cell nb-bento__cell--narrow-b">
                    <img src="{{ $decorImages[2] }}" alt="Hoàng hôn trên sông và thành phố Ninh Bình" class="nb-bento__img" loading="lazy">
                    <div class="nb-bento__caption">
                        <strong>Hoàng hôn</strong>
                        <span>Dòng sông soi bóng trời chiều trên thành phố.</span>
                    </div>
                </div>
                <div class="nb-bento__cell nb-bento__cell--wide-b">
                    <img src="{{ $decorImages[3] }}" alt="Khu vui chơi giải trí Ninh Bình" class="nb-bento__img" loading="lazy">
                    <div class="nb-bento__caption">
                        <strong>Giải trí</strong>
                        <span>Nhịp sống hiện đại bên cạnh miền di sản.</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: center; margin-top: 48px;">
                <a href="{{ route('home') }}" class="nb-btn nb-btn--outline">
                    Mở bản đồ khám phá
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const header = document.querySelector('.site-header--overlay');
        if (!header) return;

        const syncScrollState = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 40);
        };

        syncScrollState();
        window.addEventListener('scroll', syncScrollState, { passive: true });

        // Menu mobile mở ra trên nền ảnh sẽ khó đọc nên ép header về nền trắng.
        const menu = document.getElementById('siteNavCollapse');
        if (menu) {
            menu.addEventListener('show.bs.collapse', () => header.classList.add('is-solid'));
            menu.addEventListener('hidden.bs.collapse', () => header.classList.remove('is-solid'));
        }
    })();
</script>
@endpush
