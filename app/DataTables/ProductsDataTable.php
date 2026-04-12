<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" value="' . $row->id . '" class="product-checkbox">';
            })
            ->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '-';
            })
            ->addColumn('brand_name', function ($row) {
                return $row->brand ? $row->brand->name : '-';
            })
            ->addColumn('price_formatted', function ($row) {
                $variants = $row->variants;
                
                if ($variants->isEmpty()) {
                    // No variants, show base price
                    return number_format($row->base_price, 0, ',', '.');
                }
                
                $minPrice = $variants->min('price');
                $maxPrice = $variants->max('price');
                
                if ($minPrice == $maxPrice) {
                    // All variants have the same price
                    return number_format($minPrice, 0, ',', '.');
                }
                
                // Show price range
                return number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
            })
            ->addColumn('stock', function ($row) {
                $totalStock = $row->variants->sum('stock');
                return $totalStock;
            })
            ->addColumn('variants_list', function ($row) {
                $variants = $row->variants;
                $html = '';
                
                if ($variants->isEmpty()) {
                    $html = '<span class="text-muted mr-1">-</span>';
                } else {
                    $variantNames = $variants->pluck('name')->filter()->unique()->toArray();
                    
                    if (empty($variantNames)) {
                        $html = '<span class="text-muted mr-1">-</span>';
                    } else {
                        $maxDisplay = 3;
                        $totalVariants = count($variantNames);
                        $displayedVariants = array_slice($variantNames, 0, $maxDisplay);
                        $remainingCount = $totalVariants - $maxDisplay;
                        
                        $badges = array_map(function ($name) {
                            return '<span class="badge badge-info mr-1">' . e($name) . '</span>';
                        }, $displayedVariants);
        
                        $html = implode(' ', $badges);
        
                        if ($remainingCount > 0) {
                            $html .= ' <span class="badge badge-secondary mr-1">+' . $remainingCount . ' ' . __('product.more_variants') . '</span>';
                        }
                    }
                }

                $html .= ' <br><a href="' . route('admin.products.variants.index', $row->id) . '" class="btn btn-xs btn-warning" title="' . __('product.manage_variants') . '"><i class="fas fa-cubes"></i> </a>';
                
                return $html;
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">' . __('general.active') . '</span>'
                    : '<span class="badge badge-danger">' . __('general.inactive') . '</span>';
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.products.edit', $row->id) . '" class="btn btn-xs btn-primary" title="' . __('general.edit') . '">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '" title="' . __('general.delete') . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['checkbox', 'status', 'variants_list', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['category', 'brand', 'variants.variantValues', 'primaryImage']);

        if (request()->has('category_id') && request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        if (request()->has('brand_id') && request('brand_id')) {
            $query->where('brand_id', request('brand_id'));
        }

        return $query->orderBy('name', 'asc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'asc')
            ->pageLength(500)
            ->lengthMenu([10, 25, 50, 100, 500])
            ->selectStyleSingle()
            ->autoWidth(false)
            ->responsive(true)
            ->addTableClass('table-striped table-bordered w-100');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('checkbox')
                ->title('<input type="checkbox" id="select-all-products">')
                ->exportable(false)
                ->printable(false)
                ->width(30)
                ->addClass('text-center'),
            Column::computed('DT_RowIndex', '#')->width(50),

            Column::make('sku')->title(__('product.sku'))->width(100),
            Column::make('name')->title(__('product.name')),
            Column::computed('category_name')->title(__('category.singular')),
            Column::computed('brand_name')->title(__('brand.singular')),
            Column::computed('price_formatted')->title(__('product.price'))->width(180),
            Column::computed('variants_list')->title(__('product.variants'))->width(200),
            Column::computed('stock')->title(__('product.stock'))->width(60),
            Column::computed('status')->title(__('product.status'))->width(70),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(150)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}
