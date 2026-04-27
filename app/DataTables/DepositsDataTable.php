<?php

namespace App\DataTables;

use App\Models\CustomerDeposit;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DepositsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('date_formatted', fn($row) => $row->created_at->format('d M Y H:i'))
            ->addColumn('customer_name', fn($row) => $row->customer->name ?? '-')
            ->addColumn('type_badge', function ($row) {
                $color = $row->type === 'top_up' ? 'success' : 'info';
                return '<span class="badge badge-' . $color . '">' . $row->type_label . '</span>';
            })
            ->addColumn('amount_formatted', fn($row) =>
                '<span class="font-weight-bold">Rp ' . number_format($row->amount, 0, ',', '.') . '</span>'
            )
            ->addColumn('balance_after_formatted', fn($row) =>
                'Rp ' . number_format($row->balance_after, 0, ',', '.')
            )
            ->addColumn('processed_by_name', fn($row) => $row->processedBy->name ?? '-')
            ->addColumn('action', function ($row) {
                $actions = '<div class="btn-group">';
                $actions .= '<a href="' . route('admin.customers.show', $row->customer_id) . '" class="btn btn-xs btn-info" title="View Customer"><i class="fas fa-user"></i></a>';
                if ($row->type === 'top_up') {
                    $actions .= '<button class="btn btn-xs btn-warning btn-edit-deposit" data-id="' . $row->id . '" data-amount="' . $row->amount . '" data-method="' . $row->payment_method . '" data-notes="' . e($row->notes) . '" title="Edit"><i class="fas fa-edit"></i></button>';
                    $actions .= '<button class="btn btn-xs btn-danger btn-delete-deposit" data-id="' . $row->id . '" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['type_badge', 'amount_formatted', 'action']);
    }

    public function query(CustomerDeposit $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['customer', 'processedBy'])
            ->select('customer_deposits.*');

        if (request('type')) {
            $query->where('type', request('type'));
        }
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('deposits-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', "data.type = $('#filter_type').val(); data.date_from = $('#date_from').val(); data.date_to = $('#date_to').val();")
            ->orderBy(1, 'desc')
            ->pageLength(25)
            ->autoWidth(false)
            ->responsive(true)
            ->addTableClass('table-striped table-bordered w-100');
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', '#')->width(50),
            Column::computed('date_formatted')->title('Date'),
            Column::computed('customer_name')->title('Customer'),
            Column::computed('type_badge')->title('Type')->addClass('text-center'),
            Column::computed('amount_formatted')->title('Amount')->addClass('text-right'),
            Column::computed('balance_after_formatted')->title('Balance After')->addClass('text-right'),
            Column::make('payment_method')->title('Method')->addClass('text-center'),
            Column::make('notes')->title('Notes'),
            Column::computed('processed_by_name')->title('Processed By'),
            Column::computed('action')->exportable(false)->printable(false)->width(120)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Deposits_' . date('YmdHis');
    }
}
