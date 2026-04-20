<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'stock_qty'], 'products_active_stock_idx');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->index(['product_id', 'transaction_id'], 'transaction_items_product_transaction_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropIndex('transaction_items_product_transaction_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_stock_idx');
        });
    }
};
