@extends('client.layouts.app')

@section('title', 'Sự Kiện Lễ Hội Tại Hà Nam')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb or simple Title -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-uppercase" style="color: #2c3e50;">SỰ KIỆN - LỄ HỘI</h2>
    </div>

    <div class="row">
        <!-- Main Events List -->
        <div class="col-lg-12">
            <div class="d-flex flex-column gap-4">
                @forelse($events as $event)
                    <article class="news-list-item pb-4 border-bottom">
                        <a href="{{ route('client.events.show', $event->slug) }}" class="text-decoration-none">
                            <div class="row g-3 align-items-start">
                                <!-- Thumbnail -->
                                <div class="col-sm-4 col-md-5 position-relative">
                                    <div class="img-wrapper rounded overflow-hidden shadow-sm">
                                        <img src="{{ $event->featured_image ? (str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . ltrim($event->featured_image, '/'))) : 'https://via.placeholder.com/600x400?text=No+Image' }}" alt="{{ $event->title }}" class="img-fluid w-100" style="aspect-ratio: 4/3; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>
                                    <span class="position-absolute top-0 start-0 m-2 badge bg-danger shadow-sm">Sự kiện</span>
                                </div>
                                <!-- Content -->
                                <div class="col-sm-8 col-md-7">
                                    <h4 class="news-title fw-bold mb-2" style="color: #1a1a1a; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: color 0.2s;">
                                        {{ $event->title }}
                                    </h4>
                                    <p class="small fw-semibold text-danger mb-2"><i class="fa-regular fa-calendar me-1"></i> {{ $event->published_at ? $event->published_at->format('d/m/Y H:i') : $event->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="news-excerpt text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.5;">
                                        {{ strip_tags($event->summary) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="text-center py-5">
                        <p class="text-muted">Chưa có sự kiện nào được đăng tải.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 custom-pagination">
                {{ $events->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>

<style>
    /* Hover Effects */
    .news-list-item:hover .news-title { color: #27ae60 !important; }
    .news-list-item:hover .img-wrapper img { transform: scale(1.05); }

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
