<?php

namespace App\DataTables;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class CustomerTransactionsDataTable extends TransactionsDataTable
{
    /**
     * Get the query source of dataTable.
     */
    public function query(Transaction $model): QueryBuilder
    {
        // Use parent query to inherit filters (date, payment method, etc.)
        $query = parent::query($model);

        // Filter by customer from route
        $customer = $this->request()->route('customer');
        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('customer-transactions-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.date_from = $('#date_from').val(); data.date_to = $('#date_to').val();")
            ->orderBy(0, 'desc')
            ->selectStyleSingle()
            ->autoWidth(false)
            ->responsive(true)
            ->addTableClass('table-striped table-bordered w-100');
    }
    
    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'CustomerTransactions_' . date('YmdHis');
    }
}
