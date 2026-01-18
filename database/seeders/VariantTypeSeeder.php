<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VariantType;
use App\Models\VariantValue;

class VariantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Color variant type
        $color = VariantType::create([
            'name' => 'Color',
            'slug' => 'color',
            'sort_order' => 1,
        ]);

        // Color values
        $colors = [
            ['value' => 'Black', 'color_code' => '#000000'],
            ['value' => 'White', 'color_code' => '#FFFFFF'],
            ['value' => 'Red', 'color_code' => '#FF0000'],
            ['value' => 'Blue', 'color_code' => '#0000FF'],
            ['value' => 'Navy', 'color_code' => '#000080'],
            ['value' => 'Green', 'color_code' => '#008000'],
            ['value' => 'Yellow', 'color_code' => '#FFFF00'],
            ['value' => 'Pink', 'color_code' => '#FFC0CB'],
            ['value' => 'Purple', 'color_code' => '#800080'],
            ['value' => 'Gray', 'color_code' => '#808080'],
            ['value' => 'Brown', 'color_code' => '#A52A2A'],
            ['value' => 'Orange', 'color_code' => '#FFA500'],
            ['value' => 'Beige', 'color_code' => '#F5F5DC'],
        ];

        foreach ($colors as $index => $colorData) {
            VariantValue::create([
                'variant_type_id' => $color->id,
                'value' => $colorData['value'],
                'color_code' => $colorData['color_code'],
                'sort_order' => $index + 1,
            ]);
        }

        // Size variant type
        $size = VariantType::create([
            'name' => 'Size',
            'slug' => 'size',
            'sort_order' => 2,
        ]);

        // Size values
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        foreach ($sizes as $index => $sizeValue) {
            VariantValue::create([
                'variant_type_id' => $size->id,
                'value' => $sizeValue,
                'sort_order' => $index + 1,
            ]);
        }

        // Material variant type
        $material = VariantType::create([
            'name' => 'Material',
            'slug' => 'material',
            'sort_order' => 3,
        ]);

        // Material values
        $materials = ['Cotton', 'Polyester', 'Silk', 'Denim', 'Leather', 'Wool', 'Linen'];

        foreach ($materials as $index => $materialValue) {
            VariantValue::create([
                'variant_type_id' => $material->id,
                'value' => $materialValue,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
