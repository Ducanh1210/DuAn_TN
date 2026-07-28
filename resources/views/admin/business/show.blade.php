@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu doanh nghiệp: ' . $businessProfile->business_name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #adminBizMap {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
    }
    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
    }
    .photo-gallery-item {
        aspect-ratio: 4/3;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        position: relative;
    }
    .photo-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .photo-gallery-item:hover img {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('admin.business-profiles.index') }}" class="btn-minimal text-decoration-none">
        ← Quay lại danh sách
    </a>

    <div class="d-flex gap-2 align-items-center">
        @if($businessProfile->status === 'pending')
            <button type="button" class="btn-minimal text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                Từ chối yêu cầu
            </button>
            <form action="{{ route('admin.business-profiles.approve', $businessProfile->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-minimal btn-minimal-primary px-3" onclick="return confirm('Bạn có chắc chắn muốn phê duyệt doanh nghiệp này?')">
                    Phê duyệt đăng ký
                </button>
            </form>
        @elseif($businessProfile->status === 'approved')
            <span class="badge-minimal badge-minimal-success py-2 px-3" style="font-size: 0.8rem;"><i class="fas fa-check-circle me-1"></i> Đã phê duyệt</span>
        @elseif($businessProfile->status === 'rejected')
            <span class="badge-minimal badge-minimal-danger py-2 px-3" style="font-size: 0.8rem;"><i class="fas fa-times-circle me-1"></i> Đã từ chối</span>
            <button type="button" class="btn-minimal ms-2" data-bs-toggle="modal" data-bs-target="#reApproveModal">
                Phê duyệt lại
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Main Left Column -->
    <div class="col-lg-8">
        <!-- Business General Info Card -->
        <div class="card-minimal p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                <div>
                    <h4 class="fw-semibold mb-1 text-dark">{{ $businessProfile->business_name }}</h4>
                    <span class="badge-minimal me-2">
                        {{ $businessProfile->category ? $businessProfile->category->name : 'N/A' }}
                    </span>
                    @if(!empty($businessProfile->business_types))
                        @foreach((array)$businessProfile->business_types as $type)
                            <span class="badge-minimal me-1">{{ $type }}</span>
                        @endforeach
                    @endif
                </div>
                <div>
                    @if($businessProfile->status === 'pending')
                        <span class="badge-minimal badge-minimal-warning px-3 py-2" style="font-size: 0.8rem;">Chờ xét duyệt</span>
                    @elseif($businessProfile->status === 'approved')
                        <span class="badge-minimal badge-minimal-success px-3 py-2" style="font-size: 0.8rem;">Đã kích hoạt</span>
                    @else
                        <span class="badge-minimal badge-minimal-danger px-3 py-2" style="font-size: 0.8rem;">Bị từ chối</span>
                    @endif
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="text-muted small">Số điện thoại liên hệ</div>
                    <div class="fw-semibold"><i class="fas fa-phone me-1 text-secondary"></i>{{ $businessProfile->phone }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Trang web (Website)</div>
                    <div class="fw-semibold">
                        @if($businessProfile->website)
                            <a href="{{ $businessProfile->website }}" target="_blank" class="text-primary text-decoration-none">
                                <i class="fas fa-globe me-1"></i>{{ $businessProfile->website }}
                            </a>
                        @else
                            <span class="text-muted fw-normal">Chưa cập nhật</span>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Địa chỉ kinh doanh</div>
                    <div class="fw-semibold">
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                        {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }} (Mã bưu chính: {{ $businessProfile->address_postal_code }})
                    </div>
                </div>
            </div>

            <!-- Leaflet Map Inspection -->
            <div class="mb-4">
                <div class="fw-semibold mb-2"><i class="fas fa-map-marked-alt text-primary me-1"></i> Tọa độ bản đồ: [{{ $businessProfile->lat }}, {{ $businessProfile->lng }}]</div>
                <div id="adminBizMap"></div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <div class="fw-semibold mb-2"><i class="fas fa-align-left text-primary me-1"></i> Giới thiệu về doanh nghiệp</div>
                <div class="p-3 bg-light rounded-3 text-secondary border" style="white-space: pre-line; font-size: 0.875rem;">
                    {{ $businessProfile->description ?? 'Chưa có mô tả giới thiệu.' }}
                </div>
            </div>
        </div>

        <!-- Photos Card -->
        <div class="card-minimal p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-images text-primary me-2"></i>Hình ảnh xác minh doanh nghiệp</h5>
            
            <!-- Storefront Photos -->
            <div class="mb-4">
                <div class="fw-semibold small text-secondary mb-2">1. Ảnh mặt tiền cửa hàng ({{ count($businessProfile->storefront_photos ?? []) }})</div>
                @if(!empty($businessProfile->storefront_photos))
                    <div class="photo-gallery-grid">
                        @foreach($businessProfile->storefront_photos as $photo)
                            <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="photo-gallery-item">
                                <img src="{{ asset('storage/' . $photo) }}" alt="Mặt tiền">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded text-muted small text-center">Không có ảnh mặt tiền nào được tải lên.</div>
                @endif
            </div>

            <!-- Menu Photos -->
            <div>
                <div class="fw-semibold small text-secondary mb-2">2. Ảnh thực đơn / Bảng giá / Dịch vụ ({{ count($businessProfile->menu_photos ?? []) }})</div>
                @if(!empty($businessProfile->menu_photos))
                    <div class="photo-gallery-grid">
                        @foreach($businessProfile->menu_photos as $photo)
                            <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="photo-gallery-item">
                                <img src="{{ asset('storage/' . $photo) }}" alt="Thực đơn">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded text-muted small text-center">Không có ảnh thực đơn nào được tải lên.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Right Column -->
    <div class="col-lg-4">
        <!-- User Info Card -->
        <div class="card-minimal p-4 mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Người đăng ký</h6>
            <div class="d-flex align-items-center gap-3 mb-3">
                <x-user-avatar :user="$businessProfile->user" size="48" />
                <div>
                    <div class="fw-bold text-dark">{{ $businessProfile->user->display_name ?? $businessProfile->user->username }}</div>
                    <div class="text-muted small">{{ $businessProfile->user->email }}</div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1">Role: {{ $businessProfile->user->role }}</span>
                </div>
            </div>
            <div class="border-top pt-2 mt-2 small text-muted">
                <div><strong>Tên tài khoản:</strong> {{ $businessProfile->user->username }}</div>
                <div><strong>Ngày đăng ký TK:</strong> {{ $businessProfile->user->created_at ? $businessProfile->user->created_at->format('d/m/Y') : 'N/A' }}</div>
                <div><strong>Ngày gửi yêu cầu:</strong> {{ $businessProfile->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Status Details & Actions Card -->
        <div class="card-minimal p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Trạng thái xử lý</h6>
            
            @if($businessProfile->status === 'pending')
                <div class="alert alert-warning border-0 small mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> Yêu cầu đang chờ quản trị viên duyệt thông tin địa điểm và hình ảnh.
                </div>
            @elseif($businessProfile->status === 'approved')
                <div class="alert alert-success border-0 small mb-3">
                    <i class="fas fa-check-circle me-1"></i> Yêu cầu đã được phê duyệt. Địa điểm kinh doanh đã được tạo công khai trên bản đồ Ninh Bình Travel Hub.
                </div>
            @elseif($businessProfile->status === 'rejected')
                <div class="alert alert-danger border-0 small mb-3">
                    <div class="fw-bold mb-1"><i class="fas fa-times-circle me-1"></i> Đã từ chối yêu cầu</div>
                    <div><strong>Lý do từ chối:</strong> {{ $businessProfile->reject_reason }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.business-profiles.reject', $businessProfile->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Từ chối yêu cầu doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary mb-3">Vui lòng nhập lý do cụ thể để người dùng biết cách chỉnh sửa lại thông tin.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lý do từ chối *</label>
                        <textarea name="reject_reason" class="form-control" rows="4" required placeholder="Ví dụ: Hình ảnh mặt tiền không rõ ràng, vị trí tọa độ sai so với địa chỉ đường..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Xác nhận từ chối</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Re-Approve -->
<div class="modal fade" id="reApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.business-profiles.approve', $businessProfile->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-success">Phê duyệt lại doanh nghiệp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary mb-0">Bạn có chắc chắn muốn phê duyệt lại yêu cầu này? Địa điểm kinh doanh sẽ được đưa lên hệ thống.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-sm px-3">Xác nhận phê duyệt</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat("{{ $businessProfile->lat }}");
        const lng = parseFloat("{{ $businessProfile->lng }}");

        if (!isNaN(lat) && !isNaN(lng)) {
            const map = L.map('adminBizMap', {
                zoomControl: true,
                attributionControl: false
            }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            // Fetch boundary
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
                    }).addTo(map);
                })
                .catch(err => console.error(err));

            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`
                <div style="font-family: inherit; font-size: 0.85rem;">
                    <strong style="color: #1e3a5f;">{{ $businessProfile->business_name }}</strong><br>
                    <span style="color: #64748b;">{{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}</span>
                </div>
            `).openPopup();
        }
    });
</script>
@endpush
