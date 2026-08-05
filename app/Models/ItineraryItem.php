<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'day_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
