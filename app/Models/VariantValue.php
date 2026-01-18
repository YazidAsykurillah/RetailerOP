<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_type_id',
        'value',
        'color_code',
        'sort_order',
    ];

    /**
     * Get the variant type
     */
    public function variantType()
    {
        return $this->belongsTo(VariantType::class);
    }

    /**
     * Get all product variants using this value
     */
    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values');
    }
}
