<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;
use App\Models\User;
use App\Helpers\AuditLogger;

class MppController extends Controller
{
    /**
     * Show MPP dashboard.
     */
    public function dashboard()
    {
        $activeListings = Listing::where('status', 'active')
            ->latest()
            ->get();

        $reportedListings = Listing::where('report_count', '>', 0)
            ->latest()
            ->get();

        $hiddenListings = Listing::where('status', 'hidden')
            ->latest()
            ->get();

        $users = User::where('role', 'student')
            ->latest()
            ->get();

        // Recent Audit Logs
        $auditLogs = \App\Models\AuditLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('mpp.dashboard', compact(
            'activeListings',
            'reportedListings',
            'hiddenListings',
            'users',
            'auditLogs'
        ));
    }

    /**
     * Remove listing.
     */
    public function removeListing($id)
    {
        $listing = Listing::findOrFail($id);

        $listing->status = 'hidden';
        $listing->save();

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'removed_listing',
            'Listing ID: ' . $listing->id
        );

        return back()->with(
            'success',
            'Listing has been removed.'
        );
    }

    /**
     * Restore listing.
     */
    public function restoreListing($id)
    {
        $listing = Listing::findOrFail($id);

        $listing->status = 'active';
        $listing->save();

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'restored_listing',
            'Listing ID: ' . $listing->id
        );

        return back()->with(
            'success',
            'Listing has been restored.'
        );
    }

    /**
     * Suspend student.
     */
    public function suspendUser($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'suspended' => true,
        ]);

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'suspended_user',
            'User ID: ' . $user->id
        );

        return back()->with(
            'success',
            'User suspended successfully.'
        );
    }

    /**
     * Unsuspend student.
     */
    public function unsuspendUser($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'suspended' => false,
        ]);

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'unsuspended_user',
            'User ID: ' . $user->id
        );

        return back()->with(
            'success',
            'User unsuspended successfully.'
        );
    }

    public function listings()
    {
        $listings = Listing::latest()->get();

        return view('mpp.listings', compact('listings'));
    }

    public function reports()
    {
        $reportedListings = Listing::with(['user', 'reports'])
            ->where('report_count', '>', 0)
            ->latest()
            ->get();

        return view('mpp.reports', compact('reportedListings'));
    }

    public function auditLogs()
    {
        $auditLogs = \App\Models\AuditLog::with('user')
            ->latest()
            ->get();

        return view('mpp.audit-logs', compact('auditLogs'));
    }

    public function students()
    {
        $users = User::where('role', 'student')
            ->latest()
            ->get();

        return view('mpp.students', compact('users'));
    }

}