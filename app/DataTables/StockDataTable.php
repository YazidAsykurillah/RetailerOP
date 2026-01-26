<?php

namespace App\DataTables;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StockDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->smart(false)
            ->addIndexColumn()
            ->addColumn('product_name', function ($row) {
                return $row->product_name_db ?? '-';
            })
            ->addColumn('variant_name', function ($row) {
                return $row->name ?: 'Default';
            })
            ->addColumn('stock_display', function ($row) {
                $class = $row->is_low_stock ? 'text-danger font-weight-bold' : '';
                return '<span class="' . $class . '">' . number_format($row->stock) . '</span>';
            })
            ->addColumn('min_stock_display', function ($row) {
                return number_format($row->min_stock);
            })
            ->addColumn('status', function ($row) {
                if ($row->stock <= 0) {
                    return '<span class="badge badge-danger">Out of Stock</span>';
                } elseif ($row->is_low_stock) {
                    return '<span class="badge badge-warning">Low Stock</span>';
                }
                return '<span class="badge badge-success">In Stock</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.stock.in', ['variant' => $row->id]) . '" class="btn btn-xs btn-success" title="Stock In">
                            <i class="fas fa-arrow-down"></i>
                        </a>
                        <a href="' . route('admin.stock.out', ['variant' => $row->id]) . '" class="btn btn-xs btn-warning" title="Stock Out">
                            <i class="fas fa-arrow-up"></i>
                        </a>
                        <a href="' . route('admin.stock.history', $row->id) . '" class="btn btn-xs btn-info" title="History">
                            <i class="fas fa-history"></i>
                        </a>
                    </div>
                ';
            })

            ->filter(function ($query) {
                if (request()->has('search') && isset(request('search')['value']) && !empty(request('search')['value'])) {
                    $keyword = request('search')['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('products.name', 'like', "%{$keyword}%")
                          ->orWhere('product_variants.name', 'like', "%{$keyword}%")
                          ->orWhere('product_variants.sku', 'like', "%{$keyword}%");
                    });
                }
            })
            ->with('total_stock', function() use ($query) {
                $q = $query->clone();
                if ($search = request('search')['value'] ?? null) {
                    $q->where(function($sub) use ($search) {
                         // Replicate the global search logic manually since we are outside the main query build
                         $sub->where('products.name', 'like', "%{$search}%")
                             ->orWhere('product_variants.name', 'like', "%{$search}%")
                             ->orWhere('product_variants.sku', 'like', "%{$search}%");
                    });
                }
                return $q->sum('stock');
            })
            ->rawColumns(['stock_display', 'status', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProductVariant $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select([
                'product_variants.*',
                'products.name as product_name_db'
            ]);

        // Filter by status
        if (request()->has('status') && !empty(request('status'))) {
            $status = request('status');
            if ($status === 'low_stock') {
                $query->lowStock();
            } elseif ($status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($status === 'in_stock') {
                $query->where('stock', '>', 0);
            }
        }
        
        // Handle legacy link (though we can now use the status filter for this too)
        if (request()->has('low_stock') && request('low_stock') == '1' && !request()->has('status')) {
            $query->lowStock();
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('stock-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->autoWidth(false)
            ->responsive(true)
            ->addTableClass('table-striped table-bordered w-100')
            ->parameters([
                'search' => [
                    'smart' => false,
                ],
            ])
            ->drawCallback("function(settings) {
                var api = this.api();
                var json = api.ajax.json(); 
                
                // Ensure footer exists
                if ($(api.table().node()).find('tfoot').length === 0) {
                    var footerRow = $('<tr></tr>');
                    api.columns().every(function() {
                        footerRow.append('<th class=\"text-center\"></th>');
                    });
                    $('<tfoot></tfoot>').append(footerRow).appendTo(api.table().node());
                }

                // Update footer values manually since api.column().footer() might be null for injected footer
                var footerRow = $(api.table().node()).find('tfoot tr');
                if(json && json.total_stock !== undefined) {
                    footerRow.find('th').eq(4).html('<b>' + new Intl.NumberFormat().format(json.total_stock) + '</b>').addClass('text-center');
                }
            }");
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::make('product_name')->data('product_name')->name('products.name')->title('Product'),
            Column::make('variant_name')->data('variant_name')->name('product_variants.name')->title('Variant'),
            Column::make('sku')->name('product_variants.sku')->title('SKU')->footer('Grand Total'),
            Column::computed('stock_display')->title('Stock')->addClass('text-center')->footer(''),
            Column::computed('min_stock_display')->title('Min Stock')->addClass('text-center'),
            Column::computed('status')->title('Status')->addClass('text-center'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Stock_' . date('YmdHis');
    }
}
