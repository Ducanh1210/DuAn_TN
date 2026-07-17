<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PointService;
use Illuminate\Support\Facades\Auth;

class CheckDailyBonus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $awarded = PointService::checkDailyLoginBonus($user);
            if ($awarded) {
                // Flash message to user
                session()->flash('success_points', 'Chúc mừng! Bạn đã nhận được +10 điểm cho hoạt động điểm danh hằng ngày!');
            }
        }

        return $next($request);
    }
}
