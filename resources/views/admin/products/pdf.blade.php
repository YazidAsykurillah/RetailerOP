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
            margin-bottom: 0;
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
        .page-break {
            page-break-after: always;
        }
        .product-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .variant-row {
            font-size: 11px;
        }
        .variant-sku {
            padding-left: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ __('product.list') }}</h2>
        <p>{{ __('general.generated_on') ?? 'Generated on' }}: {{ date('F d, Y H:i:s') }}</p>
    </div>

    @php $rowNumber = 0; @endphp
    @foreach($productChunks as $chunkIndex => $chunk)
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
            @foreach($chunk as $product)
                @php $rowNumber++; @endphp
                <tr class="product-row">
                    <td>{{ $rowNumber }}</td>
                    <td>{{ $product->sku ?: '-' }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                </tr>
                @foreach($product->variants as $variant)
                    <tr class="variant-row">
                        <td></td>
                        <td class="variant-sku">{{ $variant->sku ?: '-' }}</td>
                        <td>{{ $variant->name ?: (__('variant.standard') ?? 'Standard') }}</td>
                        <td class="text-right">{{ number_format($variant->price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $variant->stock }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    @if($chunkIndex < count($productChunks) - 1)
        <div class="page-break"></div>
    @endif
    @endforeach

    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Arial");
                $width = $fontMetrics->getTextWidth($text, $font, $size) / 2;
                $x = $pdf->get_width() - $width - 40;
                $y = $pdf->get_height() - 30;
                $pdf->page_text($x, $y, $text, $font, $size, array(0, 0, 0));
            }
        </script>
    </div>
</body>
</html>

