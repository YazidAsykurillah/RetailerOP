<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'sku' => $this->faker->unique()->ean13,
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(10000, 100000),
            'stock' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
