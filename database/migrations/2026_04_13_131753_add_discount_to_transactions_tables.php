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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_discount', 15, 2)->default(0)->after('total_price');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->decimal('discount', 15, 2)->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('total_discount');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
