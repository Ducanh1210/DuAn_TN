<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanoramaHotspot extends Model
{
    use HasFactory;

    protected $fillable = [
        'panorama_id',
        'target_panorama_id',
        'title',
        'hotspot_type',
        'yaw',
        'pitch',
        'content',
        'link_url',
    ];

    public function panorama()
    {
        return $this->belongsTo(Panorama::class, 'panorama_id');
    }

    public function targetPanorama()
    {
        return $this->belongsTo(Panorama::class, 'target_panorama_id');
    }
}
