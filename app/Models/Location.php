<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model địa điểm du lịch (POI) - thực thể trung tâm của hệ thống.
 * Chứa thông tin mô tả, tọa độ GPS, danh mục, ảnh, panorama 360°, đánh giá...
 * và xử lý dọn dẹp dữ liệu liên quan khi địa điểm bị xóa.
 */
class Location extends Model
{
    use HasFactory;

    /** Đăng ký các sự kiện vòng đời của model. */
    protected static function booted(): void
    {
        // Khi xóa địa điểm: dọn dữ liệu liên quan và xử lý hồ sơ doanh nghiệp gắn với nó
        static::deleting(function (Location $location) {
            // Dọn pivot để user-side không còn tham chiếu tới địa điểm đã xóa.
            DB::table('favorite_locations')->where('location_id', $location->id)->delete();

            // Hạ trạng thái hồ sơ doanh nghiệp nếu địa điểm của họ bị admin gỡ.
            if ($location->created_by) {
                // Chỉ xử lý khi đây là địa điểm cuối cùng của chủ doanh nghiệp
                $otherLocations = static::where('created_by', $location->created_by)
                    ->where('id', '!=', $location->id)
                    ->exists();

                if (!$otherLocations) {
                    $profile = BusinessProfile::where('user_id', $location->created_by)
                        ->where('status', 'approved')
                        ->first();

                    if ($profile) {
                        $profile->update([
                            'status' => 'rejected',
                            'reject_reason' => 'Địa điểm đã bị gỡ khỏi hệ thống bởi quản trị viên. Bạn có thể đăng ký lại nếu cần.',
                        ]);

                        $user = User::find($location->created_by);
                        if ($user && $user->role === 'business') {
                            $user->update(['role' => 'user']);
                        }
                    }
                }
            }
        });
    }

    /** Các trường được phép gán hàng loạt. */
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
        'zalo',
        'facebook',
        'website_url',
        'thumbnail_url',
        'audio_url',
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

    /** Ép kiểu: attributes lưu JSON, tọa độ và điểm đánh giá là số thập phân. */
    protected $casts = [
        'attributes' => 'array',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'average_rating' => 'decimal:2',
    ];

    public function zaloUrl(): ?string
    {
        $zalo = trim((string) $this->zalo);
        if ($zalo === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $zalo)) {
            return preg_match('#^https?://(www\.)?zalo\.me/#i', $zalo) ? $zalo : null;
        }
        $digits = preg_replace('/\D+/', '', $zalo);
        return $digits !== '' ? 'https://zalo.me/' . $digits : null;
    }

    public function facebookUrl(): ?string
    {
        $fb = trim((string) $this->facebook);
        if ($fb === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $fb)) {
            return preg_match('#^https?://([a-z0-9-]+\.)?(facebook\.com|fb\.com)/#i', $fb) ? $fb : null;
        }
        return 'https://facebook.com/' . ltrim($fb, '@/');
    }

    /** Danh mục của địa điểm. */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Thư viện ảnh của địa điểm, sắp theo thứ tự hiển thị. */
    public function images()
    {
        return $this->hasMany(LocationImage::class)->orderBy('sort_order', 'asc');
    }

    /** Các cảnh panorama 360° của địa điểm. */
    public function panoramas()
    {
        return $this->hasMany(Panorama::class)->orderBy('sort_order', 'asc');
    }

    /** Lượt yêu thích của địa điểm. */
    public function favorites()
    {
        return $this->hasMany(FavoriteLocation::class);
    }

    /** Bình luận gốc đang hiển thị của địa điểm, mới nhất trước (không gồm trả lời). */
    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->where('status', 'visible')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Trả về URL ảnh đại diện: ưu tiên thumbnail_url, nếu trống thì lấy ảnh
     * được đánh dấu là thumbnail trong thư viện, cuối cùng lấy ảnh đầu tiên.
     */
    public function resolveThumbnailUrl(): ?string
    {
        if ($this->thumbnail_url) {
            return str_starts_with($this->thumbnail_url, 'http')
                ? $this->thumbnail_url
                : asset('storage/' . ltrim($this->thumbnail_url, '/'));
        }

        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();
        $thumbnail = $images->where('is_thumbnail', true)->first() ?? $images->first();

        if (!$thumbnail?->image_url) {
            return null;
        }

        return str_starts_with($thumbnail->image_url, 'http')
            ? $thumbnail->image_url
            : asset('storage/' . ltrim($thumbnail->image_url, '/'));
    }

    /** Trả về danh sách URL ảnh (kèm caption) đã chuẩn hóa đường dẫn đầy đủ. */
    public function resolveImageUrls(): array
    {
        $images = $this->relationLoaded('images') ? $this->images : $this->images()->get();

        return $images->map(function ($image) {
            $url = $image->image_url;
            if ($url && !str_starts_with($url, 'http')) {
                $url = asset('storage/' . ltrim($url, '/'));
            }
            return [
                'url' => $url,
                'caption' => $image->caption,
            ];
        })->filter(fn ($item) => !empty($item['url']))->values()->all();
    }

    /** Kiểm tra địa điểm có ít nhất một cảnh panorama 360° hay không. */
    public function hasPanorama(): bool
    {
        if ($this->relationLoaded('panoramas')) {
            return $this->panoramas->isNotEmpty();
        }

        return $this->panoramas()->exists();
    }
}
