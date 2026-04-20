<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index(Request $request)
    {
        $sales = Sale::query()
            ->with(['user:id,name', 'items.product:id,name'])
            ->latest()
            ->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    public function store(StoreSaleRequest $request)
    {
        $sale = $this->saleService->createSale($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sale created successfully',
            'data' => $sale,
        ], 201);
    }

    public function show(Sale $sale)
    {
        return response()->json([
            'success' => true,
            'data' => $sale->load(['user:id,name', 'items.product']),
        ]);
    }
}
