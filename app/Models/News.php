<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model tin tức / bài viết (tin, sự kiện, cẩm nang, thông báo). Có tác giả, lượt xem,
 * trạng thái xuất bản và helper chuẩn hóa URL ảnh dùng chung.
 */
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

    /** Tác giả bài viết. */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** Scope: chỉ lấy bài đã xuất bản. */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Accessor: nhãn loại bài viết tiếng Việt. */
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

    /** Accessor: nhãn trạng thái tiếng Việt. */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Bản nháp',
            'published' => 'Đã xuất bản',
            'hidden' => 'Đã ẩn',
            default => $this->status,
        };
    }

    /** Accessor: URL ảnh nổi bật đã chuẩn hóa. */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return static::resolveMediaUrl($this->featured_image);
    }

    /**
     * Chuẩn hóa đường dẫn media thành URL đầy đủ: giữ nguyên URL http(s),
     * còn lại thì gắn tiền tố storage/ cho ảnh lưu nội bộ.
     */
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
