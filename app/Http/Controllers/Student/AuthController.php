<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.auth.login');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('student')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Get intended URL or default to dashboard
            $intended = $request->session()->pull('url.intended', route('student.dashboard'));

            // Prevent redirect loop - if intended is login, go to dashboard
            if ($intended === route('student.login') || str_contains($intended, '/student/login')) {
                $intended = route('student.dashboard');
            }

            return redirect($intended);
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'has_security_registration' => 'nullable|boolean',
            'security_registration_number' => [
                Rule::requiredIf($request->boolean('has_security_registration')),
                'nullable',
                'string',
                'max:100',
            ],
            'security_registration_expiration' => [
                Rule::requiredIf($request->boolean('has_security_registration')),
                'nullable',
                'date',
            ],
        ]);

        $student = Student::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'has_security_registration' => $request->boolean('has_security_registration'),
            'security_registration_number' => $request->boolean('has_security_registration') ? $validated['security_registration_number'] : null,
            'security_registration_expiration' => $request->boolean('has_security_registration') ? $validated['security_registration_expiration'] : null,
        ]);

        Auth::guard('student')->login($student);

        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended', route('student.dashboard'));
        if ($intended === route('student.login') || str_contains($intended, '/student/login')) {
            $intended = route('student.dashboard');
        }

        return redirect($intended)->with('success', 'Account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'Logged out successfully!');
    }
}
