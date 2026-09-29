<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';
    public const CREATED_AT = 'report_created_at';
    public const UPDATED_AT = 'report_updated_at';

    protected $fillable = [
        'listing_id',
        'user_id',
        'report_reason',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id', 'listing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}