<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AuditLogger;

class ReportController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'action' => 'nullable|in:report,report_and_block',
            'blocked_user_id' => 'nullable|exists:users,user_id',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $listing) {
        // Serialize submissions for this listing before checking for an existing report.
        $listing = Listing::whereKey($listing->getKey())->lockForUpdate()->firstOrFail();
        if (Report::where('listing_id', $listing->getKey())->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already reported this listing.');
        }

        // Create report
        Report::create([
            'listing_id' => $listing->getKey(),
            'user_id' => Auth::id(),
            'report_reason' => $request->reason,
        ]);

        // Increase report count
        $listing->update(['report_count' => $listing->reports()->count()]);

        // Record report action in audit log
        AuditLogger::log(
            Auth::id(),
            'reported_listing',
            'Listing ID: ' . $listing->getKey()
        );

        // Auto-hide listing after 3 reports
        if ($listing->fresh()->report_count >= 3) {
            $listing->update([
                'listing_status' => 'hidden',
                'hidden_by_suspension' => false,
            ]);

            // Record automatic hiding in audit log
            AuditLogger::log(
                Auth::id(),
                'listing_auto_hidden',
                'Listing ID: ' . $listing->getKey()
            );
        }

        if ($request->input('action') === 'report_and_block') {
            $blockedUserId = (int) ($request->input('blocked_user_id') ?: $listing->user_id);

            if ($blockedUserId !== (int) Auth::id()) {
                Auth::user()->blockedUsers()->syncWithoutDetaching([$blockedUserId]);
            }
        }

        $listingOwner = $listing->user;
        if ($listingOwner && $listingOwner->user_role === 'student') {
            $reportCount = $listingOwner->receivedReports()->count();

            if ($reportCount > 3 && !$listingOwner->user_suspended) {
                $listingOwner->update(['user_suspended' => true]);
                AuditLogger::log(
                    Auth::id(),
                    'auto_suspended_user',
                    'User ID: ' . $listingOwner->getKey() . ' exceeded report threshold.'
                );
            }
        }

        $redirect = Listing::visibleTo($request->user())->whereKey($listing->getKey())->exists()
            ? back()
            : redirect()->route('student.listings');

        return $redirect->with(
            'success',
            'Listing reported successfully.'
        );
        });
    }
}