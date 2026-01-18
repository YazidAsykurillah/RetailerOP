<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'amount_paid',
        'change',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    /**
     * Boot method to auto-generate invoice number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->invoice_no)) {
                $transaction->invoice_no = self::generateInvoiceNumber();
            }
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastTransaction = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastTransaction ? ((int) substr($lastTransaction->invoice_no, -4) + 1) : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the cashier/user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get transaction items
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'transfer' => 'Bank Transfer',
            'other' => 'Other',
            default => $this->payment_method,
        };
    }

    /**
     * Calculate totals from items
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->grand_total = $this->subtotal - $this->discount + $this->tax;
        $this->change = $this->amount_paid - $this->grand_total;
        $this->save();

        return $this;
    }
}
