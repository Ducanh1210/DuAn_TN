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
        if ($user->last_daily_bonus_at && Carbon::parse($user->last_daily_bonus_at)->isToday()) {
            return false;
        }

        $result = MissionService::processDailyCheckin($user);
        return $result['success'] ?? false;
    }
}
