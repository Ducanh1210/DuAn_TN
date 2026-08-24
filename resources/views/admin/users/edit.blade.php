@extends('admin.layouts.app')

@section('title', 'Sửa Người dùng')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="btn-minimal">Quay lại</a>
@endsection

@section('content')
<div class="card-minimal p-4">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="username" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required style="border-color: #e2e8f0;">
                    @error('username')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="display_name" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Tên hiển thị <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $user->display_name) }}" required style="border-color: #e2e8f0;">
                    @error('display_name')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required style="border-color: #e2e8f0;">
                    @error('email')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="p-2 border rounded text-muted" style="border-color: #e2e8f0 !important; font-size: 0.75rem; background: #f8fafc;">
                            Chỉ nhập mật khẩu nếu bạn muốn thay đổi.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Mật khẩu mới</label>
                        <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" style="border-color: #e2e8f0;">
                        @error('password')
                            <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" style="border-color: #e2e8f0;">
                    </div>
                </div>
            </div>

            <div class="col-md-4 border-start" style="border-color: var(--border-light) !important;">
                <div class="mb-3">
                    <label for="role" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Vai trò</label>
                    <select class="form-select form-select-sm @error('role') is-invalid @enderror" id="role" name="role" {{ auth()->id() == $user->id ? 'disabled' : '' }} style="border-color: #e2e8f0;">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="moderator" {{ old('role', $user->role) == 'moderator' ? 'selected' : '' }}>Moderator</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @if(auth()->id() == $user->id)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div class="text-muted mt-1" style="font-size: 0.725rem;">Bạn không thể thay đổi quyền của chính mình.</div>
                    @endif
                    @error('role')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Trạng thái</label>
                    <select class="form-select form-select-sm @error('status') is-invalid @enderror" id="status" name="status" {{ auth()->id() == $user->id ? 'disabled' : '' }} style="border-color: #e2e8f0;">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Chưa kích hoạt</option>
                        <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Bị khóa</option>
                    </select>
                    @if(auth()->id() == $user->id)
                        <input type="hidden" name="status" value="{{ $user->status }}">
                    @endif
                    @error('status')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label text-dark fw-medium" style="font-size: 0.825rem;">Ảnh đại diện</label>
                    <div class="text-center mb-2">
                        @if($user->avatar_url)
                            <img id="avatarPreview" src="{{ $user->avatar_formatted_url }}" alt="Preview" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                            <div id="avatarPlaceholder" class="rounded-circle bg-light border align-items-center justify-content-center mx-auto text-muted" style="width: 80px; height: 80px; font-size: 1.5rem; display: none;">
                                U
                            </div>
                        @else
                            <img id="avatarPreview" src="" alt="Preview" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover; display: none;">
                            <div id="avatarPlaceholder" class="rounded-circle bg-light border d-flex align-items-center justify-content-center mx-auto text-muted" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->display_name ?? $user->username ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <input type="file" class="form-control form-control-sm @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*" onchange="previewImage(this)" style="border-color: #e2e8f0;">
                    @error('avatar')
                        <div class="invalid-feedback" style="font-size: 0.75rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-light) !important;">
            <a href="{{ route('admin.users.index') }}" class="btn-minimal text-decoration-none">Hủy bỏ</a>
            <button type="submit" class="btn-minimal btn-minimal-primary">Cập nhật thông tin</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        var preview = document.getElementById('avatarPreview');
        var placeholder = document.getElementById('avatarPlaceholder');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
