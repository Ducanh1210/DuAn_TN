<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'program',
        'location_text',
        'location_id',
        'start_time',
        'end_time',
        'featured_image',
        'is_featured',
        'status',
        'meta_title',
        'meta_description',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: only active events
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    /**
     * Get Vietnamese label for status
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Đang diễn ra',
            'cancelled' => 'Đã hủy',
            'expired' => 'Đã kết thúc',
            'hidden' => 'Đã ẩn',
            default => $this->status,
        };
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return News::resolveMediaUrl($this->featured_image);
    }

    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
