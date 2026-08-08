<div class="photo-viewer-stage" id="photoViewer">
    @if(count($photoSlides) > 0)
        <img
            src="{{ $photoSlides[0]['url'] }}"
            alt=""
            class="photo-viewer-stage__blur"
            id="photoViewerBlur"
            aria-hidden="true"
        >
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
        @endif
    @else
        <div class="photo-viewer-stage__empty">
            <p style="font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 8px;">{{ $location->name }}</p>
            <p style="margin: 0;">Địa điểm chưa có ảnh minh họa</p>
        </div>
    @endif
</div>
