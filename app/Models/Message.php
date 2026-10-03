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

    public function scopeVisibleTo($query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where(function ($sent) use ($userId) {
                $sent->where('sender_id', $userId)->whereNull('sender_deleted_at');
            })->orWhere(function ($received) use ($userId) {
                $received->where('receiver_id', $userId)->whereNull('receiver_deleted_at');
            });
        });
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