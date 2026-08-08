<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model lịch trình du lịch do AI tạo. Lưu tiêu đề, số ngày, chi phí ước tính,
 * payload JSON (toàn bộ lịch trình) và câu trả lời khảo sát để tái tạo.
 */
class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'total_days',
        'description',
        'summary',
        'estimated_cost',
        'payload',
        'answers',
        'share_token',
        'is_public',
    ];

    protected $casts = [
        'payload' => 'array',
        'answers' => 'array',
        'is_public' => 'boolean',
    ];

    /** Người sở hữu lịch trình. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Các ngày trong lịch trình, sắp theo thứ tự ngày. */
    public function days()
    {
        return $this->hasMany(ItineraryDay::class)->orderBy('day_number');
    }
}
