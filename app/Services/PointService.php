<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointService
{
    /** Instant awards for valuable actions (missions for these keys grant 0 xu). */
    public const POINTS_COMMENT = 5;
    public const POINTS_FAVORITE = 2;

    /** Daily check-in: day 1..7 of streak cycle → 10, 20, … 70 */
    public const CHECKIN_BASE = 10;

    /**
     * Award points to a user and unlock rank frames when thresholds are met.
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
            if ($amount !== 0) {
                $user->increment('points', $amount);
            }

            $tx = PointTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'action' => $action,
                'description' => $description,
            ]);

            if ($amount > 0) {
                MissionService::checkRankFramesUnlocked($user->fresh());
            }

            return $tx;
        });
    }

    /**
     * Check-in reward for the given streak day within a 7-day cycle (1–7).
     */
    public static function checkinPointsForStreakDay(int $streakCount): int
    {
        $dayCycle = (($streakCount - 1) % 7) + 1;

        return $dayCycle * self::CHECKIN_BASE;
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
