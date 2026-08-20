@extends('admin.layouts.app')

@section('title', 'Đề xuất địa điểm')

@section('content')
<div class="card-minimal">
    <div class="card-header-minimal">Đề xuất địa điểm từ người dùng</div>
    <div class="table-responsive">
        <table class="table table-minimal align-middle">
            <thead>
                <tr>
                    <th style="width:50px;">ID</th>
                    <th>Tên đề xuất</th>
                    <th>Người gửi</th>
                    <th>Danh mục gợi ý</th>
                    <th>Ngày</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suggestions as $item)
                <tr>
                    <td class="text-muted" style="font-size:0.75rem;">{{ $item->id }}</td>
                    <td>
                        <div class="fw-medium" style="font-size:0.825rem;color:#0f2442;">{{ $item->name }}</div>
                        @if($item->address)
                            <div class="text-muted" style="font-size:0.72rem;white-space:normal;max-width:240px;">{{ \Illuminate\Support\Str::limit($item->address, 60) }}</div>
                        @endif
                    </td>
                    <td style="font-size:0.825rem;">{{ $item->user->display_name ?? ($item->user->username ?? '—') }}</td>
                    <td><span class="badge-minimal">{{ $item->category_suggest ?: '—' }}</span></td>
                    <td class="text-muted" style="font-size:0.75rem;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($item->status === 'pending')
                            <span class="badge-minimal-warning">Chưa xem</span>
                        @elseif($item->status === 'approved')
                            <span class="badge-minimal-success">Đã ghi nhận</span>
                        @elseif($item->status === 'rejected')
                            <span class="badge-minimal-danger">Bỏ qua</span>
                        @else
                            <span class="badge-minimal">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                            <a href="{{ route('admin.contributions.suggestions.show', $item->id) }}" class="btn-minimal py-1 px-2" style="font-size:0.75rem;">Chi tiết</a>
                            <form action="{{ route('admin.contributions.suggestions.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa đề xuất địa điểm này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-minimal py-1 px-2 text-danger" style="font-size:0.75rem;">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Chưa có đề xuất nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suggestions->hasPages())
    <div class="p-3 border-top">{{ $suggestions->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
