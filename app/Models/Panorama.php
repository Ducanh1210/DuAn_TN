<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model cảnh panorama 360° của một địa điểm. Lưu ảnh, âm thanh, góc nhìn ban đầu
 * (yaw/pitch/fov) và cờ cảnh mặc định.
 */
class Panorama extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'scene_name',
        'image_url',
        'audio_url',
        'sort_order',
        'is_default',
        'status',
        'initial_yaw',
        'initial_pitch',
        'initial_fov',
    ];

    /** Địa điểm chứa cảnh panorama. */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /** Các điểm nóng (hotspot) đặt trên cảnh panorama. */
    public function hotspots()
    {
        return $this->hasMany(PanoramaHotspot::class);
    }
}
