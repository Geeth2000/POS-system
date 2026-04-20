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
        $query = Customer::withCount('transactions');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $customers = $query->paginate($request->per_page ?? 15);

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
            'email' => 'nullable|email|unique:customers',
            'phone' => 'nullable|string|max:20',
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
        $customer->load('transactions.items');

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
            'email' => 'email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
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

    /**
     * Add loyalty points
     */
    public function addLoyaltyPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $customer->loyalty_points += $request->points;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Loyalty points added successfully',
            'data' => $customer,
        ]);
    }
}
