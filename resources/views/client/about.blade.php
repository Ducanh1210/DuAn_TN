@extends('client.layouts.app')

@section('title', 'Giới thiệu — Cổng Thông Tin Du Lịch Ninh Bình')

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
        $decorImages[0],
        $decorImages[1],
        $decorImages[2],
        $decorImages[3],
    ];

    $pickImage = fn (int $i) => $decorImages[$i % count($decorImages)];

    $staticTiles = [
        ['name' => 'Hang Tối', 'sub' => 'Hành trình xuyên lòng núi đá'],
        ['name' => 'Tam Cốc', 'sub' => 'Biển lúa vàng rực rỡ'],
        ['name' => 'Sông Sào Khê', 'sub' => 'Dòng nước trong vắt êm đềm'],
    ];
@endphp

@section('content')
<div class="nb">

    {{-- ============ Hero ============ --}}
    <header class="nb-hero nb-hero--short">
        <img src="{{ asset('images/gioithieu.png') }}"
             alt="Thuyền nan giữa núi đá và sương sớm Ninh Bình"
             class="nb-hero__img"
             onerror="this.onerror=null;this.src='{{ $decorImages[1] }}';">
        <div class="nb-hero__scrim"></div>

        <div class="nb-hero__inner">
            <span class="nb-eyebrow">Về chúng tôi</span>
            <h1 class="nb-hero__title">Cổng Thông Tin<br>Du Lịch Ninh Bình</h1>
            <p class="nb-hero__sub">
                Nơi tập hợp thông tin điểm đến, tin tức, sự kiện và cẩm nang du lịch — giúp bạn
                lên kế hoạch và khám phá vùng đất cố đô một cách thuận tiện nhất.
            </p>
            <div class="nb-hero__rule"></div>
        </div>
    </header>

    {{-- ============ Chương 1 ============ --}}
    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-split nb-split--reverse">
                <div>
                    <h2 class="nb-h2">Lịch Sử &amp; Di Sản</h2>
                    <p class="nb-lead" style="margin-top: 20px;">
                        Ninh Bình từng là kinh đô của nhà Đinh, nhà Tiền Lê — giai đoạn mở đầu thời kỳ
                        độc lập tự chủ của nước Đại Cồ Việt. Quần thể di tích Hoa Lư, đền thờ các vua
                        và phế tích thành cổ vẫn còn lưu giữ dấu ấn của thời kỳ ấy.
                    </p>
                    <p class="nb-text" style="margin-top: 16px;">
                        Năm 2014, quần thể danh thắng Tràng An được UNESCO công nhận là Di sản Thế giới
                        hỗn hợp — một trong số ít di sản ở Việt Nam đạt cả hai tiêu chí về giá trị
                        thiên nhiên và văn hóa. Đây cũng là điểm đến quen thuộc của du khách trong
                        và ngoài nước.
                    </p>
                </div>

                <div class="nb-split__figure">
                    <img src="{{ $decorImages[1] }}"
                         alt="Đền đài và thuyền nan lúc chạng vạng"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='{{ $decorImages[2] }}';">
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Chương 2 ============ --}}
    <section class="nb-section nb-section--mist">
        <div class="nb-wrap">
            <div class="nb-section__head nb-section__head--center">
                <h2 class="nb-h2">Danh Thắng Nổi Bật</h2>
                <blockquote class="nb-quote" style="margin-top: 20px;">
                    “Ninh Bình được mệnh danh là vùng đất của núi non, sông nước và chùa chiền —
                    nơi có thể đi thuyền xuyên hang, leo núi hay tham quan chùa trong cùng một ngày.”
                </blockquote>
                <p class="nb-text">
                    Tràng An, Tam Cốc — Bích Động, Hang Múa, chùa Bái Đính, cố đô Hoa Lư… mỗi địa điểm
                    có cách trải nghiệm riêng. Bạn có thể tra cứu vị trí, xem ảnh 360° và lên lộ trình
                    ngay trên bản đồ tương tác của website.
                </p>
            </div>

            <div class="nb-tri nb-tri--stagger">
                @foreach($staticTiles as $index => $tile)
                    @php $loc = collect($showcase)->get($index); @endphp

                    @if($loc)
                        <a href="{{ route('client.locations.360', $loc->slug) }}" class="nb-tile">
                            <img src="{{ $loc->display_thumbnail ?: $pickImage($index + 1) }}"
                                 alt="{{ $loc->name }}"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ $fallbackImages[1] }}';">
                            <div class="nb-tile__scrim"></div>
                            <div class="nb-tile__body">
                                <h3 class="nb-tile__title">{{ $loc->name }}</h3>
                                <p class="nb-tile__sub">{{ $loc->category->name ?? $tile['sub'] }}</p>
                            </div>
                        </a>
                    @else
                        <div class="nb-tile">
                            <img src="{{ $pickImage($index + 1) }}" alt="{{ $tile['name'] }}" loading="lazy">
                            <div class="nb-tile__scrim"></div>
                            <div class="nb-tile__body">
                                <h3 class="nb-tile__title">{{ $tile['name'] }}</h3>
                                <p class="nb-tile__sub">{{ $tile['sub'] }}</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Chương 3 ============ --}}
    <section class="nb-section">
        <div class="nb-wrap">
            <div class="nb-split nb-split--even">
                <div class="nb-split__figure">
                    <img src="{{ $decorImages[3] }}"
                         alt="Khu vui chơi và nhịp sống hiện đại Ninh Bình"
                         loading="lazy"
                         onerror="this.onerror=null;this.src='{{ $decorImages[2] }}';">
                </div>

                <div>
                    <h2 class="nb-h2">Văn Hóa &amp; Trải Nghiệm</h2>
                    <p class="nb-lead" style="margin-top: 20px;">
                        Du lịch Ninh Bình không chỉ dừng ở tham quan cảnh đẹp. Lễ hội đền chùa, ẩm thực
                        địa phương và làng nghề truyền thống là những trải nghiệm nhiều du khách quan tâm
                        khi đến đây.
                    </p>

                    <ul class="nb-values">
                        <li>
                            <span class="nb-values__mark"><i class="fa-solid fa-torii-gate" aria-hidden="true"></i></span>
                            <div>
                                <h4 class="nb-values__title">Lễ hội &amp; tín ngưỡng</h4>
                                <p class="nb-values__text">
                                    Lễ hội cố đô Hoa Lư, hội chùa Bái Đính, hội đền Trần… diễn ra quanh năm,
                                    thu hút đông đảo người dân và du khách.
                                </p>
                            </div>
                        </li>
                        <li>
                            <span class="nb-values__mark"><i class="fa-solid fa-utensils" aria-hidden="true"></i></span>
                            <div>
                                <h4 class="nb-values__title">Ẩm thực đặc trưng</h4>
                                <p class="nb-values__text">
                                    Dê núi, cơm cháy, nem chua Yên Mạc, ốc núi — món ăn gắn với địa phương,
                                    dễ tìm tại nhà hàng và quán ven đường.
                                </p>
                            </div>
                        </li>
                        <li>
                            <span class="nb-values__mark"><i class="fa-solid fa-hands" aria-hidden="true"></i></span>
                            <div>
                                <h4 class="nb-values__title">Làng nghề truyền thống</h4>
                                <p class="nb-values__text">
                                    Thêu ren Văn Lâm, đá mỹ nghệ Ninh Vân, mây tre đan… nhiều làng nghề
                                    mở cửa cho khách tham quan và mua quà lưu niệm.
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


</div>
@endsection
