<?php

namespace App\DataTables;

use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CustomerGroupsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<CustomerGroup> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '<a href="'.route('admin.customer-groups.edit', $row->id).'" class="btn btn-xs btn-primary mx-1" title="Edit"><i class="fas fa-edit"></i></a>';
                // Prevent deleting default group
                if(!$row->is_default){
                     $btn = $btn.'<button class="btn btn-xs btn-danger mx-1 delete-btn" data-id="'.$row->id.'" data-url="'.route('admin.customer-groups.destroy', $row->id).'" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                return $btn;
            })
            ->editColumn('percentage_discount', function($row) {
                return $row->percentage_discount . '%';
            })
            ->editColumn('is_default', function($row) {
                return $row->is_default ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
            })
            ->rawColumns(['action', 'is_default'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<CustomerGroup>
     */
    public function query(CustomerGroup $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('customergroups-table')
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
            Column::make('name'),
            Column::make('code'),
            Column::make('percentage_discount')->title('Discount'),
            Column::make('is_default')->title('Default')->addClass('text-center'),
            Column::make('created_at'),
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
        return 'CustomerGroups_' . date('YmdHis');
    }
}
