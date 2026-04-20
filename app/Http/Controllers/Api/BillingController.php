<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\CheckoutRequest;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController
{
    public function __construct(private readonly BillingService $billingService)
    {
    }

    public function addItem(AddCartItemRequest $request)
    {
        $summary = $this->billingService->addItemToCart($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $summary,
        ]);
    }

    public function cartSummary(Request $request)
    {
        $summary = $this->billingService->getCartSummary($request->user());

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function checkout(CheckoutRequest $request)
    {
        $billSummary = $this->billingService->checkout($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Checkout completed successfully',
            'data' => $billSummary,
        ], 201);
    }
}
