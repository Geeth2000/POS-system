<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name', 'POS System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { inter: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .brand-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .input-field {
            transition: all 0.2s ease;
        }
        .input-field:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15);
        }
        .login-btn {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        }
        .login-btn:active { transform: translateY(0); }
    </style>
</head>
<body class="font-inter bg-gray-50 min-h-screen">

<div class="min-h-screen flex">

    {{-- ── Left Panel (Branding) ─────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 brand-gradient flex-col justify-between p-12 animate-fade-in">
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-lg tracking-tight">{{ config('app.name', 'POS System') }}</span>
        </div>

        {{-- Hero Text --}}
        <div class="space-y-6">
            <div class="space-y-3">
                <p class="text-indigo-200 text-sm font-medium uppercase tracking-widest">Supermarket Management</p>
                <h1 class="text-4xl font-bold text-white leading-tight">
                    Manage your store<br>with confidence.
                </h1>
                <p class="text-indigo-200 text-base leading-relaxed max-w-sm">
                    A complete point-of-sale solution for your team — from cashiers to admins, everything in one place.
                </p>
            </div>

            {{-- Feature Pills --}}
            <div class="flex flex-wrap gap-2">
                @foreach(['Role-based Access', 'Real-time Sales', 'Inventory Management', 'Reports'] as $feature)
                    <span class="px-3 py-1 bg-white/15 text-white text-xs font-medium rounded-full">
                        {{ $feature }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-indigo-300 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name', 'POS System') }}. All rights reserved.
        </p>
    </div>

    {{-- ── Right Panel (Form) ──────────────────────────--}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md animate-slide-up">

            {{-- Mobile Logo --}}
            <div class="lg:hidden flex items-center gap-2 mb-10">
                <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="font-semibold text-gray-800">{{ config('app.name', 'POS System') }}</span>
            </div>

            {{-- Header --}}
            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                <p class="mt-2 text-gray-500 text-sm">Sign in to your account to continue</p>
            </div>

            {{-- Session Status / Error --}}
            @if (session('status'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl">
                    <p class="font-medium mb-1">Authentication failed</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="input-field w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 @error('email') border-red-400 @enderror"
                    >
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 pr-12 @error('password') border-red-400 @enderror"
                        >
                        <button type="button" id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" id="submit-btn"
                    class="login-btn w-full py-3 px-4 text-white text-sm font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                    Sign in to your account
                </button>
            </form>

            {{-- Role Hint --}}
            <div class="mt-8 p-4 bg-gray-100 rounded-xl">
                <p class="text-xs text-gray-500 text-center">
                    Access is role-restricted. Contact your admin if you need an account.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    toggleBtn.addEventListener('click', () => {
        const isText = passwordInput.type === 'text';
        passwordInput.type = isText ? 'password' : 'text';
        toggleBtn.querySelector('svg').style.color = isText ? '' : '#6366f1';
    });

    // Loading state on submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.textContent = 'Signing in…';
        btn.style.opacity = '0.8';
    });
</script>

</body>
</html>
