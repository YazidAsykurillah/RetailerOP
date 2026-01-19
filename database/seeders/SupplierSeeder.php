<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Global Supplier Indonesia',
                'contact_person' => 'Budi Santoso',
                'email' => 'budi@globalsupplier.id',
                'phone' => '+62 21 5551234',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10110',
                'website' => 'https://globalsupplier.id',
                'tax_id' => '01.234.567.8-901.000',
                'payment_terms' => 'Net 30',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'CV. Mitra Sejahtera',
                'contact_person' => 'Siti Nurhaliza',
                'email' => 'siti@mitrasejahtera.co.id',
                'phone' => '+62 22 7779876',
                'address' => 'Jl. Asia Afrika No. 88, Bandung, Jawa Barat 40261',
                'website' => 'https://mitrasejahtera.co.id',
                'tax_id' => '02.345.678.9-012.000',
                'payment_terms' => 'Net 45',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'UD. Berkah Jaya',
                'contact_person' => 'Ahmad Firdaus',
                'email' => 'ahmad@berkahjaya.com',
                'phone' => '+62 31 8881234',
                'address' => 'Jl. Tunjungan No. 45, Surabaya, Jawa Timur 60275',
                'website' => null,
                'tax_id' => '03.456.789.0-123.000',
                'payment_terms' => 'COD',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'PT. Prima Wholesale',
                'contact_person' => 'Dewi Lestari',
                'email' => 'dewi@primawholesale.id',
                'phone' => '+62 274 5551122',
                'address' => 'Jl. Malioboro No. 56, Yogyakarta, DIY 55213',
                'website' => 'https://primawholesale.id',
                'tax_id' => '04.567.890.1-234.000',
                'payment_terms' => 'Net 60',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'CV. Sumber Makmur',
                'contact_person' => 'Rudi Hartono',
                'email' => 'rudi@sumbermakmur.com',
                'phone' => '+62 361 7773456',
                'address' => 'Jl. Sunset Road No. 99, Denpasar, Bali 80361',
                'website' => null,
                'tax_id' => null,
                'payment_terms' => 'Net 30',
                'is_active' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
