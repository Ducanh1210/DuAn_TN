<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'target_type',
        'target_id',
        'content',
        'status',
        'admin_response',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Nếu target_type là 'location', 'news', 'event', 'comment', có thể lấy model tương ứng
    public function target()
    {
        if (!$this->target_type || !$this->target_id) {
            return null;
        }

        $modelClass = match($this->target_type) {
            'location' => Location::class,
            'news' => News::class,
            'event' => Event::class,
            'comment' => Comment::class,
            default => null,
        };

        if ($modelClass) {
            return $this->belongsTo($modelClass, 'target_id');
        }

        return null;
    }
}
