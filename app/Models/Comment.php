<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model bình luận / đánh giá địa điểm. Hỗ trợ trả lời lồng nhau (parent_id) và
 * lưu kết quả kiểm duyệt bằng AI (cờ, điểm rủi ro, lý do, thời điểm quét).
 */
class Comment extends Model
{
    use HasFactory;

    /** Các trường được phép gán hàng loạt (gồm cả trường kết quả kiểm duyệt AI). */
    protected $fillable = [
        'user_id',
        'location_id',
        'parent_id',
        'rating',
        'content',
        'status',
        'ai_flag',
        'ai_score',
        'ai_reason',
        'ai_checked_at',
    ];

    protected $casts = [
        'ai_checked_at' => 'datetime',
        'ai_score' => 'integer',
    ];

    /** Người viết bình luận. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Địa điểm được bình luận. */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /** Bình luận cha (nếu đây là câu trả lời). */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /** Các câu trả lời đang hiển thị cho bình luận này. */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', 'visible')
            ->orderBy('created_at', 'asc');
    }
}
