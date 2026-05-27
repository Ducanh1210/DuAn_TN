@extends('admin.layouts.app')
@section('title', 'Quản lý Sự kiện')
@section('actions')
<a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm sự kiện</a>
@endsection

@push('styles')
<style>
.ev-filters{background:linear-gradient(135deg,#f8f9fc,#eef1f8);border-radius:12px;padding:20px;margin-bottom:24px;border:1px solid #e2e8f0}
.ev-card{border:none;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;transition:box-shadow .3s}
.ev-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.1)}
.ev-thumb{width:100px;height:70px;object-fit:cover;border-radius:8px;flex-shrink:0}
.ev-thumb-ph{width:100px;height:70px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:24px;color:#94a3b8}
.badge-st-active{background:linear-gradient(135deg,#10b981,#059669)}
.badge-st-cancelled{background:linear-gradient(135deg,#ef4444,#dc2626)}
.badge-st-expired{background:linear-gradient(135deg,#94a3b8,#64748b)}
.badge-st-hidden{background:linear-gradient(135deg,#f59e0b,#d97706)}
.stat-card{border:none;border-radius:12px;padding:16px 20px;color:#fff;position:relative;overflow:hidden}
.stat-card::after{content:'';position:absolute;top:-15px;right:-15px;width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.15)}
.stat-card .stat-number{font-size:1.6rem;font-weight:700}
.stat-card .stat-label{font-size:.8rem;opacity:.85}
.action-btn{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;transition:all .2s;border:1px solid transparent}
.action-btn:hover{transform:translateY(-2px);box-shadow:0 3px 8px rgba(0,0,0,.15)}
.table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:600;border-bottom:2px solid #e2e8f0}
.ev-name{font-weight:600;color:#1e293b;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.ev-loc{font-size:.82rem;color:#94a3b8;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.featured-star{color:#f59e0b;font-size:.9rem}
</style>
@endpush

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)">
            <div class="stat-number">{{ \App\Models\Event::count() }}</div>
            <div class="stat-label"><i class="fas fa-calendar-alt me-1"></i>Tổng sự kiện</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#047857)">
            <div class="stat-number">{{ \App\Models\Event::where('status','active')->count() }}</div>
            <div class="stat-label"><i class="fas fa-check-circle me-1"></i>Đang diễn ra</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#b45309)">
            <div class="stat-number">{{ \App\Models\Event::where('is_featured',true)->count() }}</div>
            <div class="stat-label"><i class="fas fa-star me-1"></i>Nổi bật</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#b91c1c)">
            <div class="stat-number">{{ \App\Models\Event::where('start_time','>=',now())->count() }}</div>
            <div class="stat-label"><i class="fas fa-clock me-1"></i>Sắp diễn ra</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="ev-filters">
    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-semibold mb-1" style="font-size:.82rem"><i class="fas fa-search me-1"></i>Tìm kiếm</label>
            <input type="text" name="search" class="form-control" placeholder="Tên sự kiện, mô tả..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold mb-1" style="font-size:.82rem">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>Đang diễn ra</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Đã hủy</option>
                <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Đã kết thúc</option>
                <option value="hidden" {{ request('status')=='hidden'?'selected':'' }}>Đã ẩn</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i>Lọc</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="ev-card card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="50" class="text-center ps-3">ID</th>
                        <th width="110">Ảnh</th>
                        <th>Tên sự kiện</th>
                        <th width="140">Thời gian</th>
                        <th width="100" class="text-center">Trạng thái</th>
                        <th width="50" class="text-center">⭐</th>
                        <th width="110">Người tạo</th>
                        <th width="140" class="text-center pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $ev)
                    <tr>
                        <td class="text-center ps-3 fw-semibold text-muted">{{ $ev->id }}</td>
                        <td>
                            @if($ev->featured_image)
                                <img src="{{ asset('storage/' . $ev->featured_image) }}" class="ev-thumb">
                            @else
                                <div class="ev-thumb-ph bg-light"><i class="fas fa-calendar-day"></i></div>
                            @endif
                        </td>
                        <td>
                            <div class="ev-name">{{ $ev->name }}</div>
                            <div class="ev-loc"><i class="fas fa-map-pin me-1"></i>{{ $ev->location_text ?? ($ev->location->name ?? '—') }}</div>
                        </td>
                        <td>
                            <div style="font-size:.82rem"><i class="fas fa-play text-success me-1"></i>{{ $ev->start_time->format('d/m/Y H:i') }}</div>
                            <div style="font-size:.82rem"><i class="fas fa-stop text-danger me-1"></i>{{ $ev->end_time->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="text-center"><span class="badge badge-st-{{ $ev->status }}">{{ $ev->status_label }}</span></td>
                        <td class="text-center">
                            @if($ev->is_featured)<i class="fas fa-star featured-star"></i>@else<span class="text-muted">—</span>@endif
                        </td>
                        <td><span class="text-muted" style="font-size:.85rem">{{ $ev->creator->display_name ?? $ev->creator->username ?? '—' }}</span></td>
                        <td class="text-center pe-3">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.events.edit', $ev->id) }}" class="action-btn btn btn-sm btn-outline-primary" title="Sửa"><i class="fas fa-edit" style="font-size:.8rem"></i></a>
                                <form action="{{ route('admin.events.toggle', $ev->id) }}" method="POST" class="d-inline">@csrf @method('PATCH')
                                    <button class="action-btn btn btn-sm {{ $ev->status==='active'?'btn-outline-warning':'btn-outline-success' }}" title="{{ $ev->status==='active'?'Ẩn':'Hiện' }}">
                                        <i class="fas {{ $ev->status==='active'?'fa-eye-slash':'fa-eye' }}" style="font-size:.8rem"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa sự kiện này?')">@csrf @method('DELETE')
                                    <button class="action-btn btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash" style="font-size:.8rem"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3 d-block" style="opacity:.3"></i>
                                <p class="mb-2 fw-semibold">Chưa có sự kiện nào</p>
                                <a href="{{ route('admin.events.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm sự kiện</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($events->hasPages())
    <div class="card-footer bg-white border-top">{{ $events->links() }}</div>
    @endif
</div>
@endsection
