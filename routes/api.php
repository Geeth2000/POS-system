<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);

        // Admin full-access modules
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::post('/customers/{customer}/loyalty-points', [CustomerController::class, 'addLoyaltyPoints']);
        Route::apiResource('transactions', TransactionController::class, ['only' => ['index', 'store', 'show']]);
        Route::get('/transactions/reports/daily', [TransactionController::class, 'dailyReport']);
        Route::get('/transactions/reports/period', [TransactionController::class, 'periodReport']);
    });

    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Products routes
    Route::middleware(RoleMiddleware::class . ':admin,manager,cashier')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/search', [ProductController::class, 'search']);
        Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product');
    });

    Route::middleware(RoleMiddleware::class . ':admin,manager')->group(function () {
        Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        // Manager can view sales
        Route::get('/sales', [SaleController::class, 'index']);
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->whereNumber('sale');

        // Reporting APIs
        Route::get('/reports/daily-sales-total', [ReportController::class, 'dailySalesTotal']);
        Route::get('/reports/top-selling-products', [ReportController::class, 'topSellingProducts']);
        Route::get('/reports/low-stock-items', [ReportController::class, 'lowStockItems']);
    });

    // Cashier and Manager can create sales (admin also allowed)
    Route::middleware(RoleMiddleware::class . ':admin,manager,cashier')->group(function () {
        Route::post('/sales', [SaleController::class, 'store']);
        Route::post('/billing/cart/items', [BillingController::class, 'addItem']);
        Route::delete('/billing/cart/items/{product_id}', [BillingController::class, 'removeItem']);
        Route::get('/billing/cart/summary', [BillingController::class, 'cartSummary']);
        Route::post('/billing/checkout', [BillingController::class, 'checkout']);
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'POS System API is running',
    ]);
});
