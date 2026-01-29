<?php

namespace App\Exports;

use App\Models\Brand;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProductTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected $categories;
    protected $brands;

    public function __construct()
    {
        $this->categories = Category::active()->orderBy('name')->pluck('name')->toArray();
        $this->brands = Brand::active()->orderBy('name')->pluck('name')->toArray();
    }

    public function array(): array
    {
        // Sample data
        return [
            ['PROD-001', 'Sample Product', '100000', '80000', 'Long description here', 'Short desc', $this->categories[0] ?? '', $this->brands[0] ?? '']
        ];
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Name',
            'Base Price',
            'Base Cost',
            'Description',
            'Short Description',
            'Category',
            'Brand',
            'Is Active',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEFEFEF'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $spreadsheet = $sheet->getDelegate()->getParent();
                
                // --- Categories Dropdown logic ---
                $categoryCount = count($this->categories);
                if ($categoryCount > 0) {
                    // Create a hidden sheet for categories
                    $catSheet = $spreadsheet->createSheet();
                    $catSheet->setTitle('Categories_Dropdown');
                    $catSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                    
                    // Populate categories
                    foreach ($this->categories as $idx => $name) {
                        $catSheet->setCellValue('A' . ($idx + 1), $name);
                    }
                    
                    // Define named range for categories
                    $spreadsheet->addNamedRange(
                        new \PhpOffice\PhpSpreadsheet\NamedRange(
                            'CategoriesList', 
                            $catSheet, 
                            '$A$1:$A$' . $categoryCount
                        )
                    );
                    
                    // Apply validation to Category Column (G) - Rows 2 to 1000
                    $validation = $sheet->getCell('G2')->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setPrompt('Please pick a value from the drop-down list.');
                    $validation->setFormula1('CategoriesList');
                    
                    // Clone validation to other rows
                    for ($i = 3; $i <= 1000; $i++) {
                        $sheet->getCell("G$i")->setDataValidation(clone $validation);
                    }
                }

                // --- Brands Dropdown logic ---
                $brandCount = count($this->brands);
                if ($brandCount > 0) {
                     // Create a hidden sheet for brands if separate sheet needed or reuse same hidden sheet with different column
                     // Let's use a separate sheet to be safe and clean
                    $brandSheet = $spreadsheet->createSheet();
                    $brandSheet->setTitle('Brands_Dropdown');
                    $brandSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                    
                    // Populate brands
                    foreach ($this->brands as $idx => $name) {
                        $brandSheet->setCellValue('A' . ($idx + 1), $name);
                    }
                    
                    // Define named range for brands
                    $spreadsheet->addNamedRange(
                        new \PhpOffice\PhpSpreadsheet\NamedRange(
                            'BrandsList', 
                            $brandSheet, 
                            '$A$1:$A$' . $brandCount
                        )
                    );

                    // Apply validation to Brand Column (H) - Rows 2 to 1000
                    $validation = $sheet->getCell('H2')->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setPrompt('Please pick a value from the drop-down list.');
                    $validation->setFormula1('BrandsList');
                    
                    for ($i = 3; $i <= 1000; $i++) {
                        $sheet->getCell("H$i")->setDataValidation(clone $validation);
                    }
                }
            },
        ];
    }
}
