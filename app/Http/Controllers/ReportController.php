<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
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
        $startDate = $request->input('start_date', Carbon::now('Asia/Colombo')->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now('Asia/Colombo')->toDateString());

        // Parse with timezone for correct start/end of day
        $start = Carbon::parse($startDate, 'Asia/Colombo')->startOfDay()->timezone('UTC');
        $end = Carbon::parse($endDate, 'Asia/Colombo')->endOfDay()->timezone('UTC');

        // 1. Data Cards (Today's stats based on sales records)
        $todayStart = Carbon::today('Asia/Colombo')->startOfDay()->timezone('UTC');
        $todayEnd = Carbon::today('Asia/Colombo')->endOfDay()->timezone('UTC');
        $dailySales = Sale::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total_amount');
        $totalTransactions = Sale::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $avgTransactionValue = $totalTransactions > 0 ? ($dailySales / $totalTransactions) : 0;
        $lowStockAlert = Product::whereRaw('COALESCE(stock_qty, quantity, 0) < 10')->count();

        // 2. Visual Analytics (Charts)
        
        // Bar Chart: Daily sales trend for the last 7 days
        $salesTrend = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now('Asia/Colombo')->subDays(6)->startOfDay()->timezone('UTC'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        // Fill missing dates with 0
        $trendLabels = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Colombo')->subDays($i)->toDateString();
            $trendLabels[] = Carbon::now('Asia/Colombo')->subDays($i)->format('M d');
            $trendData[] = $salesTrend[$date] ?? 0;
        }

        // Pie Chart: Payment Method Breakdown for the filtered period
        $paymentBreakdown = Sale::whereBetween('created_at', [$start, $end])
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // 3. Transaction History Table (Using Sale model)
        $history = Sale::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($sale) {
                // Map Sale fields to match what the Blade view expects for Transactions
                $sale->transaction_number = 'INV-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
                $sale->cashier = $sale->user; 
                return $sale;
            });

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
