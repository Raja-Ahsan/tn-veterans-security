@extends('layouts.web.master')

@section('title', 'Reset Password - TN Veterans Security')

@section('content')
@include('student.auth._styles')

<section class="auth-container py-16 lg:py-24">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="mx-auto" style="width: 450px; max-width: 100%;">
            <div class="auth-card p-8 lg:p-10">
                <div class="auth-header">
                    <h1>Reset Password</h1>
                    <p>Choose a new password for your student account.</p>
                </div>

                @if($errors->any())
                    <div class="alert bg-red-50 border-2 border-red-200 text-red-800">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('student.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $email) }}"
                               required
                               placeholder="Enter your email"
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               minlength="8"
                               placeholder="At least 8 characters"
                               class="form-input">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               minlength="8"
                               placeholder="Re-enter your new password"
                               class="form-input">
                    </div>

                    <button type="submit" class="btn-submit">
                        Update Password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('student.login') }}" class="auth-link">Back to Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
