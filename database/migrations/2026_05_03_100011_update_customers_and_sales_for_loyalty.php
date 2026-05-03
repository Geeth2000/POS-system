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
        Schema::table('customers', function (Blueprint $table) {
            // Rename phone to phone_number and make it unique
            if (Schema::hasColumn('customers', 'phone')) {
                $table->renameColumn('phone', 'phone_number');
            }
            $table->string('phone_number')->unique()->change();
            
            // Add total_spend
            $table->decimal('total_spend', 12, 2)->default(0)->after('loyalty_points');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('total_spend');
            $table->dropUnique(['phone_number']);
            $table->renameColumn('phone_number', 'phone');
        });
    }
};
