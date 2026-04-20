<?php

namespace App\DataTables;

use App\Models\TransactionPayment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class PaymentsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('payment_date_formatted', function ($row) {
                return $row->payment_date ? $row->payment_date->format('d M Y') : '-';
            })
            ->addColumn('invoice_no', function ($row) {
                return $row->transaction->invoice_no ?? '-';
            })
            ->addColumn('customer', function ($row) {
                return $row->transaction->customer_name ?: '<span class="text-muted">' . __('pos.walk_in_customer') . '</span>';
            })
            ->addColumn('amount_formatted', function ($row) {
                return '<span class="text-info font-weight-bold">' . number_format($row->amount, 0, ',', '.') . '</span>';
            })
            ->addColumn('change_formatted', function ($row) {
                return '<span class="text-muted">' . number_format($row->change, 0, ',', '.') . '</span>';
            })
            ->addColumn('net_amount_formatted', function ($row) {
                $net = $row->amount - $row->change;
                return '<span class="text-success font-weight-bold">' . number_format($net, 0, ',', '.') . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $color = $row->status === 'paid' ? 'success' : 'warning';
                return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.transactions.show', $row->transaction_id) . '" class="btn btn-xs btn-info" title="View Transaction">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['customer', 'amount_formatted', 'change_formatted', 'net_amount_formatted', 'status_badge', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(TransactionPayment $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['transaction'])
            ->select('transaction_payments.*');

        // Filter by date range
        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('payment_date', '>=', request('date_from'));
        }
        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('payment_date', '<=', request('date_to'));
        }

        // Filter by payment method
        if (request()->has('payment_method') && request('payment_method')) {
            $query->where('payment_method', request('payment_method'));
        }

        return $query->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('payments-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.date_from = $('#date_from').val(); data.date_to = $('#date_to').val(); data.payment_method = $('#payment_method').val();")
            ->orderBy(1, 'desc')
            ->pageLength(50)
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
                    
                    var amount = api.column(5, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var change = api.column(6, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var net = api.column(7, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                    $(api.column(5).footer()).html("<span class=\"font-weight-bold text-info\">" + new Intl.NumberFormat("id-ID").format(amount) + "</span>");
                    $(api.column(6).footer()).html("<span class=\"font-weight-bold text-muted\">" + new Intl.NumberFormat("id-ID").format(change) + "</span>");
                    $(api.column(7).footer()).html("<span class=\"font-weight-bold text-success\">" + new Intl.NumberFormat("id-ID").format(net) + "</span>");
                }'
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50)->footer('Total'),
            Column::computed('payment_date_formatted')->title(__('transaction.date'))->footer(''),
            Column::computed('invoice_no')->title(__('transaction.invoice_no'))->footer(''),
            Column::computed('customer')->title(__('transaction.customer'))->footer(''),
            Column::make('payment_method')->title(__('transaction.payment_method'))->footer(''),
            Column::make('amount')->title(__('transaction.amount_paid'))->addClass('text-right')->data('amount_formatted')->footer(''),
            Column::make('change')->title('Change')->addClass('text-right')->data('change_formatted')->footer(''),
            Column::computed('net_amount_formatted')->title('Net Income')->addClass('text-right')->footer(''),
            Column::computed('status_badge')->title('Status')->addClass('text-center')->footer(''),
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
        return 'Payments_' . date('YmdHis');
    }
}
