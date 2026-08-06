{{-- Gallery fallback when location has no 360 panoramas --}}
<div class="location-gallery-page">
    <header class="location-gallery-page__header">
        <a href="{{ route('home') }}" class="location-gallery-page__back">← Quay lại bản đồ</a>
        <h1 class="location-gallery-page__title">{{ $location->name }}</h1>
        @if($location->category)
            <p class="location-gallery-page__meta">{{ $location->category->name }}</p>
        @endif
    </header>

    <div class="location-gallery-page__body">
        @if($heroImage ?? null)
            <div class="location-gallery-page__hero mb-4">
                <img src="{{ $heroImage }}" alt="{{ $location->name }}" class="w-100 rounded-3" style="max-height: 420px; object-fit: cover;">
            </div>
        @endif

        @if(!empty($galleryImages))
            <div class="location-gallery-page__grid mb-4">
                @foreach($galleryImages as $image)
                    <figure class="location-gallery-page__item">
                        <img src="{{ $image['url'] }}" alt="{{ $image['caption'] ?? $location->name }}" loading="lazy">
                        @if(!empty($image['caption']))
                            <figcaption>{{ $image['caption'] }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @else
            <div class="location-gallery-page__empty mb-4">
                <p>Địa điểm này chưa có ảnh minh họa.</p>
            </div>
        @endif

        @if($location->short_description || $location->description)
            <div class="location-gallery-page__desc">
                <h2 class="h6 fw-semibold mb-2" style="color: #1e3a5f;">Giới thiệu</h2>
                <div style="color: #3f3f46; line-height: 1.7; font-size: 0.925rem;">
                    {!! nl2br(e(strip_tags($location->short_description ?: $location->description))) !!}
                </div>
            </div>
        @endif

        @if($location->address)
            <p class="location-gallery-page__meta mt-3 mb-0">{{ $location->address }}</p>
        @endif
    </div>
</div>

<style>
    .location-gallery-page {
        min-height: 100vh;
        background: #f8fafc;
        color: #27272a;
    }
    .location-gallery-page__header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px 20px;
    }
    .location-gallery-page__back {
        display: inline-block;
        color: #6482a6;
        font-size: 0.825rem;
        margin-bottom: 10px;
        text-decoration: none;
    }
    .location-gallery-page__back:hover { color: #1e3a5f; }
    .location-gallery-page__title {
        font-size: 1.35rem;
        font-weight: 600;
        color: #1e3a5f;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }
    .location-gallery-page__meta {
        color: #6482a6;
        font-size: 0.825rem;
        margin: 0;
    }
    .location-gallery-page__body {
        max-width: 960px;
        margin: 0 auto;
        padding: 24px 20px 48px;
    }
    .location-gallery-page__grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }
    .location-gallery-page__item {
        margin: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .location-gallery-page__item img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        display: block;
    }
    .location-gallery-page__item figcaption {
        padding: 8px 10px;
        font-size: 0.775rem;
        color: #64748b;
    }
    .location-gallery-page__empty,
    .location-gallery-page__desc {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
    }
    .location-gallery-page__empty p {
        margin: 0;
        color: #64748b;
        font-size: 0.875rem;
    }
</style>
