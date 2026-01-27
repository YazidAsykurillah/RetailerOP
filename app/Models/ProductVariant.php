<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'cost',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get variant values (Color: Red, Size: M, etc.)
     */
    public function variantValues()
    {
        return $this->belongsToMany(VariantValue::class, 'product_variant_values');
    }

    /**
     * Get stock movements
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope for active variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for low stock variants
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Check if variant is low stock
     */
    public function getIsLowStockAttribute()
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Get full display name (Product Name + Variant Values)
     */
    public function getFullNameAttribute()
    {
        $productName = $this->product->name ?? '';
        $variantName = $this->name ?? '';

        if ($variantName) {
            return $productName . ' - ' . $variantName;
        }

        return $productName;
    }

    /**
     * Update stock and record movement
     */
    public function adjustStock($quantity, $type, $userId, $reference = null, $notes = null, $supplierId = null)
    {
        $stockBefore = $this->stock;

        if ($type === 'in') {
            $this->stock += $quantity;
        } elseif ($type === 'out') {
            $this->stock -= $quantity;
        } else {
            $this->stock = $quantity; // adjustment sets exact value
        }

        $this->save();

        // Record movement
        StockMovement::create([
            'product_variant_id' => $this->id,
            'type' => $type,
            'quantity' => $type === 'adjustment' ? abs($this->stock - $stockBefore) : $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock,
            'reference' => $reference,
            'notes' => $notes,
            'user_id' => $userId,
            'supplier_id' => $supplierId,
        ]);

        return $this;
    }

    /**
     * Check if a variant with the same attribute combination already exists for a product.
     *
     * @param int $productId The product ID to check variants for
     * @param array $variantValueIds Array of variant value IDs
     * @param int|null $excludeVariantId Variant ID to exclude (for updates)
     * @return bool True if combination already exists
     */
    public static function combinationExists(int $productId, array $variantValueIds, ?int $excludeVariantId = null): bool
    {
        // Filter out empty values and convert to integers for consistent comparison
        $variantValueIds = array_values(array_filter(
            array_map('intval', $variantValueIds),
            fn($v) => $v > 0
        ));
        
        if (empty($variantValueIds)) {
            return false;
        }
        
        // Sort to ensure consistent comparison
        sort($variantValueIds);
        
        // Get all variants for this product (excluding current one if updating)
        $query = self::where('product_id', $productId);
        
        if ($excludeVariantId) {
            $query->where('id', '!=', $excludeVariantId);
        }
        
        $existingVariants = $query->with('variantValues')->get();
        
        foreach ($existingVariants as $existingVariant) {
            // Convert to integers for consistent comparison
            $existingValues = array_map('intval', $existingVariant->variantValues->pluck('id')->toArray());
            sort($existingValues);
            
            // Check if the combinations are exactly the same
            if ($existingValues === $variantValueIds) {
                return true;
            }
        }
        
        return false;
    }
    /**
     * Get all purchases containing this variant
     */
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
