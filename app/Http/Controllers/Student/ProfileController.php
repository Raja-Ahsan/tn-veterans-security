<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $student = Auth::guard('student')->user();

        return view('student.profile', compact('student'));
    }

    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:8|confirmed',
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

        $student->name = $validated['name'];
        $student->email = $validated['email'];
        $student->phone = $validated['phone'] ?? null;
        $student->address = $validated['address'] ?? null;
        $student->has_security_registration = $request->boolean('has_security_registration');

        if ($student->has_security_registration) {
            $student->security_registration_number = $validated['security_registration_number'];
            $student->security_registration_expiration = $validated['security_registration_expiration'];
        } else {
            $student->security_registration_number = null;
            $student->security_registration_expiration = null;
        }

        if ($request->hasFile('profile_picture')) {
            if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
                Storage::disk('public')->delete($student->profile_picture);
            }

            $path = $request->file('profile_picture')->store('student-profiles', 'public');
            $student->profile_picture = $path;
        }

        if ($request->filled('password')) {
            $student->password = Hash::make($validated['password']);
        }

        $student->save();

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}
