<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model bảng trung gian: khung avatar mà người dùng đã mở khóa, kèm cờ đang trang bị
 * và thời điểm mở khóa.
 */
class UserAvatarFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_frame_id',
        'is_equipped',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'is_equipped' => 'boolean',
    ];

    /** Người dùng sở hữu. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Khung avatar tương ứng. */
    public function frame()
    {
        return $this->belongsTo(AvatarFrame::class, 'avatar_frame_id');
    }
}
