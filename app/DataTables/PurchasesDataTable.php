<?php

namespace App\DataTables;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PurchasesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return $row->date->format('d M Y');
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier->name ?? '-';
            })
            ->addColumn('status_badge', function ($row) {
                $badges = [
                    'pending' => '<span class="badge badge-warning">Pending</span>',
                    'partial' => '<span class="badge badge-info">Partial</span>',
                    'completed' => '<span class="badge badge-success">Completed</span>',
                    'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                ];
                return $badges[$row->status] ?? $row->status;
            })
            ->addColumn('total_amount_display', function ($row) {
                return number_format($row->total_amount, 2);
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex justify-content-center">';
                $btn .= '<a href="' . route('admin.purchases.show', $row->id) . '" class="btn btn-primary btn-xs mr-1"><i class="fas fa-eye"></i></a>';
                
                if ($row->status !== 'completed' && $row->status !== 'cancelled') {
                    $btn .= '<a href="' . route('admin.purchases.edit', $row->id) . '" class="btn btn-warning btn-xs mr-1"><i class="fas fa-edit"></i></a>';
                }

                if ($row->status === 'pending') {
                    $btn .= '<form action="' . route('admin.purchases.destroy', $row->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this purchase?\')">';
                    $btn .= csrf_field();
                    $btn .= method_field('DELETE');
                    $btn .= '<button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>';
                    $btn .= '</form>';
                }
                $btn .= '</div>';
                
                return $btn;
            })
            ->rawColumns(['status_badge', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Purchase $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['supplier', 'user'])
            ->select('purchases.*');

        if ($this->request()->has('supplier_id') && $this->request()->get('supplier_id')) {
            $query->where('supplier_id', $this->request()->get('supplier_id'));
        }

        if ($this->request()->has('date_from') && $this->request()->get('date_from')) {
            $query->whereDate('date', '>=', $this->request()->get('date_from'));
        }

        if ($this->request()->has('date_to') && $this->request()->get('date_to')) {
            $query->whereDate('date', '<=', $this->request()->get('date_to'));
        }

        if ($this->request()->has('status') && $this->request()->get('status')) {
            $query->where('status', $this->request()->get('status'));
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('purchases-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2, 'desc')
            ->selectStyleSingle()
            ->responsive(true);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(30)->addClass('text-center')->orderable(false),
            Column::make('reference_number')->title('Ref No'),
            Column::make('date')->title('Date')->orderable(true)->searchable(true),
            Column::computed('supplier_name')->title('Supplier'),
            Column::computed('status_badge')->title('Status')->addClass('text-center'),
            Column::computed('total_amount_display')->title('Total')->addClass('text-right'),
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
        return 'Purchases_' . date('YmdHis');
    }
}
