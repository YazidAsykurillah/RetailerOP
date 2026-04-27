<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerGroup;
use App\Models\Transaction;
use App\Models\CustomerDeposit;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_group_id',
        'is_active',
        'deposit_balance',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'deposit_balance' => 'decimal:2',
    ];

    /**
     * Get the customer group that owns the customer.
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Get the transactions for the customer.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all deposit ledger entries for this customer.
     */
    public function deposits()
    {
        return $this->hasMany(CustomerDeposit::class)->orderBy('created_at', 'desc');
    }

    /**
     * Top-up the customer's deposit balance.
     * Wraps everything in a DB transaction and records balance snapshots.
     */
    public function topUpDeposit(float $amount, ?string $paymentMethod, ?string $notes, int $processedBy): CustomerDeposit
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $notes, $processedBy) {
            // Lock the row to prevent race conditions
            $customer = static::lockForUpdate()->find($this->id);

            $balanceBefore = (float) $customer->deposit_balance;
            $balanceAfter  = $balanceBefore + $amount;

            $deposit = CustomerDeposit::create([
                'customer_id'    => $this->id,
                'type'           => 'top_up',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'payment_method' => $paymentMethod,
                'notes'          => $notes,
                'processed_by'   => $processedBy,
            ]);

            $customer->update(['deposit_balance' => $balanceAfter]);
            $this->deposit_balance = $balanceAfter;

            return $deposit;
        });
    }

    /**
     * Deduct deposit balance when used on a transaction.
     * Returns the CustomerDeposit record created.
     */
    public function useDeposit(float $amount, int $transactionId, int $processedBy): CustomerDeposit
    {
        return DB::transaction(function () use ($amount, $transactionId, $processedBy) {
            $customer = static::lockForUpdate()->find($this->id);

            if ((float) $customer->deposit_balance < $amount) {
                throw new \Exception('Insufficient deposit balance. Available: ' . number_format($customer->deposit_balance, 0, ',', '.'));
            }

            $balanceBefore = (float) $customer->deposit_balance;
            $balanceAfter  = $balanceBefore - $amount;

            $deposit = CustomerDeposit::create([
                'customer_id'    => $this->id,
                'type'           => 'usage',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'transaction_id' => $transactionId,
                'processed_by'   => $processedBy,
            ]);

            $customer->update(['deposit_balance' => $balanceAfter]);
            $this->deposit_balance = $balanceAfter;

            return $deposit;
        });
    }

    /**
     * Recalculate deposit_balance from the ledger.
     * Called after a top_up record is edited or deleted.
     */
    public function recalculateDepositBalance(): void
    {
        DB::transaction(function () {
            $customer = static::lockForUpdate()->find($this->id);

            $topUps = CustomerDeposit::where('customer_id', $this->id)
                ->where('type', 'top_up')
                ->sum('amount');

            $usages = CustomerDeposit::where('customer_id', $this->id)
                ->where('type', 'usage')
                ->sum('amount');

            $newBalance = max(0, (float) $topUps - (float) $usages);
            $customer->update(['deposit_balance' => $newBalance]);
            $this->deposit_balance = $newBalance;
        });
    }
}