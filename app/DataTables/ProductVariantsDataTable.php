<?php

namespace App\DataTables;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductVariantsDataTable extends DataTable
{
    protected $productId;

    public function forProduct($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('variant_values', function ($row) {
                $values = $row->variantValues->map(function ($value) {
                    return '<span class="badge badge-secondary">' . $value->variantType->name . ': ' . $value->value . '</span>';
                })->implode(' ');
                return $values ?: '-';
            })
            ->addColumn('price_formatted', function ($row) {
                return 'Rp ' . number_format($row->price, 0, ',', '.');
            })
            ->addColumn('cost_formatted', function ($row) {
                return 'Rp ' . number_format($row->cost, 0, ',', '.');
            })
            ->addColumn('stock_status', function ($row) {
                $stockClass = $row->is_low_stock ? 'text-danger font-weight-bold' : 'text-success';
                return '<span class="' . $stockClass . '">' . $row->stock . '</span>';
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.products.variants.edit', [$row->product_id, $row->id]) . '" class="btn btn-xs btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['variant_values', 'stock_status', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProductVariant $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('product_id', $this->productId)
            ->with(['variantValues.variantType'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('product-variants-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::make('sku')->title('SKU')->width(120),
            Column::make('name')->title('Variant Name'),
            Column::computed('variant_values')->title('Attributes'),
            Column::computed('price_formatted')->title('Price')->width(100),
            Column::computed('cost_formatted')->title('Cost')->width(100),
            Column::computed('stock_status')->title('Stock')->width(70),
            Column::computed('status')->title('Status')->width(80),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductVariants_' . date('YmdHis');
    }
}
