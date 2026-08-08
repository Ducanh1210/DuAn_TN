<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanoramaServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'contact_name',
        'phone',
        'place_name',
        'place_type',
        'scene_estimate',
        'note',
        'status',
        'admin_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function placeTypeLabels(): array
    {
        return [
            'homestay' => 'Homestay / khách sạn',
            'restaurant' => 'Nhà hàng / quán ăn',
            'attraction' => 'Điểm tham quan',
            'other' => 'Khác',
        ];
    }

    public static function sceneEstimateLabels(): array
    {
        return [
            '1-2' => '1–2 góc',
            '3-5' => '3–5 góc',
            '6+' => 'Từ 6 góc trở lên',
            'unsure' => 'Chưa chắc — cần tư vấn',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            'pending' => 'Chờ liên hệ',
            'contacted' => 'Đã liên hệ',
            'done' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
    }
}
