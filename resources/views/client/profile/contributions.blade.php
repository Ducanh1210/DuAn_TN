<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đóng góp - Hà Nam POI</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/avatar-frames.css') }}">

    <style>
        :root {
            --q-bg: #f8fafc;
            --q-card-bg: #ffffff;
            --q-primary: #6366f1;
            --q-primary-hover: #4f46e5;
            --q-text-main: #0f172a;
            --q-text-sub: #64748b;
            --q-border: #e2e8f0;
        }

        body {
            background-color: var(--q-bg);
            font-family: 'Be Vietnam Pro', 'Plus Jakarta Sans', system-ui, sans-serif;
            color: var(--q-text-main);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Full Width Top Header Bar */
        .reward-top-bar-full {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .reward-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .reward-brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reward-brand-text {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .reward-brand-sub {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
        }

        .reward-top-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .reward-top-menu .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #475569 !important;
            padding: 8px 18px !important;
            border-radius: 0 !important;
            transition: all 0.2s ease;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            cursor: pointer;
        }

        .reward-top-menu .nav-link:hover {
            color: #6366f1 !important;
        }

        .reward-top-menu .nav-link.active {
            color: #6366f1 !important;
            font-weight: 700 !important;
            border-bottom: 3px solid #6366f1 !important;
        }

        /* Container & Content */
        .reward-full-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        .content-panel {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            border: 1px solid var(--q-border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .content-panel.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Headings */
        .panel-heading {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px dashed var(--q-border);
        }
        .panel-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #eeeffe;
            color: var(--q-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        .panel-title {
            font-weight: 800;
            font-size: 1.25rem;
            color: #0f172a;
            margin: 0;
        }
        .panel-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 1px solid var(--q-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--q-text-main);
            background-color: #f8fafc;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: var(--q-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }

        /* Map Embed */
        #pickerMap {
            height: 380px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--q-border);
            margin-bottom: 15px;
            z-index: 1;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        /* Buttons */
        .btn-indigo {
            background: var(--q-primary);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            transition: all 0.2s ease;
        }
        .btn-indigo:hover {
            background: var(--q-primary-hover);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.35);
        }

        /* Badges */
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-pending { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
        .status-approved, .status-resolved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-rejected { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-processing { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        /* Tables */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .table-custom th {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 16px 8px;
            border: none;
        }
        .table-custom td {
            background: #ffffff;
            padding: 16px;
            border-top: 1px solid var(--q-border);
            border-bottom: 1px solid var(--q-border);
            vertical-align: middle;
        }
        .table-custom td:first-child {
            border-left: 1px solid var(--q-border);
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }
        .table-custom td:last-child {
            border-right: 1px solid var(--q-border);
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        .table-custom tr {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .table-custom tr:hover td {
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transform: scale(1.002);
            z-index: 10;
        }

        /* Custom Dropdown for Categories */
        .custom-dropdown { position: relative; width: 100%; }
        .dropdown-selected {
            border: 1px solid var(--q-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--q-text-main);
            background-color: #f8fafc;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .dropdown-selected.active {
            border-color: var(--q-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            background-color: #ffffff;
        }
        .dropdown-options {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid var(--q-border);
            border-radius: 10px;
            margin-top: 5px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            max-height: 250px;
            overflow-y: auto;
        }
        .dropdown-options.show { display: block; animation: slideDown 0.2s ease; }
        .dropdown-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .dropdown-item:hover { background: #f1f5f9; }
        .dropdown-item img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
        .selected-item-view {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Top Header Bar (Missions Style) -->
    <div class="reward-top-bar-full">
        <a href="{{ url('/') }}" class="reward-brand" title="Quay về Bản đồ">
            <div class="reward-brand-icon">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <div>
                <div class="reward-brand-text">ĐÓNG GÓP CỘNG ĐỒNG</div>
                <div class="reward-brand-sub">Hà Nam POI</div>
            </div>
        </a>

        <!-- Horizontal Tab Menu -->
        <ul class="reward-top-menu">
            <li><a class="nav-link" href="{{ url('/') }}"><i class="fa-solid fa-map me-1"></i> Trang chủ Bản đồ</a></li>
            <li><button class="nav-link active" onclick="switchTab('tab-suggest')">Đề xuất địa điểm</button></li>
            <li><button class="nav-link" onclick="switchTab('tab-feedback')">Góp ý hệ thống</button></li>
            <li><button class="nav-link" onclick="switchTab('tab-history')">Lịch sử của bạn</button></li>
        </ul>

        @if(Auth::check())
        <div class="d-flex align-items-center gap-3">
            <div style="background: #fffbe6; border: 1px solid #fde68a; padding: 5px 14px; border-radius: 20px; font-weight: 700; color: #d97706; font-size: 0.85rem;">
                <i class="fa-solid fa-coins text-warning"></i> {{ number_format($user->points) }} xu
            </div>
            <div class="avatar-frame-wrapper {{ $user->equippedFrame ? $user->equippedFrame->css_style : '' }}" style="width: 36px; height: 36px;">
                <img src="{{ $user->avatar_formatted_url }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->display_name ?? $user->username) }}&background=6366f1&color=fff';" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="btn-indigo" style="padding: 6px 16px; font-size: 0.85rem;">Đăng nhập</a>
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="reward-full-container">
        
        <!-- Tab 1: Đề xuất -->
        <div class="content-panel active" id="tab-suggest">
            <div class="panel-heading">
                <div class="panel-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                <div>
                    <h2 class="panel-title">Thêm Địa Điểm Mới</h2>
                    <p class="panel-desc">Giúp cộng đồng khám phá thêm nhiều địa điểm thú vị tại Hà Nam</p>
                </div>
            </div>

            @if(Auth::check())
            <form id="suggestLocationForm" onsubmit="submitLocationSuggestion(event)">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên địa điểm (*)</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nhập tên địa điểm...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Danh mục (*)</label>
                        <div class="custom-dropdown" id="categoryDropdown">
                            <div class="dropdown-selected" id="dropdownSelected" onclick="toggleDropdown()">
                                <span id="selectedCategoryText">-- Chọn danh mục --</span>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>
                            <div class="dropdown-options" id="dropdownOptions">
                                @foreach($categories as $category)
                                    <div class="dropdown-item" onclick="selectCategory('{{ $category->name }}', '{{ asset($category->icon) }}')">
                                        <img src="{{ asset($category->icon) }}" alt="{{ $category->name }}">
                                        <span class="fw-medium">{{ $category->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="category_suggest" id="categoryInput" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Chọn vị trí trên bản đồ (*)</label>
                    <div id="pickerMap"></div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <input type="text" id="suggestLat" name="lat" class="form-control" placeholder="Vĩ độ" readonly required style="background: #f1f5f9; border-color: #cbd5e1; cursor: not-allowed;">
                        </div>
                        <div class="col-6">
                            <input type="text" id="suggestLng" name="lng" class="form-control" placeholder="Kinh độ" readonly required style="background: #f1f5f9; border-color: #cbd5e1; cursor: not-allowed;">
                        </div>
                    </div>
                    <small class="text-muted fw-medium mt-1 d-block"><i class="fa-solid fa-circle-info me-1 text-primary"></i>Hãy click trực tiếp vào bản đồ để lấy tọa độ.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ chi tiết</label>
                    <input type="text" name="address" class="form-control" placeholder="Số nhà, đường, xã/phường, quận/huyện...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Giới thiệu đôi nét hấp dẫn về địa điểm này..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Hình ảnh đính kèm (Tối đa 5MB)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*" style="padding: 10px;">
                </div>
                
                <hr style="border-color: var(--q-border); margin: 24px 0;">
                <div class="text-end">
                    <button type="submit" class="btn btn-indigo px-5 py-2"><i class="fa-solid fa-paper-plane me-2"></i> Gửi Đề Xuất</button>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <div class="panel-icon mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.5rem; background: #f1f5f9; color: #94a3b8;"><i class="fa-solid fa-lock"></i></div>
                <h4 class="fw-bold mb-2">Cần Đăng Nhập</h4>
                <p class="text-muted mb-4">Bạn phải đăng nhập tài khoản để có thể gửi đề xuất địa điểm mới.</p>
                <a href="{{ route('login') }}" class="btn btn-indigo px-5">Đi đến Đăng nhập</a>
            </div>
            @endif
        </div>

        <!-- Tab 2: Góp ý -->
        <div class="content-panel" id="tab-feedback">
            <div class="panel-heading">
                <div class="panel-icon" style="background: #ffedd5; color: #ea580c;"><i class="fa-solid fa-bullhorn"></i></div>
                <div>
                    <h2 class="panel-title">Góp Ý Hệ Thống & Báo Lỗi</h2>
                    <p class="panel-desc">Mọi ý kiến của bạn đều giúp Hà Nam POI hoàn thiện hơn mỗi ngày</p>
                </div>
            </div>

            @if(Auth::check())
            <form id="systemFeedbackForm" onsubmit="submitSystemFeedback(event)">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Phân loại vấn đề (*)</label>
                    <select name="report_type" class="form-select" required>
                        <option value="" disabled selected>-- Chọn loại vấn đề --</option>
                        <option value="system_suggestion">💡 Góp ý cải thiện tính năng / Giao diện web</option>
                        <option value="wrong_info">⚠️ Báo cáo sai thông tin (Giờ mở cửa, giá vé, SĐT...)</option>
                        <option value="wrong_position">📍 Báo cáo sai vị trí tọa độ bản đồ</option>
                        <option value="image_error">🖼️ Báo cáo hình ảnh bị lỗi / Không phù hợp</option>
                        <option value="location_closed">🚪 Báo cáo địa điểm đã đóng cửa vĩnh viễn</option>
                        <option value="other">💬 Vấn đề khác</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mô tả chi tiết (*)</label>
                    <textarea name="content" class="form-control" rows="5" required placeholder="Vui lòng cung cấp chi tiết lỗi hoặc mong muốn cải thiện của bạn..."></textarea>
                </div>
                <hr style="border-color: var(--q-border); margin: 24px 0;">
                <div class="text-end">
                    <button type="submit" class="btn btn-indigo px-5 py-2"><i class="fa-solid fa-paper-plane me-2"></i> Gửi Góp Ý</button>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <div class="panel-icon mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.5rem; background: #f1f5f9; color: #94a3b8;"><i class="fa-solid fa-lock"></i></div>
                <h4 class="fw-bold mb-2">Cần Đăng Nhập</h4>
                <p class="text-muted mb-4">Bạn phải đăng nhập tài khoản để gửi góp ý.</p>
                <a href="{{ route('login') }}" class="btn btn-indigo px-5">Đi đến Đăng nhập</a>
            </div>
            @endif
        </div>

        <!-- Tab 3: Lịch sử -->
        <div class="content-panel" id="tab-history">
            <div class="panel-heading mb-4">
                <div class="panel-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div>
                    <h2 class="panel-title">Lịch Sử Đóng Góp</h2>
                    <p class="panel-desc">Theo dõi trạng thái duyệt các bài đăng và góp ý của bạn</p>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 mt-2"><i class="fa-solid fa-location-dot text-primary me-2"></i> Đề xuất địa điểm mới</h5>
            @if($suggestions->isEmpty())
                <div class="text-center text-muted py-4 mb-4 border rounded-3 bg-light" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
                    <p class="mb-0 fw-medium">Chưa có dữ liệu đề xuất nào.</p>
                </div>
            @else
                <div class="mb-5">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Tên địa điểm</th>
                                <th>Thời gian gửi</th>
                                <th>Trạng thái</th>
                                <th>Phản hồi / Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suggestions as $suggestion)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $suggestion->name }}</div>
                                    <div class="text-muted" style="font-size: 0.8rem; margin-top: 2px;"><i class="fa-solid fa-map-pin me-1"></i>{{ Str::limit($suggestion->address, 35) }}</div>
                                </td>
                                <td style="font-size: 0.85rem; color: #64748b; font-weight: 500;">{{ $suggestion->created_at->format('d/m/Y - H:i') }}</td>
                                <td>
                                    <span class="badge-status status-{{ $suggestion->status }}">
                                        @if($suggestion->status == 'pending') Chờ duyệt
                                        @elseif($suggestion->status == 'approved') Đã duyệt
                                        @elseif($suggestion->status == 'rejected') Từ chối
                                        @else {{ $suggestion->status }} @endif
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: #475569;">{{ $suggestion->admin_note ?? $suggestion->reject_reason ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <h5 class="fw-bold text-dark mb-3 mt-4"><i class="fa-solid fa-message text-warning me-2"></i> Góp ý & Báo lỗi hệ thống</h5>
            @if($feedbacks->isEmpty())
                <div class="text-center text-muted py-4 border rounded-3 bg-light" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
                    <p class="mb-0 fw-medium">Chưa có dữ liệu góp ý nào.</p>
                </div>
            @else
                <div>
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Loại vấn đề</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Phản hồi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbacks as $feedback)
                            <tr>
                                <td class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    @if($feedback->report_type == 'wrong_info') Sai thông tin
                                    @elseif($feedback->report_type == 'image_error') Lỗi hình ảnh
                                    @elseif($feedback->report_type == 'wrong_position') Sai vị trí
                                    @elseif($feedback->report_type == 'system_suggestion') Góp ý hệ thống
                                    @elseif($feedback->report_type == 'location_closed') Đã đóng cửa
                                    @else Khác @endif
                                </td>
                                <td style="font-size: 0.85rem; color: #475569; max-width: 300px; line-height: 1.4;">{{ Str::limit($feedback->content, 60) }}</td>
                                <td>
                                    <span class="badge-status status-{{ $feedback->status }}">
                                        @if($feedback->status == 'pending') Chờ duyệt
                                        @elseif($feedback->status == 'processing') Đang xử lý
                                        @elseif($feedback->status == 'resolved') Đã xử lý
                                        @elseif($feedback->status == 'rejected') Từ chối
                                        @else {{ $feedback->status }} @endif
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: #64748b; font-weight: 500;">{{ $feedback->admin_response ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Custom Dropdown Logic
        function toggleDropdown() {
            document.getElementById('dropdownOptions').classList.toggle('show');
            document.getElementById('dropdownSelected').classList.toggle('active');
        }

        function selectCategory(name, iconUrl) {
            document.getElementById('categoryInput').value = name;
            document.getElementById('selectedCategoryText').innerHTML = `
                <div class="selected-item-view">
                    <img src="${iconUrl}" style="width: 24px; height: 24px; object-fit: contain;">
                    <span class="fw-bold">${name}</span>
                </div>
            `;
            document.getElementById('dropdownOptions').classList.remove('show');
            document.getElementById('dropdownSelected').classList.remove('active');
        }

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('categoryDropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                document.getElementById('dropdownOptions').classList.remove('show');
                document.getElementById('dropdownSelected').classList.remove('active');
            }
        });

        function switchTab(tabId) {
            document.querySelectorAll('.reward-top-menu .nav-link').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');
            
            document.querySelectorAll('.content-panel').forEach(panel => panel.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            if(tabId === 'tab-suggest' && pickerMap) {
                setTimeout(() => { pickerMap.invalidateSize(); }, 150);
            }
        }

        let pickerMap = null;
        let pickerMarker = null;

        @if(Auth::check())
        document.addEventListener('DOMContentLoaded', function() {
            pickerMap = L.map('pickerMap', {
                maxBoundsViscosity: 0.8,
                zoomControl: true,
                attributionControl: false,
                minZoom: 10
            }).setView([20.545, 105.912], 11);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(pickerMap);

            // Tải ranh giới Hà Nam
            fetch('{{ asset('geo/ha-nam-old.geojson') }}')
                .then(res => res.json())
                .then(data => {
                    const border = L.geoJSON(data, {
                        style: {
                            color: '#7ba7d4',
                            weight: 2,
                            opacity: 0.55,
                            fillColor: '#f8fafc',
                            fillOpacity: 0.04
                        }
                    }).addTo(pickerMap);
                    
                    const bounds = border.getBounds();
                    pickerMap.fitBounds(bounds);
                    pickerMap.setMaxBounds(bounds.pad(0.2));
                })
                .catch(err => console.error('Lỗi tải ranh giới:', err));

            pickerMap.on('click', function(e) {
                let lat = e.latlng.lat;
                let lng = e.latlng.lng;
                
                document.getElementById('suggestLat').value = lat.toFixed(6);
                document.getElementById('suggestLng').value = lng.toFixed(6);

                if(pickerMarker) {
                    pickerMarker.setLatLng(e.latlng);
                } else {
                    pickerMarker = L.marker(e.latlng).addTo(pickerMap);
                }
            });
        });

        function submitLocationSuggestion(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if(!formData.get('category_suggest')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu danh mục',
                    text: 'Vui lòng chọn danh mục địa điểm!',
                    confirmButtonColor: '#6366f1'
                });
                return;
            }

            if(!formData.get('lat') || !formData.get('lng')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu tọa độ',
                    text: 'Vui lòng click vào bản đồ để chọn tọa độ địa điểm!',
                    confirmButtonColor: '#6366f1'
                });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang gửi...';

            fetch('{{ route('client.locations.suggest') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tuyệt vời!',
                        text: data.message,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        form.reset();
                        if(pickerMarker) pickerMap.removeLayer(pickerMarker);
                        pickerMarker = null;
                        document.getElementById('suggestLat').value = '';
                        document.getElementById('suggestLng').value = '';
                        document.querySelectorAll('.reward-top-menu .nav-link')[3].click();
                        setTimeout(() => location.reload(), 500);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra, vui lòng thử lại.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Gửi Đề Xuất';
            });
        }

        function submitSystemFeedback(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang gửi...';

            fetch('{{ route('client.feedback.submit') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Ghi nhận thành công',
                        text: data.message,
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        form.reset();
                        document.querySelectorAll('.reward-top-menu .nav-link')[3].click();
                        setTimeout(() => location.reload(), 500);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra, vui lòng thử lại.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i> Gửi Góp Ý';
            });
        }
        @endif
    </script>
</body>
</html>
