<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif;
            font-size: 12px;
            font-weight: 600;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
            color: #000;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .store-name {
            font-size: 18px;
            font-weight: 900;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .store-info {
            font-size: 10px;
            color: #000;
        }
        .invoice-info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 2px 0;
        }
        .invoice-info .label {
            width: 35%;
        }
        .items {
            margin-bottom: 10px;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            padding: 3px 0;
            text-align: left;
        }
        .items th {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
        }
        .items .qty {
            text-align: center;
            width: 10%;
        }
        .items .price {
            text-align: right;
            width: 25%;
        }
        .items .discount {
            text-align: right;
            width: 20%;
        }
        .items .subtotal {
            text-align: right;
            width: 25%;
        }
        .item-row td {
            padding-top: 5px;
        }
        .item-name {
            font-weight: bold;
        }
        .item-variant {
            font-size: 10px;
            color: #000;
        }
        .summary {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        .summary table {
            width: 100%;
        }
        .summary td {
            padding: 3px 0;
        }
        .summary .label {
            text-align: left;
        }
        .summary .value {
            text-align: right;
        }
        .summary .total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .summary .grand-total {
            font-size: 18px;
            font-weight: bold;
        }
        .payment {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        .payment table {
            width: 100%;
        }
        .payment td {
            padding: 3px 0;
        }
        .change {
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer .thank-you {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer .message {
            font-size: 10px;
            color: #000;
        }
        @media print {
            body {
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Print Receipt
    </button>

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            @if(isset($businessProfile) && $businessProfile->logo_path)
                @php
                     // Convert absolute path to relative public path if needed, or use asset()
                     // Usually storage linkage is required: php artisan storage:link
                     $logoUrl = asset('storage/' . $businessProfile->logo_path);
                @endphp
                 <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 50px; display: block; margin: 0 auto 5px auto;">
            @endif
            <div class="store-name">{{ $businessProfile->business_name ?? 'SISKHA STORE' }}</div>
            <div class="store-info">
                @if(isset($businessProfile))
                    @if($businessProfile->business_address)
                        {{ $businessProfile->business_address }}<br>
                    @endif
                    @if($businessProfile->business_phone)
                        Telp: {{ $businessProfile->business_phone }}<br>
                    @endif
                    @if($businessProfile->business_website)
                        {{ $businessProfile->business_website }}
                    @endif
                @else
                    Jl. Contoh Alamat No. 123<br>
                    Telp: (021) 1234567<br>
                    www.siskhastore.com
                @endif
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <table>
                <tr>
                    <td class="label">Invoice</td>
                    <td>: {{ $transaction->invoice_no }}</td>
                </tr>
                <tr>
                    <td class="label">Date</td>
                    <td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td class="label">Cashier</td>
                    <td>: {{ $transaction->user->name ?? '-' }}</td>
                </tr>
                @if($transaction->customer_name)
                <tr>
                    <td class="label">Customer</td>
                    <td>: {{ $transaction->customer_name }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Items -->
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Item</th>
                        <th class="qty">Qty</th>
                        <th class="price">Price</th>
                        <th class="discount">Disc</th>
                        <th class="subtotal">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                    <tr class="item-row">
                        <td>
                            <div class="item-name">{{ Str::limit($item->product_name, 15) }}</div>
                            @if($item->variant_name)
                            <div class="item-variant">{{ $item->variant_name }}</div>
                            @endif
                        </td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="price">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="discount">
                            @php
                                $totalItemDiscount = ($item->discount ?? 0) + ($item->cut_amount ?? 0);
                            @endphp
                            @if($totalItemDiscount > 0)
                                @php
                                    $itemGross = $item->price * $item->quantity;
                                    $percent = $itemGross > 0 ? ($totalItemDiscount / $itemGross) * 100 : 0;
                                @endphp
                                <div style="font-size: 9px; line-height: 1.1;">
                                    {{ round($percent) }}%<br>
                                    <!-- Don't display total item discount -->
                                    <!-- {{ number_format($totalItemDiscount, 0, ',', '.') }} -->
                                </div>
                            @else
                                0
                            @endif
                        </td>
                        <td class="subtotal">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary">
            <table>
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">{{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($transaction->discount > 0)
                @php
                    $discountPercent = $transaction->subtotal > 0 ? ($transaction->discount / $transaction->subtotal) * 100 : 0;
                @endphp
                <tr>
                    <td class="label">Discount ({{ round($discountPercent) }}%)</td>
                    <!-- <td class="value">-{{ number_format($transaction->discount, 0, ',', '.') }}</td> -->
                </tr>
                @endif
                @if($transaction->tax > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">{{ number_format($transaction->tax, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td class="label grand-total">TOTAL</td>
                    <td class="value grand-total">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment -->
        <div class="payment">
            <table>
                <tr>
                    <td class="label">Payment Status</td>
                    <td class="value">{{ $transaction->payment_status_label }}</td>
                </tr>
                <tr>
                    <td class="label">Total Paid</td>
                    <td class="value">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
                </tr>
                @if($transaction->grand_total > $transaction->amount_paid)
                <tr>
                    <td class="label" style="color: #000; font-weight: bold;">{{ __('transaction.label_remaining') }}</td>
                    <td class="value" style="color: #000; font-weight: bold;">Rp {{ number_format($transaction->grand_total - $transaction->amount_paid, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Payment History -->
        <div class="payment-history" style="margin-bottom: 10px; font-size: 10px; border-top: 1px dashed #000; padding-top: 5px;">
            <div style="font-weight: bold; margin-bottom: 3px; text-transform: uppercase;">Payment History</div>
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($transaction->payments as $payment)
                <tr>
                    <td>
                        {{ $payment->payment_date->format('d/m/y') }}
                        @if($payment->payment_method === 'deposit')
                            (Deposit)
                        @else
                            ({{ ucfirst($payment->payment_method) }})
                        @endif
                        @if($payment->change > 0)
                            <br><span style="font-size: 8px; opacity: 0.8;">(Change: {{ number_format($payment->change, 0, ',', '.') }})</span>
                        @endif
                    </td>
                    <td style="text-align: right; vertical-align: top;">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td style="text-align: right; width: 50px; vertical-align: top;">[{{ ucfirst($payment->status) }}]</td>
                </tr>
                @endforeach
            </table>
            @php
                $depositPayments = $transaction->payments->where('payment_method', 'deposit');
                $totalDepositUsed = $depositPayments->sum('amount');
            @endphp
            @if($totalDepositUsed > 0 && $transaction->customer_id)
            <div style="margin-top: 5px; border-top: 1px dashed #ccc; padding-top: 4px;">
                <table style="width: 100%;">
                    <tr>
                        <td>Deposit Used</td>
                        <td style="text-align: right;">Rp {{ number_format($totalDepositUsed, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Remaining Deposit</td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp {{ number_format(optional($transaction->customer)->deposit_balance ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
            @endif
        </div>

        <!--Outstanding-->
        <div class="outstanding-information">
            @php
                $customerDebt = 0;
                $previousDebt = 0;
                $currentDebt = 0;
                
                if ($transaction->customer) {
                    // Previous debt only includes transactions made BEFORE this one
                    $previousDebt = $transaction->customer->transactions()
                        ->where('id', '<', $transaction->id)
                        ->whereIn('payment_status', ['unpaid', 'partial'])
                        ->get()
                        ->sum(function ($t) {
                            return $t->grand_total - $t->amount_paid;
                        });
                        
                    $currentDebt = in_array($transaction->payment_status, ['unpaid', 'partial']) 
                        ? max(0, $transaction->grand_total - $transaction->amount_paid) 
                        : 0;
                        
                    $customerDebt = $previousDebt + $currentDebt;
                }
            @endphp
            @if($customerDebt > 0 || $previousDebt > 0)
            <div style="margin-bottom: 10px; font-size: 11px; border-top: 1px dashed #000; padding-top: 5px;">
                <table style="width: 100%;">
                    @if($previousDebt > 0)
                    <tr>
                        <td style="text-transform: uppercase;">{{ __('transaction.previous_outstanding') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($previousDebt, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($currentDebt > 0)
                    <tr>
                        <td style="text-transform: uppercase;">{{ __('transaction.current_outstanding')}}</td>
                        <td style="text-align: right;">Rp {{ number_format($currentDebt, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold; text-transform: uppercase; border-top: 1px solid #000; padding-top: 3px;">{{ __('transaction.total_outstanding') }}</td>
                        <td style="text-align: right; font-weight: bold; border-top: 1px solid #000; padding-top: 3px;">Rp {{ number_format($customerDebt, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            @endif
        </div>


        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Thank You!</div>
            <div class="message">
                @if(isset($businessProfile) && $businessProfile->footer_text)
                    {!! nl2br(e($businessProfile->footer_text)) !!}
                @else
                    Please keep this receipt for your records.<br>
                    No refund without receipt.
                @endif
            </div>
            <br>
            <div style="font-size: 10px;">
                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
