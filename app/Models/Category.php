<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model danh mục địa điểm (tâm linh, ẩm thực, sinh thái...). Dùng để phân loại
 * và lọc địa điểm; có icon, màu và thứ tự hiển thị.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'icon_color',
        'display_order',
        'status',
        'meta_title',
        'meta_description',
    ];

    /** Các địa điểm thuộc danh mục này. */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
