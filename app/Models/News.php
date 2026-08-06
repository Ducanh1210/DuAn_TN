<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'type',
        'status',
        'author_id',
        'view_count',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: only published articles
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get Vietnamese label for type
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'news' => 'Tin tức',
            'event' => 'Sự kiện',
            'guide' => 'Cẩm nang',
            'announcement' => 'Thông báo',
            default => $this->type,
        };
    }

    /**
     * Get Vietnamese label for status
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Bản nháp',
            'published' => 'Đã xuất bản',
            'hidden' => 'Đã ẩn',
            default => $this->status,
        };
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return static::resolveMediaUrl($this->featured_image);
    }

    public static function resolveMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
