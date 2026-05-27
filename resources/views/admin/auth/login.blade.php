<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); border: none; }
        .login-card h3 { font-weight: 700; color: #333; }
        .btn-primary { background-color: #007bff; border-color: #007bff; font-weight: 600; padding: 10px; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h3 class="text-center mb-4">Đăng Nhập Quản Trị</h3>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-bold">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>
