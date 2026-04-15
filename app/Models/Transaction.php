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
        'payment_mode',
        'payment_status',
        'amount_paid',
        'change',
        'notes',
        'customer_id',
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
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get transaction items
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get transaction payments (installments)
     */
    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
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
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'Completed',
            'partial' => 'Not Completed',
            'unpaid' => 'Unpaid',
            default => $this->payment_status,
        };
    }

    /**
     * Refresh payment status and amount paid based on payment history
     */
    public function refreshPaymentStatus()
    {
        $totalPaid = $this->payments()
            ->where('status', 'paid')
            ->sum('amount');
        
        $this->amount_paid = $totalPaid;
        
        if ($totalPaid >= $this->grand_total) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->change = $totalPaid > $this->grand_total ? ($totalPaid - $this->grand_total) : 0;
        $this->save();

        return $this;
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

    /**
     * Get the outstanding balance (remaining amount due)
     */
    public function getOutstandingBalanceAttribute()
    {
        return max(0, $this->grand_total - ($this->amount_paid - $this->change));
    }
}
