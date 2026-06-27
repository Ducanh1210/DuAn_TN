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
        // 'password_hash' => 'hashed', // Disable default hashing casting since we use custom MD5
    ];

    /**
     * Get the locations favorited by the user.
     */
    public function favorites()
    {
        return $this->belongsToMany(Location::class, 'favorites', 'user_id', 'location_id')
                    ->withPivot('created_at');
    }

    /**
     * Get the comments created by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }
}
