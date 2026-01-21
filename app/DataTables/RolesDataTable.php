<?php

namespace App\DataTables;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('permissions', function($row){
                return $row->permissions->pluck('name')->implode(', ');
            })
            ->addColumn('action', function($row){
                $btn = '<a href="'.route('roles.edit', $row->id).'" class="edit btn btn-primary btn-xs"><i class="fas fa-edit"></i></a>';
                $btn .= ' <button data-id="'.$row->id.'" class="delete btn btn-danger btn-xs ml-1"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->setRowId('id');
    }

    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->with('permissions');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('roles-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->responsive(true)
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('print'),
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                  ->title('No')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('name'),
            Column::computed('permissions')->title('Permissions'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(150)
                  ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Roles_' . date('YmdHis');
    }
}
