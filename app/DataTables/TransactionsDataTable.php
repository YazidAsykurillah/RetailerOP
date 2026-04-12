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
                return $row->customer_name ?: '<span class="text-muted">' . __('pos.walk_in_customer') . '</span>';
            })
            ->addColumn('grand_total_formatted', function ($row) {
                return '<span class="font-weight-bold">' . number_format($row->grand_total, 0, ',', '.') . '</span>';
            })
            ->addColumn('amount_paid_formatted', function ($row) {
                return '<span class="text-success">' . number_format($row->amount_paid, 0, ',', '.') . '</span>';
            })
            ->addColumn('outstanding_payment_formatted', function ($row) {
                $outstanding = (float) $row->outstanding_balance;
                $color = $outstanding > 0 ? 'text-danger' : 'text-success';
                return '<span class="font-weight-bold ' . $color . '">' . number_format($outstanding, 0, ',', '.') . '</span>';
            })
            ->addColumn('payment_status_badge', function ($row) {
                $statusColors = [
                    'paid' => 'success',
                    'partial' => 'warning',
                    'unpaid' => 'danger',
                ];
                $statusColor = $statusColors[$row->payment_status] ?? 'secondary';
                return '<span class="badge badge-' . $statusColor . '">' . $row->payment_status_label . '</span>';
            })
            ->addColumn('payment_mode_badge', function ($row) {
                $color = $row->payment_mode === 'full' ? 'info' : 'warning';
                return '<span class="badge badge-pill badge-' . $color . '">' . ucfirst(__("transaction.payment_mode_{$row->payment_mode}")) . '</span>';
            })
            ->addColumn('cashier', function ($row) {
                return $row->user->name ?? '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.transactions.edit', $row->id) . '" class="btn btn-xs btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . route('admin.transactions.show', $row->id) . '" class="btn btn-xs btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.transactions.print', $row->id) . '" class="btn btn-xs btn-secondary" title="Print Receipt" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        ' . (auth()->user()->can('Delete Transaction') ? '
                        <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="' . $row->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        ' : '') . '
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
            ->rawColumns(['customer', 'grand_total_formatted', 'amount_paid_formatted', 'outstanding_payment_formatted', 'payment_status_badge', 'payment_mode_badge', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Transaction $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select('transactions.*')
            ->selectRaw('(grand_total - amount_paid) as outstanding_balance')
            ->with(['user', 'customer']);

        // Filter by date range
        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }


        // Filter by customer
        if (request()->has('customer_id') && request('customer_id')) {
            $query->where('customer_id', request('customer_id'));
        }

        // Filter by payment status
        if (request()->has('payment_status') && request('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        // Filter by payment mode
        if (request()->has('payment_mode') && request('payment_mode')) {
            $query->where('payment_mode', request('payment_mode'));
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
            ->minifiedAjax('', "data.date_from = $('#date_from').val(); data.date_to = $('#date_to').val(); data.payment_mode = $('#payment_mode').val(); data.payment_status = $('#payment_status').val(); data.customer_id = $('#customer_id').val();")
            ->orderBy(0, 'desc')
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
                    var total = api.column(6, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var paid = api.column(7, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var outstanding = api.column(8, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                    $(api.column(6).footer()).html("<span class=\"font-weight-bold\">" + new Intl.NumberFormat("id-ID").format(total) + "</span>");
                    $(api.column(7).footer()).html("<span class=\"font-weight-bold text-success\">" + new Intl.NumberFormat("id-ID").format(paid) + "</span>");
                    $(api.column(8).footer()).html("<span class=\"font-weight-bold text-danger\">" + new Intl.NumberFormat("id-ID").format(outstanding) + "</span>");
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
            Column::make('invoice_no')->title(__('transaction.invoice_no'))->footer(''),
            Column::computed('date')->title(__('transaction.date'))->footer(''),
            Column::computed('customer')->title(__('customer.singular'))->footer(''),
            Column::computed('payment_mode_badge')->title(__('transaction.payment_mode'))->addClass('text-center')->footer(''),
            Column::computed('payment_status_badge')->title(__('transaction.payment_status'))->addClass('text-center')->footer(''),
            Column::computed('grand_total_formatted')->title(__('transaction.total_amount'))->addClass('text-right')->footer(''),
            Column::make('amount_paid')->title(__('transaction.paid'))->data('amount_paid_formatted')->addClass('text-right')->footer(''),
            Column::make('outstanding_balance')->title(__('transaction.balance'))->data('outstanding_payment_formatted')->addClass('text-right')->footer(''),
            
            Column::computed('cashier')->title(__('user.singular'))->footer(''),
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
