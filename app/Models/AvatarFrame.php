<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'required_points',
        'css_style',
        'image_url',
        'icon',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_avatar_frames')
                    ->withPivot('is_equipped', 'unlocked_at')
                    ->withTimestamps();
    }
}
