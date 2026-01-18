<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'short_description',
        'base_price',
        'base_cost',
        'category_id',
        'brand_id',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'base_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get product images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get primary image
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get product variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total stock across all variants
     */
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock');
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute()
    {
        $primary = $this->primaryImage;
        if ($primary) {
            return asset('storage/' . $primary->image_path);
        }

        $first = $this->images()->first();
        return $first ? asset('storage/' . $first->image_path) : null;
    }
}
