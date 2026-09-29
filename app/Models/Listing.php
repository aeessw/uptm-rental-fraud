<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $primaryKey = 'listing_id';
    public const CREATED_AT = 'listing_created_at';
    public const UPDATED_AT = 'listing_updated_at';

    public const FACILITIES = [
        'wifi' => 'WiFi', 'air_conditioning' => 'Air Conditioning',
        'washing_machine' => 'Washing Machine', 'study_table' => 'Study Table',
        'wardrobe' => 'Wardrobe', 'private_bathroom' => 'Private Bathroom',
        'parking' => 'Parking', 'kitchen' => 'Kitchen',
    ];
    public const RENTAL_PERIODS = ['short_term' => 'Short Term', 'long_term' => 'Long Term', 'flexible' => 'Flexible'];
    public const TENANT_PREFERENCES = ['any' => 'Any', 'male' => 'Male', 'female' => 'Female'];

    protected $casts = ['available_from' => 'date', 'facilities' => 'array', 'pax' => 'integer'];

    protected $fillable = [
        'user_id',
        'listing_title',
        'listing_description',
        'listing_location',
        'listing_rent',
        'room_type',
        'pax',
        'available_from',
        'rental_period',
        'preferred_tenant',
        'facilities',
        'listing_photo',
        'listing_status',
        'listing_availability',
        'report_count',
    ];

    public function scopeVisibleTo($query, User $viewer)
    {
        return $query
            ->whereNotIn('listings.user_id', \Illuminate\Support\Facades\DB::table('user_blocks')
                ->select('blocked_id')->where('blocker_id', $viewer->getKey()))
            ->whereNotIn('listings.user_id', \Illuminate\Support\Facades\DB::table('user_blocks')
                ->select('blocker_id')->where('blocked_id', $viewer->getKey()));
    }

    // Relationship to user who posted
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship to additional photos
    public function photos()
    {
        return $this->hasMany(ListingPhoto::class, 'listing_id', 'listing_id');
    }

    // Relationship to reports
    public function reports()
    {
        return $this->hasMany(Report::class, 'listing_id', 'listing_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'listing_saves', 'listing_id', 'user_id', 'listing_id', 'user_id')->using(ListingSave::class)->withTimestamps('save_created_at', 'save_updated_at');
    }
}