<?php

namespace App\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
{
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
            ->addColumn('customer', function ($row) {
                return $row->customer_name ?: '<span class="text-muted">Walk-in Customer</span>';
            })
            ->addColumn('grand_total_formatted', function ($row) {
                return '<span class="font-weight-bold">' . number_format($row->grand_total, 0, ',', '.') . '</span>';
            })
            ->addColumn('payment_method_badge', function ($row) {
                $colors = [
                    'cash' => 'success',
                    'card' => 'info',
                    'transfer' => 'primary',
                    'other' => 'secondary',
                ];
                $color = $colors[$row->payment_method] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . $row->payment_method_label . '</span>';
            })
            ->addColumn('cashier', function ($row) {
                return $row->user->name ?? '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.transactions.show', $row->id) . '" class="btn btn-xs btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.transactions.print', $row->id) . '" class="btn btn-xs btn-secondary" title="Print Receipt" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                ';
            })
            ->filterColumn('customer', function($query, $keyword) {
                $query->where('customer_name', 'like', "%{$keyword}%");
            })
            ->filterColumn('cashier', function($query, $keyword) {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['customer', 'grand_total_formatted', 'payment_method_badge', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Transaction $model): QueryBuilder
    {
        $query = $model->newQuery()->with('user');

        // Filter by date range
        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        // Filter by payment method
        if (request()->has('payment_method') && request('payment_method')) {
            $query->where('payment_method', request('payment_method'));
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.date_from = $('#date_from').val(); data.date_to = $('#date_to').val(); data.payment_method = $('#payment_method').val();")
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->autoWidth(false)
            ->responsive(true)
            ->addTableClass('table-striped table-bordered w-100')
            ->parameters([
                'footerCallback' => 'function (row, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === "string" ?
                            i.replace(/<[^>]+>/g, "").replace(/[\$.]/g, "") * 1 :
                            typeof i === "number" ?
                                i : 0;
                    };
                    var total = api
                        .column(4, { page: "current" })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    $(api.column(4).footer()).html(
                        new Intl.NumberFormat("id-ID").format(total)
                    );
                }'
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50)->footer(''),
            Column::make('invoice_no')->title('Invoice')->footer(''),
            Column::computed('date')->title('Date')->footer(''),
            Column::computed('customer')->title('Customer')->footer(''),
            Column::computed('grand_total_formatted')->title('Total')->addClass('text-right')->footer(''),
            Column::computed('payment_method_badge')->title('Payment')->addClass('text-center')->footer(''),
            Column::computed('cashier')->title('Cashier')->footer(''),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center')
                ->footer(''),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Transactions_' . date('YmdHis');
    }
}
