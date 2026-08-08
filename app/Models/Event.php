<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model sự kiện du lịch/lễ hội. Có thời gian bắt đầu/kết thúc, có thể gắn với một địa điểm,
 * kèm ảnh nổi bật và các scope/nhãn tiếng Việt hỗ trợ hiển thị.
 */
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

    /** Địa điểm tổ chức sự kiện (nếu có). */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /** Người tạo sự kiện. */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scope: chỉ lấy sự kiện đang diễn ra. */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Scope: chỉ lấy sự kiện sắp diễn ra. */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    /** Accessor: nhãn trạng thái tiếng Việt. */
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

    /** Accessor: URL ảnh nổi bật đã chuẩn hóa (dùng chung helper của News). */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return News::resolveMediaUrl($this->featured_image);
    }

    /** Accessor: cho phép dùng $event->title như một alias của tên sự kiện. */
    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
