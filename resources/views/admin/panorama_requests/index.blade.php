@extends('admin.layouts.app')

@section('title', 'Yêu cầu làm tour 360')

@section('content')
<div class="metric-strip mb-3 d-flex flex-wrap align-items-center justify-content-between p-0 overflow-hidden">
    @foreach([
        'all' => 'Tất cả',
        'pending' => 'Chờ liên hệ',
        'contacted' => 'Đã liên hệ',
        'done' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ] as $key => $label)
        <a href="{{ route('admin.panorama-requests.index', ['status' => $key, 'search' => $search]) }}"
           class="metric-item flex-fill text-decoration-none py-3 px-3 transition-all {{ $status === $key ? 'bg-light border-bottom border-2' : '' }}"
           style="{{ $status === $key ? 'border-bottom-color:#1e3a5f!important;' : '' }}">
            <div class="metric-label">{{ $label }}</div>
            <div class="metric-value text-dark" style="font-size: 1.1rem;">{{ $counts[$key] ?? 0 }}</div>
        </a>
    @endforeach
</div>

<div class="card-minimal mb-3 p-3">
    <form action="{{ route('admin.panorama-requests.index') }}" method="GET" class="row g-2 align-items-center">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên địa điểm, SĐT, tài khoản..." value="{{ $search }}" style="border-color: #e2e8f0;">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn-minimal btn-minimal-primary w-100 py-1" style="font-size: 0.8rem;">Tìm kiếm</button>
        </div>
    </form>
</div>

<div class="card-minimal">
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Địa điểm</th>
                    <th>Liên hệ</th>
                    <th>Nhu cầu</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th class="text-end pe-3">Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $index => $item)
                    @php
                        $typeLabels = \App\Models\PanoramaServiceRequest::placeTypeLabels();
                        $sceneLabels = \App\Models\PanoramaServiceRequest::sceneEstimateLabels();
                        $statusLabels = \App\Models\PanoramaServiceRequest::statusLabels();
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $requests->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-medium text-dark" style="font-size:0.825rem;">{{ $item->place_name }}</div>
                            <div class="text-muted" style="font-size:0.72rem;">{{ $typeLabels[$item->place_type] ?? '—' }}</div>
                            @if($item->note)
                                <div class="text-secondary mt-1" style="font-size:0.72rem;max-width:280px;">{{ $item->note }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:0.8rem;">{{ $item->contact_name }}</div>
                            <div class="text-muted" style="font-size:0.72rem;">{{ $item->phone }}</div>
                            @if($item->user)
                                <div class="text-muted" style="font-size:0.7rem;">{{ $item->user->email }}</div>
                            @else
                                <div class="text-muted" style="font-size:0.7rem;">Khách (chưa có TK)</div>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;">{{ $sceneLabels[$item->scene_estimate] ?? '—' }}</td>
                        <td>
                            <span class="badge-minimal">{{ $statusLabels[$item->status] ?? $item->status }}</span>
                        </td>
                        <td class="small text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end pe-3">
                            <form action="{{ route('admin.panorama-requests.update', $item) }}" method="POST" class="d-inline-flex gap-1.5 align-items-center justify-content-end flex-wrap">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="width: auto; min-width: 130px;">
                                    @foreach($statusLabels as $key => $label)
                                        <option value="{{ $key }}" @selected($item->status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-minimal btn-minimal-primary py-1 px-2.5" style="font-size:0.75rem;">Lưu</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có yêu cầu nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="p-3">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
