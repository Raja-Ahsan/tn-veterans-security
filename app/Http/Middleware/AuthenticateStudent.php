<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateStudent
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guard = 'student';

        if (Auth::guard($guard)->check()) {
            Auth::shouldUse($guard);

            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('student.login'));
    }
}
