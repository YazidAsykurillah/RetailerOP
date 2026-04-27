<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDeposit;
use App\DataTables\DepositsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    /**
     * Display a listing of all deposit movements (admin overview).
     */
    public function index(DepositsDataTable $dataTable)
    {
        abort_if(!auth()->user()->can('Manage Deposits'), 403);

        $totalTopUps  = CustomerDeposit::where('type', 'top_up')->sum('amount');
        $totalUsages  = CustomerDeposit::where('type', 'usage')->sum('amount');
        $totalBalance = Customer::sum('deposit_balance');

        return $dataTable->render('admin.deposits.index', compact(
            'totalTopUps',
            'totalUsages',
            'totalBalance'
        ));
    }

    /**
     * Store a new top-up for a customer.
     */
    public function store(Request $request, Customer $customer)
    {
        abort_if(!auth()->user()->can('Manage Deposits'), 403);

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'notes'          => 'nullable|string|max:1000',
        ]);

        try {
            $customer->topUpDeposit(
                (float) $request->amount,
                $request->payment_method,
                $request->notes,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Deposit top-up recorded successfully.',
                'new_balance' => (float) $customer->fresh()->deposit_balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record top-up: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a top-up record (edit amount/notes) and recalculate balance.
     */
    public function update(Request $request, CustomerDeposit $deposit)
    {
        abort_if(!auth()->user()->can('Manage Deposits'), 403);
        abort_if($deposit->type !== 'top_up', 403, 'Only top-up records can be edited.');

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'notes'          => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $deposit) {
                $deposit->update([
                    'amount'         => $request->amount,
                    'payment_method' => $request->payment_method,
                    'notes'          => $request->notes,
                ]);

                // Recalculate all balance snapshots and the customer's running balance
                $deposit->customer->recalculateDepositBalance();
            });

            return response()->json([
                'success' => true,
                'message' => 'Deposit record updated successfully.',
                'new_balance' => (float) $deposit->customer->fresh()->deposit_balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update deposit: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a top-up record and recalculate balance.
     */
    public function destroy(CustomerDeposit $deposit)
    {
        abort_if(!auth()->user()->can('Manage Deposits'), 403);
        abort_if($deposit->type !== 'top_up', 403, 'Only top-up records can be deleted.');

        try {
            DB::transaction(function () use ($deposit) {
                $customer = $deposit->customer;
                $deposit->delete();
                $customer->recalculateDepositBalance();
            });

            return response()->json([
                'success' => true,
                'message' => 'Deposit record deleted and balance recalculated.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete deposit: ' . $e->getMessage(),
            ], 500);
        }
    }
}
