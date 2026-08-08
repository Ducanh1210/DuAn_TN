<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * Controller xác thực phía người dùng: đăng nhập/đăng ký thường và đăng nhập qua Google (Socialite).
 */
class AuthController extends Controller
{
    /** Hiển thị form đăng nhập. */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /** Xử lý đăng nhập: kiểm tra thông tin, chặn tài khoản bị khóa và điều hướng theo vai trò. */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'status' => 'Tài khoản của bạn đã bị khóa hoặc không hoạt động.',
                ])->onlyInput('username');
            }

            if (in_array($user->role, ['admin', 'moderator'])) {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập thành công!');
            }

            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'username' => 'Tên đăng nhập hoặc mật khẩu không chính xác.',
        ])->onlyInput('username');
    }

    /** Hiển thị form đăng ký. */
    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    /** Xử lý đăng ký tài khoản mới và tự động đăng nhập. */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'display_name' => $validated['display_name'] ?? $validated['username'],
            'password_hash' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'active',
            'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($validated['display_name'] ?? $validated['username']) . '&background=0072FF&color=fff',
        ]);

        // Tự động đăng nhập ngay sau khi đăng ký
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công!');
    }

    /** Đăng xuất và hủy phiên đăng nhập. */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }

    /** Chuyển hướng người dùng sang trang đăng nhập Google. */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý callback từ Google: nếu email đã tồn tại thì đăng nhập (và bổ sung thông tin provider),
     * ngược lại tạo tài khoản mới rồi đăng nhập.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Kiểm tra người dùng đã tồn tại theo email chưa
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Tài khoản đăng ký thường trước đó -> bổ sung thông tin provider Google
                if (!$user->provider) {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $googleUser->getId(),
                    ]);
                }
                
                if ($user->status !== 'active') {
                    return redirect()->route('login')->withErrors([
                        'status' => 'Tài khoản của bạn đã bị khóa hoặc không hoạt động.',
                    ]);
                }

                Auth::login($user);
                if (in_array($user->role, ['admin', 'moderator'])) {
                    return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập thành công!');
                }
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }

            // Tạo mật khẩu ngẫu nhiên vì cột password_hash không cho phép null
            $randomPassword = Str::random(24);

            // Đăng ký người dùng mới từ thông tin Google
            $newUser = User::create([
                'username' => 'user_' . uniqid(),
                'email' => $googleUser->getEmail(),
                'display_name' => $googleUser->getName(),
                'password_hash' => Hash::make($randomPassword),
                'role' => 'user',
                'status' => 'active',
                'avatar_url' => $googleUser->getAvatar(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
            ]);

            Auth::login($newUser);
            return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công qua Google!');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.',
            ]);
        }
    }
}
