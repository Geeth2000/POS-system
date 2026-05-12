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
            darkMode: 'class',
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
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-item {
            width: 3.5rem;
            height: 3.5rem;
            transition: all 0.15s ease;
        }
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        .nav-item.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.18);
            box-shadow: inset 3px 0 0 #ffffff;
        }
    </style>
    @yield('styles')
</head>
<body class="font-inter bg-gray-50 dark:bg-slate-900 min-h-screen transition-colors duration-300">

<div class="flex min-h-screen" id="app-wrapper">

    {{-- ── Sidebar (compact blue) ─────────────────────────────────── --}}
    <aside id="sidebar" class="flex flex-col w-20 bg-indigo-600 dark:bg-slate-800 shadow-sm flex-shrink-0 fixed top-0 left-0 h-full z-30 transition-colors">

        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-indigo-500">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 flex flex-col items-center py-4 gap-2">

            <a href="{{ route(auth()->user()->isCashier() ? 'cashier.dashboard' : 'dashboard') }}" title="Dashboard" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ (request()->routeIs('dashboard') || request()->routeIs('cashier.dashboard') || request()->routeIs('admin.dashboard')) ? 'active' : '' }}">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[9px] font-medium">Dashboard</span>
            </a>

            <a href="{{ route('pos') }}" title="New Sale" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ request()->routeIs('pos') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-[9px] font-medium">New Sale</span>
            </a>

            @if(Auth::user()->canManageUsers())
                <a href="{{ route('users.index') }}" title="Staff" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-[9px] font-medium">Staff</span>
                </a>
            @endif

            @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                <a href="{{ route('products.index') }}" title="Inventory" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="text-[9px] font-medium">Inventory</span>
                </a>
            @endif

            @if(Auth::user()->canManageCustomers())
                <a href="{{ route('customers.index') }}" title="Customers" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-[9px] font-medium">Customers</span>
                </a>
            @endif

            @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                <a href="{{ route('reports.index') }}" title="Reports" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-[9px] font-medium">Reports</span>
                </a>
            @endif

        </nav>

        <!-- Logout -->
        <div class="py-6 flex flex-col items-center border-t border-indigo-500/30">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-red-300 transition-colors">
                <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="text-[9px] font-medium">Logout</span>
            </a>
        </div>
    </aside>

    {{-- ── Main Content ────────────────────────────────────────────── --}}
    <div class="flex-1 ml-20 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 px-8 py-4 flex items-center justify-between sticky top-0 z-10 transition-colors">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-400 mt-0.5">@yield('page-subtitle', date('l, F j, Y'))</p>
            </div>

            <div class="flex items-center gap-4">
                {{-- Theme & Fullscreen Toggles --}}
                <div class="flex items-center bg-gray-100 dark:bg-slate-700 p-1 rounded-xl gap-1">
                    <button onclick="toggleDarkMode()" class="p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-white dark:hover:bg-slate-600 transition-all shadow-sm" title="Toggle Dark Mode">
                        <svg id="sun-icon" class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg id="moon-icon" class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    <button onclick="toggleFullScreen()" class="p-2 rounded-lg text-gray-500 dark:text-gray-300 hover:bg-white dark:hover:bg-slate-600 transition-all shadow-sm" title="Full Screen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                </div>

                {{-- Role Badge --}}
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : (Auth::user()->isManager() ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>

                {{-- Avatar --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm"
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
<script>
    function toggleDarkMode() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('dark-mode', isDark);
    }

    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.error(`Error attempting to enable full-screen mode: ${err.message}`);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }
</script>
</body>
</html>
