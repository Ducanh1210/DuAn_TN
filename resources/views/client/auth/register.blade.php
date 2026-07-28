<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Ninh Bình Travel Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-form: #f1f5f9;
            --text-heading: #1e3a5f;
            --text-body: #3b5980;
            --text-muted: #6482a6;
            --border-color: #cbdbe8;
            --border-focus: #1e3a5f;
            --btn-primary: #1e3a5f;
            --btn-hover: #2b4c7e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            background-color: var(--bg-form);
            color: var(--text-body);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .split-container {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
        }

        /* Left Half: Form Panel */
        .split-form-side {
            flex: 1;
            min-height: 100vh;
            background-color: var(--bg-form);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 24px;
            position: relative;
            z-index: 1;
        }

        /* Right Half: Image Panel */
        .split-image-side {
            flex: 1;
            background: url('{{ asset('images/nen02.png') }}') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }

        .split-image-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 180px;
            background: linear-gradient(to right, var(--bg-form) 0%, rgba(241, 245, 249, 0) 100%);
            z-index: 2;
            pointer-events: none;
        }

        .top-back-link {
            position: absolute;
            top: 24px;
            left: 28px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .top-back-link:hover {
            color: var(--text-heading);
        }

        .auth-form-box {
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            margin-bottom: 20px;
            text-align: left;
        }

        .brand-title {
            font-size: 1.45rem;
            font-weight: 600;
            color: var(--text-heading);
            letter-spacing: -0.01em;
            display: block;
        }

        .brand-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 400;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .col-half {
            flex: 1;
        }

        .form-group {
            margin-bottom: 14px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 0.775rem;
            font-weight: 500;
            color: var(--text-heading);
            margin-bottom: 3px;
        }

        .required-star {
            color: #ef4444;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-control {
            width: 100%;
            padding: 7px 0;
            background-color: transparent;
            border: none;
            border-bottom: 1px solid var(--border-color);
            border-radius: 0;
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-heading);
            outline: none;
            transition: border-color 0.15s ease;
        }

        .form-control.has-right-icon {
            padding-right: 30px;
        }

        .form-control::placeholder {
            color: #94adca;
        }

        .form-control:focus {
            border-bottom: 2px solid var(--border-focus);
            box-shadow: none;
            background-color: transparent;
        }

        .toggle-password {
            position: absolute;
            right: 0;
            background: none;
            border: none;
            color: #94adca;
            cursor: pointer;
            font-size: 0.8125rem;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .toggle-password:hover {
            color: var(--text-heading);
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: var(--btn-primary);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s ease;
            margin-top: 6px;
        }

        .btn-submit:hover {
            background-color: var(--btn-hover);
        }

        .divider {
            margin: 14px 0;
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 10px;
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px;
            background-color: #ffffff;
            color: var(--text-heading);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.825rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-google svg {
            width: 15px;
            height: 15px;
        }

        .btn-google:hover {
            background-color: #e6eef5;
            border-color: #b5cce0;
        }

        .auth-footer {
            margin-top: 16px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--text-heading);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background-color: #fdf2f2;
            border: 1px solid #f8d7d7;
            color: #991b1b;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.775rem;
            margin-bottom: 14px;
            text-align: left;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 16px;
        }

        .field-error-msg {
            display: block;
            color: #dc2626;
            font-size: 0.725rem;
            font-weight: 500;
            margin-top: 4px;
            text-align: left;
            transition: all 0.15s ease;
        }

        .form-control.is-invalid {
            border-bottom: 2px solid #ef4444 !important;
        }

        /* Mobile Responsiveness */
        @media (max-width: 991.98px) {
            .split-container {
                flex-direction: column;
            }
            .split-image-side {
                display: none;
            }
            .split-form-side {
                width: 100%;
                padding: 50px 20px 30px;
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .top-back-link {
                top: 16px;
                left: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Form 50% -->
        <div class="split-form-side">
            <a href="{{ route('home') }}" class="top-back-link">
                <i class="fa-solid fa-arrow-left" style="font-size: 11px;"></i>
                <span>Quay lại bản đồ</span>
            </a>

            <div class="auth-form-box">
                <div class="brand-header">
                    <h1 class="brand-title">Đăng ký</h1>
                    <div class="brand-subtitle">Tạo tài khoản mới để trải nghiệm dịch vụ</div>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                    @csrf
                    
                    <!-- Row 1: Username & Email side-by-side -->
                    <div class="form-row">
                        <div class="form-group col-half">
                            <label for="username" class="form-label">Tên đăng nhập <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Tên đăng nhập" value="{{ old('username') }}" autofocus>
                            </div>
                            <span class="field-error-msg" id="usernameError">@error('username'){{ $message }}@enderror</span>
                        </div>
                        
                        <div class="form-group col-half">
                            <label for="email" class="form-label">Email <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email của bạn" value="{{ old('email') }}">
                            </div>
                            <span class="field-error-msg" id="emailError">@error('email'){{ $message }}@enderror</span>
                        </div>
                    </div>

                    <!-- Row 2: Display name -->
                    <div class="form-group">
                        <label for="display_name" class="form-label">Tên hiển thị (không bắt buộc)</label>
                        <div class="input-wrapper">
                            <input type="text" id="display_name" name="display_name" class="form-control @error('display_name') is-invalid @enderror" placeholder="Tên hiển thị trên bản đồ" value="{{ old('display_name') }}">
                        </div>
                        <span class="field-error-msg" id="displayNameError">@error('display_name'){{ $message }}@enderror</span>
                    </div>
                    
                    <!-- Row 3: Password & Confirm Password side-by-side -->
                    <div class="form-row">
                        <div class="form-group col-half">
                            <label for="password" class="form-label">Mật khẩu <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" class="form-control has-right-icon @error('password') is-invalid @enderror" placeholder="Tối thiểu 6 ký tự">
                                <button type="button" class="toggle-password" onclick="togglePassword('password', 'passwordIcon')">
                                    <i class="fa-regular fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            <span class="field-error-msg" id="passwordError">@error('password'){{ $message }}@enderror</span>
                        </div>

                        <div class="form-group col-half">
                            <label for="password_confirmation" class="form-label">Xác nhận <span class="required-star">*</span></label>
                            <div class="input-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control has-right-icon" placeholder="Nhập lại mật khẩu">
                                <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">
                                    <i class="fa-regular fa-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <span class="field-error-msg" id="passwordConfirmError"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Đăng ký tài khoản</button>
                </form>

                <div class="divider">
                    <span>hoặc</span>
                </div>

                <a href="{{ route('client.login.google') }}" class="btn-google">
                    <svg width="15" height="15" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Đăng ký bằng Google</span>
                </a>

                <div class="auth-footer">
                    <div>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
                </div>
            </div>
        </div>

        <!-- Right Image 50% -->
        <div class="split-image-side"></div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Inline Validation on Submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                let isValid = true;

                const username = document.getElementById('username');
                const usernameErr = document.getElementById('usernameError');
                if (!username.value.trim()) {
                    showError(username, usernameErr, 'Vui lòng nhập tên đăng nhập.');
                    isValid = false;
                }

                const email = document.getElementById('email');
                const emailErr = document.getElementById('emailError');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email.value.trim()) {
                    showError(email, emailErr, 'Vui lòng nhập địa chỉ email.');
                    isValid = false;
                } else if (!emailRegex.test(email.value.trim())) {
                    showError(email, emailErr, 'Email không đúng định dạng.');
                    isValid = false;
                }

                const password = document.getElementById('password');
                const passwordErr = document.getElementById('passwordError');
                if (!password.value) {
                    showError(password, passwordErr, 'Vui lòng nhập mật khẩu.');
                    isValid = false;
                } else if (password.value.length < 6) {
                    showError(password, passwordErr, 'Mật khẩu phải có tối thiểu 6 ký tự.');
                    isValid = false;
                }

                const confirmPassword = document.getElementById('password_confirmation');
                const confirmErr = document.getElementById('passwordConfirmError');
                if (!confirmPassword.value) {
                    showError(confirmPassword, confirmErr, 'Vui lòng xác nhận mật khẩu.');
                    isValid = false;
                } else if (confirmPassword.value !== password.value) {
                    showError(confirmPassword, confirmErr, 'Mật khẩu xác nhận không khớp.');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function showError(input, errorEl, message) {
                input.classList.add('is-invalid');
                if (errorEl) {
                    errorEl.textContent = message;
                }
            }

            function clearError(input, errorEl) {
                input.classList.remove('is-invalid');
                if (errorEl) {
                    errorEl.textContent = '';
                }
            }

            ['username', 'email', 'password', 'password_confirmation'].forEach(id => {
                const input = document.getElementById(id);
                const errorEl = document.getElementById(id === 'password_confirmation' ? 'passwordConfirmError' : id + 'Error');
                if (input) {
                    input.addEventListener('input', function() {
                        clearError(this, errorEl);
                    });
                }
            });
        });
    </script>
</body>
</html>
