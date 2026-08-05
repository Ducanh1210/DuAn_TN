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

    /**
     * Collapse per-minute active_session rows into daily summaries for readable history.
     *
     * @return array{history: array<int, array>, raw_total: int}
     */
    public static function aggregatedHistory(int $userId): array
    {
        $rawPointTx = PointTransaction::where('user_id', $userId)
            ->where('amount', '!=', 0)
            ->orderByDesc('created_at')
            ->get();

        $sessionBuckets = [];
        $pointHistory = [];

        foreach ($rawPointTx as $tx) {
            if ($tx->action === 'active_session') {
                $dayKey = $tx->created_at->format('Y-m-d');
                if (!isset($sessionBuckets[$dayKey])) {
                    $sessionBuckets[$dayKey] = [
                        'key' => 'session-' . $dayKey,
                        'action' => 'active_session',
                        'amount' => 0,
                        'count' => 0,
                        'created_at' => $tx->created_at,
                        'description' => '',
                        'aggregated' => true,
                    ];
                }
                $sessionBuckets[$dayKey]['amount'] += (int) $tx->amount;
                $sessionBuckets[$dayKey]['count']++;
                if ($tx->created_at->gt($sessionBuckets[$dayKey]['created_at'])) {
                    $sessionBuckets[$dayKey]['created_at'] = $tx->created_at;
                }
                continue;
            }

            $pointHistory[] = [
                'key' => 'tx-' . $tx->id,
                'action' => $tx->action,
                'amount' => (int) $tx->amount,
                'count' => 1,
                'created_at' => $tx->created_at,
                'description' => $tx->description,
                'aggregated' => false,
            ];
        }

        foreach ($sessionBuckets as $dayKey => $bucket) {
            $bucket['description'] = 'Online ' . $bucket['count'] . ' phút · gộp ' . $bucket['count'] . ' bản ghi · '
                . Carbon::parse($dayKey)->format('d/m/Y');
            $pointHistory[] = $bucket;
        }

        usort($pointHistory, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return [
            'history' => $pointHistory,
            'raw_total' => $rawPointTx->count(),
        ];
    }

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'active_session' => 'Thời gian online',
            'daily_login' => 'Điểm danh',
            'comment' => 'Bình luận',
            'favorite' => 'Yêu thích',
            'mission_reward' => 'Nhiệm vụ',
            'manual_adjust' => 'Admin chỉnh',
            'checkin' => 'Check-in',
            'milestone' => 'Mốc thưởng',
            default => $action,
        };
    }
}
