<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model thông báo gửi tới người dùng (vd: địa điểm doanh nghiệp bị gỡ).
 * Có thể kèm liên kết và đánh dấu đã đọc qua trường read_at.
 */
class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /** Người nhận thông báo. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Scope: lọc các thông báo chưa đọc. */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
