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
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 100mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .store-info {
            font-size: 10px;
            color: #666;
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
            color: #666;
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
            color: #666;
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
            <div class="store-name">SISKHA STORE</div>
            <div class="store-info">
                Jl. Contoh Alamat No. 123<br>
                Telp: (021) 1234567<br>
                www.siskhastore.com
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
                        <td class="discount">{{ number_format($item->discount, 0, ',', '.') }}</td>
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
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">-{{ number_format($transaction->discount, 0, ',', '.') }}</td>
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
                    <td class="label">Payment</td>
                    <td class="value">{{ $transaction->payment_method_label }}</td>
                </tr>
                <tr>
                    <td class="label">Paid</td>
                    <td class="value">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label change">Change</td>
                    <td class="value change">Rp {{ number_format($transaction->change, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Thank You!</div>
            <div class="message">
                Please keep this receipt for your records.<br>
                No refund without receipt.
            </div>
            <br>
            <div style="font-size: 10px;">
                {{ $transaction->created_at->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
