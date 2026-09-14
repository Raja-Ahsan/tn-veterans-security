<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'phone' => ['required', 'string', 'max:20', function (string $attribute, mixed $value, \Closure $fail): void {
                if (strlen(Student::digitsOnly((string) $value)) < 10) {
                    $fail('Enter a valid phone number with at least 10 digits.');
                }
            }],
            'ssn' => [
                Rule::requiredIf(blank($student->ssn_last_four)),
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($student): void {
                    if (blank($value) && filled($student->ssn_last_four)) {
                        return;
                    }
                    if (! Student::isValidSsn((string) $value)) {
                        $fail('Enter a valid 9-digit Social Security Number.');
                    }
                },
            ],
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

        $student->applyLegalName(
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
        );
        $student->email = $validated['email'];
        $student->phone = $validated['phone'];
        $student->address = $validated['address'] ?? null;
        if (filled($validated['ssn'] ?? null)) {
            $student->applySsn($validated['ssn']);
        }
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
            $student->password = $validated['password'];
        }

        $student->save();

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}
