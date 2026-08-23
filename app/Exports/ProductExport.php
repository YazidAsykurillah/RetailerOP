<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    use Exportable;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function array(): array
    {
        // Gunakan logic query yang sama persis dengan exportPdf
        $query = Product::select(['id', 'name', 'sku']);

        if ($this->request->has('category_id') && $this->request->category_id) {
            $query->where('category_id', $this->request->category_id);
        }

        if ($this->request->has('brand_id') && $this->request->brand_id) {
            $query->where('brand_id', $this->request->brand_id);
        }

        $stockOnly = $this->request->has('stock_only') && $this->request->stock_only == 'true';
        
        if ($stockOnly) {
            $query->whereHas('variants', function($q) {
                $q->where('stock', '>', 0);
            });
            $query->with(['variants' => function($q) {
                $q->select(['id', 'product_id', 'name', 'sku', 'price', 'stock'])
                  ->where('stock', '>', 0)
                  ->orderBy('name');
            }]);
        } else {
            $query->with(['variants' => function($q) {
                $q->select(['id', 'product_id', 'name', 'sku', 'price', 'stock'])
                  ->orderBy('name');
            }]);
        }

        $rows = [];
        $rowNumber = 0;
        
        $query->orderBy('name', 'asc')->chunk(100, function ($chunk) use (&$rows, &$rowNumber) {
            foreach ($chunk as $product) {
                $rowNumber++;
                
                // Baris Produk (Sama seperti TR utama di PDF)
                $rows[] = [
                    $rowNumber,
                    $product->sku ?: '-',
                    $product->name,
                    '', // Price kosong untuk parent product
                    '', // Stock kosong untuk parent product
                ];

                // Baris Varian (Sama seperti TR loop variant di PDF)
                foreach ($product->variants as $variant) {
                    $rows[] = [
                        '', // No kosong untuk baris variant
                        '   ' . ($variant->sku ?: '-'), // Tambahkan spasi agar terlihat indent seperti padding-left di PDF
                        $variant->name ?: (__('variant.standard') ?? 'Standard'),
                        $variant->price,
                        $variant->stock,
                    ];
                }
            }
        });

        return $rows;
    }

    public function headings(): array
    {
        return [
            __('general.no') ?? 'No',
            __('product.sku') ?? 'SKU',
            __('product.name') ?? 'Name',
            __('product.price') ?? 'Price',
            __('product.stock') ?? 'Stock'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header table bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Format price column with thousands separator
                $sheet->getStyle("D2:D{$highestRow}")->getNumberFormat()->setFormatCode('#,##0');
                
                // Align Stock to right
                $sheet->getStyle("E2:E{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                // Styling logic matching the PDF: Product rows get bold font and grey background
                for ($row = 2; $row <= $highestRow; $row++) {
                    $noColumnValue = $sheet->getCell("A{$row}")->getValue();
                    
                    // Jika kolom 'No' ada isinya, berarti ini adalah baris Parent Product
                    if ($noColumnValue != '') {
                        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F9F9F9'] // Warna background sesuai dengan PDF (#f9f9f9)
                            ]
                        ]);
                    }
                }
            },
        ];
    }
}
