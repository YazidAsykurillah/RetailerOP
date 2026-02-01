<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use Illuminate\Database\Seeder;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (BusinessProfile::count() == 0) {
            BusinessProfile::create([
                'business_name' => 'Siskha Store',
                'business_address' => '123 Store Street, City, Country',
                'business_email' => 'info@siskha.store',
                'business_website' => 'https://siskha.store',
                'business_phone' => '+1234567890',
                'footer_text' => 'Thank you for your business!',
            ]);
        }
    }
}
