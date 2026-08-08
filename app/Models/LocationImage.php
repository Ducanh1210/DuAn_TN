<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ảnh trong thư viện của địa điểm. Đánh dấu ảnh đại diện (is_thumbnail)
 * và thứ tự hiển thị (sort_order).
 */
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

    /** Địa điểm chứa ảnh. */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
