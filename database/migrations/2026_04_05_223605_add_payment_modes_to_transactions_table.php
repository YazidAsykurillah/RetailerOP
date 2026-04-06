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
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other', 'multiple'])->default('cash')->change();
            $table->enum('payment_mode', ['full', 'partial'])->default('full')->after('payment_method');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid')->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])->default('cash')->change();
            $table->dropColumn(['payment_mode', 'payment_status']);
        });
    }
};
