@extends('client.layouts.app')

@section('title', 'Tin Tức & Cẩm Nang Du Lịch Ninh Bình')

@section('content')
<div class="page-shell">
    <div class="container py-4">
        <div class="page-header">
            <h1 class="page-header__title">Tin tức & Cẩm nang</h1>
            <p class="page-header__subtitle mb-0">Thông tin du lịch, văn hóa và sự kiện nổi bật tại Ninh Bình</p>
        </div>

        @if($newsList->currentPage() == 1 && $newsList->count() > 0)
            @php
                $heroNews = $newsList->first();
                $subFeatured = $newsList->slice(1, 2);
                $remainingNews = $newsList->slice(3);
            @endphp

            <div class="row g-4 mb-4 pb-4 border-bottom" style="border-color: #e5e7eb !important;">
                <div class="col-lg-7">
                    <article>
                        <a href="{{ route('client.news.show', $heroNews->slug) }}" class="text-decoration-none editorial-link d-block">
                            @include('client.partials.cover-image', [
                                'src' => $heroNews->featured_image_url,
                                'alt' => $heroNews->title,
                                'class' => 'mb-3',
                                'ratio' => '16/9',
                            ])
                            <h2 class="editorial-link__title fw-semibold mb-2" style="color: #27272a; font-size: 1.25rem; line-height: 1.4;">
                                {{ $heroNews->title }}
                            </h2>
                            <p class="mb-2" style="color: #52525b; font-size: 0.875rem; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ strip_tags($heroNews->summary ?? $heroNews->content) }}
                            </p>
                            <div class="meta-text">
                                {{ $heroNews->published_at ? $heroNews->published_at->format('d/m/Y') : $heroNews->created_at->format('d/m/Y') }}
                            </div>
                        </a>
                    </article>
                </div>

                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3 h-100">
                        @foreach($subFeatured as $sub)
                            <article class="pb-3 border-bottom" style="border-color: #f4f4f5 !important;">
                                <a href="{{ route('client.news.show', $sub->slug) }}" class="text-decoration-none editorial-link d-flex gap-3 align-items-start">
                                    <div style="width: 130px; flex-shrink: 0;">
                                        @include('client.partials.cover-image', [
                                            'src' => $sub->featured_image_url,
                                            'alt' => $sub->title,
                                            'ratio' => '4/3',
                                        ])
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <h3 class="editorial-link__title fw-semibold mb-2" style="color: #27272a; font-size: 0.9rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $sub->title }}
                                        </h3>
                                        <div class="meta-text">
                                            {{ $sub->published_at ? $sub->published_at->format('d/m/Y') : $sub->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            @php $remainingNews = $newsList; @endphp
        @endif

        <div class="row g-4">
            <div class="col-lg-8 pe-lg-4">
                <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                    <span class="section-label">Các bài viết mới</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($remainingNews as $item)
                        <article class="pb-3 border-bottom" style="border-color: #f4f4f5 !important;">
                            <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none editorial-link">
                                <div class="row g-3 align-items-start">
                                    <div class="col-4 col-sm-3">
                                        @include('client.partials.cover-image', [
                                            'src' => $item->featured_image_url,
                                            'alt' => $item->title,
                                            'ratio' => '4/3',
                                        ])
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        <h4 class="editorial-link__title fw-semibold mb-1" style="color: #27272a; font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->title }}
                                        </h4>
                                        <p class="d-none d-sm-block mb-1" style="color: #52525b; font-size: 0.825rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ strip_tags($item->summary ?? $item->content) }}
                                        </p>
                                        <div class="meta-text">
                                            {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state__title">Chưa có bài viết</div>
                            <p class="empty-state__text mb-0">Nội dung tin tức sẽ được cập nhật sớm. Bạn có thể khám phá bản đồ du lịch trong lúc chờ.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary btn-sm mt-3">Xem bản đồ</a>
                        </div>
                    @endforelse
                </div>

                @if($newsList->hasPages())
                    <div class="mt-4 pt-2 custom-pagination">
                        {{ $newsList->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="position-sticky" style="top: 90px;">
                    <div class="mb-4 pb-3 border-bottom" style="border-color: #e5e7eb !important;">
                        <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                            <span class="section-label">Xem nhiều nhất</span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @forelse($popularNews as $index => $item)
                                <article class="d-flex gap-3 align-items-baseline">
                                    <span style="font-size: 0.9rem; font-weight: 600; color: #a1a1aa; width: 18px; flex-shrink: 0;">
                                        {{ $index + 1 }}.
                                    </span>
                                    <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none editorial-link flex-grow-1">
                                        <h6 class="editorial-link__title fw-normal mb-1" style="color: #27272a; font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->title }}
                                        </h6>
                                        <div class="meta-text">{{ number_format($item->view_count) }} lượt xem</div>
                                    </a>
                                </article>
                            @empty
                                <p class="meta-text mb-0">Chưa có dữ liệu.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                            <span class="section-label">Sự kiện sắp tới</span>
                            <a href="{{ route('client.events.index') }}" class="meta-text" style="color: #6482a6;">Xem tất cả</a>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @forelse($upcomingEvents as $item)
                                <article>
                                    <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none editorial-link d-flex gap-3 align-items-center">
                                        <div style="width: 72px; flex-shrink: 0;">
                                            @include('client.partials.cover-image', [
                                                'src' => $item->featured_image_url,
                                                'alt' => $item->name,
                                                'ratio' => '4/3',
                                            ])
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <h6 class="editorial-link__title fw-normal mb-1" style="color: #27272a; font-size: 0.825rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->name }}
                                            </h6>
                                            <div class="meta-text">
                                                @if($item->start_time)
                                                    {{ $item->start_time->format('d/m/Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @empty
                                <p class="meta-text mb-0">Không có sự kiện nào sắp tới.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
