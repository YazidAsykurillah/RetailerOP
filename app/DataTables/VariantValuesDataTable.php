<?php

namespace App\DataTables;

use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VariantValuesDataTable extends DataTable
{
    protected $variantTypeId;

    /**
     * Set the variant type to filter values.
     */
    public function forVariantType(int $variantTypeId): self
    {
        $this->variantTypeId = $variantTypeId;
        return $this;
    }

    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('color_swatch', function ($row) {
                if ($row->color_code) {
                    return '<span class="badge" style="background-color: ' . $row->color_code . '; width: 24px; height: 24px; display: inline-block; border: 1px solid #ddd;"></span> ' . $row->color_code;
                }
                return '-';
            })
            ->addColumn('usage_count', function ($row) {
                return $row->product_variants_count ?? 0;
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.variant-types.values.edit', [$row->variant_type_id, $row->id]) . '" class="btn btn-xs btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-xs btn-danger delete" data-id="' . $row->id . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['color_swatch', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(VariantValue $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('variant_type_id', $this->variantTypeId)
            ->withCount('productVariants')
            ->orderBy('sort_order');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('variant-values-table')
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
            Column::make('value')->title('Value'),
            Column::computed('color_swatch')->title('Color Code')->width(120),
            Column::computed('usage_count')->title('Used In')->width(80),
            Column::make('sort_order')->title('Sort Order')->width(100),
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
        return 'VariantValues_' . date('YmdHis');
    }
}
