<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\DataTables\TransactionsDataTable;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display transaction history.
     */
    public function index(TransactionsDataTable $dataTable)
    {
        // Get summary statistics
        $todaySales = Transaction::whereDate('created_at', today())->sum('grand_total');
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $monthSales = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');
        $totalTransactions = Transaction::count();

        return $dataTable->render('admin.transactions.index', compact(
            'todaySales',
            'todayTransactions',
            'monthSales',
            'totalTransactions'
        ));
    }

    /**
     * Display transaction details.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user'])->findOrFail($id);

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Print transaction receipt.
     */
    public function printReceipt($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user'])->findOrFail($id);

        return view('admin.transactions.print', compact('transaction'));
    }
}
