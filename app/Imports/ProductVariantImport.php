<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;

class ProductVariantImport implements ToCollection, WithHeadingRow
{
    protected $product;
    protected $updateExisting;

    public function __construct(Product $product, bool $updateExisting = false)
    {
        $this->product = $product;
        $this->updateExisting = $updateExisting;
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
                'name' => 'required|string',
                'sku' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'cost' => 'nullable|numeric|min:0',
                'min_stock' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                // You might want to collect errors and report them, 
                // but for now let's just throw for the first error to keep it simple 
                // or log it. For better UX, we could bubble this up.
                // Let's create a custom message with row number.
                throw ValidationException::withMessages([
                    'file' => "Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all())
                ]);
            }

            $sku = $row['sku'];
            $variant = $this->product->variants()->where('sku', $sku)->first();

            if ($variant) {
                if (!$this->updateExisting) {
                    return; // Skip existing
                }
                // Update existing
                $variant->update([
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'cost' => $row['cost'] ?? 0,
                    'stock' => $row['stock'],
                    'min_stock' => $row['min_stock'] ?? 5,
                    'is_active' => true,
                ]);
            } else {
                // Create new
                $variant = $this->product->variants()->create([
                    'sku' => $sku,
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'cost' => $row['cost'] ?? 0,
                    'stock' => $row['stock'],
                    'min_stock' => $row['min_stock'] ?? 5,
                    'is_active' => true,
                ]);
            }

            // Handle Attributes
            $this->processAttributes($variant, $row);
        });
    }

    protected function processAttributes(ProductVariant $variant, $row)
    {
        $variantValueIds = [];

        foreach ($row as $key => $value) {
            if (str_starts_with($key, 'attribute_') && filled($value)) {
                // Extract attribute name (e.g., 'attribute_color' -> 'Color')
                $typeName = str_replace('attribute_', '', $key);
                $typeName = str_replace('_', ' ', $typeName); // 'attribute_screen_size' -> 'screen size'
                
                // Find or create Variant Type (Type name is case-insensitive usually, but let's strict match or capitalize)
                // Let's rely on exact match or create new if not exists? 
                // Usually types are pre-defined. But for flexible import, let's find existing by name (insensitive).
                
                $variantType = VariantType::where('name', 'LIKE', $typeName)->first();
                
                if (!$variantType) {
                    // Create new type if it doesn't exist? Or skip?
                    // Let's create it to be helpful.
                    $variantType = VariantType::create([
                        'name' => ucwords($typeName),
                        'type' => 'text', // Default to text
                        'sort_order' => 0,
                    ]);
                }

                // Find or create Variant Value
                $variantValue = VariantValue::firstOrCreate(
                    [
                        'variant_type_id' => $variantType->id,
                        'value' => $value,
                    ],
                    [
                        'sort_order' => 0,
                    ]
                );

                $variantValueIds[] = $variantValue->id;
            }
        }

        if (!empty($variantValueIds)) {
            $variant->variantValues()->sync($variantValueIds);
        }
    }
}
