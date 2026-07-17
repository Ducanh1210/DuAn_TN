<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Award points to a user.
     *
     * @param User $user
     * @param int $amount
     * @param string $action
     * @param string|null $description
     * @return PointTransaction
     */
    public static function awardPoints(User $user, int $amount, string $action, string $description = null)
    {
        return DB::transaction(function () use ($user, $amount, $action, $description) {
            // Update user points
            $user->increment('points', $amount);

            // Log transaction
            return PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'action' => $action,
                'description' => $description,
            ]);
        });
    }

    /**
     * Check and award daily login points.
     *
     * @param User $user
     * @return bool True if points were awarded, false otherwise
     */
    public static function checkDailyLoginBonus(User $user)
    {
        $today = Carbon::today();
        
        // If they already got it today, skip
        if ($user->last_daily_bonus_at && Carbon::parse($user->last_daily_bonus_at)->isToday()) {
            return false;
        }

        DB::transaction(function () use ($user) {
            $user->last_daily_bonus_at = Carbon::now();
            $user->save();

            self::awardPoints($user, 10, 'daily_login', 'Điểm danh hằng ngày');
        });

        return true;
    }
}
