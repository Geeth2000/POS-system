@extends('layouts.dashboard')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Comprehensive sales and inventory insights')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="space-y-8">

    {{-- ── Data Cards ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Daily Sales --}}
        <div class="stat-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Daily Sales</p>
                <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($dailySales, 2) }}</p>
            </div>
        </div>

        {{-- Total Transactions --}}
        <div class="stat-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Transactions Today</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalTransactions }}</p>
            </div>
        </div>

        {{-- Avg Transaction Value --}}
        <div class="stat-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Avg. Order Value</p>
                <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($avgTransactionValue, 2) }}</p>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        <div class="stat-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Low Stock Items</p>
                <p class="text-2xl font-bold text-gray-900">{{ $lowStockAlert }}</p>
            </div>
        </div>
    </div>

    {{-- ── Visual Analytics ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Daily Sales Trend (Bar Chart) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Daily Sales Trend (Last 7 Days)</h3>
                <span class="text-xs text-gray-400">Values in Rs.</span>
            </div>
            <div class="chart-container">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        {{-- Payment Method Breakdown (Pie Chart) --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Payment Methods</h3>
                <span class="text-xs text-gray-400">Volume</span>
            </div>
            <div class="chart-container">
                <canvas id="paymentBreakdownChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Transaction History Table ────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        {{-- Filters Bar --}}
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Transaction History</h3>
            
            <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-500">From:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-500">To:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 px-3 py-1.5">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-1.5 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4">Invoice ID</th>
                        <th class="px-6 py-4">Cashier Name</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Payment Method</th>
                        <th class="px-6 py-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($history as $tx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-indigo-600">{{ $tx->transaction_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-600">
                                        {{ strtoupper(substr($tx->cashier->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $tx->cashier->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $tx->created_at->format('M d, Y') }}
                                <span class="text-[10px] block text-gray-400">{{ $tx->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $tx->payment_method === 'cash' ? 'bg-green-50 text-green-600' : 
                                       ($tx->payment_method === 'card' ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $tx->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">Rs. {{ number_format($tx->total_amount, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                No transactions found for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ── Bar Chart: Daily Sales Trend ──────────────────────────────
    const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Sales Revenue',
                data: @json($trendData),
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderColor: '#4f46e5',
                borderWidth: 2,
                borderRadius: 6,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: {
                        callback: function(value) { return 'Rs. ' + value.toLocaleString(); },
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // ── Pie Chart: Payment Breakdown ──────────────────────────────
    const ctxPayment = document.getElementById('paymentBreakdownChart').getContext('2d');
    new Chart(ctxPayment, {
        type: 'doughnut',
        data: {
            labels: @json($paymentBreakdown->pluck('payment_method')),
            datasets: [{
                data: @json($paymentBreakdown->pluck('count')),
                backgroundColor: [
                    'rgba(16, 185, 129, 0.7)', // emerald
                    'rgba(59, 130, 246, 0.7)', // blue
                    'rgba(245, 158, 11, 0.7)', // amber
                    'rgba(107, 114, 128, 0.7)', // gray
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>
@endsection
