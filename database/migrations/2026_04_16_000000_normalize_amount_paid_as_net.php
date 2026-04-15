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
        // Normalize 'amount_paid' in 'transactions' table to be the net amount (amount - change)
        // based on existing 'transaction_payments' records.
        $transactions = DB::table('transactions')->get();

        foreach ($transactions as $transaction) {
            $netPaid = DB::table('transaction_payments')
                ->where('transaction_id', $transaction->id)
                ->where('status', 'paid')
                ->sum(DB::raw('amount - `change`'));

            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update(['amount_paid' => $netPaid]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: Re-calculate 'amount_paid' as the gross sum of payments
        $transactions = DB::table('transactions')->get();

        foreach ($transactions as $transaction) {
            $grossPaid = DB::table('transaction_payments')
                ->where('transaction_id', $transaction->id)
                ->where('status', 'paid')
                ->sum('amount');

            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update(['amount_paid' => $grossPaid]);
        }
    }
};
