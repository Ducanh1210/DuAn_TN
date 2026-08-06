<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function redemptions(): HasMany
    {
        return $this->hasMany(UserRedemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

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
