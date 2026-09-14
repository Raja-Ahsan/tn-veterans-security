<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Student::query()->withCount('bookings');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('ssn_last_four', 'like', "%{$search}%")
                    ->orWhere('security_registration_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load(['bookings.service', 'bookings.classSchedule', 'bookings.payments']);

        $payments = $student->payments()
            ->with(['booking.service'])
            ->orderByDesc('payment_date')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.students.show', compact('student', 'payments'));
    }

    public function edit(Student $student): View
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
            'phone' => ['required', 'string', 'max:50', function (string $attribute, mixed $value, \Closure $fail): void {
                if (strlen(Student::digitsOnly((string) $value)) < 10) {
                    $fail('Enter a valid phone number with at least 10 digits.');
                }
            }],
            'ssn' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (blank($value)) {
                    return;
                }
                if (! Student::isValidSsn((string) $value)) {
                    $fail('Enter a valid 9-digit Social Security Number.');
                }
            }],
            'address' => ['nullable', 'string', 'max:500'],
            'has_security_registration' => ['nullable', 'boolean'],
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
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'phone.required' => 'Phone number is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already used by another student.',
            'security_registration_number.required' => 'Registration number is required when security registration is enabled.',
            'security_registration_expiration.required' => 'Expiration date is required when security registration is enabled.',
        ]);

        $student->applyLegalName(
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
        );
        $student->email = $validated['email'];
        $student->phone = $validated['phone'];
        $student->address = $validated['address'] ?? null;
        $student->has_security_registration = $request->boolean('has_security_registration');
        $student->security_registration_number = $request->boolean('has_security_registration')
            ? $validated['security_registration_number']
            : null;
        $student->security_registration_expiration = $request->boolean('has_security_registration')
            ? $validated['security_registration_expiration']
            : null;
        if (filled($validated['ssn'] ?? null)) {
            $student->applySsn($validated['ssn']);
        }
        $student->save();

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }
}
