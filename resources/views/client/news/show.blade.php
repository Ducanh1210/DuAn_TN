@extends('client.layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-3 py-md-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.news.index') }}">Tin tức</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($news->title, 50) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white p-3 p-md-4 rounded-3 shadow-sm">
                <span class="badge bg-primary mb-3">{{ $news->type_label }}</span>
                <h1 class="fw-bold mb-3">{{ $news->title }}</h1>
                <div class="d-flex align-items-center text-muted mb-3 pb-3 border-bottom small">
                    <span class="me-4"><i class="fa-regular fa-calendar me-2"></i> {{ $news->published_at ? $news->published_at->format('d/m/Y') : $news->created_at->format('d/m/Y') }}</span>
                    <span><i class="fa-regular fa-eye me-2"></i> {{ $news->view_count }} lượt xem</span>
                </div>

                @if($news->featured_image)
                    <img src="{{ str_starts_with($news->featured_image, 'http') ? $news->featured_image : asset('storage/' . ltrim($news->featured_image, '/')) }}" class="img-fluid rounded-3 mb-4 w-100" alt="{{ $news->title }}" style="max-height: 400px; object-fit: cover;">
                @endif

                @if($news->summary)
                    <div class="fw-semibold mb-3 text-dark" style="font-size: 0.95rem; line-height: 1.6;">
                        {{ $news->summary }}
                    </div>
                @endif

                <div class="content-body" style="line-height: 1.6; font-size: 0.9rem;">
                    {!! $news->content !!}
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="bg-white p-3 rounded-3 shadow-sm position-sticky" style="top: 90px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Tin tức liên quan</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($relatedNews as $item)
                        <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none">
                            <div class="d-flex gap-3 align-items-center group-hover">
                                <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150' }}" alt="{{ $item->title }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="text-dark fw-bold mb-1 text-hover-primary" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $item->title }}</h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted">Không có bài viết nào khác.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-hover-primary { transition: color 0.2s; }
    .group-hover:hover .text-hover-primary { color: #0072FF !important; }
    .content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0; }
</style>
@endsection
