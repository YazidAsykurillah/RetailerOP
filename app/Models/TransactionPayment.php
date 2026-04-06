<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the transaction that owns the payment.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
