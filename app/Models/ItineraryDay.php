<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model một ngày trong lịch trình. Gồm số thứ tự ngày, ghi chú và danh sách hoạt động.
 */
class ItineraryDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_id',
        'day_number',
        'notes',
    ];

    /** Lịch trình chứa ngày này. */
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    /** Các hoạt động trong ngày, sắp theo thứ tự. */
    public function items()
    {
        return $this->hasMany(ItineraryItem::class, 'day_id')->orderBy('order_index');
    }
}
