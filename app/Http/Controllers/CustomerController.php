<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query()->withCount('sales');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('total_spend', 'desc')->paginate(15);

        return view('customers.index', compact('customers'));
    }

    /**
     * Store a newly created customer in storage.
     * (Web version for management, though POS uses API)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:customers,phone_number',
        ]);

        Customer::create($validated);

        return back()->with('success', 'Customer registered successfully.');
    }
}
