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
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}