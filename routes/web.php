<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MppController;
use App\Models\Listing;

//-- Guest & Public Routes --//

// Home / Login Route (named 'login' so auth middleware redirects here on session expiry)
Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('login');

// Google OAuth Login
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->middleware('guest')
    ->name('google.login');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


//-- Student Protected Routes --//
Route::middleware(['auth', 'student'])->group(function () {

    Route::get('/student/profile', function (\Illuminate\Http\Request $request) {
        $listings = Listing::where('user_id', $request->user()->getKey())
            ->latest()
            ->orderByDesc('listing_id')
            ->paginate(6);

        $blockedUsers = $request->user()->blockedUsers()->orderBy('user_name')->get();
        return view('student.profile', compact('listings', 'blockedUsers'));
    })->name('student.profile');

    Route::post('/student/users/{user}/block', [MessageController::class, 'block'])->name('student.users.block');
    Route::post('/student/users/{user}/unblock', [MessageController::class, 'unblockFromSettings'])->name('student.users.unblock');

    // Student Dashboard
    Route::get('/student/dashboard', function () {
        $listings = Listing::visibleTo(auth()->user())->where('listing_status', 'active')
            ->where('listing_availability', 'available')
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard', compact('listings'));
    })->name('student.dashboard');

    // Listings Management
    Route::get('/student/listings', [ListingController::class, 'index'])
        ->name('student.listings');

    Route::get('/student/saved-listings', [ListingController::class, 'saved'])
        ->name('student.saved');

    Route::post('/student/listings/{listing}/save', [ListingController::class, 'toggleSave'])
        ->name('student.listings.save');

    Route::patch('/student/listings/{listing}/availability', [ListingController::class, 'updateAvailability'])
        ->name('student.listings.availability');

    Route::post('/student/saved-listings/remove-selected', [ListingController::class, 'bulkRemoveSaved'])
        ->name('student.saved.bulk-remove');

    Route::get('/student/listings/create', [ListingController::class, 'create'])
        ->name('student.listings.create');

    Route::post('/student/listings', [ListingController::class, 'store'])
        ->name('student.listings.store');

    Route::get('/student/listings/{listing}', [ListingController::class, 'show'])
        ->name('student.listings.show');

    Route::get('/student/listings/{listing}/edit', [ListingController::class, 'edit'])
        ->name('student.listings.edit');

    Route::put('/student/listings/{listing}', [ListingController::class, 'update'])
        ->name('student.listings.update');

    // Listing Reports
    Route::post('/student/listings/{listing}/report', [ReportController::class, 'store'])
        ->name('student.listings.report');

    // Messages inbox
    Route::get(
        '/student/messages',
        [MessageController::class, 'inbox']
    )->name('student.message.inbox');

    Route::get('/student/messages/unread-count', [MessageController::class, 'unreadCount'])
        ->name('student.messages.unread-count');
    Route::post('/student/messages/{userId}/read', [MessageController::class, 'markRead'])
        ->name('student.messages.read');
    Route::post('/student/messages/{userId}/unblock', [MessageController::class, 'unblock'])
        ->name('student.messages.unblock');
    Route::post('/student/messages/{userId}/delete', [MessageController::class, 'deleteConversation'])
        ->name('student.messages.delete');
    Route::post('/student/messages/read-all', [MessageController::class, 'markAllRead'])
        ->name('student.messages.read-all');
    // Specific conversation
    Route::get(
        '/student/messages/{userId}',
        [MessageController::class, 'index']
    )->name('student.messages');

    // Send message
    Route::post(
        '/student/messages',
        [MessageController::class, 'store']
    )->name('student.messages.store');
});

//-- MPP Admin Protected Routes --//
Route::middleware(['auth', 'mpp'])->group(function () {
    Route::get('/mpp/notifications', [\App\Http\Controllers\MppNotificationController::class, 'index'])->name('mpp.notifications');
    Route::post('/mpp/notifications/read', [\App\Http\Controllers\MppNotificationController::class, 'read'])->name('mpp.notifications.read');

    Route::get('/mpp/dashboard', [MppController::class, 'dashboard'])
        ->name('mpp.dashboard');

    Route::get('/mpp/listings', [MppController::class, 'listings'])
        ->name('mpp.listings');

    Route::get('/mpp/reports', [MppController::class, 'reports'])
        ->name('mpp.reports');

    Route::get('/mpp/students/{id}', [MppController::class, 'studentDetails'])->name('mpp.students.show');

    Route::get('/mpp/students', [MppController::class, 'students'])
        ->name('mpp.students');

    Route::post('/mpp/listings/{id}/remove', [MppController::class, 'removeListing'])
        ->name('mpp.listings.remove');

    Route::post('/mpp/listings/{id}/restore', [MppController::class, 'restoreListing'])
        ->name('mpp.listings.restore');

    Route::post('/mpp/users/{id}/suspend', [MppController::class, 'suspendUser'])
        ->name('mpp.users.suspend');

    Route::post('/mpp/users/{id}/unsuspend', [MppController::class, 'unsuspendUser'])
        ->name('mpp.users.unsuspend');

    Route::get('/mpp/audit-logs', [MppController::class, 'auditLogs'])
        ->name('mpp.audit.logs');


});
