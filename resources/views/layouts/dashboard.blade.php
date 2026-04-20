<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'POS System') }}</title>
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
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideIn: { '0%': { opacity: '0', transform: 'translateX(-10px)' }, '100%': { opacity: '1', transform: 'translateX(0)' } },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }
        .sidebar-link.active {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            font-weight: 600;
        }
        .sidebar-link.active svg { color: #4f46e5; }
        .sidebar-link:hover svg { color: #4f46e5; }
    </style>
    @yield('styles')
</head>
<body class="font-inter bg-gray-50 min-h-screen">

<div class="flex min-h-screen" id="app-wrapper">

    {{-- ── Sidebar ────────────────────────────────────────────────── --}}
    <aside id="sidebar"
           class="fixed top-0 left-0 h-full w-64 bg-white border-r border-gray-100 shadow-sm flex flex-col z-30 transition-transform duration-300">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background: linear-gradient(135deg, #4f46e5, #7c3aed)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900 leading-tight">{{ config('app.name', 'POS System') }}</p>
                <p class="text-xs text-gray-400">Supermarket</p>
            </div>
        </div>

        {{-- User Info --}}
        <div class="px-4 py-3 mx-3 mt-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg, #4f46e5, #7c3aed)">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- ── All Roles ── --}}
            <p class="px-3 pt-1 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-widest">Main</p>

            {{-- Role-specific Dashboard Link --}}
            @php
                $dashboardRoute = auth()->user()->isCashier() ? 'cashier.dashboard' : 'dashboard';
                $isActive = request()->routeIs('dashboard') || request()->routeIs('cashier.dashboard') || request()->routeIs('admin.dashboard');
            @endphp

            <a href="{{ route($dashboardRoute) }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 {{ $isActive ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('pos') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 {{ request()->routeIs('pos') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                New Sale
            </a>

            {{-- ── Admin + Manager Only ── --}}
            @if(Auth::user()->canManageUsers())
                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-widest">Management</p>

                <a href="{{ route('users.index') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Staff Management
                    @php
                        $staffCount = \App\Models\User::where('id', '!=', Auth::id())
                            ->when(Auth::user()->isManager(), fn($q) => $q->where('role', 'cashier'))
                            ->count();
                    @endphp
                    @if($staffCount > 0)
                        <span class="ml-auto text-xs bg-indigo-100 text-indigo-600 font-semibold px-2 py-0.5 rounded-full">
                            {{ $staffCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- Admin + Manager: Inventory Management --}}
            @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                <a href="{{ route('products.index') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Inventory
                </a>
            @endif
        </nav>

        {{-- Bottom: Logout --}}
        <div class="px-3 pb-4 border-t border-gray-100 pt-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main Content ────────────────────────────────────────────── --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400 mt-0.5">@yield('page-subtitle', date('l, F j, Y'))</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Role Badge --}}
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (Auth::user()->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>

                {{-- Avatar --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                     style="background: linear-gradient(135deg, #4f46e5, #7c3aed)">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-8 mt-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-center gap-2 animate-fade-in" id="flash-success">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-8 mt-6 px-4 py-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl flex items-center gap-2" id="flash-error">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-8 animate-fade-in">
            @yield('content')
        </main>
    </div>
</div>

@yield('scripts')
</body>
</html>
