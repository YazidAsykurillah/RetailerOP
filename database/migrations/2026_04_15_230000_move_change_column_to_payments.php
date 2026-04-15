<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add 'change' column to transaction_payments
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->decimal('change', 15, 2)->default(0)->after('amount');
        });

        // 2. Normalize Data: Move existing 'change' from transactions to the latest payment
        $transactionsWithChange = DB::table('transactions')
            ->where('change', '>', 0)
            ->get();

        foreach ($transactionsWithChange as $transaction) {
            $latestPaymentId = DB::table('transaction_payments')
                ->where('transaction_id', $transaction->id)
                ->where('status', 'paid')
                ->latest('id')
                ->value('id');

            // Fallback to any latest payment if no 'paid' status found (unlikely for change > 0)
            if (!$latestPaymentId) {
                $latestPaymentId = DB::table('transaction_payments')
                    ->where('transaction_id', $transaction->id)
                    ->latest('id')
                    ->value('id');
            }

            if ($latestPaymentId) {
                DB::table('transaction_payments')
                    ->where('id', $latestPaymentId)
                    ->update(['change' => $transaction->change]);
            }
        }

        // 3. Drop 'change' column from transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('change');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add 'change' column back to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('change', 15, 2)->default(0)->after('amount_paid');
        });

        // 2. Move data back to transactions (summarized change from payments)
        $transactionsWithPaymentsChange = DB::table('transaction_payments')
            ->select('transaction_id', DB::raw('SUM(`change`) as total_change'))
            ->where('change', '>', 0)
            ->groupBy('transaction_id')
            ->get();

        foreach ($transactionsWithPaymentsChange as $paymentGroup) {
            DB::table('transactions')
                ->where('id', $paymentGroup->transaction_id)
                ->update(['change' => $paymentGroup->total_change]);
        }

        // 3. Drop 'change' column from transaction_payments
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropColumn('change');
        });
    }
};
