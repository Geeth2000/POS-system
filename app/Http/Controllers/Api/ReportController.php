<?php

namespace App\Http\Controllers\Api;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function dailySalesTotal(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = $request->input('date', today()->toDateString());

        return response()->json([
            'success' => true,
            'data' => $this->reportService->dailySalesTotal($date),
        ]);
    }

    public function topSellingProducts(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->reportService->topSellingProducts(
                $request->input('from_date'),
                $request->input('to_date'),
                (int) $request->input('limit', 10)
            ),
        ]);
    }

    public function lowStockItems(Request $request)
    {
        $request->validate([
            'threshold' => 'nullable|integer|min:0|max:1000000',
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->reportService->lowStockItems((int) $request->input('threshold', 10)),
        ]);
    }
}
