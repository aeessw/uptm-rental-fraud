<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public const CREATED_AT = 'user_created_at';
    public const UPDATED_AT = 'user_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'user_email',
        'password',
        'google_id',
        'user_role',
        'user_suspended',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_suspended' => 'boolean',
        ];
    }

    public function getEmailForPasswordReset()
    {
        return $this->user_email;
    }

    public function getEmailForVerification()
    {
        return $this->user_email;
    }

    public function routeNotificationForMail($notification)
    {
        return [$this->user_email => $this->user_name];
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'user_id', 'user_id');
    }

    public function auditActivity()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id', 'user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id');
    }

    public function savedListings()
    {
        return $this->belongsToMany(Listing::class, 'listing_saves', 'user_id', 'listing_id', 'user_id', 'listing_id')->using(ListingSave::class)->withTimestamps('save_created_at', 'save_updated_at');
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(self::class, 'user_blocks', 'blocker_id', 'blocked_id', 'user_id', 'user_id')->using(UserBlock::class)->withTimestamps('block_created_at', 'block_updated_at');
    }

    public function blockedByUsers()
    {
        return $this->belongsToMany(self::class, 'user_blocks', 'blocked_id', 'blocker_id', 'user_id', 'user_id')->using(UserBlock::class)->withTimestamps('block_created_at', 'block_updated_at');
    }

    public function receivedReports()
    {
        return $this->hasManyThrough(
            Report::class,
            Listing::class,
            'user_id',
            'listing_id',
            'user_id',
            'listing_id'
        );
    }
}
