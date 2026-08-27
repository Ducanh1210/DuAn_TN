<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model hồ sơ doanh nghiệp: lưu thông tin đăng ký nâng cấp tài khoản lên doanh nghiệp
 * (thông tin cơ sở, ảnh, giấy tờ, dữ liệu xác minh vị trí) và trạng thái duyệt.
 */
class BusinessProfile extends Model
{
    use HasFactory;

    /** Các trường được phép gán hàng loạt. */
    protected $fillable = [
        'user_id',
        'location_id',
        'business_name',
        'business_types',
        'category_id',
        'address_country',
        'address_street',
        'address_city',
        'address_province',
        'address_postal_code',
        'phone',
        'public_phone',
        'zalo',
        'facebook',
        'website',
        'lat',
        'lng',
        'receive_tips',
        'receive_surveys',
        'description',
        'menu_photos',
        'storefront_photos',
        'avatar_photo',
        'business_documents',
        'verification_photo',
        'verification_photos',
        'verification_lat',
        'verification_lng',
        'verification_time',
        'status',
        'reject_reason',
    ];

    /** Tiền tố lý do khi Admin thu hồi quyền quản lý DN (giữ POI trên map). */
    public const BIZ_REVOKED_REASON_PREFIX = '[BUSINESS_REVOKED]';

    /** Các trường lưu dạng JSON (mảng) và cờ boolean. */
    protected $casts = [
        'business_types' => 'array',
        'menu_photos' => 'array',
        'storefront_photos' => 'array',
        'business_documents' => 'array',
        'verification_photos' => 'array',
        'receive_tips' => 'boolean',
        'receive_surveys' => 'boolean',
    ];

    /** Tài khoản người dùng sở hữu hồ sơ doanh nghiệp. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Danh mục ngành nghề của doanh nghiệp. */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Địa điểm trên bản đồ do chủ doanh nghiệp này tạo (sau khi duyệt). */
    public function location()
    {
        return $this->hasOne(Location::class, 'created_by', 'user_id');
    }

    /** Địa điểm đang yêu cầu nhận quyền (claim) — có thể khác created_by trước khi duyệt. */
    public function claimedLocation()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /** Địa điểm published chưa thuộc DN nào (có thể nhận quyền). */
    public static function isLocationClaimable(?Location $location): bool
    {
        if (!$location || $location->trashed() || $location->status !== 'published') {
            return false;
        }

        if (!$location->created_by) {
            return true;
        }

        $owner = User::find($location->created_by);
        if (!$owner) {
            return true;
        }

        if ($owner->role === 'business') {
            return false;
        }

        return !static::where('user_id', $owner->id)->where('status', 'approved')->exists();
    }
}
