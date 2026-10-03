<?php

namespace App\Http\Controllers;

use App\Helpers\EncryptionHelper;
use App\Helpers\AuditLogger;
use App\Models\Message;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'listing_id' => 'nullable|exists:listings,listing_id',
            'message' => 'required|string|max:5000',
        ]);
        if ((int) $data['receiver_id'] === (int) $request->user()->getKey()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'You cannot message yourself.'], 422)
                : back()->with('error', 'You cannot message yourself.');
        }

        $recipient = User::findOrFail($data['receiver_id']);
        if ($request->user()->fresh()->user_suspended || $recipient->user_suspended) {
            $reason = 'This account has been suspended by MPP Admin.';
            return $request->expectsJson()
                ? response()->json(['message' => $reason], 403)
                : back()->with('error', $reason);
        }
        if ($request->user()->blockedUsers()->whereKey($recipient->getKey())->exists()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'You blocked this user. Unblock them to continue messaging.'], 422)
                : back()->with('error', 'You blocked this user. Unblock them to continue messaging.');
        }
        if ($request->user()->blockedByUsers()->whereKey($recipient->getKey())->exists()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Messaging is unavailable for this conversation.'], 422)
                : back()->with('error', 'Messaging is unavailable for this conversation.');
        }

        $message = Message::create([
            'sender_id' => $request->user()->getKey(),
            'receiver_id' => $data['receiver_id'],
            'listing_id' => $data['listing_id'] ?? null,
            'message_content' => EncryptionHelper::encrypt($data['message']),
        ]);
        AuditLogger::log($request->user()->getKey(), 'sent_message', 'Message ID: '.$message->getKey().' | User ID: '.$recipient->getKey().($message->listing_id ? ' | Listing ID: '.$message->listing_id : ''));
        if ($request->expectsJson()) {
            return response()->json(['html' => $this->conversationView($recipient)->getContent()], 201);
        }
        return redirect()->route('student.messages', array_filter([
            'userId' => $data['receiver_id'],
            'listing_id' => $data['listing_id'] ?? null,
        ]));
    }

    public function index($userId)
    {
        if ((int) $userId === (int) auth()->id()) {
            return redirect()->route('student.message.inbox');
        }
        return $this->conversationView(User::findOrFail($userId));
    }

    public function inbox()
    {
        return $this->conversationView();
    }

    private function conversationView(?User $user = null)
    {
        $id = auth()->id();

        if ($user && request()->filled('listing_id')) {
            $conversationListing = Listing::visibleTo(auth()->user())->whereKey(request('listing_id'))
                ->where(function ($query) use ($user, $id) {
                    $query->where('user_id', $user->getKey())->orWhere('user_id', $id);
                })
                ->first();

            if ($conversationListing) {
                Message::where(function ($query) use ($user, $id) {
                    $query->where(function ($thread) use ($id, $user) {
                        $thread->where('sender_id', $id)->where('receiver_id', $user->getKey());
                    })->orWhere(function ($thread) use ($id, $user) {
                        $thread->where('sender_id', $user->getKey())->where('receiver_id', $id);
                    });
                })->whereNull('listing_id')->update(['listing_id' => $conversationListing->getKey()]);
            }
        }

        $all = Message::with('listing')->visibleTo($id)
            ->orderBy('message_id')->get();
        $threads = $all->groupBy(fn ($message) => (int) $message->sender_id === (int) $id
            ? $message->receiver_id : $message->sender_id);
        $users = User::whereIn('user_id', $threads->keys())->get()->map(function ($contact) use ($threads, $id) {
            $thread = $threads->get($contact->getKey());
            $last = $thread->last();
            $contact->last_message = EncryptionHelper::decrypt($last->message_content);
            $contact->last_message_at = $last->message_created_at;
            $contact->last_message_id = $last->getKey();
            $contact->unread_count = $thread->filter(fn ($message) =>
                (int) $message->receiver_id === (int) $id && !$message->message_read_at)->count();
            return $contact;
        })->reject(fn ($contact) => auth()->user()->blockedUsers()->whereKey($contact->getKey())->exists())
            ->sortByDesc('last_message_id')->values();
        $messages = $user ? $threads->get($user->getKey(), collect()) : collect();
        $isSuspended = $user && ($user->user_suspended || auth()->user()->fresh()->user_suspended);
        $isBlocked = $user && auth()->user()->blockedUsers()->whereKey($user->getKey())->exists();
        $isBlockedBy = $user && auth()->user()->blockedByUsers()->whereKey($user->getKey())->exists();
        $messages->each(function ($message) {
            $message->message_content = EncryptionHelper::decrypt($message->message_content);
        });
        $conversationListing = $messages
            ->sortByDesc('message_id')
            ->first(fn ($message) => $message->listing_id !== null)?->listing;

        if (!$conversationListing && request('listing_id')) {
            $conversationListing = Listing::visibleTo(auth()->user())->find(request('listing_id'));
        }

        if ($isBlocked || $isBlockedBy) {
            $conversationListing = null;
        }

        $canViewListing = $conversationListing && $conversationListing->listing_status === 'active'
            && !$conversationListing->user?->user_suspended;

        return response()->view('student.messages', compact('users', 'user', 'messages', 'conversationListing', 'isBlocked', 'isBlockedBy', 'isSuspended', 'canViewListing'))
            ->header('Cache-Control', 'no-store');
    }

    public function block(Request $request, User $user)
    {
        abort_if($user->getKey() === $request->user()->getKey() || $user->user_role !== 'student', 403);
        $changes = $request->user()->blockedUsers()->syncWithoutDetaching([$user->getKey()]);
        if ($changes['attached']) AuditLogger::log($request->user()->getKey(), 'blocked_user', 'User ID: '.$user->getKey());
        return back()->with('success', 'User blocked. You can unblock them in Profile settings.');
    }

    public function unblockFromSettings(Request $request, User $user)
    {
        if ($request->user()->blockedUsers()->detach($user->getKey())) AuditLogger::log($request->user()->getKey(), 'unblocked_user', 'User ID: '.$user->getKey());
        return back()->with('success', 'User unblocked.');
    }

    public function unblock(Request $request, $userId)
    {
        if ($request->user()->blockedUsers()->detach($userId)) AuditLogger::log($request->user()->getKey(), 'unblocked_user', 'User ID: '.$userId);

        return redirect()->route('student.messages', ['userId' => $userId])
            ->with('success', 'User unblocked.');
    }

    public function deleteConversation(Request $request, $userId)
    {
        $id = $request->user()->getKey();
        $deleted = Message::where('sender_id', $id)->where('receiver_id', $userId)
            ->whereNull('sender_deleted_at')->update(['sender_deleted_at' => now()]);
        $deleted += Message::where('sender_id', $userId)->where('receiver_id', $id)
            ->whereNull('receiver_deleted_at')->update(['receiver_deleted_at' => now()]);

        if ($deleted) AuditLogger::log($id, 'deleted_conversation', 'Deleted for self only | User ID: '.$userId);
        return redirect()->route('student.message.inbox')->with('success', 'Conversation deleted for you only.');
    }

    public function unreadCount(Request $request)
    {
        $unreadMessageQuery = Message::visibleTo($request->user()->getKey())->where('receiver_id', $request->user()->getKey())
            ->whereNull('message_read_at');
        $unreadMessages = (clone $unreadMessageQuery)->count();
        $recentUnreadMessages = $unreadMessageQuery->with('sender:user_id,user_name')
            ->latest()
            ->take(5)
            ->get(['message_id', 'sender_id']);

        $newListings = Listing::visibleTo(auth()->user())->where('listing_status', 'active')
            ->where('user_id', '!=', $request->user()->getKey())
            ->where('listing_created_at', '>=', now()->subDay())
            ->latest()
            ->take(5)
            ->get(['listing_id', 'listing_title', 'listing_created_at']);

        return response()->json([
            'count' => $unreadMessages,
            'unread_messages' => $recentUnreadMessages->map(fn ($message) => [
                'sender' => $message->sender?->user_name ?? 'Someone',
            ])->values(),
            'new_listings_count' => $newListings->count(),
            'new_listings' => $newListings->map(fn ($listing) => [
                'id' => $listing->getKey(),
                'title' => $listing->listing_title,
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }

    public function markRead(Request $request, $userId)
    {
        $data = $request->validate(['through_id' => 'required|integer|min:1']);
        $read = Message::visibleTo($request->user()->getKey())->where('receiver_id', $request->user()->getKey())
            ->where('sender_id', $userId)->where('message_id', '<=', $data['through_id'])
            ->whereNull('message_read_at')->update(['message_read_at' => now()]);
        if ($read) AuditLogger::log($request->user()->getKey(), 'read_messages', 'User ID: '.$userId);
        return response()->noContent();
    }

    public function markAllRead(Request $request)
    {
        $read = Message::visibleTo($request->user()->getKey())->where('receiver_id', $request->user()->getKey())
            ->whereNull('message_read_at')
            ->update(['message_read_at' => now()]);

        if ($read) AuditLogger::log($request->user()->getKey(), 'read_all_messages');
        return response()->noContent();
    }
}