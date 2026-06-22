<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hà Nam POI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0072FF;
            --primary-hover: #005ce6;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background: url('https://images.unsplash.com/photo-1596422846543-75c6fc197f07?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1;
        }

        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .auth-logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .auth-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 114, 255, 0.1);
            background: #fff;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 114, 255, 0.3);
        }

        .auth-links {
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: #fff;
            color: #3c4043;
            border: 1px solid #dadce0;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            margin-top: 15px;
        }

        .btn-google img {
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        .btn-google:hover {
            background: #f8f9fa;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
        }

        .divider {
            margin: 20px 0;
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-muted);
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .divider span {
            padding: 0 10px;
            font-size: 13px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="auth-overlay"></div>
    <div class="auth-container">
        <a href="<?php echo e(route('home')); ?>" class="auth-logo">
            <i class="fa-solid fa-map-location-dot"></i>
            Hà Nam POI
        </a>
        <div class="auth-subtitle">Đăng nhập để khám phá các địa điểm thú vị</div>

        <?php if($errors->any()): ?>
            <div class="alert-error">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên đăng nhập..." value="<?php echo e(old('username')); ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit" class="btn-submit">Đăng nhập</button>
        </form>

        <div class="divider">
            <span>HOẶC</span>
        </div>

        <a href="<?php echo e(route('client.login.google')); ?>" class="btn-google">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo">
            Tiếp tục với Google
        </a>

        <div class="auth-links">
            Chưa có tài khoản? <a href="<?php echo e(route('register')); ?>">Đăng ký ngay</a>
        </div>
        <div class="auth-links" style="margin-top: 10px;">
            <a href="<?php echo e(route('home')); ?>" style="color: var(--text-muted); font-weight: normal;"><i class="fa-solid fa-arrow-left"></i> Quay lại bản đồ</a>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\laragon\www\datnv2\DuAn_TN\resources\views/client/auth/login.blade.php ENDPATH**/ ?>