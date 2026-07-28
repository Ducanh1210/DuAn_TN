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

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /**
     * Handle login logic.
     */
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

    /**
     * Show the register form.
     */
    public function showRegisterForm()
    {
        return view('client.auth.register');
    }

    /**
     * Handle register logic.
     */
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

        // Automatically log in after registration
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công!');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists but registered normally, update provider info
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

            // Generate a random password since password_hash is not nullable
            $randomPassword = Str::random(24);

            // Register new user
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
