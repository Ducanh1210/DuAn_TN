<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model định nghĩa nhiệm vụ (nhiệm vụ ngày/tuần/thành tích). Xác định hành động cần theo dõi
 * (action_key), mục tiêu, phần thưởng (xu và/hoặc khung avatar).
 */
class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'action_key',
        'target_count',
        'reward_points',
        'reward_frame_id',
        'icon',
        'status',
    ];

    /** Khung avatar được thưởng khi hoàn thành (nếu có). */
    public function rewardFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'reward_frame_id');
    }

    /** Tiến độ của người dùng đối với nhiệm vụ này. */
    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }
}
