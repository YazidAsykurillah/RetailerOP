@extends('adminlte::page')

@section('title', 'Transaction Details - ' . $transaction->invoice_no)

@section('content_header')
    <h1>
        <i class="fas fa-receipt"></i> Transaction Details
        <small class="text-muted">#{{ $transaction->invoice_no }}</small>
    </h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Transaction Details Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Invoice Details
                </h3>
                <div class="card-tools">
                    @if($transaction->payment_status !== 'paid')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#paymentModal">
                        <i class="fas fa-money-bill-wave"></i> Record Payment
                    </button>
                    @endif
                    <a href="{{ route('admin.transactions.print', $transaction->id) }}" 
                       class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="40%">Invoice No.</th>
                                <td><strong>{{ $transaction->invoice_no }}</strong></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cashier</th>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td>
                                    @php
                                        $colors = [
                                            'cash' => 'success',
                                            'card' => 'info',
                                            'transfer' => 'primary',
                                            'other' => 'secondary',
                                        ];
                                        $color = $colors[$transaction->payment_method] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">
                                        {{ $transaction->payment_method_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Mode</th>
                                <td>
                                    <span class="badge badge-{{ $transaction->payment_mode === 'full' ? 'info' : 'warning' }}">
                                        {{ ucfirst($transaction->payment_mode) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td>
                                    @php
                                        $statusColors = [
                                            'paid' => 'success',
                                            'partial' => 'warning',
                                            'unpaid' => 'danger',
                                        ];
                                        $statusColor = $statusColors[$transaction->payment_status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }}">
                                        {{ $transaction->payment_status_label }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th width="40%">Customer</th>
                                <td>{{ $transaction->customer_name ?: 'Walk-in Customer' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $transaction->customer_phone ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>{{ $transaction->notes ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Items ({{ $transaction->items->count() }})
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-right">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->productVariant)
                                    <br>
                                    <small class="text-muted">SKU: {{ $item->productVariant->sku }}</small>
                                @endif
                            </td>
                            <td>{{ $item->variant_name ?: 'Default' }}</td>
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->discount, 0, ',', '.') }}</td>
                            <td class="text-right">
                                <strong>{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Payment Summary -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calculator"></i> Payment Summary
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Subtotal</th>
                        <td class="text-right">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td class="text-right text-danger">-{{ number_format($transaction->discount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <th class="h4 text-success">Grand Total</th>
                        <td class="text-right h4 text-success font-weight-bold">
                            {{ number_format($transaction->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Schedule -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt"></i> Payment Schedule
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td class="text-right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $payment->status === 'paid' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="2">Total Paid</th>
                            <th class="text-right">{{ number_format($transaction->amount_paid, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                        @if($transaction->grand_total > $transaction->amount_paid)
                        <tr class="text-danger">
                            <th colspan="2">Remaining</th>
                            <th class="text-right font-weight-bold">
                                {{ number_format($transaction->grand_total - $transaction->amount_paid, 0, ',', '.') }}
                            </th>
                            <th></th>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('admin.transactions.print', $transaction->id) }}" 
                   class="btn btn-info btn-block" target="_blank">
                    <i class="fas fa-print"></i> Print Receipt
                </a>
                <a href="{{ route('admin.pos.index') }}" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> New Transaction
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">Record New Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Remaining Balance</label>
                        <div class="h4 text-danger">Rp {{ number_format($transaction->grand_total - $transaction->amount_paid, 0, ',', '.') }}</div>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount to Pay</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                               value="{{ $transaction->grand_total - $transaction->amount_paid }}" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" name="payment_method" id="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="savePaymentBtn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('#savePaymentBtn');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: '{{ route("admin.transactions.add-payment", $transaction->id) }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Failed to record payment');
                        $btn.prop('disabled', false).text('Save Payment');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Something went wrong';
                    toastr.error(message);
                    $btn.prop('disabled', false).text('Save Payment');
                }
            });
        });
    });
</script>
@stop
