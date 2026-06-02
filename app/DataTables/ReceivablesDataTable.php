<?php

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReceivablesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Customer> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('group_name', function ($row) {
                return e($row->customerGroup->name ?? '-');
            })
            ->addColumn('total_transaction_formatted', function ($row) {
                return number_format($row->total_transaction ?? 0, 0, ',', '.');
            })
            ->addColumn('total_paid_formatted', function ($row) {
                return number_format($row->total_paid ?? 0, 0, ',', '.');
            })
            ->addColumn('outstanding_formatted', function ($row) {
                $outstanding = max(0, $row->outstanding ?? 0);
                if ($outstanding > 0) {
                    return '<span class="badge badge-warning">' . number_format($outstanding, 0, ',', '.') . '</span>';
                }
                return '<span class="badge badge-success">0</span>';
            })
            ->addColumn('transaction_count_display', function ($row) {
                return (int) ($row->transaction_count ?? 0);
            })
            ->addColumn('last_transaction_date', function ($row) {
                return $row->last_transaction ? date('d M Y', strtotime($row->last_transaction)) : '-';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.customers.show', $row->id) . '" class="btn btn-xs btn-info" title="View Detail"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['outstanding_formatted', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Customer>
     */
    public function query(Customer $model): QueryBuilder
    {
        $transactionSummary = DB::table('transactions')
            ->select(
                'customer_id',
                DB::raw('COALESCE(SUM(grand_total), 0) as total_transaction'),
                DB::raw('COALESCE(SUM(amount_paid), 0) as total_paid'),
                DB::raw('COALESCE(SUM(grand_total) - SUM(amount_paid), 0) as outstanding'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('MAX(created_at) as last_transaction')
            )
            ->whereNotNull('customer_id')
            ->groupBy('customer_id');

        $query = $model->newQuery()
            ->select('customers.*')
            ->selectRaw('COALESCE(transaction_summary.total_transaction, 0) as total_transaction')
            ->selectRaw('COALESCE(transaction_summary.total_paid, 0) as total_paid')
            ->selectRaw('COALESCE(transaction_summary.outstanding, 0) as outstanding')
            ->selectRaw('COALESCE(transaction_summary.transaction_count, 0) as transaction_count')
            ->selectRaw('transaction_summary.last_transaction')
            ->with('customerGroup')
            ->joinSub($transactionSummary, 'transaction_summary', function ($join) {
                $join->on('customers.id', '=', 'transaction_summary.customer_id');
            })
            ->where('transaction_summary.outstanding', '>', 0);

        // Filter by customer group
        if ($this->request()->get('customer_group_id')) {
            $query->where('customers.customer_group_id', $this->request()->get('customer_group_id'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('receivables-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.customer_group_id = $('#filter_group_id').val();")
            ->orderBy(5, 'desc')
            ->selectStyleSingle()
            ->responsive(true)
            ->parameters([
                'footerCallback' => 'function (row, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === "string" ?
                            i.replace(/<[^>]+>/g, "").replace(/[\$.]/g, "") * 1 :
                            typeof i === "number" ?
                                i : 0;
                    };

                    var totalTransaction = api.column(3, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var totalPaid = api.column(4, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var totalOutstanding = api.column(5, { page: "current" }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                    $(api.column(3).footer()).html("<span class=\"font-weight-bold\">" + new Intl.NumberFormat("id-ID").format(totalTransaction) + "</span>");
                    $(api.column(4).footer()).html("<span class=\"font-weight-bold text-success\">" + new Intl.NumberFormat("id-ID").format(totalPaid) + "</span>");
                    $(api.column(5).footer()).html("<span class=\"font-weight-bold text-danger\">" + new Intl.NumberFormat("id-ID").format(totalOutstanding) + "</span>");
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
            Column::make('name')->title(__('customer.name'))->footer(''),
            Column::computed('group_name')->title(__('customer.group'))->footer(''),
            Column::computed('total_transaction_formatted')->title(__('customer.total_transaction'))->addClass('text-right')->footer(''),
            Column::computed('total_paid_formatted')->title(__('customer.total_paid'))->addClass('text-right')->footer(''),
            Column::computed('outstanding_formatted')->title(__('customer.outstanding'))->addClass('text-right')->footer(''),
            Column::computed('transaction_count_display')->title('Trx')->addClass('text-center')->footer(''),
            Column::computed('last_transaction_date')->title(__('transaction.date'))->footer(''),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->footer(''),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Receivables_' . date('YmdHis');
    }
}
