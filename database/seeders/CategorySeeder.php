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

        $menCategories = ['T-Shirts', 'Shirts', 'Pants', 'Jeans', 'Jackets', 'Suits', 'Shorts'];
        foreach ($menCategories as $index => $cat) {
            Category::create([
                'name' => $cat,
                'slug' => 'men-' . strtolower(str_replace(' ', '-', $cat)),
                'parent_id' => $men->id,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        // Women's Fashion
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'women',
            'description' => 'Women\'s fashion collection',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $womenCategories = ['Dresses', 'Tops', 'Blouses', 'Skirts', 'Pants', 'Jeans', 'Jackets'];
        foreach ($womenCategories as $index => $cat) {
            Category::create([
                'name' => $cat,
                'slug' => 'women-' . strtolower(str_replace(' ', '-', $cat)),
                'parent_id' => $women->id,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        // Kids Fashion
        $kids = Category::create([
            'name' => 'Kids',
            'slug' => 'kids',
            'description' => 'Kids fashion collection',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $kidsCategories = ['Boys', 'Girls', 'Baby'];
        foreach ($kidsCategories as $index => $cat) {
            Category::create([
                'name' => $cat,
                'slug' => 'kids-' . strtolower(str_replace(' ', '-', $cat)),
                'parent_id' => $kids->id,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }

        // Accessories
        $accessories = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Fashion accessories',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $accessoriesCategories = ['Bags', 'Belts', 'Hats', 'Scarves', 'Watches', 'Jewelry'];
        foreach ($accessoriesCategories as $index => $cat) {
            Category::create([
                'name' => $cat,
                'slug' => 'accessories-' . strtolower(str_replace(' ', '-', $cat)),
                'parent_id' => $accessories->id,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
