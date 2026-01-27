<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_variant_id',
        'quantity',
        'quantity_received',
        'cost',
        'subtotal',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->quantity_received);
    }
}
