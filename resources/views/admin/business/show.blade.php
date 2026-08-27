@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu doanh nghiệp: ' . $businessProfile->business_name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .biz-show {
        --ink: #1e3a5f;
        --body: #3b5980;
        --muted: #6482a6;
        --line: #cbdbe8;
        --line-soft: #e5e7eb;
        --mist: #f1f5f9;
        --paper: #ffffff;
    }

    .biz-show__top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .biz-show__back {
        color: var(--muted);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.825rem;
    }
    .biz-show__back:hover { color: var(--ink); }

    .biz-show__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .biz-panel {
        background: var(--paper);
        border: 1px solid var(--line-soft);
        border-radius: 10px;
        padding: 22px 24px;
        margin-bottom: 16px;
    }

    .biz-panel__title {
        margin: 0 0 14px;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--ink);
        letter-spacing: -0.01em;
    }

    .biz-name {
        margin: 0 0 6px;
        font-size: 1.35rem;
        font-weight: 600;
        color: var(--ink);
        letter-spacing: -0.02em;
        line-height: 1.3;
    }

    .biz-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line-soft);
    }

    .biz-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 6px;
        background: var(--mist);
        color: var(--ink);
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid var(--line);
    }

    .biz-status {
        margin-left: auto;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--muted);
        padding: 3px 10px;
        border-radius: 6px;
        background: var(--mist);
        border: 1px solid var(--line);
    }
    .biz-status.is-pending { color: #8a6d3b; background: #faf6ef; border-color: #e8dcc8; }
    .biz-status.is-ok { color: var(--ink); background: var(--mist); }
    .biz-status.is-bad { color: #9b3b3b; background: #faf2f2; border-color: #ead4d4; }

    .biz-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 767px) {
        .biz-fields { grid-template-columns: 1fr; }
    }
    .biz-field--full { grid-column: 1 / -1; }

    .biz-label {
        font-size: 0.72rem;
        font-weight: 500;
        color: var(--muted);
        margin-bottom: 4px;
    }
    .biz-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--ink);
        line-height: 1.5;
    }
    .biz-value a {
        color: var(--ink);
        text-decoration: none;
        border-bottom: 1px solid var(--line);
    }
    .biz-value a:hover { border-bottom-color: var(--ink); }
    .biz-value .empty { color: var(--muted); font-weight: 400; }

    .biz-section-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--ink);
        margin: 0 0 10px;
    }

    #adminBizMap {
        height: 280px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: var(--mist);
    }

    .biz-coords {
        font-size: 0.75rem;
        color: var(--muted);
        margin-bottom: 8px;
        font-weight: 400;
    }

    .biz-maps-links {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 10px;
        font-size: 0.75rem;
    }
    .biz-maps-links a {
        color: var(--ink);
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid var(--line);
    }
    .biz-maps-links a:hover {
        border-bottom-color: var(--ink);
    }

    .biz-sapo {
        background: #fafafa;
        border-left: 2.5px solid var(--ink);
        padding: 12px 14px;
        color: var(--body);
        font-size: 0.875rem;
        line-height: 1.7;
        white-space: pre-line;
        border-radius: 0 6px 6px 0;
    }

    .biz-note {
        background: var(--mist);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 0.8rem;
        color: var(--body);
        line-height: 1.55;
        margin-bottom: 0;
    }
    .biz-note.is-warn {
        background: #faf6ef;
        border-color: #e8dcc8;
        color: #6b5428;
    }
    .biz-note.is-bad {
        background: #faf2f2;
        border-color: #ead4d4;
        color: #7a3535;
    }

    .biz-verify-grid {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 16px;
    }
    @media (max-width: 991px) {
        .biz-verify-grid { grid-template-columns: 1fr; }
    }

    .biz-factbox {
        background: var(--mist);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 14px;
        font-size: 0.8rem;
        color: var(--body);
        line-height: 1.6;
    }
    .biz-factbox dt {
        font-weight: 500;
        color: var(--muted);
        font-size: 0.72rem;
        margin-top: 10px;
    }
    .biz-factbox dt:first-child { margin-top: 0; }
    .biz-factbox dd {
        margin: 2px 0 0;
        color: var(--ink);
        font-weight: 500;
    }

    .biz-dist {
        display: inline-block;
        margin-top: 2px;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--ink);
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 6px;
        padding: 2px 8px;
    }
    .biz-dist.is-far { color: #9b3b3b; border-color: #ead4d4; background: #faf2f2; }
    .biz-dist.is-mid { color: #8a6d3b; border-color: #e8dcc8; background: #faf6ef; }

    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(112px, 1fr));
        gap: 8px;
    }
    .photo-gallery-item {
        aspect-ratio: 4/3;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid var(--line-soft);
        background: var(--mist);
        display: block;
    }
    .photo-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .biz-empty {
        padding: 14px;
        text-align: center;
        color: var(--muted);
        font-size: 0.8rem;
        background: var(--mist);
        border: 1px dashed var(--line);
        border-radius: 8px;
    }

    .biz-side-user {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
    }
    .biz-side-user__name {
        font-weight: 600;
        color: var(--ink);
        font-size: 0.875rem;
    }
    .biz-side-user__email {
        color: var(--muted);
        font-size: 0.75rem;
    }

    .biz-kv {
        border-top: 1px solid var(--line-soft);
        padding-top: 12px;
        display: grid;
        gap: 8px;
        font-size: 0.78rem;
    }
    .biz-kv div {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    .biz-kv span { color: var(--muted); }
    .biz-kv strong { color: var(--ink); font-weight: 500; text-align: right; }

    .biz-modal .modal-content {
        border: 1px solid var(--line-soft);
        border-radius: 10px;
    }
    .biz-modal .modal-header {
        border-bottom: 1px solid var(--line-soft);
        background: var(--mist);
    }
    .biz-modal .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--ink);
    }
    .biz-modal textarea.form-control {
        border: none;
        border-bottom: 1px solid var(--line);
        border-radius: 0;
        background: transparent;
        box-shadow: none;
    }
    .biz-modal textarea.form-control:focus {
        border-bottom: 2px solid var(--ink);
        box-shadow: none;
        background: transparent;
    }
</style>
@endpush

@section('content')
@php
    $verificationDistMeters = null;
    if ($businessProfile->verification_lat && $businessProfile->verification_lng && $businessProfile->lat && $businessProfile->lng) {
        $earthRadius = 6371000;
        $dLat = deg2rad($businessProfile->verification_lat - $businessProfile->lat);
        $dLng = deg2rad($businessProfile->verification_lng - $businessProfile->lng);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($businessProfile->lat)) * cos(deg2rad($businessProfile->verification_lat)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $verificationDistMeters = round($earthRadius * $c);
    }

    $vPhotos = !empty($businessProfile->verification_photos)
        ? (array) $businessProfile->verification_photos
        : (!empty($businessProfile->verification_photo) ? [$businessProfile->verification_photo] : []);

    $statusClass = match ($businessProfile->status) {
        'pending' => 'is-pending',
        'approved' => 'is-ok',
        default => 'is-bad',
    };
    $statusLabel = match ($businessProfile->status) {
        'pending' => 'Chờ xét duyệt',
        'approved' => 'Đã kích hoạt',
        default => 'Bị từ chối',
    };
@endphp

<div class="biz-show">
    <div class="biz-show__top">
        <a href="{{ route('admin.business-profiles.index') }}" class="biz-show__back">← Quay lại danh sách</a>

        <div class="biz-show__actions">
            @if($businessProfile->status === 'pending')
                <button type="button" class="btn-minimal text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    Từ chối
                </button>
                <form action="{{ route('admin.business-profiles.approve', $businessProfile->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-minimal btn-minimal-primary px-3" onclick="return confirm('Phê duyệt doanh nghiệp này?')">
                        Phê duyệt
                    </button>
                </form>
            @elseif($businessProfile->status === 'approved')
            @elseif($businessProfile->status === 'rejected')
                <button type="button" class="btn-minimal btn-minimal-primary px-3" data-bs-toggle="modal" data-bs-target="#reApproveModal">
                    Phê duyệt lại
                </button>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <section class="biz-panel">
                <div class="biz-meta-row">
                    <div style="min-width:0;flex:1;">
                        <h1 class="biz-name">{{ $businessProfile->business_name }}</h1>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="biz-chip">{{ $businessProfile->category->name ?? 'Chưa phân loại' }}</span>
                            @foreach((array) ($businessProfile->business_types ?? []) as $type)
                                <span class="biz-chip">{{ $type }}</span>
                            @endforeach
                            @if($businessProfile->location_id)
                                <span class="biz-chip" style="background:#eef2ff;color:#4338ca;">Nhận địa điểm có sẵn</span>
                            @endif
                        </div>
                        @if($businessProfile->claimedLocation)
                            <div class="mt-2" style="font-size:0.82rem;color:#475569;">
                                POI: <strong>{{ $businessProfile->claimedLocation->name }}</strong>
                                <a href="{{ route('admin.locations.edit', $businessProfile->claimedLocation->id) }}" class="ms-1">Xem / sửa địa điểm</a>
                            </div>
                        @endif
                    </div>
                    <span class="biz-status {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="biz-fields">
                    <div>
                        <div class="biz-label">Số điện thoại</div>
                        <div class="biz-value">{{ $businessProfile->phone ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="biz-label">Website</div>
                        <div class="biz-value">
                            @if($businessProfile->website)
                                <a href="{{ $businessProfile->website }}" target="_blank" rel="noopener">{{ $businessProfile->website }}</a>
                            @else
                                <span class="empty">Chưa cập nhật</span>
                            @endif
                        </div>
                    </div>
                    <div class="biz-field--full">
                        <div class="biz-label">Địa chỉ</div>
                        <div class="biz-value">
                            {{ $businessProfile->address_street }}, {{ $businessProfile->address_city }}, {{ $businessProfile->address_province }}
                            @if($businessProfile->address_postal_code)
                                <span class="empty">· {{ $businessProfile->address_postal_code }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @php
                    $mapsAddress = trim(implode(', ', array_filter([
                        $businessProfile->address_street,
                        $businessProfile->address_city,
                        $businessProfile->address_province,
                    ])));
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="biz-section-label mb-0">Vị trí</div>
                        <div class="biz-maps-links">
                            @if($mapsAddress !== '')
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapsAddress) }}" target="_blank" rel="noopener">Địa chỉ</a>
                            @endif
                            @if($businessProfile->lat && $businessProfile->lng)
                                <a href="https://www.google.com/maps?q={{ $businessProfile->lat }},{{ $businessProfile->lng }}" target="_blank" rel="noopener">Ghim</a>
                            @endif
                            @if($businessProfile->verification_lat && $businessProfile->verification_lng)
                                <a href="https://www.google.com/maps?q={{ $businessProfile->verification_lat }},{{ $businessProfile->verification_lng }}" target="_blank" rel="noopener">GPS chụp</a>
                            @endif
                            @if($businessProfile->verification_lat && $businessProfile->lat)
                                <a href="https://www.google.com/maps/dir/{{ $businessProfile->verification_lat }},{{ $businessProfile->verification_lng }}/{{ $businessProfile->lat }},{{ $businessProfile->lng }}" target="_blank" rel="noopener">So sánh</a>
                            @endif
                        </div>
                    </div>
                    <div class="biz-coords">{{ number_format((float) $businessProfile->lat, 6) }}, {{ number_format((float) $businessProfile->lng, 6) }}</div>
                    <div id="adminBizMap"></div>
                </div>

                <div>
                    <div class="biz-section-label">Giới thiệu</div>
                    <div class="biz-sapo">{{ $businessProfile->description ?: 'Chưa có mô tả giới thiệu.' }}</div>
                </div>
            </section>

            <section class="biz-panel">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h2 class="biz-panel__title mb-0">Xác thực thực địa</h2>
                    <span class="biz-chip">{{ count($vPhotos) > 0 ? count($vPhotos) . ' ảnh' : 'Chưa có ảnh' }}</span>
                </div>

                @if(count($vPhotos) > 0)
                    <div class="biz-verify-grid">
                        <div>
                            <div class="biz-label mb-2">Ảnh chụp tại chỗ</div>
                            <div class="photo-gallery-grid">
                                @foreach($vPhotos as $photo)
                                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="photo-gallery-item">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Ảnh xác thực">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <dl class="biz-factbox mb-0">
                            <dt>GPS khi chụp</dt>
                            <dd>
                                @if($businessProfile->verification_lat)
                                    {{ number_format($businessProfile->verification_lat, 6) }}, {{ number_format($businessProfile->verification_lng, 6) }}
                                @else
                                    —
                                @endif
                            </dd>
                            <dt>Ghim trên bản đồ</dt>
                            <dd>{{ number_format((float) $businessProfile->lat, 6) }}, {{ number_format((float) $businessProfile->lng, 6) }}</dd>
                            @if($verificationDistMeters !== null)
                                <dt>Khoảng cách lệch</dt>
                                <dd>
                                    @if($verificationDistMeters <= 100)
                                        <span class="biz-dist">Trùng khớp · ~{{ $verificationDistMeters }}m</span>
                                    @elseif($verificationDistMeters <= 500)
                                        <span class="biz-dist is-mid">Lệch ~{{ $verificationDistMeters }}m</span>
                                    @else
                                        <span class="biz-dist is-far">Cách xa {{ number_format($verificationDistMeters / 1000, 1) }} km</span>
                                    @endif
                                </dd>
                            @endif
                            <dt>Thời điểm chụp</dt>
                            <dd>
                                {{ $businessProfile->verification_time ? \Carbon\Carbon::parse($businessProfile->verification_time)->format('d/m/Y H:i') : '—' }}
                            </dd>
                        </dl>
                    </div>
                @else
                    <div class="biz-empty">Người đăng ký chưa gửi ảnh chụp thực địa.</div>
                @endif
            </section>

            <section class="biz-panel">
                <h2 class="biz-panel__title">Hình ảnh doanh nghiệp <span class="text-muted" style="font-size:.8rem;font-weight:400;">(công khai)</span></h2>

                <div class="mb-4">
                    <div class="biz-label mb-2">Ảnh đại diện</div>
                    @if(!empty($businessProfile->avatar_photo))
                        <div class="photo-gallery-grid">
                            <a href="{{ asset('storage/' . $businessProfile->avatar_photo) }}" target="_blank" class="photo-gallery-item">
                                <img src="{{ asset('storage/' . $businessProfile->avatar_photo) }}" alt="Ảnh đại diện">
                            </a>
                        </div>
                    @else
                        <div class="biz-empty">Chưa chọn ảnh đại diện.</div>
                    @endif
                </div>

                @php
                    $galleryPhotos = array_values(array_filter(array_unique(array_merge(
                        $businessProfile->storefront_photos ?? [],
                        $businessProfile->menu_photos ?? []
                    ))));
                @endphp
                <div>
                    <div class="biz-label mb-2">Ảnh gallery địa điểm ({{ count($galleryPhotos) }})</div>
                    @if(!empty($galleryPhotos))
                        <div class="photo-gallery-grid">
                            @foreach($galleryPhotos as $photo)
                                <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="photo-gallery-item">
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Ảnh địa điểm">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="biz-empty">Không có ảnh gallery.</div>
                    @endif
                </div>
            </section>

            <section class="biz-panel">
                <h2 class="biz-panel__title">Giấy tờ chứng minh chủ sở hữu <span class="text-muted" style="font-size:.8rem;font-weight:400;">(riêng tư · chỉ admin)</span></h2>
                @if(!empty($businessProfile->business_documents))
                    <div class="photo-gallery-grid">
                        @foreach($businessProfile->business_documents as $doc)
                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="photo-gallery-item">
                                <img src="{{ asset('storage/' . $doc) }}" alt="Giấy tờ chứng minh">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="biz-empty">Người đăng ký không gửi giấy tờ chứng minh (tùy chọn).</div>
                @endif
            </section>
        </div>

        <div class="col-lg-4">
            <aside class="biz-panel">
                <h2 class="biz-panel__title">Người đăng ký</h2>
                <div class="biz-side-user">
                    <x-user-avatar :user="$businessProfile->user" size="44" />
                    <div>
                        <div class="biz-side-user__name">{{ $businessProfile->user->display_name ?? $businessProfile->user->username }}</div>
                        <div class="biz-side-user__email">{{ $businessProfile->user->email }}</div>
                    </div>
                </div>
                <div class="biz-kv">
                    <div>
                        <span>Tài khoản</span>
                        <strong>{{ $businessProfile->user->username }}</strong>
                    </div>
                    <div>
                        <span>Vai trò</span>
                        <strong>{{ $businessProfile->user->role }}</strong>
                    </div>
                    <div>
                        <span>Ngày tạo TK</span>
                        <strong>{{ $businessProfile->user->created_at?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                    <div>
                        <span>Ngày gửi yêu cầu</span>
                        <strong>{{ $businessProfile->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </aside>

            <aside class="biz-panel">
                <h2 class="biz-panel__title">Trạng thái xử lý</h2>

                @if($businessProfile->status === 'pending')
                    <p class="biz-note is-warn mb-0">
                        Đang chờ duyệt. Kiểm tra địa chỉ, GPS xác thực và ảnh trước khi phê duyệt.
                    </p>
                @elseif($businessProfile->status === 'approved')
                    <p class="biz-note mb-0">
                        Đã phê duyệt. Địa điểm đã lên bản đồ.
                    </p>
                @elseif($businessProfile->status === 'rejected')
                    <p class="biz-note is-bad mb-0">
                        <strong style="font-weight:600;">Đã từ chối.</strong><br>
                        {{ $businessProfile->reject_reason }}
                    </p>
                @endif
            </aside>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade biz-modal" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.business-profiles.reject', $businessProfile->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Từ chối yêu cầu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small mb-3" style="color:#6482a6;">Nhập lý do để người dùng biết cần chỉnh gì.</p>
                    <label class="biz-label">Lý do từ chối *</label>
                    <textarea name="reject_reason" class="form-control" rows="4" required placeholder="Ví dụ: Ảnh mặt tiền không rõ, tọa độ lệch địa chỉ..."></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal text-danger px-3">Xác nhận từ chối</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Re-Approve --}}
<div class="modal fade biz-modal" id="reApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.business-profiles.approve', $businessProfile->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Phê duyệt lại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" style="color:#3b5980;font-size:0.875rem;line-height:1.6;">
                        Phê duyệt lại yêu cầu này? Địa điểm sẽ được đưa lên hệ thống.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-minimal" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-minimal btn-minimal-primary px-3">Xác nhận phê duyệt</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = parseFloat(@json($businessProfile->lat));
        const lng = parseFloat(@json($businessProfile->lng));

        if (isNaN(lat) || isNaN(lng)) return;

        const map = L.map('adminBizMap', {
            zoomControl: true,
            attributionControl: false
        }).setView([lat, lng], 15);

        L.tileLayer(@json(config('services.carto.tile_url')), {
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        fetch(@json(asset('geo/ninh-binh.geojson')))
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
            .catch(() => {});

        L.marker([lat, lng]).addTo(map).bindPopup(
            '<div style="font-size:0.85rem;line-height:1.45;">' +
            '<strong style="color:#1e3a5f;">' + @json($businessProfile->business_name) + '</strong><br>' +
            '<span style="color:#6482a6;">' + @json(trim($businessProfile->address_street . ', ' . $businessProfile->address_city)) + '</span>' +
            '</div>'
        ).openPopup();
    });
</script>
@endpush
