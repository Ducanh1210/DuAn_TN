<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model tiến độ nhiệm vụ của từng người dùng: số lần đã thực hiện, trạng thái
 * (đang làm / hoàn thành / đã nhận), thời điểm reset chu kỳ và meta chống đếm trùng.
 */
class UserMission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mission_id',
        'current_count',
        'status',
        'last_reset_at',
        'claimed_at',
        'meta',
    ];

    /** meta lưu JSON (vd danh sách địa điểm đã tính để chống đếm trùng). */
    protected $casts = [
        'last_reset_at' => 'datetime',
        'claimed_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Người dùng sở hữu tiến độ. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Nhiệm vụ tương ứng. */
    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
