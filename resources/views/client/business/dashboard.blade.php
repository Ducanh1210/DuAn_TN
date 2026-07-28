<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Doanh Nghiệp - {{ $businessProfile->business_name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --primary: #1e3a5f;
            --primary-hover: #2b4c7e;
            --bg-body: #f8fafc;
            --text-main: #0f2442;
            --text-sub: #64748b;
            --border-color: #cbdbe8;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 0.875rem;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header Navigation */
        .biz-navbar {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back-link {
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s ease;
        }
        .btn-back-link:hover {
            color: var(--primary);
        }

        /* Dashboard Hero Section */
        .biz-hero {
            background: linear-gradient(135deg, #1e3a5f 0%, #2b4c7e 100%);
            color: white;
            padding: 32px 0;
            margin-bottom: 24px;
            border-bottom: 1px solid #cbdbe8;
        }

        .metric-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.06);
        }
        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background-color: #f1f5f9;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-title-line {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 6px;
            display: inline-block;
            margin-bottom: 20px;
        }

        /* Custom Tabs */
        .biz-nav-tabs .nav-link {
            color: #475569;
            font-weight: 500;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 10px 16px;
            border-radius: 0;
            transition: all 0.15s ease;
        }
        .biz-nav-tabs .nav-link:hover {
            color: var(--primary);
        }
        .biz-nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: transparent;
            font-weight: 600;
        }

        #dashboardMap {
            height: 320px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }
        .photo-grid-item {
            aspect-ratio: 4/3;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: #f8fafc;
            position: relative;
        }
        .photo-grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease;
        }
        .photo-grid-item:hover img {
            transform: scale(1.05);
        }

        .review-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
            background: #ffffff;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="biz-navbar">
    <a href="{{ route('client.profile') }}" class="btn-back-link">
        <i class="fa-solid fa-chevron-left"></i> Quay lại trang cá nhân
    </a>
    <div class="d-flex align-items-center gap-2">
        <span class="fw-bold text-primary" style="font-size: 1.05rem;">Portal Doanh Nghiệp</span>
        <span class="badge bg-success rounded-pill px-2.5 py-1">Đã kích hoạt</span>
    </div>
    <div>
        @if($location)
            <a href="{{ route('client.locations.360', $location->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Xem trang bản đồ
            </a>
        @endif
    </div>
</div>

<!-- Hero Banner -->
<div class="biz-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary fw-semibold mb-2">
                    {{ $businessProfile->category ? $businessProfile->category->name : 'Doanh nghiệp' }}
                </span>
                <h2 class="fw-bold text-white mb-1" style="font-size: 1.6rem;">{{ $businessProfile->business_name }}</h2>
                <div class="text-white-50 small">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-light text-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Chỉnh sửa thông tin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="container pb-5">
    <!-- Alert Flash -->
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-4 small" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Metric Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <div class="text-secondary small">Lượt xem địa điểm</div>
                    <div class="fw-bold fs-4 text-dark">{{ number_format($viewsCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon" style="color: #f59e0b; background-color: #fffbe6;">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div>
                    <div class="text-secondary small">Đánh giá trung bình</div>
                    <div class="fw-bold fs-4 text-dark">{{ number_format($averageRating, 1) }} / 5</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon" style="color: #ef4444; background-color: #fef2f2;">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div>
                    <div class="text-secondary small">Lượt lưu yêu thích</div>
                    <div class="fw-bold fs-4 text-dark">{{ number_format($favoritesCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon" style="color: #0284c7; background-color: #f0f9ff;">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div>
                    <div class="text-secondary small">Tổng nhận xét</div>
                    <div class="fw-bold fs-4 text-dark">{{ number_format($comments->count()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="content-panel p-0 overflow-hidden mb-4">
        <ul class="nav nav-tabs biz-nav-tabs px-3 pt-2 bg-light border-bottom" id="bizTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
                    <i class="fa-solid fa-chart-pie me-1"></i> Tổng quan & Bản đồ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-gallery-btn" data-bs-toggle="tab" data-bs-target="#tab-gallery" type="button" role="tab">
                    <i class="fa-solid fa-images me-1"></i> Hình ảnh & Thực đơn
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-reviews-btn" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button" role="tab">
                    <i class="fa-solid fa-star me-1"></i> Đánh giá khách hàng ({{ $comments->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content p-4" id="bizTabsContent">
            <!-- Tab 1: Overview & Map -->
            <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="section-title-line">Thông tin doanh nghiệp</div>
                        <div class="mb-3">
                            <div class="text-secondary small">Số điện thoại liên hệ</div>
                            <div class="fw-semibold fs-6"><i class="fa-solid fa-phone me-2 text-primary"></i>{{ $businessProfile->phone }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small">Trang web (Website)</div>
                            <div class="fw-semibold fs-6">
                                @if($businessProfile->website)
                                    <a href="{{ $businessProfile->website }}" target="_blank" class="text-primary text-decoration-none">
                                        <i class="fa-solid fa-globe me-2"></i>{{ $businessProfile->website }}
                                    </a>
                                @else
                                    <span class="text-muted fw-normal">Chưa cập nhật</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small">Địa chỉ hiển thị</div>
                            <div class="fw-semibold fs-6">
                                <i class="fa-solid fa-map-pin me-2 text-danger"></i>
                                {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-secondary small">Mô tả doanh nghiệp</div>
                            <div class="p-3 bg-light rounded-3 text-secondary mt-1" style="white-space: pre-line;">
                                {{ $businessProfile->description ?? 'Chưa có mô tả nào được thêm.' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="section-title-line">Vị trí địa điểm trên bản đồ</div>
                        <div id="dashboardMap"></div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Gallery Management -->
            <div class="tab-pane fade" id="tab-gallery" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="section-title-line mb-0">Thư viện ảnh cửa hàng & Thực đơn</div>
                    <button type="button" class="btn btn-sm btn-primary rounded-2 px-3" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Tải ảnh mới lên
                    </button>
                </div>

                <!-- Storefront Photos -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-store me-2 text-primary"></i>Ảnh mặt tiền cửa hàng ({{ count($businessProfile->storefront_photos ?? []) }})</h6>
                    @if(!empty($businessProfile->storefront_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->storefront_photos as $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Mặt tiền">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-light rounded-3 text-center text-muted small">Chưa có ảnh mặt tiền nào.</div>
                    @endif
                </div>

                <!-- Menu Photos -->
                <div>
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-utensils me-2 text-primary"></i>Ảnh thực đơn & Bảng giá ({{ count($businessProfile->menu_photos ?? []) }})</h6>
                    @if(!empty($businessProfile->menu_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->menu_photos as $photo)
                                <div class="photo-grid-item">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Thực đơn">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-light rounded-3 text-center text-muted small">Chưa có ảnh thực đơn nào.</div>
                    @endif
                </div>
            </div>

            <!-- Tab 3: Customer Reviews -->
            <div class="tab-pane fade" id="tab-reviews" role="tabpanel">
                <div class="section-title-line">Nhận xét & Đánh giá từ khách hàng</div>

                @forelse($comments as $comment)
                    <div class="review-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <x-user-avatar :user="$comment->user" size="32" />
                                <div>
                                    <div class="fw-semibold text-dark">{{ $comment->user->display_name ?? $comment->user->username }}</div>
                                    <div class="text-muted" style="font-size: 0.725rem;">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="text-warning fw-bold small">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($comment->rating ?? 5))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <p class="text-secondary small mb-0 mt-2">{{ $comment->content }}</p>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-comments fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0 small">Chưa có nhận xét nào từ khách hàng.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Info -->
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('business.update_info') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Cập nhật thông tin doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên doanh nghiệp *</label>
                        <input type="text" class="form-control" name="business_name" value="{{ $businessProfile->business_name }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại liên hệ *</label>
                            <input type="text" class="form-control" name="phone" value="{{ $businessProfile->phone }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trang web (Website)</label>
                            <input type="url" class="form-control" name="website" value="{{ $businessProfile->website }}" placeholder="https://">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đường / Số nhà *</label>
                            <input type="text" class="form-control" name="address_street" value="{{ $businessProfile->address_street }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thành phố / Huyện *</label>
                            <input type="text" class="form-control" name="address_city" value="{{ $businessProfile->address_city }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả doanh nghiệp</label>
                        <textarea class="form-control" name="description" rows="4">{{ $businessProfile->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Upload Photo -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('business.upload_photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">Tải ảnh mới lên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại hình ảnh *</label>
                        <select class="form-select" name="type" required>
                            <option value="storefront">Mặt tiền cửa hàng</option>
                            <option value="menu">Thực đơn / Bảng giá / Dịch vụ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn hình ảnh *</label>
                        <input type="file" class="form-control" name="photo" accept="image/*" required>
                        <div class="form-text small">Dung lượng tối đa 5MB. Định dạng PNG, JPG, JPEG, WEBP.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Tải lên</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap & Leaflet JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let dashboardMap = null;

        function initDashboardMap() {
            const mapEl = document.getElementById('dashboardMap');
            if (!mapEl || dashboardMap) return;

            const lat = parseFloat("{{ $businessProfile->lat }}");
            const lng = parseFloat("{{ $businessProfile->lng }}");

            if (isNaN(lat) || isNaN(lng)) return;

            dashboardMap = L.map('dashboardMap', {
                zoomControl: true,
                attributionControl: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(dashboardMap);

            // Fetch GeoJSON boundary
            fetch('{{ asset('geo/ha-nam-old.geojson') }}')
                .then(res => res.json())
                .then(data => {
                    L.geoJSON(data, {
                        style: {
                            color: '#7ba7d4',
                            weight: 2,
                            opacity: 0.55,
                            fillColor: '#f8fafc',
                            fillOpacity: 0.04
                        }
                    }).addTo(dashboardMap);
                })
                .catch(err => console.error(err));

            const marker = L.marker([lat, lng]).addTo(dashboardMap);
            marker.bindPopup(`
                <div style="font-family: inherit; font-size: 0.85rem;">
                    <strong style="color: #1e3a5f;">{{ $businessProfile->business_name }}</strong><br>
                    <span style="color: #64748b;">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}</span>
                </div>
            `).openPopup();
        }

        initDashboardMap();

        // Invalidate map size on tab click
        const tabOverviewBtn = document.getElementById('tab-overview-btn');
        if (tabOverviewBtn) {
            tabOverviewBtn.addEventListener('shown.bs.tab', function() {
                if (dashboardMap) setTimeout(() => dashboardMap.invalidateSize(), 200);
            });
        }
    });
</script>

</body>
</html>
