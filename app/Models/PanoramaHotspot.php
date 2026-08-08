<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model điểm nóng (hotspot) trên cảnh panorama 360°: có thể là điểm chuyển sang cảnh khác
 * hoặc điểm thông tin. Lưu vị trí (yaw/pitch), loại, nội dung và liên kết.
 */
class PanoramaHotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'panorama_id',
        'target_panorama_id',
        'target_yaw',
        'target_pitch',
        'title',
        'hotspot_type',
        'yaw',
        'pitch',
        'scale',
        'content',
        'link_url',
    ];

    /** Cảnh panorama chứa hotspot này. */
    public function panorama()
    {
        return $this->belongsTo(Panorama::class, 'panorama_id');
    }

    /** Cảnh panorama đích khi bấm vào hotspot (nếu là hotspot chuyển cảnh). */
    public function targetPanorama()
    {
        return $this->belongsTo(Panorama::class, 'target_panorama_id');
    }
}
