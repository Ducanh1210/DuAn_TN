<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware kiểm tra trạng thái tài khoản người dùng real-time.
 * Nếu tài khoản đang đăng nhập nhưng bị Admin khóa (status != 'active'),
 * tự động đăng xuất và chuyển hướng về trang đăng nhập kèm thông báo.
 */
class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Tài khoản của bạn đã bị khóa hoặc không còn hoạt động.',
                    'need_login' => true,
                    'redirect' => route('login'),
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'status' => 'Tài khoản của bạn đã bị khóa hoặc không còn hoạt động.',
            ]);
        }

        return $next($request);
    }
}
