<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_types',
        'category_id',
        'address_country',
        'address_street',
        'address_city',
        'address_province',
        'address_postal_code',
        'phone',
        'website',
        'lat',
        'lng',
        'receive_tips',
        'receive_surveys',
        'description',
        'menu_photos',
        'storefront_photos',
        'verification_photo',
        'verification_photos',
        'verification_lat',
        'verification_lng',
        'verification_time',
        'status',
        'reject_reason',
    ];

    protected $casts = [
        'business_types' => 'array',
        'menu_photos' => 'array',
        'storefront_photos' => 'array',
        'verification_photos' => 'array',
        'receive_tips' => 'boolean',
        'receive_surveys' => 'boolean',
    ];

    /**
     * Get the user that owns the business profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the business.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'created_by', 'user_id');
    }
}
