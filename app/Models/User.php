<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
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

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_daily_bonus_at' => 'datetime',
        'last_streak_at' => 'date',
    ];

    /**
     * Get properly formatted avatar URL.
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

    /**
     * Get the equipped avatar frame.
     */
    public function equippedFrame()
    {
        return $this->belongsTo(AvatarFrame::class, 'equipped_frame_id');
    }

    /**
     * Get all unlocked avatar frames for the user.
     */
    public function avatarFrames()
    {
        return $this->belongsToMany(AvatarFrame::class, 'user_avatar_frames')
                    ->withPivot('is_equipped', 'unlocked_at')
                    ->withTimestamps();
    }

    /**
     * Get user mission progress.
     */
    public function userMissions()
    {
        return $this->hasMany(UserMission::class);
    }

    /**
     * Get the point transactions for the user.
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the locations favorited by the user (Many-to-Many).
     */
    public function favorites()
    {
        return $this->belongsToMany(Location::class, 'favorite_locations', 'user_id', 'location_id')
                    ->withPivot('created_at');
    }

    /**
     * Get the favorite locations models (One-to-Many).
     */
    public function favoriteLocations()
    {
        return $this->hasMany(FavoriteLocation::class);
    }

    /**
     * Get the comments created by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the business profile associated with the user.
     */
    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    /**
     * Get the reports submitted by the user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Lịch trình AI đã lưu.
     */
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderByDesc('created_at');
    }
}
