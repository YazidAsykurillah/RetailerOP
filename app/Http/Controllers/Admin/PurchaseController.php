<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PurchasesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(PurchasesDataTable $dataTable)
    {
        $suppliers = Supplier::active()->get();
        return $dataTable->render('purchases.index', compact('suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->get();
        return view('purchases.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'reference_number' => 'required|unique:purchases,reference_number',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['cost'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'reference_number' => $request->reference_number,
                'date' => $request->date,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'quantity_received' => 0,
                    'cost' => $item['cost'],
                    'subtotal' => $item['quantity'] * $item['cost'],
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase created successfully.',
                    'redirect' => route('admin.purchases.index')
                ]);
            }

            return redirect()->route('admin.purchases.index')->with('success', 'Purchase created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating purchase: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Error creating purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'details.productVariant.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status === 'completed' || $purchase->status === 'cancelled') {
            return back()->with('error', 'Cannot edit completed or cancelled purchases.');
        }

        $purchase->load(['details.productVariant.product']);
        $suppliers = Supplier::active()->get();
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'completed' || $purchase->status === 'cancelled') {
            return $this->errorResponse($request, 'Cannot edit completed or cancelled purchases.');
        }

        $hasReceipts = $purchase->details()->where('quantity_received', '>', 0)->exists();

        // Validation based on state
        $rules = [
            'date' => 'required|date',
            'reference_number' => 'required|unique:purchases,reference_number,' . $purchase->id,
            'notes' => 'nullable|string',
        ];

        if (!$hasReceipts) {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.variant_id'] = 'required|exists:product_variants,id';
            $rules['items.*.quantity'] = 'required|integer|min:1';
            $rules['items.*.cost'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Update Header
            $purchase->date = $request->date;
            $purchase->notes = $request->notes;
            $purchase->reference_number = $request->reference_number;
            
            // Only allow changing supplier if no receipts (safe)
            if (!$hasReceipts) {
                 $purchase->supplier_id = $request->supplier_id;
            }
            
            $purchase->save();

            // Handle Items (Only if no receipts)
            if (!$hasReceipts) {
                 $totalAmount = 0;
                 $existingIds = $purchase->details()->pluck('id')->toArray();
                 $keptIds = [];

                 foreach ($request->items as $itemData) {
                      $cost = $itemData['cost'];
                      $qty = $itemData['quantity'];
                      $totalAmount += $qty * $cost;

                      if (isset($itemData['id'])) {
                           // Update existing
                           $detail = PurchaseDetail::find($itemData['id']);
                           if ($detail && $detail->purchase_id == $purchase->id) {
                                $detail->update([
                                     'product_variant_id' => $itemData['variant_id'],
                                     'quantity' => $qty,
                                     'cost' => $cost,
                                     'subtotal' => $qty * $cost,
                                ]);
                                $keptIds[] = $detail->id;
                           }
                      } else {
                           // Create New
                           $detail = PurchaseDetail::create([
                                'purchase_id' => $purchase->id,
                                'product_variant_id' => $itemData['variant_id'],
                                'quantity' => $qty,
                                'quantity_received' => 0,
                                'cost' => $cost,
                                'subtotal' => $qty * $cost,
                           ]);
                           $keptIds[] = $detail->id;
                      }
                 }

                 // Remove items not in the list
                 $detailsToDelete = array_diff($existingIds, $keptIds);
                 PurchaseDetail::destroy($detailsToDelete);

                 // Update Total Amount
                 $purchase->total_amount = $totalAmount;
                 $purchase->save();
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase updated successfully.',
                    'redirect' => route('admin.purchases.show', $purchase)
                ]);
            }

            return redirect()->route('admin.purchases.show', $purchase)->with('success', 'Purchase updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 422);
            }
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    private function errorResponse($request, $message) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->with('error', $message);
    }

    public function destroy(Purchase $purchase)
    {
         if ($purchase->status === 'completed') {
            return back()->with('error', 'Cannot delete completed purchase.');
         }
         
         // If partial, we should check if we can revert stock... but for now let's just block if any receipts
         $hasReceipts = $purchase->details()->where('quantity_received', '>', 0)->exists();
         if ($hasReceipts) {
             return back()->with('error', 'Cannot delete purchase with received items.');
         }

         $purchase->delete();
         return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted.');
    }

    public function receiveItems(Request $request, Purchase $purchase)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_details,id',
            'items.*.receive_qty' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $updated = false;

            foreach ($request->items as $itemData) {
                // Ignore if receive_qty is 0
                if ($itemData['receive_qty'] <= 0) {
                    continue;
                }

                $detail = PurchaseDetail::findOrFail($itemData['id']);
                
                // Validate not over-receiving
                $remaining = $detail->quantity - $detail->quantity_received;
                if ($itemData['receive_qty'] > $remaining) {
                    throw new \Exception("Cannot receive more than ordered for item {$detail->productVariant->name}");
                }

                $detail->increment('quantity_received', $itemData['receive_qty']);
                
                // Update Stock
                $detail->productVariant->adjustStock(
                    $itemData['receive_qty'],
                    'in',
                    auth()->id(),
                    $purchase->reference_number,
                    "Purchase Receive {$purchase->reference_number}",
                    $purchase->supplier_id
                );
                
                $updated = true;
            }

            if ($updated) {
                $purchase->updateStatus();
                DB::commit();
                return redirect()->route('admin.purchases.show', $purchase)->with('success', 'Items received and stock updated.');
            }

            DB::rollBack();
            return back()->with('warning', 'No items were received.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error receiving items: ' . $e->getMessage());
        }
    }
    
    // API endpoint for product search
    public function searchProducts(Request $request)
    {
        $term = $request->term;
        $variants = ProductVariant::with('product')
            ->whereHas('product', function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            })
            ->orWhere('sku', 'like', "%{$term}%")
            ->orWhere('name', 'like', "%{$term}%")
            ->limit(20)
            ->get();
            
        return response()->json($variants->map(function($v) {
            return [
                'id' => $v->id,
                'text' => $v->full_name . " (Cost: {$v->cost})",
                'cost' => $v->cost,
                'sku' => $v->sku
            ];
        }));
    }
}
