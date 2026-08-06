<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị Doanh Nghiệp - {{ $businessProfile->business_name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome (Minimalist Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            /* Color Palette: Misty Ice-Blue & Slate Ink */
            --primary: #1e3a5f;
            --primary-hover: #2b4c7e;
            --bg-auth-form: #f1f5f9;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-heading: #1e3a5f;
            --text-main: #3b5980;
            --text-sub: #6482a6;
            --border-color: #cbdbe8;
            --border-light: #e5e7eb;
        }

        body {
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 0.875rem;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header Navigation */
        .biz-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .btn-back-link {
            color: var(--text-sub);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: color 0.2s ease;
        }
        .btn-back-link:hover {
            color: var(--primary);
        }

        .badge-status-active {
            background-color: var(--bg-auth-form);
            color: var(--text-heading);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            padding: 3px 10px;
        }

        .btn-action-map {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 500;
            padding: 6px 14px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action-map:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* Hero Banner */
        .biz-hero {
            background: var(--primary);
            color: #ffffff;
            padding: 28px 0;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .biz-category-tag {
            background: rgba(241, 245, 249, 0.15);
            color: #f1f5f9;
            border: 1px solid rgba(203, 219, 232, 0.3);
            font-size: 0.75rem;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .biz-title {
            font-weight: 600;
            font-size: 1.6rem;
            color: #ffffff;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        .biz-subtitle {
            color: var(--border-color);
            font-size: 0.875rem;
            font-weight: 400;
        }

        .btn-edit-hero {
            background: #ffffff;
            color: var(--primary);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 8px 16px;
            transition: background 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-edit-hero:hover {
            background: var(--bg-auth-form);
            color: var(--primary-hover);
        }

        /* Metric Cards */
        .metric-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: border-color 0.2s ease;
        }
        .metric-card:hover {
            border-color: var(--primary);
        }
        .metric-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            background-color: var(--bg-auth-form);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-sub);
            font-weight: 400;
            margin-bottom: 2px;
        }

        .metric-value {
            font-size: 1.35rem;
            color: var(--text-heading);
            font-weight: 600;
            line-height: 1.2;
        }

        /* Content Panel & Nav Tabs */
        .content-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .biz-nav-tabs {
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 0 16px;
        }
        .biz-nav-tabs .nav-link {
            color: var(--text-sub);
            font-weight: 500;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 12px 18px;
            border-radius: 0;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            background: transparent;
        }
        .biz-nav-tabs .nav-link:hover {
            color: var(--primary);
        }
        .biz-nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: #ffffff;
            font-weight: 600;
        }

        .section-header-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 18px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border-color);
        }

        .info-card-item {
            padding: 12px 14px;
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .info-label {
            font-size: 0.775rem;
            color: var(--text-sub);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 0.9rem;
            color: var(--text-heading);
            font-weight: 500;
        }

        .description-box {
            background: var(--bg-auth-form);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
            color: var(--text-main);
            font-size: 0.875rem;
            line-height: 1.65;
            white-space: pre-line;
        }

        #dashboardMap {
            height: 350px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        /* Gallery Grid */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
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
            transform: scale(1.04);
        }

        .empty-photo-box {
            background: #f8fafc;
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            padding: 24px 16px;
            text-align: center;
            color: var(--text-sub);
            font-size: 0.85rem;
        }

        /* Review Cards */
        .review-card {
            border: 1px solid var(--border-light);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            background: #ffffff;
        }

        /* Primary Button Standard (DESIGN_GUIDE rule: #1e3a5f, Hover #2b4c7e, radius 8px) */
        .btn-primary-custom {
            background-color: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            font-weight: 500;
            font-size: 0.85rem;
            border-radius: 8px;
            padding: 8px 18px;
            transition: background-color 0.2s ease;
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
        }

        /* Form Customization */
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.875rem;
            color: var(--text-heading);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
        }
        .form-label {
            color: var(--text-heading);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="biz-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('client.profile') }}" class="btn-back-link">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại trang cá nhân
            </a>
            <div class="d-flex align-items-center gap-2">
                <span style="color: var(--text-heading); font-weight: 600; font-size: 1.05rem;">Portal Doanh Nghiệp</span>
                <span class="badge-status-active">Đã kích hoạt</span>
            </div>
            <div>
                @if($location)
                    <a href="{{ route('client.locations.360', $location->slug) }}" target="_blank" class="btn-action-map">
                        Xem trên bản đồ <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 0.75rem;"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Hero Banner -->
<div class="biz-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="biz-category-tag">
                    {{ $businessProfile->category ? $businessProfile->category->name : 'Doanh nghiệp' }}
                </span>
                <h1 class="biz-title">{{ $businessProfile->business_name }}</h1>
                <div class="biz-subtitle">
                    {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                </div>
            </div>
            <div>
                <button type="button" class="btn-edit-hero" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                    Chỉnh sửa thông tin
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="container pb-5">
    <!-- Alert Flash -->
    @if(session('success'))
        <div class="alert border-0 rounded-2 py-2.5 px-3 mb-4 small" role="alert" style="background-color: var(--bg-auth-form); color: var(--text-heading); border: 1px solid var(--border-color) !important;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Metric Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon-box">
                    <i class="fa-regular fa-eye"></i>
                </div>
                <div>
                    <div class="metric-label">Lượt xem địa điểm</div>
                    <div class="metric-value">{{ number_format($viewsCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon-box" style="color: #d97706;">
                    <i class="fa-regular fa-star"></i>
                </div>
                <div>
                    <div class="metric-label">Đánh giá trung bình</div>
                    <div class="metric-value">{{ number_format($averageRating, 1) }} <span style="font-size: 0.85rem; color: var(--text-sub); font-weight: 400;">/ 5</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon-box" style="color: #e11d48;">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <div>
                    <div class="metric-label">Lượt lưu yêu thích</div>
                    <div class="metric-value">{{ number_format($favoritesCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-icon-box">
                    <i class="fa-regular fa-comment"></i>
                </div>
                <div>
                    <div class="metric-label">Tổng nhận xét</div>
                    <div class="metric-value">{{ number_format($comments->count()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs & Content Panel -->
    <div class="content-panel mb-4">
        <ul class="nav nav-tabs biz-nav-tabs" id="bizTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">
                    Tổng quan & Bản đồ
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-gallery-btn" data-bs-toggle="tab" data-bs-target="#tab-gallery" type="button" role="tab">
                    Hình ảnh & Thực đơn
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-reviews-btn" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button" role="tab">
                    Đánh giá khách hàng ({{ $comments->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content p-4" id="bizTabsContent">
            <!-- Tab 1: Overview & Map -->
            <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="section-header-title">Thông tin doanh nghiệp</div>
                        
                        <div class="info-card-item">
                            <div class="info-label">Số điện thoại liên hệ</div>
                            <div class="info-value">{{ $businessProfile->phone }}</div>
                        </div>

                        <div class="info-card-item">
                            <div class="info-label">Trang web (Website)</div>
                            <div class="info-value">
                                @if($businessProfile->website)
                                    <a href="{{ $businessProfile->website }}" target="_blank" style="color: var(--primary); text-decoration: none;">
                                        {{ $businessProfile->website }}
                                    </a>
                                @else
                                    <span class="text-muted font-normal" style="font-weight: 400;">Chưa cập nhật</span>
                                @endif
                            </div>
                        </div>

                        <div class="info-card-item">
                            <div class="info-label">Địa chỉ hiển thị</div>
                            <div class="info-value">
                                {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="info-label mb-2">Mô tả doanh nghiệp</div>
                            <div class="description-box">
                                {{ $businessProfile->description ?? 'Chưa có mô tả nào được thêm.' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="section-header-title">Vị trí địa điểm trên bản đồ</div>
                        <div id="dashboardMap"></div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Gallery Management -->
            <div class="tab-pane fade" id="tab-gallery" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div class="section-header-title mb-0 border-0 p-0">Thư viện ảnh cửa hàng & Thực đơn</div>
                    <button type="button" class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                        Tải ảnh mới lên
                    </button>
                </div>

                <!-- Storefront Photos -->
                <div class="mb-4">
                    <h6 class="fw-semibold text-dark mb-3" style="font-size: 0.9rem; color: var(--text-heading);">
                        Ảnh mặt tiền cửa hàng ({{ count($businessProfile->storefront_photos ?? []) }})
                    </h6>
                    @if(!empty($businessProfile->storefront_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->storefront_photos as $index => $photo)
                                <div class="photo-grid-item position-relative">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Mặt tiền">
                                    </a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="storefront">
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-light border-0 py-0 px-1" style="font-size: 0.7rem;">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-photo-box">
                            Chưa có ảnh mặt tiền nào được tải lên.
                        </div>
                    @endif
                </div>

                <!-- Menu Photos -->
                <div>
                    <h6 class="fw-semibold text-dark mb-3" style="font-size: 0.9rem; color: var(--text-heading);">
                        Ảnh thực đơn & Bảng giá ({{ count($businessProfile->menu_photos ?? []) }})
                    </h6>
                    @if(!empty($businessProfile->menu_photos))
                        <div class="photo-grid">
                            @foreach($businessProfile->menu_photos as $index => $photo)
                                <div class="photo-grid-item position-relative">
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Thực đơn">
                                    </a>
                                    <form action="{{ route('business.delete_photo') }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Xóa ảnh này?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="type" value="menu">
                                        <input type="hidden" name="index" value="{{ $index }}">
                                        <button type="submit" class="btn btn-sm btn-light border-0 py-0 px-1" style="font-size: 0.7rem;">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-photo-box">
                            Chưa có ảnh thực đơn nào được tải lên.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab 3: Customer Reviews -->
            <div class="tab-pane fade" id="tab-reviews" role="tabpanel">
                <div class="section-header-title">Nhận xét & Đánh giá từ khách hàng</div>

                @forelse($comments as $comment)
                    <div class="review-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <x-user-avatar :user="$comment->user" size="36" />
                                <div>
                                    <div class="fw-semibold" style="color: var(--text-heading); font-size: 0.875rem;">{{ $comment->user->display_name ?? $comment->user->username }}</div>
                                    <div style="color: var(--text-sub); font-size: 0.75rem;">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                            <div class="text-warning fw-bold small">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($comment->rating ?? 5))
                                        ★
                                    @else
                                        <span style="color: var(--border-color);">★</span>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <p class="mb-0 mt-2" style="color: var(--text-main); font-size: 0.875rem; line-height: 1.6;">{{ $comment->content }}</p>
                    </div>
                @empty
                    <div class="text-center py-5" style="background: var(--bg-body); border-radius: 8px; border: 1px dashed var(--border-color);">
                        <div style="color: var(--text-sub); font-size: 0.875rem;">Chưa có nhận xét nào từ khách hàng.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Info -->
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('business.update_info') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header px-4 py-3" style="background-color: var(--bg-body); border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title fw-semibold" style="color: var(--text-heading); font-size: 1.05rem;">
                        Cập nhật thông tin doanh nghiệp
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Tên doanh nghiệp *</label>
                        <input type="text" class="form-control" name="business_name" value="{{ $businessProfile->business_name }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại liên hệ *</label>
                            <input type="text" class="form-control" name="phone" value="{{ $businessProfile->phone }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trang web (Website)</label>
                            <input type="url" class="form-control" name="website" value="{{ $businessProfile->website }}" placeholder="https://">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Đường / Số nhà *</label>
                            <input type="text" class="form-control" name="address_street" value="{{ $businessProfile->address_street }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thành phố / Huyện *</label>
                            <input type="text" class="form-control" name="address_city" value="{{ $businessProfile->address_city }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả doanh nghiệp</label>
                        <textarea class="form-control" name="description" rows="4">{{ $businessProfile->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3" style="background-color: var(--bg-body); border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Upload Photo -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('business.upload_photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header px-4 py-3" style="background-color: var(--bg-body); border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title fw-semibold" style="color: var(--text-heading); font-size: 1.05rem;">
                        Tải ảnh mới lên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Loại hình ảnh *</label>
                        <select class="form-select" name="type" required>
                            <option value="storefront">Mặt tiền cửa hàng</option>
                            <option value="menu">Thực đơn / Bảng giá / Dịch vụ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn hình ảnh *</label>
                        <input type="file" class="form-control" name="photo" accept="image/*" required>
                        <div class="form-text small" style="color: var(--text-sub);">Dung lượng tối đa 5MB. Định dạng PNG, JPG, JPEG, WEBP.</div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3" style="background-color: var(--bg-body); border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Tải lên</button>
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
                            color: '#cbdbe8',
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
