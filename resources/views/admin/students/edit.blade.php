@extends('admin.layouts.master')

@section('title', 'Edit Student')
@section('page-title', 'Edit '.$student->name)

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div class="flex items-start gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-bold text-white">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Edit student</h3>
            <p class="mt-0.5 text-sm text-gray-500">{{ $student->name }} · {{ $student->email }}</p>
            <p class="mt-1 text-xs text-gray-400">Fields marked <span class="text-red-500">*</span> are required.</p>
        </div>
    </div>
    <a href="{{ route('admin.students.show', $student) }}"
       class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
        ← Back to student
    </a>
</div>

@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Please fix these issues before saving:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-start">
        {{-- Basic info --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
            <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-sm font-bold text-white">1</span>
                <div>
                    <h4 class="text-base font-bold text-gray-900">Basic info</h4>
                    <p class="text-sm text-gray-500">Name and email are required. Phone and address are optional.</p>
                </div>
            </div>

            <div>
                <label for="name" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Full name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required
                       placeholder="Student full name"
                       class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                @error('name')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-bold text-gray-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope text-xs"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" required
                               placeholder="name@example.com"
                               class="w-full rounded-md border py-2.5 pl-9 pr-3 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-bold text-gray-700">
                        Phone <span class="font-normal text-gray-400">(optional)</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-phone text-xs"></i>
                        </span>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}"
                               placeholder="e.g. 615-555-0100"
                               class="w-full rounded-md border border-gray-300 py-2.5 pl-9 pr-3 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="address" class="mb-1.5 block text-sm font-bold text-gray-700">
                    Address <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <textarea id="address" name="address" rows="3"
                          placeholder="Street, city, state, ZIP"
                          class="w-full rounded-md border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">{{ old('address', $student->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Security registration --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-5">
            <div class="flex items-start gap-3 border-b border-gray-100 pb-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">2</span>
                <div>
                    <h4 class="text-base font-bold text-gray-900">Security registration</h4>
                    <p class="text-sm text-gray-500">Ask if they have ever had a security registration number.</p>
                </div>
            </div>

            <label for="has_security_registration" class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 bg-white p-4 transition-colors hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                <input type="hidden" name="has_security_registration" value="0">
                <input type="checkbox" id="has_security_registration" name="has_security_registration" value="1"
                       @checked(old('has_security_registration', $student->has_security_registration))
                       class="mt-0.5 rounded border-gray-400 text-green-600 focus:ring-green-500">
                <div>
                    <span class="text-sm font-semibold text-gray-900">Do you have or have you ever had a Security Registration Number?</span>
                    <p class="mt-0.5 text-xs text-gray-500">When checked, registration number and expiration date are required.</p>
                </div>
            </label>

            <div id="registration-fields" class="space-y-4 {{ old('has_security_registration', $student->has_security_registration) ? '' : 'hidden' }}">
                <div class="rounded-md border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Fill both fields below to save with security registration enabled.
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="security_registration_number" class="mb-1.5 block text-sm font-bold text-gray-700">
                            Registration number <span class="reg-required text-red-500">*</span>
                        </label>
                        <input type="text" id="security_registration_number" name="security_registration_number"
                               value="{{ old('security_registration_number', $student->security_registration_number) }}"
                               placeholder="e.g. 123456"
                               class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('security_registration_number') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                        @error('security_registration_number')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="security_registration_expiration" class="mb-1.5 block text-sm font-bold text-gray-700">
                            Expiration date <span class="reg-required text-red-500">*</span>
                        </label>
                        <input type="date" id="security_registration_expiration" name="security_registration_expiration"
                               value="{{ old('security_registration_expiration', optional($student->security_registration_expiration)->format('Y-m-d')) }}"
                               class="w-full rounded-md border px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-1 {{ $errors->has('security_registration_expiration') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }}">
                        @error('security_registration_expiration')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-4">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-green-700">
            <i class="fas fa-save"></i> Save changes
        </button>
        <a href="{{ route('admin.students.show', $student) }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>

<script>
(function () {
    var toggle = document.getElementById('has_security_registration');
    var fields = document.getElementById('registration-fields');
    if (!toggle || !fields) return;

    function sync() {
        fields.classList.toggle('hidden', !toggle.checked);
    }

    toggle.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
