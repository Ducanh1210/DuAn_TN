<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'detailed_history',
        'address',
        'ward',
        'district',
        'province',
        'lat',
        'lng',
        'opening_hours',
        'phone',
        'website_url',
        'thumbnail_url',
        'attributes',
        'average_rating',
        'review_count',
        'meta_title',
        'meta_description',
        'view_count',
        'source',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attributes' => 'array',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'average_rating' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(LocationImage::class)->orderBy('sort_order', 'asc');
    }

    public function panoramas()
    {
        return $this->hasMany(Panorama::class)->orderBy('sort_order', 'asc');
    }
}
