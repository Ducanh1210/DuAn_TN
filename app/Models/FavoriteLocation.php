<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model bảng trung gian lưu địa điểm yêu thích của người dùng.
 */
class FavoriteLocation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'location_id'];

    /** Người dùng đã thích. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Địa điểm được thích. */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
