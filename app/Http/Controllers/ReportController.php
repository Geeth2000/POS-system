<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reporting dashboard.
     */
    public function index(Request $request)
    {
        // Default to last 30 days if no range provided
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1. Data Cards (Today's Stats)
        $today = Carbon::today();
        $dailySales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $totalTransactions = Transaction::whereDate('created_at', $today)->count();
        $avgTransactionValue = $totalTransactions > 0 ? ($dailySales / $totalTransactions) : 0;
        $lowStockAlert = Product::where('stock_qty', '<', 10)->count();

        // 2. Visual Analytics (Charts)
        
        // Bar Chart: Daily sales trend for the last 7 days
        $salesTrend = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        // Fill missing dates with 0
        $trendLabels = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $trendLabels[] = Carbon::now()->subDays($i)->format('M d');
            $trendData[] = $salesTrend[$date] ?? 0;
        }

        // Pie Chart: Payment Method Breakdown for the filtered period
        $paymentBreakdown = Transaction::whereBetween('created_at', [$start, $end])
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // 3. Transaction History Table
        $history = Transaction::with('cashier')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports', [
            'dailySales' => $dailySales,
            'totalTransactions' => $totalTransactions,
            'avgTransactionValue' => $avgTransactionValue,
            'lowStockAlert' => $lowStockAlert,
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'paymentBreakdown' => $paymentBreakdown,
            'history' => $history,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
