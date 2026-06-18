@extends('admin.layouts.app')

@section('title', 'Quản lý Tin tức')

@section('actions')
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm bài viết</a>
@endsection

@push('styles')
<style>
    .news-filters {
        background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }
    .news-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }
    .news-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .news-thumb {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }
    .news-thumb-placeholder {
        width: 100px;
        height: 70px;
        border-radius: 8px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #94a3b8;
    }
    .badge-type-news { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .badge-type-guide { background: linear-gradient(135deg, #10b981, #059669); }
    .badge-type-event { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .badge-type-announcement { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .badge-status-published { background: linear-gradient(135deg, #10b981, #059669); }
    .badge-status-draft { background: linear-gradient(135deg, #94a3b8, #64748b); }
    .badge-status-hidden { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .stat-card {
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: -15px;
        right: -15px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
    }
    .stat-card .stat-number { font-size: 1.6rem; font-weight: 700; }
    .stat-card .stat-label { font-size: 0.8rem; opacity: 0.85; }
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 3px 8px rgba(0,0,0,0.15); }
    .table thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    .news-title-cell { max-width: 300px; }
    .news-title-cell .title-text {
        font-weight: 600;
        color: #1e293b;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-title-cell .summary-text {
        font-size: 0.82rem;
        color: #94a3b8;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .filter-btn {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .filter-btn:hover, .filter-btn.active-filter {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #fff;
    }
</style>
@endpush

@section('content')

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <div class="stat-number">{{ \App\Models\News::count() }}</div>
            <div class="stat-label"><i class="fas fa-newspaper me-1"></i> Tổng bài viết</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #047857);">
            <div class="stat-number">{{ \App\Models\News::where('status','published')->count() }}</div>
            <div class="stat-label"><i class="fas fa-check-circle me-1"></i> Đã xuất bản</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #b45309);">
            <div class="stat-number">{{ \App\Models\News::where('status','draft')->count() }}</div>
            <div class="stat-label"><i class="fas fa-pen me-1"></i> Bản nháp</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
            <div class="stat-number">{{ \App\Models\News::sum('view_count') }}</div>
            <div class="stat-label"><i class="fas fa-eye me-1"></i> Tổng lượt xem</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="news-filters">
    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-semibold mb-1" style="font-size: 0.82rem;"><i class="fas fa-search me-1"></i>Tìm kiếm</label>
            <input type="text" name="search" class="form-control" placeholder="Nhập tiêu đề hoặc nội dung..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold mb-1" style="font-size: 0.82rem;">Loại bài viết</label>
            <select name="type" class="form-select">
                <option value="">Tất cả</option>
                <option value="news" {{ request('type') == 'news' ? 'selected' : '' }}>Tin tức</option>
                <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Sự kiện</option>
                <option value="guide" {{ request('type') == 'guide' ? 'selected' : '' }}>Cẩm nang</option>
                <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Thông báo</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold mb-1" style="font-size: 0.82rem;">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="">Tất cả</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Đã ẩn</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Lọc</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt"></i></a>
        </div>
    </form>
</div>

{{-- News Table --}}
<div class="news-card card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="50" class="text-center ps-3">ID</th>
                        <th width="110">Ảnh</th>
                        <th class="news-title-cell">Tiêu đề</th>
                        <th width="100" class="text-center">Loại</th>
                        <th width="100" class="text-center">Trạng thái</th>
                        <th width="90" class="text-center">Lượt xem</th>
                        <th width="120">Tác giả</th>
                        <th width="110">Ngày đăng</th>
                        <th width="140" class="text-center pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <td class="text-center ps-3 fw-semibold text-muted">{{ $item->id }}</td>
                        <td>
                            @if($item->featured_image)
                                <img src="{{ asset('storage/' . $item->featured_image) }}" alt="" class="news-thumb">
                            @else
                                <div class="news-thumb-placeholder bg-light">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td class="news-title-cell">
                            <div class="title-text">{{ $item->title }}</div>
                            @if($item->summary)
                                <div class="summary-text">{{ $item->summary }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge badge-type-{{ $item->type }}">{{ $item->type_label }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-status-{{ $item->status }}">{{ $item->status_label }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold text-muted"><i class="fas fa-eye me-1" style="font-size: 0.75rem;"></i>{{ number_format($item->view_count) }}</span>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 0.85rem;">{{ $item->author->display_name ?? $item->author->username ?? '—' }}</span>
                        </td>
                        <td>
                            <span style="font-size: 0.83rem;" class="text-muted">
                                {{ $item->published_at ? $item->published_at->format('d/m/Y') : '—' }}
                            </span>
                        </td>
                        <td class="text-center pe-3">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.news.edit', $item->id) }}" class="action-btn btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                </a>
                                <form action="{{ route('admin.news.toggle', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn btn btn-sm {{ $item->status === 'published' ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $item->status === 'published' ? 'Ẩn bài viết' : 'Hiện bài viết' }}">
                                        <i class="fas {{ $item->status === 'published' ? 'fa-eye-slash' : 'fa-eye' }}" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="action-btn btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fas fa-trash" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-newspaper fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                <p class="mb-2 fw-semibold">Chưa có bài viết nào</p>
                                <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Thêm bài viết đầu tiên</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($news->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $news->links() }}
    </div>
    @endif
</div>
@endsection
