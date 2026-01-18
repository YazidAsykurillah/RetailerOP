<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantValue;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 3 products with 2 variants each for simplicity.
     */
    public function run(): void
    {
        // Get first available category and brand
        $category = Category::first();
        $brand = Brand::first();

        // Get variant values for color and size
        $colors = VariantValue::whereHas('variantType', function($q) {
            $q->where('slug', 'color');
        })->take(3)->pluck('id', 'value')->toArray();

        $sizes = VariantValue::whereHas('variantType', function($q) {
            $q->where('slug', 'size');
        })->take(3)->pluck('id', 'value')->toArray();

        $colorIds = array_values($colors);
        $sizeIds = array_values($sizes);
        $colorNames = array_keys($colors);
        $sizeNames = array_keys($sizes);

        // Sample products - only 3
        $products = [
            [
                'sku' => 'PRD-001',
                'name' => 'Classic T-Shirt',
                'description' => 'A comfortable classic t-shirt for everyday wear.',
                'short_description' => 'Classic comfort t-shirt',
                'base_price' => 150000,
                'base_cost' => 80000,
            ],
            [
                'sku' => 'PRD-002',
                'name' => 'Casual Polo',
                'description' => 'Smart casual polo shirt perfect for any occasion.',
                'short_description' => 'Smart casual polo',
                'base_price' => 250000,
                'base_cost' => 120000,
            ],
            [
                'sku' => 'PRD-003',
                'name' => 'Denim Jacket',
                'description' => 'Stylish denim jacket for a trendy look.',
                'short_description' => 'Trendy denim jacket',
                'base_price' => 450000,
                'base_cost' => 220000,
            ],
        ];

        foreach ($products as $index => $productData) {
            $productData['category_id'] = $category->id ?? null;
            $productData['brand_id'] = $brand->id ?? null;
            $productData['is_active'] = true;

            $product = Product::create($productData);

            // Create exactly 2 variants per product
            for ($v = 0; $v < 2; $v++) {
                $colorIndex = ($index + $v) % count($colorIds);
                $sizeIndex = $v % count($sizeIds);
                
                $colorName = $colorNames[$colorIndex] ?? 'Default';
                $sizeName = $sizeNames[$sizeIndex] ?? 'M';

                $variantSku = $product->sku . '-V' . ($v + 1);
                $variantName = $colorName . ' - ' . $sizeName;

                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantSku,
                    'name' => $variantName,
                    'price' => $product->base_price,
                    'cost' => $product->base_cost,
                    'stock' => ($v + 1) * 10, // 10, 20
                    'min_stock' => 5,
                    'is_active' => true,
                ]);

                // Attach variant values
                $attachValues = [];
                if (isset($colorIds[$colorIndex])) {
                    $attachValues[] = $colorIds[$colorIndex];
                }
                if (isset($sizeIds[$sizeIndex])) {
                    $attachValues[] = $sizeIds[$sizeIndex];
                }
                if (!empty($attachValues)) {
                    $productVariant->variantValues()->attach($attachValues);
                }
            }
        }
    }
}
