<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model lịch sử đổi thưởng: ghi lại việc người dùng đã dùng bao nhiêu xu để đổi phần thưởng nào.
 */
class UserRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'points_spent',
        'status',
    ];

    /** Người dùng đã đổi thưởng. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Phần thưởng được đổi. */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
