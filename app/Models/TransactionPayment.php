<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'change',
        'payment_method',
        'payment_date',
        'status',
        'notes',
        'deposit_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    /**
     * Get the transaction that owns the payment.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the deposit ledger entry linked to this payment (if paid via deposit).
     */
    public function deposit()
    {
        return $this->belongsTo(\App\Models\CustomerDeposit::class, 'deposit_id');
    }
}
