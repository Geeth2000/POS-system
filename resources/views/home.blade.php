@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->name . '!')

@section('content')

{{-- ── Stat Cards ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

    {{-- Total Users --}}
    @if(Auth::user()->canManageUsers())
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">Active</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        <p class="text-sm text-gray-500 mt-1">Total Staff Members</p>
    </div>
    @endif

    {{-- Today's Sales --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-full">Today</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['today_sales'] }}</p>
        <p class="text-sm text-gray-500 mt-1">Sales Transactions</p>
    </div>

    {{-- Today's Revenue --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-full">Revenue</span>
        </div>
        <p class="text-2xl font-bold text-gray-900">LKR {{ number_format($stats['today_revenue'], 2) }}</p>
        <p class="text-sm text-gray-500 mt-1">Today's Revenue</p>
    </div>

    {{-- Total Products --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-xs font-medium {{ $stats['low_stock'] > 0 ? 'text-red-600 bg-red-50' : 'text-gray-500 bg-gray-50' }} px-2 py-1 rounded-full">
                {{ $stats['low_stock'] > 0 ? $stats['low_stock'] . ' low stock' : 'Healthy' }}
            </span>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
        <p class="text-sm text-gray-500 mt-1">Products in Inventory</p>
    </div>
</div>

{{-- ── Quick Actions + Recent Staff ──────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('pos') }}"
               class="flex items-center gap-3 p-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm font-semibold">Start New Sale</span>
            </a>

            @if(Auth::user()->canManageUsers())
            <button onclick="document.getElementById('add-user-quick-link').click()"
                    class="w-full flex items-center gap-3 p-3 rounded-xl bg-gray-50 text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-sm font-medium">Add Staff Member</span>
            </button>
            <a id="add-user-quick-link" href="{{ route('users.index') }}#add-user" class="hidden"></a>
            @endif
        </div>
    </div>

    {{-- Recent Staff --}}
    @if(Auth::user()->canManageUsers())
    <div class="xl:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900">Recent Staff</h3>
            <a href="{{ route('users.index') }}" class="text-xs text-indigo-600 font-medium hover:underline">View all →</a>
        </div>

        <div class="space-y-3">
            @forelse($recentStaff as $staff)
            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                     style="background: linear-gradient(135deg,
                        {{ $staff->isAdmin() ? '#7c3aed, #4f46e5' : ($staff->isManager() ? '#2563eb, #0891b2' : '#059669, #10b981') }})">
                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $staff->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $staff->email }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize
                        {{ $staff->isAdmin() ? 'bg-purple-100 text-purple-700' : ($staff->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                        {{ $staff->role }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $staff->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                </div>
            </div>
            @empty
            <div class="text-center py-6">
                <p class="text-sm text-gray-400">No staff members yet.</p>
                <a href="{{ route('users.index') }}" class="text-xs text-indigo-600 font-medium mt-1 inline-block hover:underline">+ Add your first staff member</a>
            </div>
            @endforelse
        </div>
    </div>
    @endif

</div>

@endsection
