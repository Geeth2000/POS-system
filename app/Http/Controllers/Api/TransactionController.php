<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\SalesService;

class TransactionController
{
    public function __construct(private readonly SalesService $salesService)
    {
    }

    /**
     * Display a listing of transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'cashier', 'items.product']);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->has('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        $transactions = $query->orderBy('created_at', 'desc')
                              ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * Create a new transaction
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = $this->salesService->createSale(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    /**
     * Display the specified transaction
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'cashier', 'items.product']);

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    /**
     * Get daily sales report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? today();

        $transactions = Transaction::whereDate('created_at', $date)
                                   ->get();

        $report = [
            'date' => $date,
            'total_transactions' => $transactions->count(),
            'total_sales' => $transactions->sum('total_amount'),
            'total_tax' => $transactions->sum('tax_amount'),
            'total_discount' => $transactions->sum('discount_amount'),
            'by_payment_method' => $transactions->groupBy('payment_method')
                                               ->map(fn($items) => $items->sum('total_amount')),
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get period sales report
     */
    public function periodReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $transactions = Transaction::whereBetween('created_at', [
                            $request->from_date . ' 00:00:00',
                            $request->to_date . ' 23:59:59',
                        ])->get();

        $report = [
            'period' => "{$request->from_date} to {$request->to_date}",
            'total_transactions' => $transactions->count(),
            'total_sales' => $transactions->sum('total_amount'),
            'total_tax' => $transactions->sum('tax_amount'),
            'total_discount' => $transactions->sum('discount_amount'),
            'average_transaction' => $transactions->count() > 0 
                ? $transactions->sum('total_amount') / $transactions->count() 
                : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}
