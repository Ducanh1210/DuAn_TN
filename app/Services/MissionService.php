<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mission;
use App\Models\UserMission;
use App\Models\AvatarFrame;
use App\Models\UserAvatarFrame;
use App\Services\PointService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MissionService
{
    /**
     * Track user progress for a specific mission action key.
     *
     * @param User $user
     * @param string $actionKey
     * @param int $increment
     * @return void
     */
    public static function trackProgress(User $user, string $actionKey, int $value = 1, bool $isAbsolute = false, $entityId = null)
    {
        if (!$user) return;

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        $missions = Mission::where('status', 'active')
            ->where('action_key', $actionKey)
            ->get();

        foreach ($missions as $mission) {
            $userMission = UserMission::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'mission_id' => $mission->id,
                ],
                [
                    'current_count' => 0,
                    'status' => 'in_progress',
                    'last_reset_at' => Carbon::now(),
                    'meta' => [],
                ]
            );

            $meta = $userMission->meta ?? [];

            // Handle periodic resets
            if ($userMission->last_reset_at) {
                $lastReset = Carbon::parse($userMission->last_reset_at);

                if ($mission->type === 'daily' && !$lastReset->isToday()) {
                    $userMission->current_count = 0;
                    $userMission->status = 'in_progress';
                    $userMission->last_reset_at = Carbon::now();
                    $userMission->claimed_at = null;
                    $meta = [];
                } elseif ($mission->type === 'weekly' && $lastReset->lt($startOfWeek)) {
                    $userMission->current_count = 0;
                    $userMission->status = 'in_progress';
                    $userMission->last_reset_at = Carbon::now();
                    $userMission->claimed_at = null;
                    $meta = [];
                }
            }

            // If already claimed or completed in this period, skip
            if ($userMission->status === 'claimed') {
                continue;
            }

            // If entityId is specified, check uniqueness to prevent counting duplicate locations
            if ($entityId !== null) {
                $visitedEntities = $meta['visited_entities'] ?? [];
                if (in_array((string)$entityId, $visitedEntities, true)) {
                    // Already counted this location/entity for this mission in current period
                    continue;
                }
                $visitedEntities[] = (string)$entityId;
                $meta['visited_entities'] = $visitedEntities;
            }

            $userMission->meta = $meta;

            // Progress assignment
            if ($isAbsolute) {
                $userMission->current_count = min($mission->target_count, max(0, $value));
            } else {
                $userMission->current_count = min($mission->target_count, $userMission->current_count + $value);
            }

            // Check completion — auto-claim progress-only missions (0 xu, no frame)
            if ($userMission->current_count >= $mission->target_count) {
                if ((int) $mission->reward_points === 0 && empty($mission->reward_frame_id)) {
                    $userMission->status = 'claimed';
                    $userMission->claimed_at = Carbon::now();
                } else {
                    $userMission->status = 'completed';
                }
            } else {
                $userMission->status = 'in_progress';
            }

            $userMission->save();
        }
    }

    /**
     * Claim reward for a completed mission.
     *
     * @param User $user
     * @param int $missionId
     * @return array ['success' => bool, 'message' => string, 'points' => int]
     */
    public static function claimReward(User $user, int $missionId)
    {
        $mission = Mission::find($missionId);
        if (!$mission) {
            return ['success' => false, 'message' => 'Nhiệm vụ không tồn tại.'];
        }

        $userMission = UserMission::where('user_id', $user->id)
            ->where('mission_id', $mission->id)
            ->first();

        if (!$userMission || $userMission->status !== 'completed') {
            return ['success' => false, 'message' => 'Nhiệm vụ chưa hoàn thành.'];
        }

        return DB::transaction(function () use ($user, $mission, $userMission) {
            $userMission->status = 'claimed';
            $userMission->claimed_at = Carbon::now();
            $userMission->save();

            // Award points only when mission is the sole reward source for that action
            if ($mission->reward_points > 0) {
                PointService::awardPoints($user, $mission->reward_points, 'mission_reward', 'Phần thưởng nhiệm vụ: ' . $mission->title);
            }

            $unlockedFrameName = null;
            if ($mission->reward_frame_id) {
                $frame = AvatarFrame::find($mission->reward_frame_id);
                if ($frame) {
                    self::unlockFrame($user, $frame->id);
                    $unlockedFrameName = $frame->name;
                }
            }

            $msg = $mission->reward_points > 0
                ? 'Nhận thành công +' . $mission->reward_points . ' xu thưởng!'
                : 'Đã hoàn thành nhiệm vụ!';
            if ($unlockedFrameName) {
                $msg .= ' Mở khóa Khung Avatar: ' . $unlockedFrameName;
            }

            return ['success' => true, 'message' => $msg, 'points' => $user->fresh()->points];
        });
    }

    /**
     * Process daily login streak and check-in bonus.
     *
     * @param User $user
     * @return array
     */
    public static function processDailyCheckin(User $user)
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        if ($user->last_daily_bonus_at && Carbon::parse($user->last_daily_bonus_at)->isToday()) {
            return ['success' => false, 'message' => 'Bạn đã điểm danh hôm nay rồi!'];
        }

        return DB::transaction(function () use ($user, $today, $yesterday) {
            // Update Streak
            if ($user->last_streak_at && Carbon::parse($user->last_streak_at)->isSameDay($yesterday)) {
                $user->streak_count = ($user->streak_count ?? 0) + 1;
            } else {
                $user->streak_count = 1;
            }

            $user->last_streak_at = $today;
            $user->last_daily_bonus_at = Carbon::now();
            $user->save();

            // Streak cycle Day 1–7 → 10, 20, … 70 (single source; daily_login mission = 0 xu)
            $totalPoints = PointService::checkinPointsForStreakDay((int) $user->streak_count);

            PointService::awardPoints($user, $totalPoints, 'daily_login', 'Điểm danh hàng ngày (Chuỗi ' . $user->streak_count . ' ngày)');

            self::trackProgress($user, 'daily_login', 1);
            self::trackProgress($user, 'streak_7', $user->streak_count, true);

            // Award frame on Day 7 streak
            $streakFrameMsg = "";
            if ($user->streak_count >= 7) {
                $streakFrame = AvatarFrame::where('status', 'active')
                    ->where(function($q) {
                        $q->where('code', 'frame-streak')
                          ->orWhere('name', 'like', '%Duy Trì%')
                          ->orWhere('name', 'like', '%Chăm Chỉ%')
                          ->orWhere('name', 'like', '%Chuỗi%');
                    })->first();

                if (!$streakFrame) {
                    $streakFrame = AvatarFrame::where('code', 'frame-streak')->first();
                }

                if ($streakFrame && self::unlockFrame($user, $streakFrame->id)) {
                    $streakFrameMsg = " 🎉 Đã nhận phần thưởng Khung Avatar: " . $streakFrame->name;
                }
            }

            return [
                'success' => true,
                'message' => "Điểm danh thành công!",
                'coins' => $totalPoints,
                'streak' => $user->streak_count,
                'points' => $user->points,
                'frame' => (isset($streakFrame) && $streakFrame) ? [
                    'id' => $streakFrame->id,
                    'name' => $streakFrame->name,
                    'image_url' => asset($streakFrame->image_url),
                    'css_style' => $streakFrame->css_style
                ] : null
            ];
        });
    }

    /**
     * Unlock an avatar frame for user.
     *
     * @param User $user
     * @param int $frameId
     * @return bool
     */
    public static function unlockFrame(User $user, int $frameId)
    {
        $exists = UserAvatarFrame::where('user_id', $user->id)
            ->where('avatar_frame_id', $frameId)
            ->exists();

        if (!$exists) {
            UserAvatarFrame::create([
                'user_id' => $user->id,
                'avatar_frame_id' => $frameId,
                'is_equipped' => false,
                'unlocked_at' => Carbon::now(),
            ]);

            // Auto-equip removed to let users manually choose to equip frames in their inventory
            return true;
        }
        return false;
    }

    /**
     * Equip or unequip an avatar frame.
     *
     * @param User $user
     * @param int|null $frameId
     * @return array
     */
    public static function equipFrame(User $user, ?int $frameId)
    {
        if ($frameId === null) {
            $user->equipped_frame_id = null;
            $user->save();
            UserAvatarFrame::where('user_id', $user->id)->update(['is_equipped' => false]);
            return ['success' => true, 'message' => 'Đã tháo khung avatar.'];
        }

        $owned = UserAvatarFrame::where('user_id', $user->id)
            ->where('avatar_frame_id', $frameId)
            ->exists();

        if (!$owned) {
            return ['success' => false, 'message' => 'Bạn chưa sở hữu khung avatar này.'];
        }

        UserAvatarFrame::where('user_id', $user->id)->update(['is_equipped' => false]);
        UserAvatarFrame::where('user_id', $user->id)
            ->where('avatar_frame_id', $frameId)
            ->update(['is_equipped' => true]);

        $user->equipped_frame_id = $frameId;
        $user->save();

        $frame = AvatarFrame::find($frameId);
        return ['success' => true, 'message' => 'Đã trang bị khung avatar: ' . ($frame ? $frame->name : '')];
    }

    /**
     * Buy avatar frame with points.
     *
     * @param User $user
     * @param int $frameId
     * @return array
     */
    public static function purchaseFrame(User $user, int $frameId)
    {
        return ['success' => false, 'message' => 'Khung Avatar là phần thưởng thành tích, không thể dùng xu để đổi.'];
    }

    /**
     * Check and unlock rank frames based on total user points automatically.
     *
     * @param User $user
     * @return void
     */
    public static function checkRankFramesUnlocked(User $user)
    {
        $rankFrames = AvatarFrame::where('status', 'active')
            ->where('type', 'rank')
            ->where('required_points', '<=', $user->points)
            ->get();

        foreach ($rankFrames as $frame) {
            self::unlockFrame($user, $frame->id);
        }
    }
}
