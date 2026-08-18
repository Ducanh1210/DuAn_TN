@extends('client.layouts.app')

@section('title', 'Tin tức — Cổng Thông Tin Du Lịch Ninh Bình')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/heritage.css') }}?v={{ @filemtime(public_path('css/heritage.css')) }}">
<style>
    .news-merge-compact .nb-columns {
        grid-template-columns: 160px 1fr;
        gap: 28px;
    }

    .news-merge-compact .nb-card-grid {
        gap: 18px;
    }

    .news-merge-compact .nb-card__media {
        aspect-ratio: 16 / 10;
    }

    .news-merge-compact .nb-card__body {
        padding: 16px;
    }

    .news-merge-compact .nb-card__title {
        font-size: 1rem;
        line-height: 1.4;
    }

    .news-merge-compact .nb-card__excerpt {
        font-size: 0.8rem;
        line-height: 1.55;
    }

    .news-merge-compact .nb-side__link {
        padding: 12px 0;
        font-size: 0.84rem;
    }

    @media (min-width: 1280px) {
        .news-merge-compact .nb-card-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>
@endpush

@php
    $placeholder = asset('images/nen03.png');

    /** Định dạng ngày kiểu "15 Tháng 4, 2024". */
    $formatDate = function ($date) {
        if (!$date) {
            return null;
        }

        return $date->format('d') . ' Tháng ' . $date->format('n') . ', ' . $date->format('Y');
    };
@endphp

@section('content')
<div class="page-shell">
    <div class="container py-4">
        <div class="page-header">
            <h1 class="page-header__title">Tin tức & Cẩm nang</h1>
            <p class="page-header__subtitle mb-0">Thông tin du lịch, văn hóa và sự kiện nổi bật tại Ninh Bình</p>
        </div>

        @if($featured)
            <div class="row g-4 mb-4 pb-4 border-bottom" style="border-color: #e5e7eb !important;">
                <div class="col-lg-7">
                    <article>
                        <a href="{{ $featured['url'] }}" class="text-decoration-none editorial-link d-block">
                            <div class="mb-3">
                                @include('client.partials.cover-image', [
                                    'src' => $featured['image'],
                                    'alt' => $featured['title'],
                                    'ratio' => '16/9',
                                ])
                            </div>
                            <h2 class="editorial-link__title fw-semibold mb-2" style="color: #27272a; font-size: 1.25rem; line-height: 1.4;">
                                {{ $featured['title'] }}
                            </h2>
                            @if($featured['excerpt'])
                                <p class="mb-2" style="color: #52525b; font-size: 0.875rem; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $featured['excerpt'] }}
                                </p>
                            @endif
                            @if($featured['date'])
                                <div class="meta-text">
                                    {{ $featured['date']->format('d/m/Y') }}
                                </div>
                            @endif
                        </a>
                    </article>
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3 h-100">
                        @foreach($subFeatured as $sub)
                            <article class="pb-3 border-bottom" style="border-color: #f4f4f5 !important;">
                                <a href="{{ $sub['url'] }}" class="text-decoration-none editorial-link d-flex gap-3 align-items-start">
                                    <div style="width: 130px; flex-shrink: 0;">
                                        @include('client.partials.cover-image', [
                                            'src' => $sub['image'],
                                            'alt' => $sub['title'],
                                            'ratio' => '4/3',
                                        ])
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <h3 class="editorial-link__title fw-semibold mb-2" style="color: #27272a; font-size: 0.9rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $sub['title'] }}
                                        </h3>
                                        @if($sub['date'])
                                            <div class="meta-text">
                                                {{ $sub['date']->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="nb news-merge-compact">
    {{-- ============ Danh mục + lưới nội dung ============ --}}
    <section class="nb-section nb-section--tight">
        <div class="nb-wrap">
            <div class="nb-columns">

                <aside>
                    <div class="nb-side">
                        <div class="nb-side__title">Danh mục</div>
                        <ul class="nb-side__list">
                            @foreach($categories as $key => $meta)
                                <li>
                                    <a href="{{ route('client.events.index', $key === 'all' ? [] : ['cat' => $key]) }}"
                                       class="nb-side__link {{ $cat === $key ? 'is-active' : '' }}">
                                        <span>{{ $meta['label'] }}</span>
                                        <span class="nb-side__count">{{ $meta['count'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <div>
                    @if($items->isNotEmpty())
                        <div class="nb-card-grid">
                            @foreach($items as $item)
                                <a href="{{ $item['url'] }}" class="nb-card">
                                    <div class="nb-card__media">
                                        <img src="{{ $item['image'] ?: $placeholder }}"
                                             alt="{{ $item['title'] }}"
                                             class="nb-card__img"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                                        <span class="nb-chip {{ $item['kind'] === 'event' ? 'nb-chip--warm' : 'nb-chip--ink' }} nb-card__chip">{{ $item['label'] }}</span>
                                    </div>
                                    <div class="nb-card__body">
                                        @if($item['date'])
                                            <div class="nb-card__date">{{ $formatDate($item['date']) }}</div>
                                        @endif
                                        <h3 class="nb-card__title">{{ $item['title'] }}</h3>
                                        @if($item['excerpt'])
                                            <p class="nb-card__excerpt">{{ $item['excerpt'] }}</p>
                                        @endif
                                        <span class="nb-card__more">Đọc tiếp</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if($items->hasPages())
                            <div class="nb-pager">
                                {{ $items->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @else
                        <div class="nb-empty">
                            <h3 class="nb-empty__title">Chưa có nội dung trong danh mục này</h3>
                            <p class="nb-empty__text">
                                Hãy chọn một danh mục khác hoặc quay lại sau — nội dung mới được cập nhật thường xuyên.
                            </p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
