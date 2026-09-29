<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPhoto extends Model
{
    use HasFactory;

    protected $primaryKey = 'photo_id';
    public const CREATED_AT = 'photo_created_at';
    public const UPDATED_AT = 'photo_updated_at';

    protected $fillable = [
        'listing_id',
        'photo_path',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id', 'listing_id');
    }
}