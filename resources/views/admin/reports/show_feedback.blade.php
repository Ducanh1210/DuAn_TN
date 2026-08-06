@extends('admin.layouts.app')

@section('title', 'Chi tiết góp ý #' . $feedback->id)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.reports.index', ['tab' => 'feedbacks']) }}" class="text-muted text-decoration-none" style="font-size:0.78rem;">← Quay lại danh sách</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-minimal">
            <div class="card-header-minimal">Nội dung gửi</div>
            <div class="p-3" style="font-size:0.825rem;">
                <div class="mb-2"><span class="text-muted">Người gửi:</span> <strong>{{ $feedback->user->display_name ?? ($feedback->user->username ?? 'Khách') }}</strong></div>
                <div class="mb-2"><span class="text-muted">Loại:</span> <span class="badge-minimal">{{ $feedback->report_type }}</span></div>
                <div class="mb-2"><span class="text-muted">Đối tượng:</span>
                    @if($targetLink)
                        <a href="{{ $targetLink }}">{{ $targetName }}</a>
                    @else
                        {{ $targetName }}
                    @endif
                </div>
                <div class="mb-0"><span class="text-muted">Nội dung:</span>
                    <div class="mt-1 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;white-space:pre-wrap;">{{ $feedback->content }}</div>
                </div>
                <div class="text-muted mt-2" style="font-size:0.72rem;">Gửi lúc {{ $feedback->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-minimal">
            <div class="card-header-minimal">Ghi nhận nội bộ</div>
            <div class="p-3">
                <form action="{{ route('admin.reports.feedbacks.update', $feedback->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="pending" @selected($feedback->status === 'pending')>Chưa xem</option>
                            <option value="processing" @selected($feedback->status === 'processing')>Đang xem xét</option>
                            <option value="resolved" @selected($feedback->status === 'resolved')>Đã ghi nhận</option>
                            <option value="rejected" @selected($feedback->status === 'rejected')>Bỏ qua</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phản hồi / ghi chú admin</label>
                        <textarea name="admin_response" rows="4" class="form-control">{{ old('admin_response', $feedback->admin_response) }}</textarea>
                    </div>
                    <button type="submit" class="btn-minimal btn-minimal-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
