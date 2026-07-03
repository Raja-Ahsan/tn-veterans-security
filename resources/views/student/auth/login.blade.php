@extends('layouts.web.master')

@section('title', 'Student Login - TN Veterans Security')

@section('content')
<style>
    .auth-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: calc(100vh - 200px);
    }
    
    .auth-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--btn-hover-color) 100%);
    }
    
    .auth-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
    }
    
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        letter-spacing: 0.025em;
    }
    
    .form-input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        color: #1f2937;
        background-color: #f9fafb;
        transition: all 0.3s ease;
        outline: none;
    }
    
    .form-input:focus {
        border-color: var(--primary-color);
        background-color: white;
        box-shadow: 0 0 0 4px rgba(58, 166, 44, 0.1);
        transform: translateY(-2px);
    }
    
    .form-input::placeholder {
        color: #9ca3af;
    }
    
    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 1.1rem;
        transition: color 0.3s ease;
        pointer-events: none;
    }
    
    .form-group:focus-within .input-icon {
        color: var(--primary-color);
    }
    
    .form-textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        color: #1f2937;
        background-color: #f9fafb;
        transition: all 0.3s ease;
        outline: none;
        resize: vertical;
        font-family: inherit;
    }
    
    .form-textarea:focus {
        border-color: var(--primary-color);
        background-color: white;
        box-shadow: 0 0 0 4px rgba(58, 166, 44, 0.1);
    }
    
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 0.75rem;
        cursor: pointer;
        accent-color: var(--primary-color);
    }
    
    .checkbox-wrapper span {
        color: #4b5563;
        font-size: 0.875rem;
        user-select: none;
    }
    
    .btn-submit {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--btn-hover-color) 100%);
        color: white;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(58, 166, 44, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-submit::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-submit:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(58, 166, 44, 0.4);
    }
    
    .btn-submit:active {
        transform: translateY(0);
    }
    
    .btn-submit span {
        position: relative;
        z-index: 1;
    }
    
    .auth-link {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .auth-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }
    
    .auth-link:hover::after {
        width: 100%;
    }
    
    .auth-link:hover {
        color: var(--btn-hover-color);
    }
    
    .alert {
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .auth-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #1f2937 0%, var(--primary-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .auth-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }
</style>

<section class="auth-container py-16 lg:py-24">
    <div class="container mx-auto px-4 lg:px-10">
        <div class="mx-auto" style="width: 450px; max-width: 100%;">
            <div class="auth-card p-8 lg:p-10">
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to access your account</p>
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

                @if(session('success'))
                    <div class="alert bg-green-50 border-2 border-green-200 text-green-800">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('student.login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <div class="relative">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   placeholder="Enter your email"
                                   class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter your password"
                                   class="form-input">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember">
                            <span>Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('student.register') }}" class="auth-link">Create Account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
