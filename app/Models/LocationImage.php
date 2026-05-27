<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'image_url',
        'caption',
        'is_thumbnail',
        'sort_order',
        'uploaded_by',
        'status',
    ];

    protected $casts = [
        'is_thumbnail' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
