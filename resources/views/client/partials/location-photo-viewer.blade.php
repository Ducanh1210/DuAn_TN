<div class="photo-viewer-stage" id="photoViewer">
    @if(count($photoSlides) > 0)
        <img
            src="{{ $photoSlides[0]['url'] }}"
            alt="{{ $photoSlides[0]['caption'] ?? $location->name }}"
            class="photo-viewer-stage__img"
            id="photoViewerImage"
        >
        @if(count($photoSlides) > 1)
            <button type="button" class="photo-viewer-nav photo-viewer-nav--prev" id="photoViewerPrev" aria-label="Ảnh trước">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="photo-viewer-nav photo-viewer-nav--next" id="photoViewerNext" aria-label="Ảnh sau">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <div class="photo-viewer-counter" id="photoViewerCounter">1 / {{ count($photoSlides) }}</div>
            <div class="photo-viewer-thumbs" id="photoViewerThumbs">
                @foreach($photoSlides as $index => $slide)
                    <button type="button" class="photo-viewer-thumb {{ $index === 0 ? 'is-active' : '' }}" data-index="{{ $index }}" aria-label="Ảnh {{ $index + 1 }}">
                        <img src="{{ $slide['url'] }}" alt="">
                    </button>
                @endforeach
            </div>
        @endif
    @else
        <div class="photo-viewer-stage__empty">
            <p style="font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 8px;">{{ $location->name }}</p>
            <p style="margin: 0;">Địa điểm chưa có ảnh minh họa</p>
        </div>
    @endif

    <span class="photo-viewer-badge">Thư viện ảnh</span>

    <div class="photo-viewer-info">
        <h2 class="photo-viewer-info__title">{{ $location->name }}</h2>
        @if($location->category)
            <p class="photo-viewer-info__meta">{{ $location->category->name }}@if($location->address) · {{ $location->address }}@endif</p>
        @elseif($location->address)
            <p class="photo-viewer-info__meta">{{ $location->address }}</p>
        @endif
    </div>
</div>

@if(count($photoSlides) > 1)
<script>
(function () {
    const slides = @json($photoSlides);
    let current = 0;
    const img = document.getElementById('photoViewerImage');
    const counter = document.getElementById('photoViewerCounter');
    const btnPrev = document.getElementById('photoViewerPrev');
    const btnNext = document.getElementById('photoViewerNext');
    const thumbs = document.querySelectorAll('.photo-viewer-thumb');

    function render(index) {
        current = (index + slides.length) % slides.length;
        const slide = slides[current];
        if (img) {
            img.src = slide.url;
            img.alt = slide.caption || @json($location->name);
        }
        if (counter) counter.textContent = (current + 1) + ' / ' + slides.length;
        thumbs.forEach((t, i) => t.classList.toggle('is-active', i === current));
    }

    if (btnPrev) btnPrev.addEventListener('click', () => render(current - 1));
    if (btnNext) btnNext.addEventListener('click', () => render(current + 1));
    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => render(parseInt(thumb.dataset.index, 10)));
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') render(current - 1);
        if (e.key === 'ArrowRight') render(current + 1);
    });
})();
</script>
@endif
