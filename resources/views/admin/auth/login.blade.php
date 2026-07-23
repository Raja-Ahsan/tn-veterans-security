<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TN Veterans Security</title>

    @php
        $siteSettings = \App\Models\SiteSetting::first();
        $faviconUrl = ($siteSettings && $siteSettings->favicon)
            ? asset('storage/'.$siteSettings->favicon).'?v='.($siteSettings->updated_at->timestamp ?? time())
            : asset('favicon.ico');
        $logoUrl = ($siteSettings && $siteSettings->header_logo)
            ? asset('storage/'.$siteSettings->header_logo)
            : null;
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(22, 163, 74, 0.35), transparent),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(30, 58, 138, 0.25), transparent),
                linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        }

        .login-input:-webkit-autofill,
        .login-input:-webkit-autofill:hover,
        .login-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            -webkit-text-fill-color: #111827 !important;
            transition: background-color 9999s ease-in-out 0s;
        }
    </style>
</head>
<body class="flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white shadow-2xl shadow-black/40">
            <div class="border-b border-gray-100 bg-slate-50 px-8 py-7 text-center">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="TN Veterans Security" class="mx-auto mb-4 h-12 w-auto object-contain">
                @else
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-600 text-xl font-bold text-white shadow-lg shadow-green-600/30">
                        TN
                    </div>
                @endif
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Admin Login</h1>
                <p class="mt-1 text-sm text-gray-500">TN Veterans Security</p>
            </div>

            <div class="px-8 py-8">
                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold"><i class="fas fa-exclamation-circle mr-1"></i> Could not sign in</p>
                        <ul class="mt-1.5 list-disc space-y-0.5 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="username"
                                   placeholder="admin@example.com"
                                   class="login-input w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Enter your password"
                                   class="login-input w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20">
                        </div>
                    </div>

                    <label class="flex cursor-pointer items-center gap-2.5">
                        <input type="checkbox"
                               name="remember"
                               class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('training-classes') }}" class="text-sm text-slate-300 transition hover:text-white">
                ← Back to website
            </a>
        </p>
    </div>
</body>
</html>
