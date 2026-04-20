<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode', 100)->nullable()->unique();
            }

            if (!Schema::hasColumn('products', 'item_code')) {
                $table->string('item_code', 100)->nullable()->unique();
            }

            if (!Schema::hasColumn('products', 'stock_qty')) {
                $table->integer('stock_qty')->default(0);
            }

            // Helpful for text lookup and stock monitoring filters.
            $table->index('name', 'products_name_idx');
            $table->index('stock_qty', 'products_stock_qty_idx');
        });

        DB::table('products')
            ->select(['id', 'sku', 'quantity'])
            ->orderBy('id')
            ->chunkById(200, function ($products) {
                foreach ($products as $product) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update([
                            'item_code' => $product->sku,
                            'barcode' => $product->sku,
                            'stock_qty' => $product->quantity,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropUnique('products_barcode_unique');
                $table->dropColumn('barcode');
            }

            if (Schema::hasColumn('products', 'item_code')) {
                $table->dropUnique('products_item_code_unique');
                $table->dropColumn('item_code');
            }

            if (Schema::hasColumn('products', 'stock_qty')) {
                $table->dropColumn('stock_qty');
            }

            $table->dropIndex('products_name_idx');
            $table->dropIndex('products_stock_qty_idx');
        });
    }
};
