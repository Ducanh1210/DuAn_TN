<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function rewardFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'reward_frame_id');
    }

    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }
}
