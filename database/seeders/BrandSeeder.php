<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Nadheera', 'description' => 'Luxury Fashion'],
            ['name' => 'Nike', 'description' => 'Just Do It'],
            ['name' => 'Adidas', 'description' => 'Impossible Is Nothing'],
            ['name' => 'Zara', 'description' => 'Fashion Forward'],
            ['name' => 'H&M', 'description' => 'Fashion and Quality at the Best Price'],
            ['name' => 'Uniqlo', 'description' => 'Made for All'],
            ['name' => 'Levi\'s', 'description' => 'Quality Never Goes Out of Style'],
            ['name' => 'Calvin Klein', 'description' => 'Between Love and Madness Lies Obsession'],
            ['name' => 'Tommy Hilfiger', 'description' => 'Classic American Cool'],
            ['name' => 'Gucci', 'description' => 'Quality is Remembered Long After Price is Forgotten'],
            ['name' => 'Prada', 'description' => 'Luxury Fashion'],  
        ];

        foreach ($brands as $index => $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => strtolower(str_replace(['\'', ' '], ['', '-'], $brand['name'])),
                'description' => $brand['description'],
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
