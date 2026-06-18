@extends('client.layouts.app')

@section('title', $event->title)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.events.index') }}">Sự kiện</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($event->title, 50) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-4">
                <span class="badge bg-danger mb-3 px-3 py-2 fs-6"><i class="fa-solid fa-fire me-1"></i> Sự kiện</span>
                
                <h1 class="fw-bold mb-4 text-danger">{{ $event->title }}</h1>
                
                <div class="text-muted small mb-4">
                    <i class="fa-regular fa-calendar me-1"></i> Xuất bản: {{ $event->published_at ? $event->published_at->format('d/m/Y H:i') : $event->created_at->format('d/m/Y H:i') }}
                    <span class="mx-2">|</span>
                    <i class="fa-solid fa-eye me-1"></i> {{ number_format($event->view_count) }} lượt xem
                </div>
                
                @if($event->featured_image)
                    <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . ltrim($event->featured_image, '/')) }}" class="img-fluid rounded-3 mb-4 w-100" alt="{{ $event->title }}" style="max-height: 500px; object-fit: cover;">
                @endif

                @if($event->summary)
                    <div class="alert alert-light border shadow-sm mb-4" style="font-size: 1.1rem; font-style: italic;">
                        {{ $event->summary }}
                    </div>
                @endif

                @if($event->content)
                    <h4 class="fw-bold mb-3 border-start border-4 border-danger ps-3">Thông tin chi tiết</h4>
                    <div class="content-body mb-4" style="line-height: 1.8; font-size: 16px;">
                        {!! $event->content !!}
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="bg-white p-4 rounded-4 shadow-sm position-sticky" style="top: 100px;">
                <h4 class="fw-bold mb-4 border-bottom pb-2">Sự kiện khác</h4>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedEvents as $item)
                        <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none">
                            <div class="d-flex gap-3 align-items-center group-hover">
                                <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150' }}" alt="{{ $item->title }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="text-dark fw-bold mb-1 text-hover-danger" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $item->title }}</h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted">Không có sự kiện nào khác.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-hover-danger { transition: color 0.2s; }
    .group-hover:hover .text-hover-danger { color: #dc3545 !important; }
    .content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
</style>
@endsection
