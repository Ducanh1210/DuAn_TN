@extends('client.layouts.app')

@section('title', $event->name)

@section('content')
<div class="page-shell">
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}" style="color: #6482a6;">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.events.index') }}" style="color: #6482a6;">Sự kiện</a></li>
                <li class="breadcrumb-item active" style="color: #27272a;" aria-current="page">{{ Str::limit($event->name, 45) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8 pe-lg-4">
                <article>
                    <span class="section-label d-block mb-2">Sự kiện</span>

                    <h1 class="fw-semibold mb-3" style="color: #27272a; font-size: 1.6rem; line-height: 1.38; letter-spacing: -0.01em;">
                        {{ $event->name }}
                    </h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4 pb-3 border-bottom meta-text" style="border-color: #f4f4f5 !important;">
                        @if($event->start_time)
                            <span style="color: #1e3a5f; font-weight: 500;">
                                {{ $event->start_time->format('d/m/Y H:i') }}
                                @if($event->end_time)
                                    — {{ $event->end_time->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        @endif
                        @if($event->location_text)
                            <span>{{ $event->location_text }}</span>
                        @endif
                    </div>

                    @if($event->featured_image_url)
                        <div class="mb-4">
                            @include('client.partials.cover-image', [
                                'src' => $event->featured_image_url,
                                'alt' => $event->name,
                                'ratio' => '16/9',
                            ])
                        </div>
                    @endif

                    @if($event->description)
                        <div class="mb-4 p-3 rounded-2" style="background-color: #fafafa; border-left: 2.5px solid #1e3a5f; font-size: 0.975rem; font-weight: 500; color: #3f3f46; line-height: 1.6;">
                            {!! $event->description !!}
                        </div>
                    @endif

                    @if($event->program)
                        <div class="mb-4">
                            <h2 class="section-label mb-3">Chương trình</h2>
                            <div class="content-body" style="font-size: 1rem; line-height: 1.75; color: #3f3f46;">
                                {!! \App\Models\News::rewriteContentImageUrls($event->program) !!}
                            </div>
                        </div>
                    @endif

                    @if($event->location)
                        <div class="p-3 rounded-2" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="section-label mb-1">Địa điểm liên quan</div>
                            <a href="{{ url('/?location=' . $event->location->id) }}" style="color: #1e3a5f; font-weight: 500;">
                                {{ $event->location->name }}
                            </a>
                        </div>
                    @endif
                </article>
            </div>

            <div class="col-lg-4">
                <div class="position-sticky" style="top: 90px;">
                    <div class="pb-3 border-bottom" style="border-color: #e5e7eb !important;">
                        <div class="mb-3 pb-2 border-bottom" style="border-color: #e5e7eb !important;">
                            <span class="section-label">Sự kiện khác</span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @forelse($relatedEvents as $item)
                                <article>
                                    <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none editorial-link d-flex gap-3 align-items-start">
                                        <div style="width: 85px; flex-shrink: 0;">
                                            @include('client.partials.cover-image', [
                                                'src' => $item->featured_image_url,
                                                'alt' => $item->name,
                                                'ratio' => '4/3',
                                            ])
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <h6 class="editorial-link__title fw-normal mb-1" style="color: #27272a; font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $item->name }}
                                            </h6>
                                            <div class="meta-text">
                                                @if($item->start_time)
                                                    {{ $item->start_time->format('d/m/Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @empty
                                <p class="meta-text mb-0">Không có sự kiện nào khác.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-body img { max-width: 100%; height: auto; border-radius: 8px; margin: 12px 0; }
</style>
@endsection
