<?php

namespace App\Http\Controllers;

use App\Helpers\EncryptionHelper;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEND MESSAGE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        $currentUserId = Auth::id();
        $receiverId = (int) $request->receiver_id;

        /*
        |--------------------------------------------------------------------------
        | Prevent messaging yourself
        |--------------------------------------------------------------------------
        */

        if ($receiverId === (int) $currentUserId) {

            return back()->with(
                'error',
                'You cannot message yourself.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Encrypt and save message
        |--------------------------------------------------------------------------
        */

        Message::create([
            'sender_id' => $currentUserId,
            'receiver_id' => $receiverId,
            'message' => EncryptionHelper::encrypt(
                $request->message
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return to SAME conversation
        |--------------------------------------------------------------------------
        |
        | No "Message sent." message.
        |
        */

        return redirect()->route('student.messages', [
            'userId' => $receiverId
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW SPECIFIC CONVERSATION
    |--------------------------------------------------------------------------
    */

    public function index($userId)
    {
        $currentUserId = Auth::id();
        $userId = (int) $userId;

        /*
        |--------------------------------------------------------------------------
        | Cannot chat with yourself
        |--------------------------------------------------------------------------
        */

        if ($userId === (int) $currentUserId) {

            return redirect()
                ->route('student.message.inbox')
                ->with(
                    'error',
                    'You cannot open your own chat.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Find selected user
        |--------------------------------------------------------------------------
        */

        $user = User::find($userId);

        if (!$user) {

            return redirect()
                ->route('student.message.inbox')
                ->with(
                    'error',
                    'The selected user could not be found.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | GET ALL MESSAGES INVOLVING CURRENT USER
        |--------------------------------------------------------------------------
        */

        $allMessages = Message::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET OTHER USER IDS
        |--------------------------------------------------------------------------
        */

        $conversationUserIds = $allMessages
            ->map(function ($message) use ($currentUserId) {

                if ((int) $message->sender_id === (int) $currentUserId) {
                    return $message->receiver_id;
                }

                return $message->sender_id;
            })
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | GET USERS FOR INBOX
        |--------------------------------------------------------------------------
        */

        $users = User::whereIn(
            'id',
            $conversationUserIds
        )
        ->get();

        /*
        |--------------------------------------------------------------------------
        | GET ONLY SELECTED CONVERSATION
        |--------------------------------------------------------------------------
        */

        $messages = Message::where(function ($query) use (
            $currentUserId,
            $userId
        ) {

            $query->where('sender_id', $currentUserId)
                ->where('receiver_id', $userId);

        })
        ->orWhere(function ($query) use (
            $currentUserId,
            $userId
        ) {

            $query->where('sender_id', $userId)
                ->where('receiver_id', $currentUserId);

        })
        ->orderBy('created_at', 'asc')
        ->get();

        /*
        |--------------------------------------------------------------------------
        | DECRYPT MESSAGES
        |--------------------------------------------------------------------------
        */

        $messages->transform(function ($message) {

            $message->message =
                EncryptionHelper::decrypt($message->message);

            return $message;
        });

        /*
        |--------------------------------------------------------------------------
        | SHOW SAME MESSAGES PAGE
        |--------------------------------------------------------------------------
        */

        return view('student.messages', [
            'users' => $users,
            'user' => $user,
            'messages' => $messages,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | INBOX
    |--------------------------------------------------------------------------
    */

    public function inbox()
    {
        $currentUserId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | GET ALL MESSAGES INVOLVING CURRENT USER
        |--------------------------------------------------------------------------
        */

        $allMessages = Message::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->orderBy('created_at', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET OTHER USER IDS
        |--------------------------------------------------------------------------
        */

        $conversationUserIds = $allMessages
            ->map(function ($message) use ($currentUserId) {

                if ((int) $message->sender_id === (int) $currentUserId) {
                    return $message->receiver_id;
                }

                return $message->sender_id;
            })
            ->filter()
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | GET USERS
        |--------------------------------------------------------------------------
        */

        $users = User::whereIn(
            'id',
            $conversationUserIds
        )
        ->get();

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | DO NOT SELECT FIRST USER
        |--------------------------------------------------------------------------
        |
        | /student/messages should open with:
        |
        | LEFT  = Inbox
        | RIGHT = No conversation selected
        |
        | A conversation is only selected after clicking a user.
        |
        */

        $user = null;

        /*
        |--------------------------------------------------------------------------
        | No messages are loaded here
        |--------------------------------------------------------------------------
        |
        | Messages are only loaded by index($userId)
        | after the user clicks a conversation.
        |
        */

        $messages = collect();

        /*
        |--------------------------------------------------------------------------
        | SHOW MESSAGE PAGE
        |--------------------------------------------------------------------------
        */

        return view('student.messages', [
            'users' => $users,
            'user' => $user,
            'messages' => $messages,
        ]);
    }
}