<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DataTables\PaymentsDataTable;
use App\Models\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(PaymentsDataTable $dataTable)
    {
        $todayIncome = TransactionPayment::whereDate('payment_date', today())->sum(DB::raw('amount - `change`'));
        $totalIncome = TransactionPayment::sum(DB::raw('amount - `change`'));
        $monthIncome = TransactionPayment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum(DB::raw('amount - `change`'));

        return $dataTable->render('admin.payments.index', compact('todayIncome', 'totalIncome', 'monthIncome'));
    }
}
