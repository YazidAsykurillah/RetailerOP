<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'reference_number',
        'date',
        'status',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the purchase
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchase details
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Check if purchase is fully completed
     */
    public function getIsCompletedAttribute()
    {
        return $this->details->every(function ($detail) {
            return $detail->quantity_received >= $detail->quantity;
        });
    }

    /**
     * Update status based on received quantities
     */
    public function updateStatus()
    {
        // Refresh details to get latest quantities
        $this->load('details');
        
        $totalQuantity = $this->details->sum('quantity');
        $totalReceived = $this->details->sum('quantity_received');

        if ($totalReceived === 0) {
            $this->status = 'pending';
        } elseif ($totalReceived >= $totalQuantity) {
            $this->status = 'completed';
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
