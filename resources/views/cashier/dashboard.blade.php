@extends('layouts.dashboard')

@section('title', 'Cashier Dashboard')
@section('page-title', 'My Overview')
@section('page-subtitle', 'Track your performance for today')

@section('content')

{{-- ── Welcome Header ─────────────────────────────────────────── --}}
<div class="mb-8 p-6 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Welcome Back, {{ Auth::user()->name }}! 👋</h2>
        <p class="text-sm text-gray-400 mt-1">Ready to start another successful shift?</p>
    </div>
    <a href="{{ route('pos') }}"
       class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-indigo-200"
       style="background: linear-gradient(135deg, #4f46e5, #7c3aed)">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        Start New Sale
    </a>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    {{-- My Sales Today --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group hover:border-indigo-200 transition-colors">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">My Sales Today</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['my_sales_today']) }}</h3>
            </div>
        </div>
    </div>

    {{-- Today's Revenue --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group hover:border-emerald-200 transition-colors">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">My Revenue</p>
                <h3 class="text-2xl font-bold text-gray-800">Rs. {{ number_format($stats['my_revenue_today'], 2) }}</h3>
            </div>
        </div>
    </div>

    {{-- Average Sale --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group hover:border-amber-200 transition-colors">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Avg. Sale Value</p>
                <h3 class="text-2xl font-bold text-gray-800">Rs. {{ number_format($stats['avg_sale_value'], 2) }}</h3>
            </div>
        </div>
    </div>

</div>

{{-- ── Recent Activity ─────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">My Recent Transactions</h3>
        <span class="text-xs text-gray-400">Last 10 sales</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <th class="px-6 py-3.5">Invoice #</th>
                    <th class="px-6 py-3.5 text-center">Payment</th>
                    <th class="px-6 py-3.5 text-right">Amount</th>
                    <th class="px-6 py-3.5 text-right">Time</th>
                    <th class="px-6 py-3.5 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentSales as $sale)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-700">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium uppercase tracking-tighter">
                            {{ $sale->payment_method }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right text-gray-400 text-xs">{{ $sale->created_at->format('h:i A') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                            <span class="w-1 h-1 rounded-full bg-green-500"></span>
                            Success
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        No sales recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
