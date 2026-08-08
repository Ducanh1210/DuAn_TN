<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model một hoạt động (slot) trong ngày của lịch trình: khung giờ, loại (tham quan/ăn/nghỉ...),
 * địa điểm liên quan, mô tả và mẹo.
 */
class ItineraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'location_id',
        'order_index',
        'estimated_time',
        'note',
        'activity',
        'slot_type',
        'time_label',
        'location_label',
        'tip',
    ];

    /** Ngày chứa hoạt động này. */
    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'day_id');
    }

    /** Địa điểm gắn với hoạt động (nếu có). */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
