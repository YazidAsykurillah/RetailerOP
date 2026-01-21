<?php

namespace App\DataTables;

use App\Models\VariantType;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VariantTypesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('values_count', function ($row) {
                return $row->values_count ?? 0;
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.variant-types.values.index', $row->id) . '" class="btn btn-xs btn-info" title="Manage Values">
                        <i class="fas fa-list"></i>
                    </a>
                    <a href="' . route('admin.variant-types.edit', $row->id) . '" class="btn btn-xs btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(VariantType $model): QueryBuilder
    {
        return $model->newQuery()
            ->withCount('values')
            ->orderBy('sort_order');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('variant-types-table')
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
            Column::make('name')->title('Type Name'),
            Column::make('slug')->title('Slug'),
            Column::computed('values_count')->title('Values')->width(80),
            Column::make('sort_order')->title('Sort Order')->width(100),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(140)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'VariantTypes_' . date('YmdHis');
    }
}
