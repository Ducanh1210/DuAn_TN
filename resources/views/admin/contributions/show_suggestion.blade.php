@extends('admin.layouts.app')

@section('title', $suggestion->name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #suggestionMap {
        height: 320px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .suggestion-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem 1.25rem;
        font-size: 0.825rem;
    }
    @media (max-width: 575.98px) {
        .suggestion-meta-grid { grid-template-columns: 1fr; }
    }
    .suggestion-meta-item .label {
        display: block;
        color: #64748b;
        font-size: 0.72rem;
        margin-bottom: 0.15rem;
    }
    .suggestion-photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.65rem;
    }
    .suggestion-photo-grid a {
        display: block;
        aspect-ratio: 4/3;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .suggestion-photo-grid img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.2s ease;
    }
    .suggestion-photo-grid a:hover img { transform: scale(1.04); }
    .nearby-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.55rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8rem;
    }
    .nearby-item:last-child { border-bottom: 0; padding-bottom: 0; }
</style>
@endpush

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.contributions.index', ['tab' => 'suggestions']) }}" class="text-muted text-decoration-none" style="font-size:0.78rem;">← Quay lại danh sách</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-minimal mb-3">
            <div class="card-header-minimal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Thông tin đề xuất</span>
                @if($suggestion->status === 'pending')
                    <span class="badge-minimal-warning">Chưa xem</span>
                @elseif($suggestion->status === 'approved')
                    <span class="badge-minimal-success">Đã ghi nhận</span>
                @elseif($suggestion->status === 'rejected')
                    <span class="badge-minimal-danger">Bỏ qua</span>
                @elseif($suggestion->status === 'need_more_info')
                    <span class="badge-minimal">Cần thêm thông tin</span>
                @else
                    <span class="badge-minimal">{{ $suggestion->status }}</span>
                @endif
            </div>
            <div class="p-3">
                <div class="suggestion-meta-grid mb-3">
                    <div class="suggestion-meta-item">
                        <span class="label">Người gửi</span>
                        <strong>{{ $suggestion->user->display_name ?? $suggestion->user->username }}</strong>
                        @if($suggestion->user->email ?? null)
                            <div class="text-muted" style="font-size:0.75rem;">{{ $suggestion->user->email }}</div>
                        @endif
                    </div>
                    <div class="suggestion-meta-item">
                        <span class="label">Ngày gửi</span>
                        <span>{{ $suggestion->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="suggestion-meta-item">
                        <span class="label">Danh mục gợi ý</span>
                        <span>{{ $suggestion->category_suggest ?: '—' }}</span>
                    </div>
                    <div class="suggestion-meta-item">
                        <span class="label">Mã đề xuất</span>
                        <span class="font-monospace">#{{ $suggestion->id }}</span>
                    </div>
                    <div class="suggestion-meta-item" style="grid-column: 1 / -1;">
                        <span class="label">Địa chỉ</span>
                        <span>{{ $suggestion->address ?: '—' }}</span>
                    </div>
                    @if($suggestion->lat && $suggestion->lng)
                    <div class="suggestion-meta-item">
                        <span class="label">Vĩ độ (Lat)</span>
                        <span class="font-monospace">{{ number_format((float) $suggestion->lat, 7) }}</span>
                    </div>
                    <div class="suggestion-meta-item">
                        <span class="label">Kinh độ (Lng)</span>
                        <span class="font-monospace">{{ number_format((float) $suggestion->lng, 7) }}</span>
                    </div>
                    @endif
                </div>

                <div class="mb-0">
                    <span class="text-muted d-block mb-1" style="font-size:0.72rem;">Mô tả</span>
                    <div style="white-space:pre-wrap;color:#334155;font-size:0.825rem;">{{ $suggestion->description ?: 'Không có' }}</div>
                </div>
            </div>
        </div>

        @if($suggestion->lat && $suggestion->lng)
        <div class="card-minimal mb-3">
            <div class="card-header-minimal d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Vị trí trên bản đồ</span>
                <div class="d-flex gap-1 flex-wrap">
                    <a href="https://www.google.com/maps?q={{ $suggestion->lat }},{{ $suggestion->lng }}" target="_blank" rel="noopener" class="btn-minimal py-1 px-2" style="font-size:0.72rem;">Google Maps</a>
                    <a href="https://www.openstreetmap.org/?mlat={{ $suggestion->lat }}&mlon={{ $suggestion->lng }}#map=16/{{ $suggestion->lat }}/{{ $suggestion->lng }}" target="_blank" rel="noopener" class="btn-minimal py-1 px-2" style="font-size:0.72rem;">OpenStreetMap</a>
                </div>
            </div>
            <div class="p-3 pt-2">
                <div id="suggestionMap"></div>
            </div>
        </div>
        @else
        <div class="card-minimal mb-3">
            <div class="card-header-minimal">Vị trí trên bản đồ</div>
            <div class="p-3 text-muted" style="font-size:0.825rem;">Người gửi chưa cung cấp tọa độ.</div>
        </div>
        @endif

        @if($suggestion->images && count($suggestion->images))
        <div class="card-minimal">
            <div class="card-header-minimal">Ảnh kèm theo ({{ count($suggestion->images) }})</div>
            <div class="p-3">
                <div class="suggestion-photo-grid">
                    @foreach($suggestion->images as $img)
                        <a href="{{ asset($img) }}" target="_blank" rel="noopener">
                            <img src="{{ asset($img) }}" alt="Ảnh đề xuất">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card-minimal mb-3">
            <div class="card-header-minimal">Ghi nhận nội bộ</div>
            <div class="p-3">
                <form action="{{ route('admin.contributions.suggestions.update', $suggestion->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="pending" @selected($suggestion->status === 'pending')>Chưa xem</option>
                            <option value="approved" @selected($suggestion->status === 'approved')>Đã ghi nhận</option>
                            <option value="need_more_info" @selected($suggestion->status === 'need_more_info')>Cần thêm thông tin</option>
                            <option value="rejected" @selected($suggestion->status === 'rejected')>Bỏ qua</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú admin</label>
                        <textarea name="admin_note" rows="4" class="form-control" placeholder="Ghi chú nội bộ…">{{ old('admin_note', $suggestion->admin_note) }}</textarea>
                    </div>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Lưu</button>
                </form>
                @if($suggestion->processed_at)
                    <div class="text-muted mt-3" style="font-size:0.72rem;">
                        Cập nhật lần cuối: {{ $suggestion->processed_at->format('d/m/Y H:i') }}
                        @if($suggestion->processor)
                            · {{ $suggestion->processor->display_name ?? $suggestion->processor->username }}
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($suggestion->lat && $suggestion->lng)
        <div class="card-minimal">
            <div class="card-header-minimal">Địa điểm có sẵn gần đó (≤ 3 km)</div>
            <div class="p-3">
                @forelse($nearbyLocations as $loc)
                    <div class="nearby-item">
                        <div>
                            <a href="{{ route('admin.locations.edit', $loc->id) }}" class="fw-medium text-decoration-none" style="color:#1e3a5f;font-size:0.825rem;">{{ $loc->name }}</a>
                            @if($loc->address)
                                <div class="text-muted" style="font-size:0.72rem;">{{ \Illuminate\Support\Str::limit($loc->address, 60) }}</div>
                            @endif
                        </div>
                        <span class="text-muted flex-shrink-0" style="font-size:0.72rem;">
                            @if($loc->distance_km < 1)
                                ~{{ round($loc->distance_km * 1000) }} m
                            @else
                                ~{{ number_format($loc->distance_km, 1) }} km
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="text-muted" style="font-size:0.825rem;">Không có địa điểm nào trong bán kính 3 km.</div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@if($suggestion->lat && $suggestion->lng)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = @json((float) $suggestion->lat);
    const lng = @json((float) $suggestion->lng);
    const nearby = @json($nearbyMapData);
    const suggestionName = @json($suggestion->name);
    const suggestionAddress = @json($suggestion->address ? \Illuminate\Support\Str::limit($suggestion->address, 80) : 'Không có địa chỉ');
    const geoJsonUrl = @json(asset('geo/ha-nam-old.geojson'));

    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

    const map = L.map('suggestionMap', {
        zoomControl: true,
        attributionControl: false,
    }).setView([lat, lng], 15);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
    }).addTo(map);

    fetch(geoJsonUrl)
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, {
                style: {
                    color: '#7ba7d4',
                    weight: 2,
                    opacity: 0.55,
                    fillColor: '#f8fafc',
                    fillOpacity: 0.04,
                },
            }).addTo(map);
        })
        .catch(() => {});

    const suggestionIcon = L.divIcon({
        className: '',
        html: '<div style="width:18px;height:18px;background:#1e3a5f;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(15,36,66,.35);"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9],
    });

    L.marker([lat, lng], { icon: suggestionIcon })
        .addTo(map)
        .bindPopup(`
            <div style="font-family:inherit;font-size:0.85rem;min-width:160px;">
                <strong style="color:#1e3a5f;">${suggestionName}</strong><br>
                <span style="color:#64748b;">${suggestionAddress}</span>
            </div>
        `)
        .openPopup();

    nearby.forEach(function (loc) {
        L.circleMarker([loc.lat, loc.lng], {
            radius: 6,
            color: '#94a3b8',
            weight: 2,
            fillColor: '#e2e8f0',
            fillOpacity: 0.9,
        })
            .addTo(map)
            .bindPopup(`
                <div style="font-family:inherit;font-size:0.8rem;">
                    <strong>${loc.name}</strong><br>
                    <span style="color:#64748b;">~${loc.distance_km < 1 ? Math.round(loc.distance_km * 1000) + ' m' : loc.distance_km + ' km'}</span>
                </div>
            `);
    });

    if (nearby.length) {
        const bounds = L.latLngBounds([[lat, lng]]);
        nearby.forEach(loc => bounds.extend([loc.lat, loc.lng]));
        map.fitBounds(bounds, { padding: [36, 36], maxZoom: 15 });
    }
});
</script>
@endpush
@endif
