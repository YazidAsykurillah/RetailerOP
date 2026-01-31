<?php

namespace App\DataTables;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use App\DataTables\PurchasesDataTable;

class SupplierPurchasesDataTable extends PurchasesDataTable
{
    /**
     * Get the query source of dataTable.
     */
    public function query(Purchase $model): QueryBuilder
    {
        $supplierId = request()->route('supplier')->id;

        return parent::query($model)->where('supplier_id', $supplierId);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('supplier-purchases-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc') // Adjust order index since column count changes
            ->selectStyleSingle()
            ->responsive(true);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = parent::getColumns();
        
        // Remove supplier_name column as it's redundant in this view
        return array_filter($columns, function($column) {
            return $column->name !== 'supplier_name';
        });
    }
}
