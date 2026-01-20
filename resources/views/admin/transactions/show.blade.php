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
                            <td class="text-right">Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                            <td class="text-right">
                                <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
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
                        <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td class="text-right text-danger">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td class="text-right">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <th class="h4 text-success">Grand Total</th>
                        <td class="text-right h4 text-success font-weight-bold">
                            Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave"></i> Payment Details
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Amount Paid</th>
                        <td class="text-right">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <th class="h5">Change</th>
                        <td class="text-right h5 font-weight-bold text-primary">
                            Rp {{ number_format($transaction->change, 0, ',', '.') }}
                        </td>
                    </tr>
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
@stop
