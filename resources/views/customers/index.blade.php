@extends('layouts.dashboard')

@section('title', 'Customer Management')
@section('page-title', 'Customers')
@section('page-subtitle', 'Manage relationships and loyalty programs')

@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('customers.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search by name or phone number..." 
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm shadow-sm">
            </div>
        </form>

        <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-xl shadow-md transition-all flex items-center gap-2 text-sm">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Add New Customer
        </button>
    </div>

    {{-- Customers Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <th class="px-6 py-4">Customer Details</th>
                        <th class="px-6 py-4">Phone Number</th>
                        <th class="px-6 py-4">Loyalty Points</th>
                        <th class="px-6 py-4">Total Spend</th>
                        <th class="px-6 py-4">Sales Count</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $customer->name }}</div>
                                        <div class="text-[10px] text-gray-400">ID: #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $customer->phone_number }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold">
                                    <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                    {{ number_format($customer->loyalty_points) }} Points
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                Rs. {{ number_format($customer->total_spend, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ $customer->sales_count }} sales</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-indigo-600 hover:text-indigo-800 text-xs font-bold uppercase tracking-wider">
                                    View History
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Add Customer Modal (Web) --}}
<div id="addCustomerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Add New Customer</h3>
            <button onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Phone Number</label>
                <input type="text" name="phone_number" required class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-md transition-all">
                Register Customer
            </button>
        </form>
    </div>
</div>
@endsection
