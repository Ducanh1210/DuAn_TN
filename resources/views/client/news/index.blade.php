@extends('client.layouts.app')

@section('title', 'Tin Tức & Cẩm Nang Du Lịch Ninh Bình')

@section('content')
<div class="news-page-wrapper" style="background-color: #ffffff; min-height: 100vh; padding-bottom: 60px;">
    <div class="container py-4">
        <!-- Clean Main Header -->
        <div class="news-main-header mb-4 pb-3 border-bottom" style="border-color: #e5e7eb !important;">
            <h2 class="fw-semibold mb-1" style="color: #27272a; font-size: 1.4rem; letter-spacing: -0.01em;">Tin tức & Cẩm nang</h2>
            <div style="color: #71717a; font-size: 0.825rem; font-weight: 400;">Thông tin du lịch, văn hóa và sự kiện nổi bật tại Ninh Bình</div>
        </div>

        @if($newsList->currentPage() == 1 && $newsList->count() > 0)
            @php
                $heroNews = $newsList->first();
                $subFeatured = $newsList->slice(1, 2);
                $remainingNews = $newsList->slice(3);
            @endphp

            <!-- Hero Section -->
            <div class="row g-4 mb-4 pb-4 border-bottom" style="border-color: #e5e7eb !important;">
                <!-- Main Hero Article -->
                <div class="col-lg-7">
                    <article class="hero-news-item">
                        <a href="{{ route('client.news.show', $heroNews->slug) }}" class="text-decoration-none group-news-link">
                            <div class="overflow-hidden rounded-2 mb-3 bg-light" style="aspect-ratio: 16/9;">
                                <img src="{{ $heroNews->featured_image ? (str_starts_with($heroNews->featured_image, 'http') ? $heroNews->featured_image : asset('storage/' . ltrim($heroNews->featured_image, '/'))) : 'https://via.placeholder.com/800x450?text=News' }}" alt="{{ $heroNews->title }}" class="w-100 h-100 img-fade" style="object-fit: cover; transition: opacity 0.2s ease;">
                            </div>
                            <h3 class="hero-title fw-semibold mb-2" style="color: #27272a; font-size: 1.25rem; line-height: 1.4;">
                                {{ $heroNews->title }}
                            </h3>
                            <p class="mb-2" style="color: #52525b; font-size: 0.85rem; line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-weight: 400;">
                                {{ strip_tags($heroNews->summary ?? $heroNews->content) }}
                            </p>
                            <div style="color: #a1a1aa; font-size: 0.775rem;">
                                {{ $heroNews->published_at ? $heroNews->published_at->format('d/m/Y') : $heroNews->created_at->format('d/m/Y') }}
                            </div>
                        </a>
                    </article>
                </div>

                <!-- Sub Featured Articles -->
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-3 h-100 justify-content-between">
                        @foreach($subFeatured as $sub)
                            <article class="sub-featured-item pb-3 border-bottom border-light">
                                <a href="{{ route('client.news.show', $sub->slug) }}" class="text-decoration-none d-flex gap-3 align-items-start group-news-link">
                                    <div class="overflow-hidden rounded-2 bg-light flex-shrink-0" style="width: 130px; aspect-ratio: 4/3;">
                                        <img src="{{ $sub->featured_image ? (str_starts_with($sub->featured_image, 'http') ? $sub->featured_image : asset('storage/' . ltrim($sub->featured_image, '/'))) : 'https://via.placeholder.com/300x225?text=News' }}" alt="{{ $sub->title }}" class="w-100 h-100 img-fade" style="object-fit: cover; transition: opacity 0.2s ease;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="sub-title fw-semibold mb-2" style="color: #27272a; font-size: 0.9rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $sub->title }}
                                        </h5>
                                        <div style="color: #a1a1aa; font-size: 0.75rem;">
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
            <!-- Left Stream -->
            <div class="col-lg-8 pe-lg-4">
                <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                    <span class="fw-semibold" style="color: #3f3f46; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em;">Các bài viết mới</span>
                </div>

                <div class="news-feed d-flex flex-column gap-3">
                    @forelse($remainingNews as $item)
                        <article class="feed-item pb-3 border-bottom" style="border-color: #f4f4f5 !important;">
                            <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none group-news-link">
                                <div class="row g-3 align-items-start">
                                    <div class="col-4 col-sm-3">
                                        <div class="overflow-hidden rounded-2 bg-light" style="aspect-ratio: 4/3;">
                                            <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x225?text=News' }}" alt="{{ $item->title }}" class="w-100 h-100 img-fade" style="object-fit: cover; transition: opacity 0.2s ease;">
                                        </div>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        <h4 class="item-title fw-semibold mb-1" style="color: #27272a; font-size: 0.95rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->title }}
                                        </h4>
                                        <p class="d-none d-sm-block mb-1" style="color: #52525b; font-size: 0.825rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-weight: 400;">
                                            {{ strip_tags($item->summary ?? $item->content) }}
                                        </p>
                                        <div style="color: #a1a1aa; font-size: 0.75rem;">
                                            {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @empty
                        <div class="text-center py-5">
                            <p style="color: #71717a; font-size: 0.85rem;">Chưa có bài viết nào.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 pt-2 custom-pagination">
                    {{ $newsList->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar position-sticky" style="top: 90px;">
                    
                    <!-- TIN XEM NHIỀU -->
                    <div class="sidebar-block mb-4 pb-3 border-bottom" style="border-color: #e5e7eb !important;">
                        <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e5e7 !important;">
                            <span class="fw-semibold" style="color: #3f3f46; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em;">Xem nhiều nhất</span>
                        </div>

                        <div class="most-read-list d-flex flex-column gap-3">
                            @forelse($popularNews as $index => $item)
                                <article class="most-read-item d-flex gap-3 align-items-baseline">
                                    <span style="font-size: 0.95rem; font-weight: 600; color: #a1a1aa; width: 18px; flex-shrink: 0;">
                                        {{ $index + 1 }}.
                                    </span>
                                    <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none group-news-link flex-grow-1">
                                        <h6 class="sidebar-title fw-normal mb-1" style="color: #27272a; font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $item->title }}
                                        </h6>
                                        <div style="color: #a1a1aa; font-size: 0.725rem;">{{ number_format($item->view_count) }} lượt xem</div>
                                    </a>
                                </article>
                            @empty
                                <p style="color: #71717a; font-size: 0.8rem;">Chưa có dữ liệu.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- SỰ KIỆN DU LỊCH -->
                    <div class="sidebar-block mb-4 pb-3 border-bottom" style="border-color: #e5e7eb !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                            <span class="fw-semibold" style="color: #3f3f46; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.03em;">Sự kiện sắp tới</span>
                            <a href="{{ route('client.events.index') }}" class="text-decoration-none style-link" style="color: #71717a; font-size: 0.775rem;">Xem tất cả</a>
                        </div>

                        <div class="events-list d-flex flex-column gap-3">
                            @forelse($upcomingEvents as $item)
                                <article>
                                    <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none group-news-link d-flex gap-3 align-items-center">
                                        <div class="overflow-hidden rounded bg-light flex-shrink-0" style="width: 65px; height: 50px;">
                                            <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150' }}" alt="{{ $item->title }}" class="w-100 h-100 img-fade" style="object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-normal mb-1" style="color: #27272a; font-size: 0.825rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->title }}
                                            </h6>
                                            <div style="color: #a1a1aa; font-size: 0.725rem;">
                                                {{ $item->published_at ? $item->published_at->format('d/m/Y') : '' }}
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @empty
                                <p style="color: #71717a; font-size: 0.8rem;">Không có sự kiện nào sắp tới.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- NHIỆM VỤ TÍCH XU -->
                    <div class="sidebar-block p-3 rounded-2" style="background-color: #fafafa; border: 1px solid #e5e7eb;">
                        <div class="fw-semibold mb-2" style="color: #27272a; font-size: 0.825rem;">Nhiệm vụ tích xu</div>
                        @auth
                            <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.775rem;">
                                <span style="color: #71717a;">Xu hiện tại:</span>
                                <span class="fw-semibold" style="color: #27272a;" id="sidebarMissionPoints">{{ Auth::user()->points }} xu</span>
                            </div>
                            @if(Auth::user()->last_daily_bonus_at && \Carbon\Carbon::parse(Auth::user()->last_daily_bonus_at)->isToday())
                                <span class="d-block text-center py-1 rounded" style="background: #f4f4f5; color: #71717a; font-size: 0.75rem;">Đã điểm danh hôm nay</span>
                            @else
                                <button type="button" id="claimDailyBtn" class="btn btn-sm w-100 fw-medium" style="background-color: #27272a; color: #ffffff; font-size: 0.75rem; border-radius: 6px;">Điểm danh nhận xu</button>
                            @endif
                        @else
                            <p style="color: #71717a; font-size: 0.775rem;" class="mb-2">Đăng nhập để nhận xu thưởng hàng ngày.</p>
                            <a href="{{ route('login') }}" class="btn btn-sm w-100 fw-medium" style="background-color: #27272a; color: #ffffff; font-size: 0.75rem; border-radius: 6px;">Đăng nhập</a>
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Soft hover transitions without loud colors */
    .group-news-link:hover .hero-title,
    .group-news-link:hover .sub-title,
    .group-news-link:hover .item-title,
    .group-news-link:hover .sidebar-title {
        color: #000000 !important;
        text-decoration: underline;
    }

    .group-news-link:hover .img-fade {
        opacity: 0.88;
    }

    /* Custom Pagination */
    .custom-pagination .pagination {
        gap: 4px;
    }
    .custom-pagination .page-link {
        color: #3f3f46;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 5px 11px;
        font-size: 0.825rem;
        font-weight: 400;
        background: #ffffff;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #27272a;
        border-color: #27272a;
        color: #ffffff;
    }
</style>

@auth
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const claimDailyBtn = document.getElementById("claimDailyBtn");
        if (claimDailyBtn) {
            claimDailyBtn.addEventListener("click", function() {
                claimDailyBtn.disabled = true;
                claimDailyBtn.innerHTML = 'Đang nhận...';

                fetch("{{ route('client.profile.claim_daily') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        claimDailyBtn.outerHTML = '<span class="d-block text-center py-1 rounded" style="background: #f4f4f5; color: #71717a; font-size: 0.75rem;">Đã điểm danh hôm nay</span>';
                        const sidebarPoints = document.getElementById("sidebarMissionPoints");
                        if (sidebarPoints) {
                            sidebarPoints.textContent = data.points + " xu";
                        }
                    } else {
                        claimDailyBtn.disabled = false;
                        claimDailyBtn.innerHTML = "Điểm danh nhận xu";
                    }
                })
                .catch(error => {
                    claimDailyBtn.disabled = false;
                    claimDailyBtn.innerHTML = "Điểm danh nhận xu";
                });
            });
        }
    });
</script>
@endauth
@endsection
