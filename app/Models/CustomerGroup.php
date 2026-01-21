<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class CustomerGroup extends Model
{
    protected $fillable = [
        'name',
        'code',
        'percentage_discount',
        'is_default',
    ];

    protected $casts = [
        'percentage_discount' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    /**
     * Get the customers for the customer group.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
