<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Dịch vụ quản lý điểm (xu) của người dùng: cộng/trừ điểm, ghi lịch sử giao dịch,
 * tính thưởng điểm danh theo chuỗi ngày và tổng hợp lịch sử để hiển thị.
 */
class PointService
{
    /** Thưởng ngay cho hành động có giá trị (nhiệm vụ ứng với các key này cộng 0 xu để tránh cộng trùng). */
    public const POINTS_COMMENT = 5;
    public const POINTS_FAVORITE = 2;

    /** Điểm danh hằng ngày: ngày 1..7 trong chu kỳ chuỗi → 10, 20, … 70 */
    public const CHECKIN_BASE = 10;

    /**
     * Cộng điểm cho người dùng và tự mở khóa khung avatar theo hạng khi đạt ngưỡng.
     * Chạy trong transaction để đảm bảo cộng điểm và ghi giao dịch luôn đồng bộ.
     *
     * @param User $user
     * @param int $amount Số điểm (có thể âm khi trừ)
     * @param string $action Mã hành động (comment, favorite, checkin...)
     * @param string|null $description Mô tả hiển thị trong lịch sử
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

            return $tx;
        });
    }

    /**
     * Tính điểm thưởng điểm danh cho ngày thứ mấy trong chu kỳ 7 ngày (1–7).
     * Ví dụ: chuỗi ngày 8 tương ứng ngày 1 của chu kỳ mới → 10 điểm.
     */
    public static function checkinPointsForStreakDay(int $streakCount): int
    {
        $dayCycle = (($streakCount - 1) % 7) + 1;

        return $dayCycle * self::CHECKIN_BASE;
    }

    /**
     * Kiểm tra và cộng điểm đăng nhập hằng ngày (điểm danh).
     *
     * @param User $user
     * @return bool True nếu đã cộng điểm, false nếu hôm nay đã điểm danh rồi
     */
    public static function checkDailyLoginBonus(User $user)
    {
        // Đã nhận thưởng trong ngày hôm nay thì bỏ qua
        if ($user->last_daily_bonus_at && Carbon::parse($user->last_daily_bonus_at)->isToday()) {
            return false;
        }

        $result = MissionService::processDailyCheckin($user);
        return $result['success'] ?? false;
    }

    /**
     * Gộp các bản ghi điểm "active_session" (cộng theo từng phút online) thành
     * tóm tắt theo ngày để lịch sử điểm dễ đọc, tránh hiển thị hàng trăm dòng vụn.
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
            // Điểm thời gian online: gom chung theo ngày thay vì hiện từng phút
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

        // Sắp xếp toàn bộ lịch sử theo thời gian mới nhất lên đầu
        usort($pointHistory, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return [
            'history' => $pointHistory,
            'raw_total' => $rawPointTx->count(),
        ];
    }

    /** Chuyển mã hành động sang nhãn tiếng Việt để hiển thị trong lịch sử điểm. */
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
