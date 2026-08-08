<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model phần thưởng đổi bằng xu (huy hiệu, vật phẩm, ưu đãi...).
 * Quản lý số xu cần đổi, tồn kho và trạng thái khả dụng.
 */
class Reward extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'cost_points',
        'image',
        'stock',
        'status',
    ];

    /** Các lượt đổi phần thưởng này. */
    public function redemptions(): HasMany
    {
        return $this->hasMany(UserRedemption::class);
    }

    /** Scope: chỉ lấy phần thưởng đang bật. */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Còn có thể đổi không: phải đang bật và còn tồn kho (stock null = không giới hạn). */
    public function isAvailable(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->stock === null) {
            return true;
        }

        return $this->stock > 0;
    }
}
