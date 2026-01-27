<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Men's Fashion
        $men = Category::create([
            'name' => 'Men',
            'slug' => 'men',
            'description' => 'Men\'s fashion collection',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Women's Fashion
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'women',
            'description' => 'Women\'s fashion collection',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        
    }
}
