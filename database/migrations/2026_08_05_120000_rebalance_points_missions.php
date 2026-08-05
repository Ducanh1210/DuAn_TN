<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rebalance mission rewards so they don't double-dip with instant point awards.
     * Instant: check-in streak, comment +5, favorite +2.
     * Missions for those actions become progress-only (0 xu).
     * Online time: claim-only after 15 phút, +10 xu (no per-minute farm).
     */
    public function up(): void
    {
        $updates = [
            'favorite_location' => [
                'reward_points' => 0,
                'target_count' => 3,
                'description' => 'Lưu 3 địa điểm — mỗi lần lưu nhận ngay +2 xu (nhiệm vụ chỉ theo dõi tiến độ).',
            ],
            'write_comment' => [
                'reward_points' => 0,
                'target_count' => 1,
                'description' => 'Đăng 1 đánh giá — nhận ngay +5 xu (nhiệm vụ chỉ theo dõi tiến độ).',
            ],
            'active_session' => [
                'reward_points' => 10,
                'target_count' => 15,
                'title' => 'Hoạt động trên trang',
                'description' => 'Online đủ 15 phút trong ngày rồi nhận thưởng (không cộng xu từng phút).',
            ],
            'daily_login' => [
                'reward_points' => 0,
                'target_count' => 1,
                'description' => 'Điểm danh mỗi ngày nhận 10–70 xu theo chuỗi (không cộng thêm khi claim nhiệm vụ).',
            ],
        ];

        foreach ($updates as $actionKey => $data) {
            DB::table('missions')->where('action_key', $actionKey)->update($data);
        }

        // Rank frame thresholds: avoid two frames unlocking at the same 100 xu.
        DB::table('avatar_frames')->where('code', 'frame-silver')->update(['required_points' => 200]);
        DB::table('avatar_frames')->where('code', 'frame-gold')->update(['required_points' => 500]);
        DB::table('avatar_frames')->where('code', 'frame-diamond')->update(['required_points' => 1500]);
        DB::table('avatar_frames')->where('code', 'frame-bronze')->update(['required_points' => 100]);
    }

    public function down(): void
    {
        $reverts = [
            'daily_login' => ['reward_points' => 10, 'target_count' => 1],
            'favorite_location' => ['reward_points' => 15, 'target_count' => 3],
            'write_comment' => ['reward_points' => 20, 'target_count' => 1],
            'active_session' => [
                'reward_points' => 30,
                'target_count' => 60,
                'title' => 'Thời gian truy cập online',
                'description' => null,
            ],
        ];

        foreach ($reverts as $actionKey => $data) {
            DB::table('missions')->where('action_key', $actionKey)->update(array_filter($data, fn ($v) => $v !== null));
        }

        DB::table('avatar_frames')->where('code', 'frame-silver')->update(['required_points' => 100]);
        DB::table('avatar_frames')->where('code', 'frame-gold')->update(['required_points' => 500]);
        DB::table('avatar_frames')->where('code', 'frame-diamond')->update(['required_points' => 1500]);
        DB::table('avatar_frames')->where('code', 'frame-bronze')->update(['required_points' => 100]);
    }
};
