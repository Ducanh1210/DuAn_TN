<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model báo cáo vi phạm (report abuse). Dùng quan hệ đa hình (reportable) để báo cáo
 * nhiều loại đối tượng khác nhau (bình luận, địa điểm...), kèm người báo cáo và người xử lý.
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reportable_id',
        'reportable_type',
        'reason',
        'description',
        'status',
        'handled_by',
    ];

    /** Người gửi báo cáo. */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /** Người (admin) đã xử lý báo cáo. */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /** Đối tượng bị báo cáo (quan hệ đa hình). */
    public function reportable()
    {
        return $this->morphTo();
    }

    /** Các biến thể class name từng bị lưu (có/không dấu \ đầu). */
    public static function morphTypes(string $class): array
    {
        $normalized = ltrim($class, '\\');

        return array_values(array_unique([$normalized, '\\' . $normalized]));
    }
}
