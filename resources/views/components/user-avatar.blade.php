@props(['user' => null, 'size' => 36, 'class' => ''])

@php
    $sizePx = is_numeric($size) ? $size . 'px' : $size;
    $displayName = $user ? ($user->display_name ?? $user->username) : 'Guest';
    $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0072FF&color=fff';
    $avatarUrl = $user ? $user->avatar_formatted_url : $fallbackUrl;
    
    $frameClass = '';
    if ($user && $user->equippedFrame) {
        $frameClass = $user->equippedFrame->css_style;
    }
@endphp

<div class="avatar-frame-wrapper {{ $frameClass }} {{ $class }}" style="width: {{ $sizePx }}; height: {{ $sizePx }}; flex-shrink: 0;" title="{{ $displayName }}">
    <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" onerror="this.onerror=null; this.src='{{ $fallbackUrl }}';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
</div>
