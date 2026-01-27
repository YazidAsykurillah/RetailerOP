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
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
