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
                
                if ($variants->isEmpty()) {
                    return '<span class="text-muted">-</span>';
                }
                
                $variantNames = $variants->pluck('name')->filter()->unique()->toArray();
                
                if (empty($variantNames)) {
                    return '<span class="text-muted">-</span>';
                }
                
                $badges = array_map(function ($name) {
                    return '<span class="badge badge-info mr-1">' . e($name) . '</span>';
                }, $variantNames);
                
                return implode(' ', $badges);
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('image', function ($row) {
                $imageUrl = $row->primary_image_url;
                if ($imageUrl) {
                    return '<img src="' . $imageUrl . '" alt="' . $row->name . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
                }
                return '<span class="text-muted">No image</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.products.variants.index', $row->id) . '" class="btn btn-xs btn-info" title="Manage Variants">
                        <i class="fas fa-cubes"></i>
                    </a>
                    <a href="' . route('admin.products.edit', $row->id) . '" class="btn btn-xs btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['status', 'variants_list', 'image', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['category', 'brand', 'variants.variantValues', 'primaryImage'])
            ->orderBy('created_at', 'desc');
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
            ->orderBy(1)
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
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::computed('image', 'Image')->width(70),
            Column::make('sku')->title('SKU')->width(100),
            Column::make('name')->title('Product Name'),
            Column::computed('category_name')->title('Category'),
            Column::computed('brand_name')->title('Brand'),
            Column::computed('price_formatted')->title('Price')->width(180),
            Column::computed('variants_list')->title('Variants')->width(200),
            Column::computed('stock')->title('Stock')->width(60),
            Column::computed('status')->title('Status')->width(70),
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
