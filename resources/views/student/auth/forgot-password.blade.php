@extends('layouts.web.master')

@section('title', 'Forgot Password - TN Veterans Security')

@section('content')
@include('student.auth._styles')

<section class="auth-container py-16 lg:py-24">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="mx-auto" style="width: 450px; max-width: 100%;">
            <div class="auth-card p-8 lg:p-10">
                <div class="auth-header">
                    <h1>Forgot Password</h1>
                    <p>Enter your email and we will send a reset link if an account exists.</p>
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

                @if(session('status'))
                    <div class="alert bg-green-50 border-2 border-green-200 text-green-800">
                        <p class="text-sm font-medium">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('student.password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="Enter your email"
                               class="form-input">
                    </div>

                    <button type="submit" class="btn-submit">
                        Send Reset Link
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Remember your password?
                        <a href="{{ route('student.login') }}" class="auth-link">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
