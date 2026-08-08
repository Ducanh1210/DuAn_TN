<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model đề xuất địa điểm do người dùng đóng góp. Chờ admin duyệt; khi duyệt sẽ tạo ra
 * một Location thật (created_location_id). Lưu thông tin gợi ý, ảnh và trạng thái xử lý.
 */
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

    /** Người dùng gửi đề xuất. */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Người (admin) đã xử lý đề xuất. */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /** Địa điểm thật được tạo ra sau khi đề xuất được duyệt. */
    public function createdLocation()
    {
        return $this->belongsTo(Location::class, 'created_location_id');
    }
}
