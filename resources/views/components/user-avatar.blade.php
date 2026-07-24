@props(['user' => null, 'size' => 36, 'class' => ''])

@php
    $sizePx = is_numeric($size) ? $size . 'px' : $size;
    $displayName = $user ? ($user->display_name ?? $user->username) : 'Guest';
    $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=1e3a5f&color=fff';
    $avatarUrl = $user ? $user->avatar_formatted_url : $fallbackUrl;
    
    $frameClass = '';
    $frameImageUrl = null;
    if ($user && $user->equippedFrame) {
        $frameClass = $user->equippedFrame->css_style;
        $frameImageUrl = $user->equippedFrame->image_url;
    }
@endphp

<div class="avatar-frame-wrapper {{ $frameImageUrl ? 'has-png-frame' : $frameClass }} {{ $class }}" style="width: {{ $sizePx }}; height: {{ $sizePx }}; flex-shrink: 0;" title="{{ $displayName }}">
    <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" onerror="this.onerror=null; this.src='{{ $fallbackUrl }}';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
    @if($frameImageUrl)
        <img src="{{ asset($frameImageUrl) }}" alt="Frame" class="avatar-frame-png-overlay">
    @endif
</div>
