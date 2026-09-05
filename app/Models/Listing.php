<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'rent',
        'room_type',
        'photo',
        'status',
        'report_count',
    ];

    // Relationship to user who posted
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to additional photos
    public function photos()
    {
        return $this->hasMany(ListingPhoto::class);
    }

    // Relationship to reports
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}