@extends('client.layouts.app')

@section('title', 'Tin Tức & Cẩm Nang Du Lịch Hà Nam')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb or simple Title -->
    <div class="mb-4 pb-2 border-bottom">
        <h2 class="fw-bold text-uppercase" style="color: #2c3e50;">TIN TỨC - SỰ KIỆN</h2>
    </div>

    <div class="row">
        <!-- Left Column: Main News List -->
        <div class="col-lg-8 pe-lg-4">
            <div class="d-flex flex-column gap-4">
                @forelse($newsList as $item)
                    <article class="news-list-item pb-4 border-bottom">
                        <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none">
                            <div class="row g-3 align-items-start">
                                <!-- Thumbnail -->
                                <div class="col-sm-4 col-md-5">
                                    <div class="img-wrapper rounded overflow-hidden shadow-sm">
                                        <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/600x400?text=No+Image' }}" alt="{{ $item->title }}" class="img-fluid w-100" style="aspect-ratio: 4/3; object-fit: cover; transition: transform 0.3s ease;">
                                    </div>
                                </div>
                                <!-- Content -->
                                <div class="col-sm-8 col-md-7">
                                    <h4 class="news-title fw-bold mb-2" style="color: #1a1a1a; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="news-excerpt text-muted mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.5;">
                                        {{ strip_tags($item->summary ?? $item->content) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="text-center py-5">
                        <p class="text-muted">Chưa có bài viết nào.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-4 custom-pagination">
                {{ $newsList->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4 mt-5 mt-lg-0">
            <div class="sidebar position-sticky" style="top: 100px;">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-danger border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #c0392b;">TIN NỔI BẬT</h5>
                </div>
                
                <div class="d-flex flex-column gap-3 mb-5">
                    @forelse($popularNews as $item)
                        <article class="sidebar-item">
                            <a href="{{ route('client.news.show', $item->slug) }}" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->title }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->title }}
                                    </h6>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="text-muted small">Chưa có tin nổi bật.</p>
                    @endforelse
                </div>

                <!-- EVENTS BLOCK IN SIDEBAR -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-success border-2">
                    <h5 class="fw-bold mb-0 text-uppercase" style="color: #27ae60;">SỰ KIỆN SẮP TỚI</h5>
                    <a href="{{ route('client.events.index') }}" class="small text-success text-decoration-none fw-semibold">Xem thêm <i class="fa-solid fa-angle-right"></i></a>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($upcomingEvents as $item)
                        <article class="sidebar-item">
                            <a href="{{ route('client.events.show', $item->slug) }}" class="text-decoration-none d-flex gap-3 align-items-start">
                                <div class="sidebar-img-wrapper rounded overflow-hidden flex-shrink-0" style="width: 120px; aspect-ratio: 4/3;">
                                    <img src="{{ $item->featured_image ? (str_starts_with($item->featured_image, 'http') ? $item->featured_image : asset('storage/' . ltrim($item->featured_image, '/'))) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $item->title }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="sidebar-content flex-grow-1">
                                    <h6 class="sidebar-title fw-bold text-dark mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: 0.95rem; line-height: 1.4; transition: color 0.2s;">
                                        {{ $item->title }}
                                    </h6>
                                    <small class="text-success fw-semibold"><i class="fa-regular fa-calendar me-1"></i> {{ $item->published_at ? $item->published_at->format('d/m/Y') : '' }}</small>
                                </div>
                            </a>
                        </article>
                    @empty
                        <p class="text-muted small">Không có sự kiện nào sắp tới.</p>
                    @endforelse
                </div>

                <!-- Mission Board -->
                <div class="mt-5 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-primary border-2">
                        <h5 class="fw-bold mb-0 text-uppercase" style="color: var(--primary);"><i class="fa-solid fa-trophy text-warning me-2"></i>Nhiệm vụ tích điểm</h5>
                    </div>
                    
                    <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid rgba(0, 102, 255, 0.08) !important;">
                        <div class="card-body p-3">
                            @auth
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-coins fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted fw-semibold" style="font-size: 0.75rem;">Số điểm hiện tại</div>
                                        <div class="fw-bold text-dark fs-6" id="sidebarMissionPoints">{{ Auth::user()->points }} điểm</div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    <!-- Daily login -->
                                    <div class="mission-item p-2 rounded-3 bg-white border border-light shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold small text-dark" style="font-size: 0.8rem;"><i class="fa-solid fa-calendar-check text-success me-1"></i> Điểm danh hằng ngày</span>
                                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.7rem;">+10 điểm</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.72rem;">Nhận điểm mỗi ngày</span>
                                            @if(Auth::user()->last_daily_bonus_at && \Carbon\Carbon::parse(Auth::user()->last_daily_bonus_at)->isToday())
                                                <button class="btn btn-sm btn-success border-0 px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.7rem;" disabled><i class="fa-solid fa-check"></i> Đã nhận</button>
                                            @else
                                                <button type="button" id="claimDailyBtn" class="btn btn-sm btn-primary border-0 px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.7rem;">Nhận ngay</button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Online duration -->
                                    <div class="mission-item p-2 rounded-3 bg-white border border-light shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold small text-dark" style="font-size: 0.8rem;"><i class="fa-solid fa-clock text-primary me-1"></i> Trực tuyến tích lũy</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem;">+1 điểm/phút</span>
                                        </div>
                                        <div class="mb-1">
                                            @php
                                                $sessionPoints = \App\Models\PointTransaction::where('user_id', Auth::id())
                                                    ->where('action', 'active_session')
                                                    ->whereDate('created_at', \Carbon\Carbon::today())
                                                    ->sum('amount');
                                            @endphp
                                            <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.7rem;">
                                                <span>Thời gian tích lũy hôm nay:</span>
                                                <span class="fw-semibold text-primary" id="missionSessionProgressText">{{ $sessionPoints }}/60 phút</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div id="missionSessionProgressBar" class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, ($sessionPoints / 60) * 100) }}%" aria-valuenow="{{ $sessionPoints }}" aria-valuemin="0" aria-valuemax="60"></div>
                                            </div>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.68rem;">
                                            <i class="fa-solid fa-spinner fa-spin text-primary me-1"></i> Tự động tích lũy khi hoạt động.
                                        </div>
                                    </div>

                                    <!-- Write comments -->
                                    <div class="mission-item p-2 rounded-3 bg-white border border-light shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold small text-dark" style="font-size: 0.8rem;"><i class="fa-solid fa-comment text-info me-1"></i> Bình luận địa điểm</span>
                                            <span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.7rem;">+5 điểm</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.72rem;">Viết bình luận tại bất kỳ địa điểm nào</span>
                                            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-primary px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.7rem; border-color: rgba(0, 102, 255, 0.2);">Làm nhiệm vụ</a>
                                        </div>
                                    </div>

                                    <!-- Favorites -->
                                    <div class="mission-item p-2 rounded-3 bg-white border border-light shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold small text-dark" style="font-size: 0.8rem;"><i class="fa-solid fa-heart text-danger me-1"></i> Yêu thích địa điểm</span>
                                            <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.7rem;">+2 điểm</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted" style="font-size: 0.72rem;">Lưu địa điểm yêu thích trên bản đồ</span>
                                            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-primary px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.7rem; border-color: rgba(0, 102, 255, 0.2);">Làm nhiệm vụ</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 px-2">
                                    <div class="rounded-circle bg-light p-3 mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fa-solid fa-lock text-muted fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Đăng nhập để nhận nhiệm vụ</h6>
                                    <p class="text-muted mb-3" style="font-size: 0.75rem;">Tham gia làm nhiệm vụ tích điểm đổi quà hấp dẫn và nhiều đặc quyền khác!</p>
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">Đăng nhập ngay</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover Effects */
    .news-list-item:hover .news-title { color: #27ae60 !important; }
    .news-list-item:hover .img-wrapper img { transform: scale(1.05); }
    
    .sidebar-item:hover .sidebar-title { color: #2980b9 !important; }

    /* Mission Item Styling */
    .mission-item {
        transition: all 0.3s ease;
    }
    .mission-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Pagination */
    .custom-pagination .page-link {
        color: #2c3e50;
        border: none;
        padding: 8px 12px;
        margin: 0 2px;
        font-weight: 600;
        background: transparent;
    }
    .custom-pagination .page-item.active .page-link {
        background-color: #2c3e50;
        color: white;
        border-radius: 4px;
    }
</style>

@auth
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const claimDailyBtn = document.getElementById("claimDailyBtn");
        if (claimDailyBtn) {
            claimDailyBtn.addEventListener("click", function() {
                claimDailyBtn.disabled = true;
                claimDailyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang nhận...';

                fetch("{{ route('client.profile.claim_daily') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        claimDailyBtn.className = "btn btn-sm btn-success border-0 px-3 py-1 rounded-pill fw-semibold";
                        claimDailyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Đã nhận';
                        
                        // Update points displays
                        const headerPoints = document.getElementById("navbarUserPoints");
                        if (headerPoints) {
                            headerPoints.textContent = data.points + " điểm";
                        }
                        const sidebarPoints = document.getElementById("sidebarMissionPoints");
                        if (sidebarPoints) {
                            sidebarPoints.textContent = data.points + " điểm";
                        }
                        
                        alert(data.message);
                    } else {
                        claimDailyBtn.disabled = false;
                        claimDailyBtn.innerHTML = "Nhận ngay";
                        alert(data.message);
                    }
                })
                .catch(error => {
                    claimDailyBtn.disabled = false;
                    claimDailyBtn.innerHTML = "Nhận ngay";
                    console.error("Error claiming daily bonus:", error);
                    alert("Có lỗi xảy ra, vui lòng thử lại sau.");
                });
            });
        }
    });
</script>
@endauth
@endsection
