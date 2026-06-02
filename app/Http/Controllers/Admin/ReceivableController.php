<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\ReceivablesDataTable;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    /**
     * Display the receivables report.
     */
    public function index(ReceivablesDataTable $dataTable)
    {
        $groups = \App\Models\CustomerGroup::all();

        // Summary stats for the info boxes
        $summary = DB::table('transactions')
            ->whereNotNull('customer_id')
            ->select(
                DB::raw('COALESCE(SUM(grand_total), 0) as total_transaction'),
                DB::raw('COALESCE(SUM(amount_paid), 0) as total_paid'),
                DB::raw('COALESCE(SUM(grand_total) - SUM(amount_paid), 0) as total_outstanding'),
                DB::raw('COUNT(DISTINCT CASE WHEN (grand_total - amount_paid) > 0 THEN customer_id END) as customer_count')
            )
            ->first();

        return $dataTable->render('admin.reports.receivables', compact('groups', 'summary'));
    }
}
