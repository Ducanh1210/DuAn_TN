<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAvatarFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_frame_id',
        'is_equipped',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'is_equipped' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frame()
    {
        return $this->belongsTo(AvatarFrame::class, 'avatar_frame_id');
    }
}
