@extends('client.layouts.app')

@section('title', 'Địa Điểm Yêu Thích')

@section('content')
<div class="container py-3 py-md-4">
    <div class="mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-uppercase mb-0" style="color: #2c3e50;">Địa Điểm Yêu Thích Của Tôi</h2>
    </div>

    <div class="row g-4">
        @forelse($favorites as $fav)
            @php
                $loc = $fav->location;
            @endphp
            @if(!$loc)
                @continue
            @endif
            <div class="col-md-4 col-sm-6 fav-card-wrapper" id="fav-{{ $fav->id }}">
                <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden fav-card">
                    <a href="{{ route('client.locations.360', $loc->slug) }}" class="text-decoration-none">
                        <div class="position-relative" style="aspect-ratio: 4/3;">
                            <img src="{{ $loc->thumbnail_url ? (str_starts_with($loc->thumbnail_url, 'http') ? $loc->thumbnail_url : asset('storage/' . ltrim($loc->thumbnail_url, '/'))) : 'https://via.placeholder.com/400x300?text=No+Image' }}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $loc->name }}">
                            @if($loc->category)
                                <span class="position-absolute top-0 start-0 m-2 badge" style="background-color: {{ $loc->category->icon_color ?? '#primary' }}">{{ $loc->category->name }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark text-truncate">{{ $loc->name }}</h5>
                            <p class="card-text text-muted small mb-0 text-truncate"><i class="fa-solid fa-location-dot me-1"></i> {{ $loc->address }}</p>
                        </div>
                    </a>
                    
                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle btn-remove-fav" data-id="{{ $loc->id }}" title="Xóa khỏi yêu thích" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                        <i class="fa-solid fa-heart-crack"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-regular fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                <h4 class="text-muted">Bạn chưa lưu địa điểm nào.</h4>
                <a href="{{ url('/') }}" class="btn btn-primary mt-3">Khám phá ngay</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4 custom-pagination">
        {{ $favorites->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    .fav-card { transition: transform 0.2s, box-shadow 0.2s; }
    .fav-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .btn-remove-fav { opacity: 0; transition: opacity 0.2s, background 0.2s; }
    .fav-card:hover .btn-remove-fav { opacity: 1; }
    .btn-remove-fav:hover { background: #c0392b !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const removeBtns = document.querySelectorAll('.btn-remove-fav');
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!confirm('Bạn có chắc chắn muốn xóa địa điểm này khỏi danh sách yêu thích?')) return;
            
            const locId = this.dataset.id;
            const cardWrapper = this.closest('.fav-card-wrapper');
            
            fetch(`/locations/${locId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'removed') {
                    cardWrapper.style.transition = 'all 0.3s';
                    cardWrapper.style.opacity = '0';
                    cardWrapper.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        cardWrapper.remove();
                        // check if empty
                        if (document.querySelectorAll('.fav-card-wrapper').length === 0) {
                            location.reload(); // reload to show empty state
                        }
                    }, 300);
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endsection
