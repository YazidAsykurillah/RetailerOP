<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => $this->faker->unique()->ean13,
            'base_price' => $this->faker->numberBetween(10000, 100000),
            'base_cost' => $this->faker->numberBetween(5000, 50000),
            'is_active' => true,
        ];
    }
}
