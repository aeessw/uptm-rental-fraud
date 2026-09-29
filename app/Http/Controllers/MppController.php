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
        $activeListings = Listing::where('listing_status', 'active')
            ->latest()
            ->get();

        $reportedListings = Listing::whereHas('reports')
            ->latest()
            ->get();

        $hiddenListings = Listing::where('listing_status', 'hidden')
            ->latest()
            ->get();

        $users = User::where('user_role', 'student')
            ->latest()
            ->get();

        // Keep the dashboard focused on significant activity; full history stays in Audit Logs.
        $auditLogs = \App\Models\AuditLog::with('user')
            ->whereIn('audit_action', ['login', 'google_login', 'failed_login', 'created_listing', 'reported_listing', 'removed_listing', 'hidden_listing', 'listing_auto_hidden', 'restored_listing', 'suspended_user', 'suspended_student', 'auto_suspended_user', 'unsuspended_user'])
            ->latest()->orderByDesc('audit_id')->take(5)->get();
        $listingNames = Listing::whereIn('listing_id', $auditLogs->map(fn ($log) => $log->listingId())->filter()->unique())->get(['listing_id', 'listing_title'])->keyBy('listing_id');
        foreach ($auditLogs as $log) $log->setRelation('auditListing', $listingNames->get($log->listingId()));

        $targetUserIds = $auditLogs->map(function ($log) {
            return preg_match('/User ID: ?([0-9]+)/', (string) $log->audit_target, $matches) ? (int) $matches[1] : null;
        })->filter()->unique();
        $targetUsers = User::whereIn('user_id', $targetUserIds)->get(['user_id', 'user_name'])->keyBy('user_id');
        foreach ($auditLogs as $log) {
            preg_match('/User ID: ?([0-9]+)/', (string) $log->audit_target, $matches);
            $log->setRelation('affectedUser', isset($matches[1]) ? $targetUsers->get((int) $matches[1]) : null);
        }

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
    public function removeListing(Request $request, $id)
    {
        $data = $request->validate(['reason' => ['required', \Illuminate\Validation\Rule::in(['Fraud reports', 'Fraud / Scam', 'Misleading information', 'Duplicate listing', 'Inappropriate Content', 'Other'])], 'note' => ['nullable', 'string', 'max:100', 'required_if:reason,Other']]);
        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
            $listing = Listing::whereKey($id)->lockForUpdate()->firstOrFail();
            $listing->update(['listing_status' => 'hidden']);
            AuditLogger::log(Auth::id(), 'removed_listing', 'Listing ID: '.$listing->getKey().' | Reason: '.$data['reason'].' | Note: '.($data['note'] ?? ''));
        });

        return back()->with(
            'success',
            'Listing has been hidden.'
        );
    }

    /**
     * Restore listing.
     */
    public function restoreListing($id)
    {
        $listing = Listing::findOrFail($id);

        $listing->listing_status = 'active';
        $listing->save();

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'restored_listing',
            'Listing ID: ' . $listing->getKey()
        );

        return back()->with(
            'success',
            'Listing has been restored.'
        );
    }

    /**
     * Suspend student.
     */
    public function suspendUser(Request $request, $id)
    {
        $user = User::where('user_role', 'student')->findOrFail($id);
        $data = $request->validate([
            'reason' => ['required', \Illuminate\Validation\Rule::in(['Repeated suspicious listings', 'Misleading information', 'Fraud-related activity', 'Violation of platform rules', 'Other'])],
            'note' => ['nullable', 'string', 'max:100', 'required_if:reason,Other'],
            'hide_listings' => ['sometimes', 'boolean'],
        ]);
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $user, $data) {
            $user->update(['user_suspended' => true]);
            $hidden = $request->boolean('hide_listings')
                ? $user->listings()->where('listing_status', 'active')->update(['listing_status' => 'hidden']) : 0;
            AuditLogger::log(Auth::id(), 'suspended_user', 'User ID: '.$user->getKey().' | Reason: '.$data['reason'].' | Note: '.($data['note'] ?? '').' | Listings hidden: '.$hidden);
        });
        return back()->with('success', 'Student suspended and reason recorded.');
    }

    /**
     * Unsuspend student.
     */
    public function unsuspendUser($id)
    {
        $user = User::where('user_role', 'student')->findOrFail($id);

        $user->update([
            'user_suspended' => false,
        ]);

        // Record MPP action
        AuditLogger::log(
            Auth::id(),
            'unsuspended_user',
            'User ID: ' . $user->getKey()
        );

        return back()->with(
            'success',
            'User unsuspended successfully.'
        );
    }

    public function listings(Request $request)
    {
        $request->validate(['user_id' => 'nullable|integer|exists:users,user_id']);
        $request->validate(['listing_id' => 'nullable|integer|exists:listings,listing_id']);
        $listings = Listing::with(['user', 'photos', 'reports' => fn ($query) => $query->latest()->limit(5)])->withCount('reports as report_count')->when($request->filled('listing_id'), fn ($query) => $query->whereKey($request->integer('listing_id')))->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))->latest()->get();

        $history = \App\Models\AuditLog::with('user')->whereIn('audit_action', ['removed_listing', 'hidden_listing', 'restored_listing', 'listing_auto_hidden'])->latest()->get()->groupBy(fn ($log) => $log->listingId());
        foreach ($listings as $listing) $listing->setRelation('moderationHistory', $history->get($listing->getKey(), collect())->take(5));
        return view('mpp.listings', compact('listings'));
    }

    public function reports(Request $request)
    {
        $request->validate(['user_id' => 'nullable|integer|exists:users,user_id', 'listing_id' => 'nullable|integer|exists:listings,listing_id']);
        if ($request->filled('user_id')) {
            $student = User::where('user_role', 'student')->findOrFail($request->integer('user_id'));
            $reports = $student->receivedReports()->with(['user', 'listing'])->latest('reports.report_created_at')->orderByDesc('reports.report_id')->paginate(10)->withQueryString();
            $reportedListingQuery = $student->listings()->whereHas('reports');
            $reportedListingCount = (clone $reportedListingQuery)->count();
            $mostReportedListing = $reportedListingQuery->withCount('reports')->orderByDesc('reports_count')->orderBy('listing_id')->first();
            $thresholdEvent = $mostReportedListing ? \App\Models\AuditLog::where('audit_action', 'listing_auto_hidden')->where('audit_target', 'Listing ID: '.$mostReportedListing->getKey())->latest()->first() : null;
            return view('mpp.student-reports', compact('student', 'reports', 'reportedListingCount', 'mostReportedListing', 'thresholdEvent'));
        }
        $reportedListings = Listing::with(['user', 'photos', 'reports' => fn ($query) => $query->with('user')->latest()])->withCount(['reports as actual_reports_count', 'reports as report_count'])
            ->whereHas('reports')
            ->when($request->filled('listing_id'), fn ($query) => $query->whereKey($request->input('listing_id')))
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })
            ->latest()
            ->get();

        $history = \App\Models\AuditLog::with('user')->whereIn('audit_action', ['removed_listing', 'hidden_listing', 'restored_listing', 'listing_auto_hidden'])->latest()->get()->groupBy(fn ($log) => $log->listingId());
        foreach ($reportedListings as $listing) $listing->setRelation('moderationHistory', $history->get($listing->getKey(), collect())->take(5));
        return view('mpp.reports', compact('reportedListings'));
    }

    public function auditLogs(Request $request)
    {
        $request->validate(['user_id' => 'nullable|integer|exists:users,user_id', 'listing_id' => 'nullable|integer|exists:listings,listing_id']);
        $auditLogs = \App\Models\AuditLog::with('user')
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $id = $request->integer('user_id');
                $query->where(fn ($activity) => $activity->where('user_id', $id)->orWhere('audit_target', 'User ID: '.$id)->orWhere('audit_target', 'like', 'User ID: '.$id.' |%'));
            })
            ->latest()
            ->get();

        if ($request->filled('listing_id')) {
            $auditLogs = $auditLogs->filter(fn ($log) => $log->listingId() === $request->integer('listing_id'))->values();
        }
        $listingNames = Listing::whereIn('listing_id', $auditLogs->map(fn ($log) => $log->listingId())->filter()->unique())->get(['listing_id', 'listing_title'])->keyBy('listing_id');
        foreach ($auditLogs as $log) $log->setRelation('auditListing', $listingNames->get($log->listingId()));
        return view('mpp.audit-logs', compact('auditLogs'));
    }

    public function studentDetails($id)
    {
        $user = User::where('user_role', 'student')->withCount(['receivedReports', 'listings', 'listings as active_listings_count' => fn ($query) => $query->where('listing_status', 'active'), 'listings as reported_listings_count' => fn ($query) => $query->whereHas('reports')])->with(['auditActivity' => fn ($query) => $query->latest()->limit(5)])->findOrFail($id);
        $this->loadStudentActivityTargets(collect([$user]));
        return view('mpp.student-show', compact('user'));
    }

    public function students()
    {
        $users = User::where('user_role', 'student')
            ->withCount(['receivedReports', 'listings', 'listings as active_listings_count' => fn ($query) => $query->where('listing_status', 'active'), 'listings as reported_listings_count' => fn ($query) => $query->whereHas('reports')])
            ->with(['auditActivity' => fn ($query) => $query->latest()->limit(5)])
            ->latest()
            ->get();

        $this->loadStudentActivityTargets($users);
        return view('mpp.students', compact('users'));
    }

    private function loadStudentActivityTargets($users): void
    {
        $logs = $users->flatMap(fn ($user) => $user->auditActivity);
        $listings = Listing::whereIn('listing_id', $logs->map(fn ($log) => $log->listingId())->filter()->unique())->get(['listing_id', 'listing_title'])->keyBy('listing_id');
        foreach ($logs as $log) $log->setRelation('auditListing', $listings->get($log->listingId()));
    }

}