<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // User is not logged in
        if (!auth()->check()) {
            return redirect('/')->with(
                'error',
                'Please login again to continue.'
            );
        }

        // User is logged in but is not a student
        if (auth()->user()->user_role !== 'student') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request)->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}