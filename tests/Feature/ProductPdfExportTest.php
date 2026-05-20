<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_export_products_to_pdf()
    {
        // Create sample products and variants
        $product1 = Product::factory()->create(['name' => 'Alpha Product']);
        ProductVariant::create([
            'product_id' => $product1->id,
            'sku' => 'ALPHA-VAR-1',
            'name' => 'Variant A',
            'price' => 150000,
            'stock' => 10,
        ]);

        $product2 = Product::factory()->create(['name' => 'Beta Product']);
        ProductVariant::create([
            'product_id' => $product2->id,
            'sku' => 'BETA-VAR-1',
            'name' => 'Variant B',
            'price' => 250000,
            'stock' => 20,
        ]);

        $response = $this->get(route('admin.products.export-pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
