<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserRedemption;
use Illuminate\Support\Facades\DB;

/**
 * Dịch vụ đổi thưởng: kiểm tra điều kiện, trừ xu của người dùng và ghi nhận
 * lượt đổi. Toàn bộ thao tác trừ xu/ghi lịch sử/giảm tồn kho chạy trong
 * transaction + khóa dòng phần thưởng để tránh đổi trùng khi nhiều request cùng lúc.
 */
class RewardService
{
    /**
     * Thực hiện đổi một phần thưởng cho người dùng.
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function redeem(User $user, Reward $reward): array
    {
        // Kiểm tra nhanh trước khi vào transaction để phản hồi lỗi sớm
        if (!$reward->isAvailable()) {
            return ['success' => false, 'message' => 'Phần thưởng này hiện không khả dụng.'];
        }

        if ($user->points < $reward->cost_points) {
            return ['success' => false, 'message' => 'Bạn không đủ xu để đổi phần thưởng này.'];
        }

        // Huy hiệu / phần thưởng độc quyền chỉ được đổi một lần
        $alreadyRedeemed = UserRedemption::where('user_id', $user->id)
            ->where('reward_id', $reward->id)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyRedeemed && in_array($reward->category, ['badge', 'exclusive'], true)) {
            return ['success' => false, 'message' => 'Bạn đã đổi phần thưởng này rồi.'];
        }

        return DB::transaction(function () use ($user, $reward) {
            // Khóa dòng phần thưởng để kiểm tra tồn kho chính xác, tránh race condition
            $lockedReward = Reward::whereKey($reward->id)->lockForUpdate()->first();

            if (!$lockedReward || !$lockedReward->isAvailable()) {
                return ['success' => false, 'message' => 'Phần thưởng vừa hết hàng.'];
            }

            // Đọc lại số xu mới nhất rồi kiểm tra lần nữa trong transaction
            $user->refresh();
            if ($user->points < $lockedReward->cost_points) {
                return ['success' => false, 'message' => 'Bạn không đủ xu để đổi phần thưởng này.'];
            }

            $user->decrement('points', $lockedReward->cost_points);

            PointTransaction::create([
                'user_id' => $user->id,
                'amount' => -$lockedReward->cost_points,
                'action' => 'reward_redeem_' . $lockedReward->slug,
                'description' => 'Đổi thưởng: ' . $lockedReward->title,
            ]);

            UserRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $lockedReward->id,
                'points_spent' => $lockedReward->cost_points,
                'status' => 'completed',
            ]);

            // Giảm tồn kho nếu phần thưởng có giới hạn số lượng (null = không giới hạn)
            if ($lockedReward->stock !== null) {
                $lockedReward->decrement('stock');
            }

            return [
                'success' => true,
                'message' => 'Đổi thưởng thành công!',
                'points' => $user->fresh()->points,
                'reward_title' => $lockedReward->title,
            ];
        });
    }
}
