<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'description',
        'category_suggest',
        'lat',
        'lng',
        'images',
        'status',
        'reject_reason',
        'admin_note',
        'processed_by',
        'processed_at',
        'created_location_id',
    ];

    protected $casts = [
        'images' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function createdLocation()
    {
        return $this->belongsTo(Location::class, 'created_location_id');
    }
}
