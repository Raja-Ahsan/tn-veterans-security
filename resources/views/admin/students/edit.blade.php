@extends('admin.layouts.master')

@section('title', 'Edit Student')
@section('page-title', 'Edit '.$student->name)

@section('content')
<a href="{{ route('admin.students.show', $student) }}" class="text-blue-600 hover:underline mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i> Back to Student</a>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.students.update', $student) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required class="w-full border rounded px-3 py-2">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" required class="w-full border rounded px-3 py-2">
            @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full border rounded px-3 py-2">
            @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea id="address" name="address" rows="2" class="w-full border rounded px-3 py-2">{{ old('address', $student->address) }}</textarea>
            @error('address')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="has_security_registration" value="0">
            <input type="checkbox" id="has_security_registration" name="has_security_registration" value="1" @checked(old('has_security_registration', $student->has_security_registration))>
            <label for="has_security_registration" class="text-sm text-gray-700">Has security registration number</label>
        </div>

        <div id="registration-fields" class="space-y-4 {{ old('has_security_registration', $student->has_security_registration) ? '' : 'hidden' }}">
            <div>
                <label for="security_registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                <input type="text" id="security_registration_number" name="security_registration_number" value="{{ old('security_registration_number', $student->security_registration_number) }}" class="w-full border rounded px-3 py-2">
                @error('security_registration_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="security_registration_expiration" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                <input type="date" id="security_registration_expiration" name="security_registration_expiration" value="{{ old('security_registration_expiration', optional($student->security_registration_expiration)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
                @error('security_registration_expiration')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded">Save Changes</button>
    </form>
</div>

<script>
document.getElementById('has_security_registration')?.addEventListener('change', function () {
    document.getElementById('registration-fields').classList.toggle('hidden', !this.checked);
});
</script>
@endsection
