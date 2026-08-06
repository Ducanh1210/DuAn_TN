@props(['src' => null, 'alt' => '', 'class' => '', 'ratio' => '16/9'])
<div @class(['cover-image', $class]) style="aspect-ratio: {{ $ratio }};">
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" class="cover-image__img">
    @else
        <div class="cover-image__placeholder" aria-hidden="true">
            <span>Ninh Bình</span>
        </div>
    @endif
</div>
