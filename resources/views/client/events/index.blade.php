@extends('client.layouts.app')

@section('title', 'Sự Kiện Lễ Hội Tại Ninh Bình')

@section('content')
<div class="page-shell">
    <div class="container py-4">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
            <div>
                <h1 class="page-header__title">Sự kiện & Lễ hội</h1>
                <p class="page-header__subtitle mb-0">Các hoạt động văn hóa, lễ hội và sự kiện du lịch tại Ninh Bình</p>
            </div>
            <div class="tab-filter">
                <a href="{{ route('client.events.index', ['tab' => 'upcoming']) }}"
                   class="tab-filter__item text-decoration-none {{ $tab === 'upcoming' ? 'is-active' : '' }}">
                    Sắp diễn ra
                </a>
                <a href="{{ route('client.events.index', ['tab' => 'past']) }}"
                   class="tab-filter__item text-decoration-none {{ $tab === 'past' ? 'is-active' : '' }}">
                    Đã qua
                </a>
            </div>
        </div>

        @if($tab === 'upcoming' && $featured)
            <div class="mb-4 pb-4 border-bottom" style="border-color: #e5e7eb !important;">
                <a href="{{ route('client.events.show', $featured->slug) }}" class="text-decoration-none editorial-link d-block">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            @include('client.partials.cover-image', [
                                'src' => $featured->featured_image_url,
                                'alt' => $featured->name,
                                'ratio' => '16/9',
                            ])
                        </div>
                        <div class="col-lg-5">
                            <span class="section-label d-block mb-2">Sự kiện nổi bật</span>
                            <h2 class="editorial-link__title fw-semibold mb-2" style="color: #27272a; font-size: 1.3rem; line-height: 1.35;">
                                {{ $featured->name }}
                            </h2>
                            @if($featured->start_time)
                                <div class="event-card__date mb-3">
                                    {{ $featured->start_time->format('d/m/Y H:i') }}
                                    @if($featured->end_time)
                                        — {{ $featured->end_time->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            @endif
                            @if($featured->location_text)
                                <p class="meta-text mb-2">{{ $featured->location_text }}</p>
                            @endif
                            @if($featured->description)
                                <p class="mb-0" style="color: #52525b; font-size: 0.875rem; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ strip_tags($featured->description) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if($events->count() > 0)
            <div class="row g-4">
                @foreach($events as $event)
                    <div class="col-md-6 col-lg-4">
                        <article class="event-card">
                            <a href="{{ route('client.events.show', $event->slug) }}" class="text-decoration-none editorial-link d-block h-100">
                                @include('client.partials.cover-image', [
                                    'src' => $event->featured_image_url,
                                    'alt' => $event->name,
                                    'class' => 'rounded-0',
                                    'ratio' => '16/10',
                                ])
                                <div class="event-card__body">
                                    <h3 class="event-card__title editorial-link__title">{{ $event->name }}</h3>
                                    @if($event->start_time)
                                        <div class="event-card__date">
                                            {{ $event->start_time->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    @if($event->location_text)
                                        <p class="meta-text mb-2">{{ $event->location_text }}</p>
                                    @endif
                                    @if($event->description)
                                        <p class="event-card__excerpt mb-0">{{ strip_tags($event->description) }}</p>
                                    @endif
                                </div>
                            </a>
                        </article>
                    </div>
                @endforeach
            </div>

            @if($events->hasPages())
                <div class="mt-4 pt-2 custom-pagination">
                    {{ $events->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state__title">
                    @if($tab === 'past')
                        Chưa có sự kiện đã qua
                    @else
                        Chưa có sự kiện sắp tới
                    @endif
                </div>
                <p class="empty-state__text">
                    @if($tab === 'past')
                        Lịch sử các sự kiện sẽ được hiển thị tại đây khi có dữ liệu.
                    @else
                        Các lễ hội và sự kiện du lịch sẽ được cập nhật sớm. Khám phá bản đồ hoặc đọc tin tức trong lúc chờ.
                    @endif
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-sm">Xem bản đồ</a>
                    <a href="{{ route('client.news.index') }}" class="btn btn-outline-secondary btn-sm" style="border-color: #cbdbe8; color: #1e3a5f;">Tin tức & Cẩm nang</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
