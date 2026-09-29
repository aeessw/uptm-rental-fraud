<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'audit_id';
    public const CREATED_AT = 'audit_created_at';
    public const UPDATED_AT = 'audit_updated_at';

    protected $fillable = [
        'user_id',
        'audit_action',
        'audit_target',
        'audit_hmac',
    ];

    public function integrityStatus(): string
    {
        $key = config('audit.hmac_key');
        if (!$key || !$this->audit_created_at) return 'Unavailable';
        $payload = $this->user_id.$this->audit_action.$this->audit_target.$this->audit_created_at->format('Y-m-d H:i:s');
        return hash_equals(hash_hmac('sha256', $payload, $key), (string) $this->audit_hmac) ? 'Valid' : 'Invalid';
    }

    public static function actionGroups(): array
    {
        return [
            'Authentication' => ['login', 'logout'],
            'Listing Activity' => ['viewed_listing', 'created_listing', 'updated_listing', 'updated_listing_availability', 'deleted_listing', 'saved_listing', 'unsaved_listing'],
            'Reports' => ['reported_listing'],
            'Messages' => ['sent_message', 'read_messages', 'read_all_messages', 'deleted_conversation'],
            'Account Activity' => ['blocked_user', 'unblocked_user'],
            'MPP Actions' => ['removed_listing', 'restored_listing', 'suspended_user', 'unsuspended_user', 'listing_auto_hidden', 'auto_suspended_user'],
        ];
    }

    public function category(): string
    {
        foreach (self::actionGroups() as $category => $actions) {
            if (in_array($this->audit_action, $actions, true)) return $category;
        }
        if (in_array($this->audit_action, ['login', 'logout', 'google_login', 'failed_login'])) return 'Authentication';
        if (in_array($this->audit_action, ['removed_listing', 'hidden_listing', 'restored_listing', 'suspended_user', 'unsuspended_user', 'listing_auto_hidden', 'auto_suspended_user'])) return 'MPP Actions';
        if (str_contains($this->audit_action, 'report')) return 'Reports';
        if (str_contains($this->audit_action, 'message')) return 'Messages';
        if (str_contains($this->audit_action, 'listing') || str_contains($this->audit_action, 'availability')) return 'Listing Activity';
        return 'Account Activity';
    }

    public function isSecurityEvent(): bool
    {
        return $this->integrityStatus() === 'Invalid' || in_array($this->audit_action, ['reported_listing', 'failed_login', 'removed_listing', 'hidden_listing', 'restored_listing', 'listing_auto_hidden', 'suspended_user', 'unsuspended_user', 'auto_suspended_user']);
    }

    public function listingId(): ?int
    {
        return preg_match('/Listing ID: ?([0-9]+)/i', (string) $this->audit_target, $matches) ? (int) $matches[1] : null;
    }

    public function displayDetails(): string
    {
        $id = $this->listingId();
        $listing = $this->relationLoaded('auditListing') ? $this->getRelation('auditListing') : null;
        $name = $listing ? '"'.$listing->listing_title.'" (Listing #'.$id.')' : ($id ? 'Listing #'.$id : 'a listing');
        preg_match('/User ID: ?([0-9]+)/i', (string) $this->audit_target, $userMatch);
        $user = isset($userMatch[1]) ? 'User #'.$userMatch[1] : 'a user';
        if ($this->audit_action === 'login') return 'Logged into the system';
        if ($this->audit_action === 'logout') return 'Logged out of the system';
        if ($this->audit_action === 'sent_message') return 'Sent a message to '.$user.($id ? ' regarding Listing #'.$id : '');
        if ($this->audit_action === 'read_messages') return 'Read messages from '.$user;
        if ($this->audit_action === 'read_all_messages') return 'Marked all messages as read';
        if ($this->audit_action === 'deleted_conversation') return 'Deleted conversation with '.$user;
        $verbs = ['viewed_listing' => 'Viewed', 'created_listing' => 'Created', 'updated_listing' => 'Updated', 'deleted_listing' => 'Deleted', 'saved_listing' => 'Saved', 'unsaved_listing' => 'Unsaved', 'reported_listing' => 'Reported', 'removed_listing' => 'MPP hid', 'hidden_listing' => 'MPP hid', 'restored_listing' => 'MPP restored', 'listing_auto_hidden' => 'System automatically hid'];
        if (isset($verbs[$this->audit_action])) return $verbs[$this->audit_action].' '.$name.(strstr((string) $this->audit_target, ' |') ?: '');
        if ($this->audit_action === 'updated_listing_availability') {
            preg_match('/availability: (.*)$/', (string) $this->audit_target, $availability);
            return 'Changed availability of '.$name.(isset($availability[1]) ? ': '.$availability[1] : '');
        }
        if (in_array($this->audit_action, ['suspended_user', 'unsuspended_user', 'auto_suspended_user'])) {
            $verb = $this->audit_action === 'unsuspended_user' ? 'MPP unsuspended' : ($this->audit_action === 'auto_suspended_user' ? 'System suspended' : 'MPP suspended');
            $suffix = strstr((string) $this->audit_target, ' |') ?: '';
            return $verb.' Student #'.($userMatch[1] ?? 'unknown').$suffix;
        }
        if ($this->audit_action === 'blocked_user') return 'Blocked '.$user;
        if ($this->audit_action === 'unblocked_user') return 'Unblocked '.$user;
        $label = ucfirst(str_replace('_', ' ', $this->audit_action));
        if ($this->category() === 'Messages') return $label.' (message content excluded)';
        return $label.($this->audit_target ? ': '.$this->audit_target : '');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
