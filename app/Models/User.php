<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model người dùng: tài khoản đăng nhập, điểm tích lũy (xu), khung avatar đang trang bị,
 * chuỗi điểm danh và các quan hệ tới yêu thích, bình luận, hồ sơ doanh nghiệp, lịch trình...
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Các trường được phép gán hàng loạt (mass assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'display_name',
        'password_hash',
        'avatar_url',
        'role',
        'status',
        'provider',
        'provider_id',
        'points',
        'equipped_frame_id',
        'streak_count',
        'last_streak_at',
        'last_daily_bonus_at',
    ];

    /** Laravel Auth dùng cột password_hash làm mật khẩu thay vì cột 'password' mặc định. */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Các trường bị ẩn khi chuyển model thành mảng/JSON (bảo mật).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu cho các trường tương ứng.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_daily_bonus_at' => 'datetime',
        'last_streak_at' => 'date',
    ];

    /**
     * Accessor trả về URL avatar đã chuẩn hóa: dùng ảnh đã tải lên nếu có,
     * còn không thì tạo ảnh chữ cái mặc định từ ui-avatars.
     */
    public function getAvatarFormattedUrlAttribute()
    {
        $name = $this->display_name ?? $this->username ?? 'User';
        $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0072FF&color=fff';

        if (empty($this->avatar_url)) {
            return $fallback;
        }

        if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
            return $this->avatar_url;
        }

        $cleanPath = ltrim($this->avatar_url, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }

    /** Khung avatar đang được trang bị. */
    public function equippedFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'equipped_frame_id');
    }

    /** Tất cả khung avatar mà người dùng đã mở khóa (quan hệ nhiều-nhiều). */
    public function avatarFrames()
    {
        return $this->belongsToMany(AvatarFrame::class, 'user_avatar_frames')
                    ->withPivot('is_equipped', 'unlocked_at')
                    ->withTimestamps();
    }

    /** Tiến độ nhiệm vụ của người dùng. */
    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }

    /** Lịch sử giao dịch điểm (xu), mới nhất trước. */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->orderBy('created_at', 'desc');
    }

    /** Các địa điểm được yêu thích (quan hệ nhiều-nhiều qua bảng favorite_locations). */
    public function favorites()
    {
        return $this->belongsToMany(Location::class, 'favorite_locations', 'user_id', 'location_id')
                    ->withPivot('created_at');
    }

    /** Các bản ghi yêu thích (quan hệ một-nhiều). */
    public function favoriteLocations()
    {
        return $this->hasMany(FavoriteLocation::class);
    }

    /** Các bình luận do người dùng tạo, mới nhất trước. */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /** Hồ sơ doanh nghiệp gắn với người dùng (nếu có). */
    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /** Các báo cáo vi phạm do người dùng gửi. */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /** Lịch trình du lịch (AI) đã lưu. */
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderByDesc('created_at');
    }
}
