<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model yêu cầu dịch vụ chụp tour 360°. Lưu thông tin liên hệ và nhu cầu của khách
 * (kể cả khách chưa đăng nhập). Cung cấp các nhãn tiếng Việt cho loại địa điểm,
 * số lượng góc chụp và trạng thái xử lý.
 */
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

    /** Người dùng gửi yêu cầu (null nếu là khách chưa đăng nhập). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Nhãn tiếng Việt cho loại địa điểm. */
    public static function placeTypeLabels(): array
    {
        return [
            'homestay' => 'Homestay / khách sạn',
            'restaurant' => 'Nhà hàng / quán ăn',
            'attraction' => 'Điểm tham quan',
            'other' => 'Khác',
        ];
    }

    /** Nhãn tiếng Việt cho số lượng góc chụp ước tính. */
    public static function sceneEstimateLabels(): array
    {
        return [
            '1-2' => '1–2 góc',
            '3-5' => '3–5 góc',
            '6+' => 'Từ 6 góc trở lên',
            'unsure' => 'Chưa chắc — cần tư vấn',
        ];
    }

    /** Nhãn tiếng Việt cho trạng thái xử lý yêu cầu. */
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
