<!DOCTYPE html>
<html>
<head>
    <title>{{ __('product.list') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            color: #2c3e50;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .status-badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .text-right {
            text-align: right;
        }
        .price {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('product.list') }}</h2>
        <p>{{ __('general.generated_on') ?? 'Generated on' }}: {{ date('F d, Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">{{ __('general.no') }}</th>
                <th width="100">{{ __('product.sku') }}</th>
                <th>{{ __('product.name') }}</th>
                <th width="120" class="text-right">{{ __('product.price') }}</th>
                <th width="50" class="text-right">{{ __('product.stock') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->sku ?: '-' }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>
                @foreach($product->variants as $variant)
                    <tr style="font-size: 11px;">
                        <td></td>
                        <td style="padding-left: 15px;">{{ $variant->sku ?: '-' }}</td>
                        <td>{{ $variant->name ?: (__('variant.standard') ?? 'Standard') }}</td>
                        <td class="text-right">{{ number_format($variant->price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $variant->stock }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ __('general.page') ?? 'Page' }} <script type="text/php">echo $PAGE_NUM;</script> {{ __('general.of') ?? 'of' }} <script type="text/php">echo $PAGE_COUNT;</script>
    </div>
</body>
</html>
