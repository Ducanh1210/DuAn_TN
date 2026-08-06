<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserRedemption;
use Illuminate\Support\Facades\DB;

class RewardService
{
    public function redeem(User $user, Reward $reward): array
    {
        if (!$reward->isAvailable()) {
            return ['success' => false, 'message' => 'Phần thưởng này hiện không khả dụng.'];
        }

        if ($user->points < $reward->cost_points) {
            return ['success' => false, 'message' => 'Bạn không đủ xu để đổi phần thưởng này.'];
        }

        $alreadyRedeemed = UserRedemption::where('user_id', $user->id)
            ->where('reward_id', $reward->id)
            ->where('status', 'completed')
            ->exists();

        if ($alreadyRedeemed && in_array($reward->category, ['badge', 'exclusive'], true)) {
            return ['success' => false, 'message' => 'Bạn đã đổi phần thưởng này rồi.'];
        }

        return DB::transaction(function () use ($user, $reward) {
            $lockedReward = Reward::whereKey($reward->id)->lockForUpdate()->first();

            if (!$lockedReward || !$lockedReward->isAvailable()) {
                return ['success' => false, 'message' => 'Phần thưởng vừa hết hàng.'];
            }

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
