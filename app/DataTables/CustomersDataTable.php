<?php

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
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
            ->addColumn('action', function($row){
                 $btn = '<a href="'.route('admin.customers.show', $row->id).'" class="btn btn-xs btn-info mx-1" title="View"><i class="fas fa-eye"></i></a>';
                 $btn = $btn.'<a href="'.route('admin.customers.edit', $row->id).'" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                 $btn = $btn.'<button class="btn btn-xs btn-danger mx-1 delete-btn" data-id="'.$row->id.'" data-url="'.route('admin.customers.destroy', $row->id).'" title="Delete"><i class="fas fa-trash"></i></button>';
                 return $btn;
            })
            ->addColumn('group_name', function($row) {
                return $row->customerGroup->name ?? '-';
            })
            ->addColumn('total_transaction_value', function($row) {
                return number_format($row->total_transaction ?? 0, 0, ',', '.');
            })
            ->addColumn('total_paid_value', function($row) {
                return number_format($row->total_paid ?? 0, 0, ',', '.');
            })
            ->addColumn('outstanding_amount', function($row) {
                $outstanding = max(0, $row->outstanding ?? 0);
                if ($outstanding > 0) {
                    return '<span class="badge badge-warning">' . number_format($outstanding, 0, ',', '.') . '</span>';
                }
                return '<span class="badge badge-success">0</span>';
            })
            ->editColumn('is_active', function($row) {
                return $row->is_active ? '<span class="badge badge-success">' . __('general.active') . '</span>' : '<span class="badge badge-danger">' . __('general.inactive') . '</span>';
            })
            ->rawColumns(['action', 'is_active', 'outstanding_amount'])
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
                DB::raw('COALESCE(SUM(grand_total) - SUM(amount_paid), 0) as outstanding')
            )
            ->groupBy('customer_id');

        return $model->newQuery()
            ->select('customers.*')
            ->selectRaw('COALESCE(transaction_summary.total_transaction, 0) as total_transaction')
            ->selectRaw('COALESCE(transaction_summary.total_paid, 0) as total_paid')
            ->selectRaw('COALESCE(transaction_summary.outstanding, 0) as outstanding')
            ->with('customerGroup')
            ->leftJoinSub($transactionSummary, 'transaction_summary', function ($join) {
                $join->on('customers.id', '=', 'transaction_summary.customer_id');
            })
            ->when($this->request()->get('customer_group_id'), function($query) {
                return $query->where('customer_group_id', $this->request()->get('customer_group_id'));
            })
            ->when($this->request()->get('has_outstanding'), function($query) {
                return $query->where('transaction_summary.outstanding', '>', 0);
            });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('customers-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->responsive(true);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::make('name'),
            Column::make('phone'),
            Column::make('group_name')->title(__('customer.group'))->name('customerGroup.name'),
            Column::make('total_transaction_value')->title(__('customer.total_transaction'))->addClass('text-center')->searchable(false)->orderData(4),
            Column::make('total_paid_value')->title(__('customer.total_paid'))->addClass('text-center')->searchable(false)->orderData(5),
            Column::make('outstanding_amount')->title(__('customer.outstanding'))->addClass('text-center')->searchable(false)->orderData(6),
            Column::make('is_active')->title(__('general.status'))->addClass('text-center'),
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
        return 'Customers_' . date('YmdHis');
    }
}
