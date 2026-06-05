@extends('client.layouts.app')

@section('title', $event->name)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.events.index') }}">Sự kiện</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($event->name, 50) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm mb-4">
                @php
                    $now = \Carbon\Carbon::now();
                    $isHappening = $event->start_time <= $now && $event->end_time >= $now;
                    $isUpcoming = $event->start_time > $now;
                @endphp
                
                @if($isHappening)
                    <span class="badge bg-danger mb-3 px-3 py-2 fs-6"><i class="fa-solid fa-fire me-1"></i> Đang diễn ra</span>
                @elseif($isUpcoming)
                    <span class="badge bg-success mb-3 px-3 py-2 fs-6"><i class="fa-solid fa-clock me-1"></i> Sắp diễn ra</span>
                @else
                    <span class="badge bg-secondary mb-3 px-3 py-2 fs-6">Đã kết thúc</span>
                @endif
                
                <h1 class="fw-bold mb-4 text-danger">{{ $event->name }}</h1>
                
                @if($event->featured_image)
                    <img src="{{ str_starts_with($event->featured_image, 'http') ? $event->featured_image : asset('storage/' . ltrim($event->featured_image, '/')) }}" class="img-fluid rounded-3 mb-4 w-100" alt="{{ $event->name }}" style="max-height: 500px; object-fit: cover;">
                @endif

                <div class="alert alert-light border shadow-sm mb-4">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex">
                            <div class="text-danger fs-3 me-3"><i class="fa-solid fa-calendar-check"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Thời gian</h6>
                                <p class="mb-0 small">{{ $event->start_time ? $event->start_time->format('d/m/Y H:i') : '' }} <br>đến {{ $event->end_time ? $event->end_time->format('d/m/Y H:i') : '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex border-start">
                            <div class="text-primary fs-3 me-3"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Địa điểm</h6>
                                <p class="mb-0 small">{{ $event->location_text }}</p>
                                @if($event->location_id)
                                    <a href="{{ url('/?location=' . $event->location_id) }}" class="small text-decoration-none">Xem trên bản đồ</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($event->description)
                    <h4 class="fw-bold mb-3 border-start border-4 border-danger ps-3">Thông tin chi tiết</h4>
                    <div class="content-body mb-4" style="line-height: 1.8; font-size: 16px;">
                        {!! $event->description !!}
                    </div>
                @endif

                @if($event->program)
                    <h4 class="fw-bold mb-3 border-start border-4 border-primary ps-3">Nội dung chương trình</h4>
                    <div class="content-body p-4 bg-light rounded-3" style="line-height: 1.8; font-size: 16px;">
                        {!! $event->program !!}
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
                                <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/150' }}" alt="{{ $item->name }}" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <h6 class="text-dark fw-bold mb-1 text-hover-danger" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $item->name }}</h6>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ $item->start_time ? $item->start_time->format('d/m/Y') : '' }}</small>
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
