<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ $variant->name }} Barcode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .shop-name {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .variant-name {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .price {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .barcode {
            margin: 10px 0;
            display: flex;
            justify-content: center;
        }
        .barcode svg {
            max-width: 100%;
            height: 50px;
        }
        @media print {
            .no-print {
                display: none;
            }
            .label-container {
                border: none;
                width: 100mm; /* Fixed width for label */
                max-width: 100mm;
                padding: 5px; /* Small padding */
                overflow: hidden; /* Prevent spillover */
            }
            @page {
                size: 100mm auto; /* Set paper size */
                margin: 0; /* Remove default page margins */
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
        /* Screen preview matches print size */
        .label-container {
            border: 2px dashed #ccc;
            padding: 5px;
            width: 100mm;
            margin: 0 auto 10px auto; /* Add bottom margin for screen view */
            text-align: center;
            /* Avoid breaking inside a label */
            page-break-inside: avoid;
            break-inside: avoid;
            /* Allow flow, let printer decide cut based on paper size */
            page-break-after: auto; 
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print Label</button>
    </div>

    @for($i = 0; $i < $qty; $i++)
    <div class="label-container">
        <div class="shop-name">{{ config('app.name', 'SISKHA STORE') }}</div>
        <div class="product-name">{{ $product->name }}</div>
        @if($variant->name)
            <div class="variant-name">{{ $variant->name }}</div>
        @endif
        
        <div class="barcode">
            {!! $barcode !!}
        </div>
        <div style="font-size: 10px; letter-spacing: 2px;">{{ $variant->sku }}</div>
        
        <div class="price">Rp {{ number_format($variant->price, 0, ',', '.') }}</div>
    </div>
    @endfor

</body>
</html>
