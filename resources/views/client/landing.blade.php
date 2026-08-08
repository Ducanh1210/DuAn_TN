@extends('client.layouts.app')

@section('title', 'Trang chủ — Ninh Bình Travel Hub')

@section('content')
<div class="page-shell">
    {{-- Hero --}}
    <section class="landing-hero border-bottom" style="border-color: #e5e7eb !important;">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <p class="landing-eyebrow mb-2">Cổng thông tin du lịch Ninh Bình</p>
                    <h1 class="landing-hero__title mb-3">Khám phá Ninh Bình theo cách của bạn</h1>
                    <p class="landing-hero__lead mb-4">
                        Bản đồ điểm đến, tour 360°, tin tức và sự kiện — tất cả trên một nền tảng gọn gàng, dễ tra cứu.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary px-4">Mở bản đồ du lịch</a>
                        <a href="{{ route('client.news.index') }}" class="btn btn-outline-secondary px-4" style="border-color: #cbdbe8; color: #1e3a5f;">Đọc tin tức</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="landing-stats">
                        <div class="landing-stat">
                            <div class="landing-stat__num">{{ number_format($stats['locations']) }}</div>
                            <div class="landing-stat__label">Địa điểm</div>
                        </div>
                        <div class="landing-stat">
                            <div class="landing-stat__num">{{ number_format($stats['news']) }}</div>
                            <div class="landing-stat__label">Bài viết</div>
                        </div>
                        <div class="landing-stat">
                            <div class="landing-stat__num">{{ number_format($stats['events']) }}</div>
                            <div class="landing-stat__label">Sự kiện</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured locations --}}
    @if($featuredLocations->isNotEmpty())
    <section class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
            <div>
                <span class="section-label d-block mb-1">Điểm đến nổi bật</span>
                <h2 class="h5 mb-0 fw-semibold" style="color: #27272a;">Khám phá ngay</h2>
            </div>
            <a href="{{ route('home') }}" class="meta-text" style="color: #6482a6;">Xem trên bản đồ</a>
        </div>
        <div class="row g-3">
            @foreach($featuredLocations as $loc)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('client.locations.360', $loc->slug) }}" class="text-decoration-none editorial-link d-block h-100">
                        <article class="event-card h-100">
                            @include('client.partials.cover-image', [
                                'src' => $loc->display_thumbnail,
                                'alt' => $loc->name,
                                'class' => 'rounded-0',
                                'ratio' => '16/10',
                            ])
                            <div class="event-card__body">
                                <h3 class="event-card__title editorial-link__title mb-1">{{ $loc->name }}</h3>
                                @if($loc->category)
                                    <p class="meta-text mb-2">{{ $loc->category->name }}</p>
                                @endif
                                <p class="event-card__excerpt mb-0">{{ Str::limit(strip_tags($loc->short_description ?? $loc->description ?? ''), 90) }}</p>
                                <span class="d-inline-block mt-2 meta-text" style="color: #1e3a5f; font-weight: 500;">
                                    Khám phá ngay →
                                </span>
                            </div>
                        </article>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <div class="container pb-4">
        <div class="row g-4">
            {{-- News --}}
            <div class="col-lg-7">
                <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                    <span class="section-label">Tin tức & Cẩm nang</span>
                </div>
                <div class="d-flex flex-column gap-3">
                    @forelse($latestNews as $item)
                        <article class="pb-3 border-bottom" style="border-color: #f4f4f5 !important;">
                            <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none editorial-link d-flex gap-3">
                                <div style="width: 110px; flex-shrink: 0;">
                                    @include('client.partials.cover-image', [
                                        'src' => $item->featured_image_url,
                                        'alt' => $item->title,
                                        'ratio' => '4/3',
                                    ])
                                </div>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h3 class="editorial-link__title fw-semibold mb-1" style="color: #27272a; font-size: 0.9rem; line-height: 1.4;">
                                        {{ $item->title }}
                                    </h3>
                                    <div class="meta-text">{{ $item->published_at?->format('d/m/Y') }}</div>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="meta-text">Chưa có bài viết.</p>
                    @endforelse
                </div>
                <a href="{{ route('client.news.index') }}" class="d-inline-block mt-3 meta-text" style="color: #1e3a5f; font-weight: 500;">Xem tất cả tin tức →</a>
            </div>

            {{-- Events --}}
            <div class="col-lg-5">
                <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                    <span class="section-label">Sự kiện sắp tới</span>
                </div>
                <div class="d-flex flex-column gap-3">
                    @forelse($upcomingEvents as $event)
                        <article>
                            <a href="{{ route('client.events.show', $event->slug) }}" class="text-decoration-none editorial-link">
                                <h3 class="editorial-link__title fw-semibold mb-1" style="color: #27272a; font-size: 0.875rem;">{{ $event->name }}</h3>
                                <div class="event-card__date">{{ $event->start_time?->format('d/m/Y H:i') }}</div>
                                @if($event->location_text)
                                    <p class="meta-text mb-0">{{ $event->location_text }}</p>
                                @endif
                            </a>
                        </article>
                    @empty
                        <p class="meta-text">Chưa có sự kiện sắp tới.</p>
                    @endforelse
                </div>
                <a href="{{ route('client.events.index') }}" class="d-inline-block mt-3 meta-text" style="color: #1e3a5f; font-weight: 500;">Xem tất cả sự kiện →</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .landing-hero { background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); }
    .landing-eyebrow {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #6482a6;
    }
    .landing-hero__title {
        color: #1e3a5f;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 600;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }
    .landing-hero__lead {
        color: #52525b;
        font-size: 0.95rem;
        line-height: 1.65;
        max-width: 540px;
    }
    .landing-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .landing-stat { text-align: center; }
    .landing-stat__num {
        color: #1e3a5f;
        font-size: 1.35rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .landing-stat__label {
        color: #6482a6;
        font-size: 0.75rem;
        margin-top: 4px;
    }
</style>
@endpush
@endsection
