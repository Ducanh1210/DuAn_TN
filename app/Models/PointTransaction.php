<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model giao dịch điểm (xu): mỗi bản ghi là một lần cộng/trừ điểm kèm hành động và mô tả.
 * Là nguồn dữ liệu cho lịch sử điểm của người dùng.
 */
class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'action',
        'description',
    ];

    /** Người dùng của giao dịch. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
