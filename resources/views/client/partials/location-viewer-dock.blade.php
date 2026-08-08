{{-- Bottom dock + horizontal photo strip — same for photo & 360 modes --}}
@php
    $isFavorited = Auth::check() && Auth::user()->favoriteLocations()->where('location_id', $location->id)->exists();
    $hasPhotos = count($photoSlides) > 0;
@endphp

{{-- Photo peek overlay (360: xem ảnh trên panorama; photo mode: optional) --}}
@if($hasPhotos)
<div class="pano-photo-overlay" id="panoPhotoOverlay" hidden>
    <img src="" alt="" class="pano-photo-overlay__blur" id="panoPhotoOverlayBlur" aria-hidden="true">
    <img src="" alt="" class="pano-photo-overlay__img" id="panoPhotoOverlayImg">
    @if(count($photoSlides) > 1)
    <button type="button" class="photo-viewer-nav photo-viewer-nav--prev" id="panoPhotoOverlayPrev" aria-label="Ảnh trước">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button type="button" class="photo-viewer-nav photo-viewer-nav--next" id="panoPhotoOverlayNext" aria-label="Ảnh sau">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
    @endif
    <button type="button" class="pano-photo-overlay__close" id="panoPhotoOverlayClose" aria-label="Đóng ảnh">×</button>
</div>
@endif

{{-- Horizontal photo strip (opens via nút Ảnh) --}}
@if($hasPhotos)
<div class="viewer-photo-strip" id="viewerPhotoStrip" hidden>
    <div class="viewer-photo-strip__inner">
        <button type="button" class="viewer-photo-strip__collapse" id="btnCollapsePhotos" aria-label="Thu gọn danh sách ảnh">
            <i class="fa-solid fa-chevron-down"></i>
        </button>
        <div class="viewer-photo-strip__scroller" id="viewerPhotoStripList"></div>
    </div>
</div>
@endif

{{-- Bottom action dock --}}
<div class="viewer-dock" id="viewerDock">
    <button type="button" class="viewer-dock__btn {{ $isFavorited ? 'is-active' : '' }}" id="btnToggleFavorite" title="Lưu">
        <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
        <span class="viewer-dock__label">Lưu</span>
    </button>

    <button type="button" class="viewer-dock__btn" id="btnToggleComments" title="Bình luận">
        <span class="viewer-dock__icon-wrap">
            <i class="fa-regular fa-comment"></i>
            <span class="viewer-dock__badge" id="commentsCountBadge">{{ $location->comments->count() }}</span>
        </span>
        <span class="viewer-dock__label">Bình luận</span>
    </button>

    @if($hasPhotos)
    <button type="button" class="viewer-dock__btn" id="btnTogglePhotos" title="Ảnh">
        <i class="fa-regular fa-image"></i>
        <span class="viewer-dock__label">Ảnh</span>
    </button>
    @endif
</div>

@if($hasPhotos)
<script>
(function () {
    const slides = @json($photoSlides);
    const locationName = @json($location->name);
    const isPhotoMode = @json((bool) $photoMode);
    if (!slides.length) return;

    // Dock script nằm trước #photoViewer trong DOM — chờ DOM sẵn sàng
    function boot() {
        const strip = document.getElementById('viewerPhotoStrip');
        const list = document.getElementById('viewerPhotoStripList');
        const btnPhotos = document.getElementById('btnTogglePhotos');
        const btnCollapse = document.getElementById('btnCollapsePhotos');
        const overlay = document.getElementById('panoPhotoOverlay');
        const overlayImg = document.getElementById('panoPhotoOverlayImg');
        const overlayBlur = document.getElementById('panoPhotoOverlayBlur');
        const overlayClose = document.getElementById('panoPhotoOverlayClose');
        if (!strip || !list) return;

        let current = isPhotoMode ? 0 : -1;
        let stripOpen = false;
        let autoTimer = null;
        const AUTO_MS = 4000;

        function mainImg() { return document.getElementById('photoViewerImage'); }
        function mainBlur() { return document.getElementById('photoViewerBlur'); }

        function wrap(i) {
            return ((i % slides.length) + slides.length) % slides.length;
        }

        function stopAutoplay() {
            if (autoTimer) {
                clearInterval(autoTimer);
                autoTimer = null;
            }
        }

        function startAutoplay() {
            stopAutoplay();
            if (!isPhotoMode || slides.length < 2 || stripOpen) return;
            autoTimer = setInterval(function () {
                current = wrap(current + 1);
                applyPhoto(true);
            }, AUTO_MS);
        }

        function openOverlay() {
            if (isPhotoMode || !overlay || !overlayImg || current < 0) return;
            const slide = slides[current];
            overlayImg.src = slide.url;
            overlayImg.alt = slide.caption || locationName;
            if (overlayBlur) overlayBlur.src = slide.url;
            if (overlay.parentElement !== document.body) {
                document.body.appendChild(overlay);
            }
            overlay.hidden = false;
            document.body.classList.add('pano-photo-open');
            const drawer = document.getElementById('commentsDrawer');
            if (drawer && drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                document.body.classList.remove('reviews-drawer-open');
            }
        }

        function closeOverlay() {
            if (!overlay) return;
            overlay.hidden = true;
            document.body.classList.remove('pano-photo-open');
        }

        function applyPhoto(fromAuto) {
            if (current < 0) return;
            const slide = slides[current];
            const img = mainImg();
            const blur = mainBlur();
            if (img) {
                img.src = slide.url;
                img.alt = slide.caption || locationName;
            }
            if (blur) blur.src = slide.url;
            if (!isPhotoMode) openOverlay();
            syncActive();
            if (stripOpen) scrollActiveIntoView();
            if (!fromAuto && isPhotoMode) startAutoplay();
        }

        function syncActive() {
            list.querySelectorAll('.viewer-photo-strip__item').forEach(function (el, i) {
                el.classList.toggle('is-active', current >= 0 && i === current);
            });
            if (btnPhotos) btnPhotos.classList.toggle('is-active', stripOpen);
        }

        function scrollActiveIntoView() {
            if (current < 0) return;
            const el = list.children[current];
            if (el && el.scrollIntoView) {
                el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        }

        function select(index) {
            current = wrap(index);
            applyPhoto(false);
        }

        function clearSelection() {
            if (isPhotoMode) return;
            current = -1;
            syncActive();
            closeOverlay();
        }

        function openStrip() {
            strip.hidden = false;
            stripOpen = true;
            document.body.classList.add('viewer-photo-strip-open');
            stopAutoplay();
            syncActive();
            if (current >= 0) scrollActiveIntoView();
            if (btnPhotos) btnPhotos.classList.add('is-active');
        }

        function closeStrip() {
            strip.hidden = true;
            stripOpen = false;
            document.body.classList.remove('viewer-photo-strip-open');
            if (btnPhotos) btnPhotos.classList.remove('is-active');
            if (!isPhotoMode) {
                closeOverlay();
                clearSelection();
            } else {
                startAutoplay();
            }
        }

        function toggleStrip() {
            if (stripOpen) closeStrip();
            else openStrip();
        }

        function captionFor(slide, i) {
            const c = String(slide.caption || '').trim();
            if (!c || c === locationName) return 'Ảnh ' + (i + 1);
            return c;
        }

        function buildList() {
            list.innerHTML = '';
            slides.forEach(function (slide, i) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'viewer-photo-strip__item';
                const label = captionFor(slide, i);
                btn.innerHTML =
                    '<span class="viewer-photo-strip__check"><i class="fa-solid fa-check"></i></span>' +
                    '<img src="' + slide.url + '" alt="' + label.replace(/"/g, '&quot;') + '" draggable="false">';
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    select(i);
                });
                list.appendChild(btn);
            });
            syncActive();
        }

        function goPrev(e) {
            e.preventDefault();
            e.stopPropagation();
            if (current < 0 && !isPhotoMode) return;
            select(current - 1);
        }
        function goNext(e) {
            e.preventDefault();
            e.stopPropagation();
            if (current < 0 && !isPhotoMode) return;
            select(current + 1);
        }

        buildList();

        if (isPhotoMode && slides.length > 0) {
            applyPhoto(true);
            startAutoplay();
        }

        ['photoViewerPrev', 'panoPhotoOverlayPrev'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', goPrev);
        });
        ['photoViewerNext', 'panoPhotoOverlayNext'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', goNext);
        });

        if (btnPhotos) btnPhotos.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleStrip();
        });
        if (btnCollapse) btnCollapse.addEventListener('click', function (e) {
            e.stopPropagation();
            closeStrip();
        });
        if (overlayClose) overlayClose.addEventListener('click', function (e) {
            e.stopPropagation();
            closeOverlay();
            clearSelection();
        });
        if (overlay) overlay.addEventListener('click', function (e) {
            if (e.target === overlay || e.target === overlayBlur) {
                closeOverlay();
                clearSelection();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeOverlay();
                closeStrip();
                return;
            }
            if (current < 0) return;
            if (isPhotoMode && !stripOpen) {
                if (e.key === 'ArrowLeft') select(current - 1);
                if (e.key === 'ArrowRight') select(current + 1);
                return;
            }
            const overlayVisible = overlay && !overlay.hidden;
            if (!stripOpen && !overlayVisible) return;
            if (e.key === 'ArrowLeft') select(current - 1);
            if (e.key === 'ArrowRight') select(current + 1);
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stopAutoplay();
            else if (isPhotoMode) startAutoplay();
        });

        window.ViewerPhotos = {
            select: select,
            openStrip: openStrip,
            closeStrip: closeStrip,
            toggleStrip: toggleStrip,
            get current() { return current; },
            get slides() { return slides; }
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
</script>
@endif
