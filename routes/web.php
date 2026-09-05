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

    // Student Dashboard
    Route::get('/student/dashboard', function () {
        $listings = Listing::where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard', compact('listings'));
    })->name('student.dashboard');

    // Listings Management
    Route::get('/student/listings', [ListingController::class, 'index'])
        ->name('student.listings');

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

    Route::get('/mpp/dashboard', [MppController::class, 'dashboard'])
        ->name('mpp.dashboard');

    Route::get('/mpp/listings', [MppController::class, 'listings'])
        ->name('mpp.listings');

    Route::get('/mpp/reports', [MppController::class, 'reports'])
        ->name('mpp.reports');

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