<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect user to Google OAuth page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        try {
            return Socialite::driver('google')
                ->stateless()
                ->with(['prompt' => 'select_account'])
                ->redirect();
        } catch (Exception $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            
            return redirect()->route('login')->with(
                'error',
                'Unable to connect to Google authentication. Please try again.'
            );
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            // Retrieve Google user
            $googleUser = Socialite::driver('google')->stateless()->user();
            $email = strtolower(trim((string) $googleUser->getEmail()));

            if (empty($email)) {
                return redirect()->route('login')->with(
                    'error',
                    'Unable to retrieve email address from your Google account.'
                );
            }

            // Determine user role based on domain
            $role = $this->resolveRoleFromEmail($email);

            if (!$role) {
                return redirect()->route('login')->with(
                    'error',
                    'Access denied. Only official UPTM student and MPP accounts are allowed.'
                );
            }

            // Find existing user or create a new user instance
            $user = User::firstOrCreate(
                [
                    'user_email' => $email,
                ],
                [
                    'user_name' => $googleUser->getName() ?? 'UPTM User',
                    'google_id' => $googleUser->getId(),
                    'user_role' => $role,
                    // Support existing databases where password is still required.
                    // Students authenticate with Google, not this random password.
                    'password' => Str::random(64),
                ]
            );

            // Synchronize Google ID and role attributes for existing users
            $user->update([
                'google_id' => $googleUser->getId(),
                'user_role' => $role,
            ]);

            // Prevent login for suspended accounts
            if (!empty($user->user_suspended)) {
                return redirect()->route('login')->with(
                    'error',
                    'Your account has been suspended. Please contact MPP administration.'
                );
            }

            // Login user and regenerate session to prevent session fixation attacks
            Auth::login($user);
            $request->session()->regenerate();
            \App\Helpers\AuditLogger::log($user->getKey(), 'login', 'Google OAuth');

            // Role-based routing
            if ($user->user_role === 'mpp') {
                return redirect()->intended(route('mpp.dashboard', [], false) ?? '/mpp/dashboard');
            }

            return redirect()->intended(route('student.dashboard', [], false) ?? '/student/dashboard');

        } catch (Exception $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage());

            return redirect()->route('login')->with(
                'error',
                'Authentication failed due to an unexpected error. Please try logging in again.'
            );
        }
    }

    /**
     * Logout user and invalidate active session.
     */
    public function logout(Request $request): RedirectResponse
    {
        try {
            if (Auth::check()) \App\Helpers\AuditLogger::log(Auth::id(), 'logout');
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'You have been successfully logged out.');
        } catch (Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return redirect('/');
        }
    }

    /**
     * Resolve user role based on email domain.
     */
    private function resolveRoleFromEmail(string $email): ?string
    {
        // TEMPORARY FOR TESTING ONLY
        if ($email === 'aliaasssn@gmail.com') {
            return 'mpp';
        }

        elseif ($email === 'aliafatini21@gmail.com') {
            return 'student';
        }

        $domain = substr(strrchr($email, "@"), 1);

        return match ($domain) {
            'student.uptm.edu.my' => 'student',
            'mpp.uptm.edu.my'     => 'mpp',
            default               => null,
        };
    }
}
