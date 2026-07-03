<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions for student guard
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Get the guard name from the exception
            $guards = $e->guards();
            $guard = ! empty($guards) ? $guards[0] : null;

            // Check request path first (most reliable method)
            $path = $request->path();
            if (str_starts_with($path, 'student/')) {
                return redirect()->route('student.login');
            }

            if (str_starts_with($path, 'admin/')) {
                return redirect()->route('admin.login');
            }

            // Check guard name as fallback
            if ($guard === 'student') {
                return redirect()->route('student.login');
            }

            if ($guard === 'web' || ! $guard) {
                // Default to admin login for web guard or if guard is null
                return redirect()->route('admin.login');
            }

            // Final fallback: student login
            return redirect()->route('student.login');
        });
    })->create();
