@extends('client.layouts.app')

@section('title', 'Tin Tức & Cẩm Nang Du Lịch Hà Nam')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb or simple Title -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-uppercase" style="color: #2c3e50;">TIN TỨC - SỰ KIỆN</h2>
    </div>

    <div class="row">
        <!-- Left Column: Main News List -->
        <div class="col-lg-8 pe-lg-4">
            <div class="d-flex flex-column gap-4">
                @forelse($newsList as $item)
                    <article class="news-list-item pb-4 border-bottom">
                        <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none">
                            <div class="row g-3 align-items-start">
                                <!-- Thumbnail -->
                                <div class="col-sm-4 col-md-5">
                                    <div class="img-wrapper rounded overflow-hidden shadow-sm">
                                        <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/600x400?text=No+Image' }}" alt="{{ $item->title }}" class="img-fluid w-100" style="aspect-ratio: 4/3; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>
                                </div>
                                <!-- Content -->
                                <div class="col-sm-8 col-md-7">
                                    <h4 class="news-title fw-bold mb-2" style="color: #1a1a1a; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="news-excerpt text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.5;">
                                        {{ strip_tags($item->summary ?? $item->content) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="text-center py-5">
                        <p class="text-muted">Chưa có bài viết nào.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 custom-pagination">
                {{ $newsList->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="sidebar position-sticky" style="top: 100px;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-danger border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #c0392b;">TIN NỔI BẬT</h5>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    @forelse($popularNews as $item)
                        <article class="sidebar-item">
                            <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->title }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->title }}
                                    </h6>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="text-muted small">Chưa có tin nổi bật.</p>
                    @endforelse
                </div>

                <!-- EVENTS BLOCK IN SIDEBAR -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-success border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #27ae60;">SỰ KIỆN SẮP TỚI</h5>
                    <a href="{{ route('client.events.index') }}" class="small text-success text-decoration-none fw-semibold">Xem thêm <i class="fa-solid fa-angle-right"></i></a>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($upcomingEvents as $item)
                        <article class="sidebar-item">
                            <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->name }}
                                    </h6>
                                    <small class="text-success fw-semibold"><i class="fa-regular fa-calendar me-1"></i> {{ $item->start_time ? $item->start_time->format('d/m/Y') : '' }}</small>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="text-muted small">Không có sự kiện nào sắp tới.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .news-list-item:hover .news-title { color: #27ae60 !important; }
    .news-list-item:hover .img-wrapper img { transform: scale(1.05); }
    
    .sidebar-item:hover .sidebar-title { color: #2980b9 !important; }

    /* Pagination */
    .custom-pagination .page-link {
        color: #2c3e50;
        border: none;
        padding: 8px 12px;
        margin: 0 2px;
        font-weight: 600;
        background: transparent;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #2c3e50;
        color: white;
        border-radius: 4px;
    }
</style>
@endsection
