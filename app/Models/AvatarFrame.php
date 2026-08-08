<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model khung avatar (trang trí ảnh đại diện). Có nhiều loại: theo hạng điểm,
 * theo thành tích hoặc trong cửa hàng; kèm style CSS/ảnh và ngưỡng điểm mở khóa.
 */
class AvatarFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'required_points',
        'css_style',
        'image_url',
        'icon',
        'status',
    ];

    /** Những người dùng đã sở hữu khung này. */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_avatar_frames')
                    ->withPivot('is_equipped', 'unlocked_at')
                    ->withTimestamps();
    }
}
