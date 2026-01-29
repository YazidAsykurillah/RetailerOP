<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    protected $updateExisting;

    public function __construct(bool $updateExisting = false)
    {
        $this->updateExisting = $updateExisting;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        $rows->each(function ($row, $index) {
            // Skip empty rows
            if ($row->filter()->isEmpty()) {
                return;
            }

            // Validate Row Data
            $validator = Validator::make($row->toArray(), [
                'sku' => 'required|string',
                'name' => 'required|string',
                'base_price' => 'required|numeric|min:0',
                'base_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'category' => 'nullable|string',
                'brand' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'file' => "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all())
                ]);
            }

            // Handle Category
            $categoryId = null;
            if (!empty($row['category'])) {
                $category = Category::firstOrCreate(
                    ['name' => $row['category']],
                    ['slug' => Str::slug($row['category'])]
                );
                $categoryId = $category->id;
            }

            // Handle Brand
            $brandId = null;
            if (!empty($row['brand'])) {
                $brand = Brand::firstOrCreate(
                    ['name' => $row['brand']],
                    ['slug' => Str::slug($row['brand'])]
                );
                $brandId = $brand->id;
            }

            $sku = $row['sku'];
            $product = Product::where('sku', $sku)->first();

            $data = [
                'name' => $row['name'],
                'slug' => Str::slug($row['name']),
                'base_price' => $row['base_price'],
                'base_cost' => $row['base_cost'] ?? 0,
                'description' => $row['description'],
                'short_description' => $row['short_description'],
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
            ];

            if ($product) {
                if (!$this->updateExisting) {
                    return; // Skip existing
                }
                // Update existing
                $product->update($data);
            } else {
                // Create new
                $data['sku'] = $sku;
                Product::create($data);
            }
        });
    }
}
