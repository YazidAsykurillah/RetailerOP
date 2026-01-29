<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ProductVariantImportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Setup necessary data
        $this->product = Product::factory()->create();
    }

    /** @test */
    public function it_can_import_variants()
    {
        Excel::fake();

        $response = $this->post(route('admin.products.variants.process-import', $this->product->id), [
            'file' => UploadedFile::fake()->create('variants.xlsx'),
        ]);

        $response->assertRedirect();
        Excel::assertImported('variants.xlsx');
    }

    // Since actual import logic test with Excel::fake() is limited to asserting import was called,
    // we should unit test the Import class itself if we want to test logic, 
    // or use a real file. But constructing a real Excel file in test is complex.
    // Let's rely on standard Laravel Excel testing or basic integration test if possible.
    // Alternatively, we can test the `collection` method of the Import class directly.

    /** @test */
    public function import_class_handles_rows_correctly()
    {
        $import = new \App\Imports\ProductVariantImport($this->product, false);
        
        $rows = collect([
            [
                'sku' => 'VAR-001',
                'name' => 'Variant 1',
                'price' => 10000,
                'stock' => 10,
                'attribute_color' => 'Red',
            ]
        ]);

        $import->collection($rows);

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $this->product->id,
            'sku' => 'VAR-001',
            'name' => 'Variant 1',
        ]);

        $variant = ProductVariant::where('sku', 'VAR-001')->first();
        $this->assertTrue($variant->variantValues()->where('value', 'Red')->exists());
    }

    /** @test */
    public function import_updates_existing_when_enabled()
    {
        // Create existing variant
        $variant = ProductVariant::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'VAR-001',
            'price' => 5000,
        ]);

        $import = new \App\Imports\ProductVariantImport($this->product, true);
        
        $rows = collect([
            [
                'sku' => 'VAR-001',
                'name' => 'Variant Updated',
                'price' => 10000,
                'stock' => 20,
            ]
        ]);

        $import->collection($rows);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'price' => 10000,
            'name' => 'Variant Updated',
        ]);
    }

    /** @test */
    public function import_skips_existing_when_disabled()
    {
        // Create existing variant
        $variant = ProductVariant::factory()->create([
            'product_id' => $this->product->id,
            'sku' => 'VAR-001',
            'price' => 5000,
        ]);

        $import = new \App\Imports\ProductVariantImport($this->product, false);
        
        $rows = collect([
            [
                'sku' => 'VAR-001',
                'name' => 'Should Not Update',
                'price' => 10000,
                'stock' => 20,
            ]
        ]);

        $import->collection($rows);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'price' => 5000,
        ]);
    }
}
