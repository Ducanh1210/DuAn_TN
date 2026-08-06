<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Location extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (Location $location) {
            // Dọn pivot/bảng cũ để user-side không còn tham chiếu tới địa điểm đã xóa.
            DB::table('favorite_locations')->where('location_id', $location->id)->delete();
            if (Schema::hasTable('favorites')) {
                DB::table('favorites')->where('location_id', $location->id)->delete();
            }

            // Hạ trạng thái hồ sơ doanh nghiệp nếu địa điểm của họ bị admin gỡ.
            if ($location->created_by) {
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

    protected $casts = [
        'attributes' => 'array',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'average_rating' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(LocationImage::class)->orderBy('sort_order', 'asc');
    }

    public function panoramas()
    {
        return $this->hasMany(Panorama::class)->orderBy('sort_order', 'asc');
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteLocation::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('status', 'visible')->orderBy('created_at', 'desc');
    }

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

    public function hasPanorama(): bool
    {
        if ($this->relationLoaded('panoramas')) {
            return $this->panoramas->isNotEmpty();
        }

        return $this->panoramas()->exists();
    }
}
