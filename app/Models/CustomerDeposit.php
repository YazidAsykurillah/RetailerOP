<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDeposit extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'payment_method',
        'transaction_id',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
    ];

    /**
     * The customer who owns this deposit record.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The transaction this deposit was used for (nullable, only for usage type).
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * The admin/cashier who processed this deposit.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get a human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'top_up' => 'Top Up',
            'usage'  => 'Usage',
            default  => ucfirst($this->type),
        };
    }
}
