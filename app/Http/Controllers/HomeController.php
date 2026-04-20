<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'web.role:admin,manager,cashier']);
    }

    /**
     * Show the admin/manager dashboard with live stats.
     */
    public function index()
    {
        $user = auth()->user();

        // ── Admin / Manager View ──────────────────────────────────
        if ($user->isAdmin() || $user->isManager()) {
            $stats = [
                'total_users'     => User::where('id', '!=', $user->id)
                                         ->when($user->isManager(), fn($q) => $q->where('role', 'cashier'))
                                         ->count(),
                'today_sales'     => Sale::whereDate('created_at', today())->count(),
                'today_revenue'   => Sale::whereDate('created_at', today())->sum('total_amount'),
                'total_products'  => Product::count(),
                'low_stock'       => Product::where('stock_qty', '<=', 10)->count(),
            ];

            $recentStaff = User::where('id', '!=', $user->id)
                ->when($user->isManager(), fn($q) => $q->where('role', 'cashier'))
                ->latest()
                ->take(5)
                ->get();

            return view('home', compact('stats', 'recentStaff'));
        }

        // ── Cashier View ──────────────────────────────────────────
        $stats = [
            'my_sales_today'   => $user->sales()->whereDate('created_at', today())->count(),
            'my_revenue_today' => $user->sales()->whereDate('created_at', today())->sum('total_amount'),
            'avg_sale_value'   => $user->sales()->whereDate('created_at', today())->avg('total_amount') ?? 0,
        ];

        $recentSales = $user->sales()->latest()->take(10)->get();

        return view('cashier.dashboard', compact('stats', 'recentSales'));
    }
}
