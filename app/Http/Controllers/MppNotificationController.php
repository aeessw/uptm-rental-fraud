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
        $read = DB::table('mpp_notification_reads')->where('user_id', $request->user()->getKey())->pluck('notification_key');
        $reports = Report::with('listing:listing_id,listing_title')->whereHas('listing')->get()->map(fn ($report) => [
            'key' => 'report:'.$report->getKey(),
            'title' => 'New fraud report',
            'description' => $report->listing->listing_title,
            'url' => route('mpp.reports', ['listing_id' => $report->listing_id]),
            'created_at' => $report->report_created_at?->toIso8601String(),
        ]);
        $risks = Listing::where('report_count', '>=', 3)->get()->map(fn ($listing) => [
            'key' => 'risk:'.$listing->getKey(),
            'title' => 'High-risk listing: 3 or more reports',
            'description' => $listing->listing_title,
            'url' => route('mpp.reports', ['listing_id' => $listing->getKey()]),
            'created_at' => $listing->listing_updated_at?->toIso8601String(),
        ]);
        return $reports->concat($risks)->reject(fn ($item) => $read->contains($item['key']))->sortByDesc('created_at')->values();
    }

    public function index(Request $request)
    {
        $items = $this->unread($request);
        return response()->json(['count' => $items->count(), 'items' => $items->take(50)->values()]);
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
                'user_id' => $request->user()->getKey(), 'notification_key' => $item['key'],
            ])->values()->all());
        }
        return response()->json(['ok' => true]);
    }
}
