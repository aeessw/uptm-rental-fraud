<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $primaryKey = 'message_id';
    public const CREATED_AT = 'message_created_at';
    public const UPDATED_AT = 'message_updated_at';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'listing_id',
        'message_content',
    ];

    protected function casts(): array
    {
        return ['message_read_at' => 'datetime'];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id', 'listing_id');
    }
}