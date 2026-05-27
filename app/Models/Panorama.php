<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function hotspots()
    {
        return $this->hasMany(PanoramaHotspot::class);
    }
}
