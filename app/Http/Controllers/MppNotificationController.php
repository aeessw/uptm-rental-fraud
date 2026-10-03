<?php
namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MppNotificationController extends Controller
{
    private function unread(Request $request)
    {
        $read = DB::table('mpp_notification_reads')->where('user_id', $request->user()->getKey())->pluck('notification_read_key');
        $reports = Report::with('listing:listing_id,listing_title')->whereHas('listing')->get()->map(fn ($report) => [
            'key' => 'report:'.$report->getKey(),
            'title' => 'New fraud report',
            'description' => $report->listing->listing_title,
            'url' => route('mpp.reports', ['listing_id' => $report->listing_id]),
            'created_at' => $report->report_created_at?->toIso8601String(),
        ]);
        $risks = Listing::where('report_count', '>=', 3)->get()->map(fn ($listing) => [
            'key' => 'risk:'.$listing->getKey(),
            'title' => 'Listing reached 3 reports',
            'description' => $listing->listing_title,
            'url' => route('mpp.reports', ['listing_id' => $listing->getKey()]),
            'created_at' => $listing->listing_updated_at?->toIso8601String(),
        ]);
        $submissions = \App\Models\AuditLog::whereIn('audit_action', ['created_listing', 'updated_listing'])
            ->orderByDesc('audit_id')->get()->unique(fn ($log) => $log->listingId())->keyBy(fn ($log) => $log->listingId());
        $pending = Listing::where('review_status', 'pending')
            ->whereHas('user', fn ($query) => $query->where('user_suspended', false))
            ->get()->map(fn ($listing) => [
                'key' => 'approval:'.$listing->getKey().':'.($submissions->get($listing->getKey())?->getKey() ?? 0),
                'title' => 'New listing awaiting approval', 'description' => $listing->listing_title,
                'url' => route('mpp.listings', ['listing_id' => $listing->getKey()]),
                'created_at' => ($submissions->get($listing->getKey())?->audit_created_at ?? $listing->listing_created_at)?->toIso8601String(),
            ]);
        return $reports->concat($risks)->concat($pending)->reject(fn ($item) => $read->contains($item['key']))->sortByDesc('created_at')->values();
    }

    public function index(Request $request)
    {
        $items = $this->unread($request);
        if (! $request->expectsJson()) {
            $page = max(1, $request->integer('page', 1));
            $notifications = new \Illuminate\Pagination\LengthAwarePaginator($items->forPage($page, 15)->values(), $items->count(), 15, $page, ['path' => route('mpp.notifications')]);
            return response()->view('mpp.notifications', compact('notifications'))->header('Cache-Control', 'no-store');
        }
        return response()->json(['count' => $items->count(), 'items' => $items->take(50)->values()])->header('Cache-Control', 'no-store');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'keys' => ['required', 'array', 'min:1', 'max:100'],
            'keys.*' => ['required', 'string', 'max:100', 'distinct'],
        ]);
        // MPP alerts are generated from live records; dismiss them only for this user.
        $items = $this->unread($request)->whereIn('key', $data['keys']);
        foreach ($items->chunk(500) as $chunk) {
            DB::table('mpp_notification_reads')->insertOrIgnore($chunk->map(fn ($item) => [
                'user_id' => $request->user()->getKey(), 'notification_read_key' => $item['key'],
            ])->values()->all());
        }
        return redirect()->route('mpp.notifications')->with('success', 'Selected notifications removed.');
    }
    public function read(Request $request)
    {
        $request->validate(['key' => ['nullable', 'string', 'max:100']]);
        $items = $this->unread($request);
        if ($request->filled('key')) {
            $items = $items->where('key', $request->input('key'));
        }
        foreach ($items->chunk(500) as $chunk) {
            DB::table('mpp_notification_reads')->insertOrIgnore($chunk->map(fn ($item) => [
                'user_id' => $request->user()->getKey(), 'notification_read_key' => $item['key'],
            ])->values()->all());
        }
        if (! $request->expectsJson()) {
            return redirect()->route('mpp.notifications')->with('success', 'Notifications marked as read.');
        }
        return response()->json(['ok' => true]);
    }
}
