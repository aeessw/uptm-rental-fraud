<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentNotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('listing_notifications')->where('listing_notifications.user_id', $request->user()->getKey());
        $count = (clone $query)->whereNull('notification_read_at')->count();
        $query->join('listings', 'listings.listing_id', '=', 'listing_notifications.listing_id')
            ->select('listing_notifications.*', 'listings.listing_title')
            ->orderByDesc('listing_notifications.notification_id');

        if ($request->expectsJson()) {
            $items = $query->whereNull('notification_read_at')->limit(50)->get()->map(fn ($item) => [
                'key' => (string) $item->notification_id,
                'title' => $item->notification_decision === 'rejected' ? 'Your listing was not approved' : 'Your listing has been approved',
                'description' => $item->listing_title,
                'reason' => $item->notification_decision === 'rejected' ? $item->notification_reason : null,
                'time_ago' => \Illuminate\Support\Carbon::parse($item->notification_created_at)->diffForHumans(),
                'url' => route('student.listings.show', $item->listing_id), 'created_at' => $item->notification_created_at,
            ]);
            return response()->json(compact('count', 'items'))->header('Cache-Control', 'no-store');
        }

        $notifications = $query->paginate(15);
        return response()->view('student.notifications', compact('notifications', 'count'))->header('Cache-Control', 'no-store');
    }

    public function read(Request $request)
    {
        $request->validate(['key' => ['nullable', 'required_if:open,1', 'integer', 'min:1'], 'open' => ['nullable', 'boolean']]);
        $query = DB::table('listing_notifications')->where('user_id', $request->user()->getKey())
            ->when($request->filled('key'), fn ($query) => $query->where('notification_id', $request->input('key')));
        $notification = $request->boolean('open') ? (clone $query)->first() : null;
        if ($request->boolean('open')) abort_unless($notification, 404);
        $query->whereNull('notification_read_at')->update(['notification_read_at' => now()]);
        if ($notification) return redirect()->route('student.listings.show', $notification->listing_id);
        if ($request->expectsJson()) return response()->json(['ok' => true]);
        return redirect()->route('student.notifications')->with('success', 'Notifications marked as read.');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'keys' => ['required', 'array', 'min:1', 'max:100'],
            'keys.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        DB::table('listing_notifications')->where('user_id', $request->user()->getKey())
            ->whereIn('notification_id', $data['keys'])->delete();
        return redirect()->route('student.notifications')->with('success', 'Selected notifications removed.');
    }
}
