<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductVariantTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Return a sample row
        return [
            ['VAR-001', 'Variant Name', '100000', '10', '80000', '5', 'Red', 'L']
        ];
    }

    public function headings(): array
    {
        return [
            'Sku',
            'Name',
            'Price',
            'Stock',
            'Cost',
            'Min Stock',
            'Attribute Color',
            'Attribute Size',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with a light gray background
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFEFEFEF',
                    ],
                ],
            ],
        ];
    }
}
