<?php

namespace App\DataTables;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoriesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('parent_name', function ($row) {
                return $row->parent ? $row->parent->name : '-';
            })
            ->addColumn('products_count', function ($row) {
                return $row->products_count ?? 0;
            })
            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.categories.edit', $row->id) . '" class="btn btn-xs btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request()->input('search.value'))) {
                    $keyword = request()->input('search.value');
                    $query->where(function ($q) use ($keyword) {
                        $q->where('categories.name', 'like', "%{$keyword}%")
                          ->orWhereHas('parent', function ($parentQuery) use ($keyword) {
                              $parentQuery->where('name', 'like', "%{$keyword}%");
                          });
                    });
                }
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Category $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('parent')
            ->withCount('products')
            ->orderBy('sort_order');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('categories-table')
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
            Column::make('name')->title('Category Name'),
            Column::computed('parent_name')->title('Parent Category'),
            Column::computed('products_count')->title('Products')->width(80),
            Column::computed('status')->title('Status')->width(80),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Categories_' . date('YmdHis');
    }
}
