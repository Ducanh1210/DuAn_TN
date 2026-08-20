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

/**
 * Dịch vụ hệ thống nhiệm vụ & khung avatar (gamification):
 * - Cập nhật tiến độ nhiệm vụ theo hành động của người dùng.
 * - Nhận thưởng nhiệm vụ, xử lý điểm danh theo chuỗi ngày.
 * - Mở khóa / trang bị khung avatar theo thành tích và theo hạng điểm.
 */
class MissionService
{
    /**
     * Cập nhật tiến độ nhiệm vụ ứng với một "action key" cụ thể.
     *
     * @param User $user
     * @param string $actionKey Mã hành động (comment, favorite, daily_login...)
     * @param int $value Giá trị cộng thêm (hoặc giá trị tuyệt đối nếu $isAbsolute)
     * @param bool $isAbsolute Gán thẳng tiến độ thay vì cộng dồn
     * @param mixed $entityId ID đối tượng liên quan để chống đếm trùng (vd cùng 1 địa điểm)
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

            // Reset tiến độ theo chu kỳ: nhiệm vụ ngày reset mỗi ngày, nhiệm vụ tuần reset mỗi tuần
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

            // Đã nhận thưởng trong chu kỳ này thì bỏ qua
            if ($userMission->status === 'claimed') {
                continue;
            }

            // Nếu có entityId: kiểm tra trùng để không đếm lặp cùng một đối tượng (vd cùng địa điểm)
            if ($entityId !== null) {
                $visitedEntities = $meta['visited_entities'] ?? [];
                if (in_array((string)$entityId, $visitedEntities, true)) {
                    // Đối tượng này đã được tính cho nhiệm vụ trong chu kỳ hiện tại
                    continue;
                }
                $visitedEntities[] = (string)$entityId;
                $meta['visited_entities'] = $visitedEntities;
            }

            $userMission->meta = $meta;

            // Gán tiến độ: tuyệt đối hoặc cộng dồn, không vượt quá mục tiêu
            if ($isAbsolute) {
                $userMission->current_count = min($mission->target_count, max(0, $value));
            } else {
                $userMission->current_count = min($mission->target_count, $userMission->current_count + $value);
            }

            // Kiểm tra hoàn thành — nhiệm vụ chỉ theo dõi tiến độ (0 xu, không khung) thì tự nhận luôn
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
     * Nhận thưởng cho một nhiệm vụ đã hoàn thành (cộng xu và/hoặc mở khóa khung avatar).
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

            // Chỉ cộng xu khi nhiệm vụ là nguồn thưởng duy nhất của hành động đó (tránh cộng trùng)
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
     * Xử lý điểm danh hằng ngày: cập nhật chuỗi ngày liên tiếp, cộng xu theo chu kỳ
     * và trao khung avatar khi đạt chuỗi 7 ngày.
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
            // Cập nhật chuỗi ngày: điểm danh hôm qua thì +1, ngắt quãng thì về 1
            if ($user->last_streak_at && Carbon::parse($user->last_streak_at)->isSameDay($yesterday)) {
                $user->streak_count = ($user->streak_count ?? 0) + 1;
            } else {
                $user->streak_count = 1;
            }

            $user->last_streak_at = $today;
            $user->last_daily_bonus_at = Carbon::now();
            $user->save();

            // Chu kỳ chuỗi ngày 1–7 → 10, 20, … 70 (nguồn cộng xu duy nhất; nhiệm vụ daily_login = 0 xu)
            $totalPoints = PointService::checkinPointsForStreakDay((int) $user->streak_count);

            PointService::awardPoints($user, $totalPoints, 'daily_login', 'Điểm danh hàng ngày (Chuỗi ' . $user->streak_count . ' ngày)');

            self::trackProgress($user, 'daily_login', 1);
            self::trackProgress($user, 'streak_7', $user->streak_count, true);

            // Trao khung avatar khi đạt chuỗi 7 ngày
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
     * Mở khóa một khung avatar cho người dùng (nếu chưa sở hữu).
     *
     * @return bool True nếu vừa mở khóa mới, false nếu đã sở hữu từ trước
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

            // Không tự trang bị: để người dùng tự chọn khung trong kho đồ của họ
            return true;
        }
        return false;
    }

    /**
     * Trang bị hoặc tháo khung avatar. Truyền $frameId = null để tháo khung hiện tại.
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

    public static function purchaseFrame(User $user, int $frameId)
    {
        $frame = AvatarFrame::where('status', 'active')->find($frameId);
        if (!$frame) {
            return ['success' => false, 'message' => 'Khung avatar không tồn tại.'];
        }

        if (in_array($frame->code, ['frame-bronze', 'frame-silver', 'frame-diamond', 'frame-streak'], true)) {
            return ['success' => false, 'message' => 'Khung này là phần thưởng tiến độ/điểm danh, không thể đổi thêm.'];
        }

        $alreadyOwned = UserAvatarFrame::where('user_id', $user->id)
            ->where('avatar_frame_id', $frame->id)
            ->exists();

        if ($alreadyOwned) {
            return ['success' => false, 'message' => 'Bạn đã sở hữu khung avatar này rồi.'];
        }

        $cost = max(0, (int) $frame->required_points);

        if ((int) $user->points < $cost) {
            return ['success' => false, 'message' => 'Bạn không đủ xu để đổi khung này.'];
        }

        return DB::transaction(function () use ($user, $frame, $cost) {
            if ($cost > 0) {
                $user->decrement('points', $cost);
            }
            self::unlockFrame($user, $frame->id);

            return [
                'success' => true,
                'message' => 'Đổi thành công khung avatar: ' . $frame->name . '.',
                'points' => $user->fresh()->points,
                'frame' => [
                    'id' => $frame->id,
                    'name' => $frame->name,
                    'image_url' => $frame->image_url ? asset($frame->image_url) : '',
                    'css_style' => $frame->css_style,
                ],
            ];
        });
    }

    /**
     * @deprecated Không dùng tự mở khóa theo số xu đang có — khung shop phải bấm đổi, khung mốc/nhiệm vụ nhận riêng.
     */
    public static function checkRankFramesUnlocked(User $user)
    {
        return;
    }
}
