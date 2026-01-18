<?php

namespace App\DataTables;

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StockMovementsDataTable extends DataTable
{
    protected $variantId = null;

    /**
     * Set variant ID for filtering
     */
    public function forVariant($variantId)
    {
        $this->variantId = $variantId;
        return $this;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->addColumn('product_variant', function ($row) {
                $productName = $row->productVariant->product->name ?? '-';
                $variantName = $row->productVariant->name ?? 'Default';
                return $productName . ' - ' . $variantName;
            })
            ->addColumn('type_badge', function ($row) {
                $badges = [
                    'in' => '<span class="badge badge-success"><i class="fas fa-arrow-down"></i> Stock In</span>',
                    'out' => '<span class="badge badge-warning"><i class="fas fa-arrow-up"></i> Stock Out</span>',
                    'adjustment' => '<span class="badge badge-info"><i class="fas fa-sync"></i> Adjustment</span>',
                ];
                return $badges[$row->type] ?? $row->type;
            })
            ->addColumn('quantity_display', function ($row) {
                $prefix = $row->type === 'in' ? '+' : ($row->type === 'out' ? '-' : '');
                $class = $row->type === 'in' ? 'text-success' : ($row->type === 'out' ? 'text-danger' : 'text-info');
                return '<span class="' . $class . ' font-weight-bold">' . $prefix . number_format($row->quantity) . '</span>';
            })
            ->addColumn('user_name', function ($row) {
                return $row->user->name ?? '-';
            })
            ->rawColumns(['type_badge', 'quantity_display']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(StockMovement $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['productVariant.product', 'user'])
            ->select('stock_movements.*')
            ->orderBy('created_at', 'desc');

        // Filter by variant if specified
        if ($this->variantId || request()->has('variant_id')) {
            $variantId = $this->variantId ?: request('variant_id');
            $query->where('product_variant_id', $variantId);
        }

        // Filter by type if specified
        if (request()->has('type') && request('type')) {
            $query->where('type', request('type'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('stock-movements-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::computed('date')->title('Date'),
            Column::computed('product_variant')->title('Product / Variant'),
            Column::computed('type_badge')->title('Type')->addClass('text-center'),
            Column::computed('quantity_display')->title('Qty')->addClass('text-center'),
            Column::make('stock_before')->title('Before')->addClass('text-center'),
            Column::make('stock_after')->title('After')->addClass('text-center'),
            Column::make('reference')->title('Reference'),
            Column::computed('user_name')->title('By'),
            Column::make('notes')->title('Notes'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'StockMovements_' . date('YmdHis');
    }
}
