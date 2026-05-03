<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::withCount('sales');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $customers = $query->paginate($request->per_page ?? 15);

        // Return simpler data for POS search if needed
        if ($request->has('search')) {
            return response()->json([
                'success' => true,
                'data' => $customers->items(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:customers,phone_number',
            'email' => 'nullable|email|unique:customers,email',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load('sales.items');

        return response()->json([
            'success' => true,
            'data' => $customer,
        ]);
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'string|max:255',
            'phone_number' => 'string|unique:customers,phone_number,' . $customer->id,
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    /**
     * Delete the specified customer
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }
}
