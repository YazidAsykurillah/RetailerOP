@extends('adminlte::page')

@section('title', 'New Transaction')

@section('content_header')
    <h1><i class="fas fa-cash-register"></i> New Transaction</h1>
@stop

@section('css')
<style>
    .content-wrapper {
        padding-bottom: 180px; /* Space for fixed bottom bar */
    }
    .transaction-panel {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .transaction-header {
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
    }
    .transaction-body {
        padding: 20px;
    }
    .item-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .item-row:hover {
        border-color: #667eea;
    }
    .item-row .row-number {
        font-weight: bold;
        color: #667eea;
        font-size: 1.1rem;
    }
    .remove-row-btn {
        opacity: 0.6;
    }
    .remove-row-btn:hover {
        opacity: 1;
    }
    .add-row-btn {
        border: 2px dashed #28a745;
        background: transparent;
        color: #28a745;
        width: 100%;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .add-row-btn:hover {
        background: #28a745;
        color: white;
    }
    .item-subtotal {
        font-weight: 600;
        color: #28a745;
        font-size: 1.1rem;
    }

    /* Fixed Bottom Bar */
    .fixed-bottom-bar {
        position: fixed;
        bottom: 0;
        left: 250px; /* AdminLTE sidebar width */
        right: 0;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 15px 25px;
        box-shadow: 0 -5px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: left 0.3s;
    }
    .sidebar-collapse .fixed-bottom-bar {
        left: 0;
    }
    .bottom-bar-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .summary-section {
        display: flex;
        align-items: center;
        gap: 30px;
    }
    .summary-item {
        text-align: center;
    }
    .summary-item .label {
        font-size: 0.75rem;
        opacity: 0.7;
        text-transform: uppercase;
    }
    .summary-item .value {
        font-size: 1.2rem;
        font-weight: 700;
    }
    .summary-item.total .value {
        color: #38ef7d;
        font-size: 1.5rem;
    }
    .payment-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .payment-methods {
        display: flex;
        gap: 5px;
    }
    .payment-method {
        padding: 8px 15px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .payment-method:hover {
        border-color: rgba(255,255,255,0.6);
    }
    .payment-method.active {
        border-color: #38ef7d;
        background: rgba(56, 239, 125, 0.2);
    }
    .payment-method i {
        margin-right: 5px;
    }
    .amount-input {
        background: rgba(255,255,255,0.1);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        width: 150px;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: right;
    }
    .amount-input:focus {
        border-color: #38ef7d;
        background: rgba(255,255,255,0.15);
        outline: none;
    }
    .amount-input::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .change-display {
        padding: 8px 15px;
        border-radius: 6px;
        font-weight: 600;
        min-width: 120px;
        text-align: center;
    }
    .change-display.positive {
        background: rgba(56, 239, 125, 0.2);
        color: #38ef7d;
    }
    .change-display.negative {
        background: rgba(239, 71, 58, 0.2);
        color: #ef473a;
    }
    .checkout-btn {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .checkout-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(56, 239, 125, 0.4);
    }
    .checkout-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .customer-toggle {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
    }
    .customer-toggle:hover {
        border-color: rgba(255,255,255,0.6);
    }
    .customer-panel {
        display: none;
        background: rgba(255,255,255,0.1);
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
    }
    .customer-panel.show {
        display: block;
    }
    .customer-input {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        margin-right: 10px;
        width: 200px;
    }
    .customer-input::placeholder {
        color: rgba(255,255,255,0.5);
    }
    .col-number {
        flex: 0 0 45px;
        max-width: 45px;
        text-align: center;
        padding-left: 5px;
        padding-right: 5px;
    }
    /* Fix for Select2 Bootstrap 4 Theme Apply Clear */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 25px; /* Adjust to not overlap with arrow */
        top: 50%;
        transform: translateY(-50%);
        z-index: 99;
        font-weight: bold;
        font-size: 1.2rem;
        color: #dc3545;
        cursor: pointer;
        display: block !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        z-index: 1; /* Ensure arrow is behind clear button if they overlap, though spacing handles it */
    }
    /* Fix for Select2 Hover Visibility */
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #e9ecef !important; /* Light gray background */
        color: #212529 !important; /* Dark text to keep contrast */
    }
</style>
@stop

@section('content')
<!-- Transaction Items Panel - Full Width -->
<div class="transaction-panel">
    <div class="transaction-header d-flex justify-content-between align-items-center">
        <h5 class="m-0">
            <i class="fas fa-list"></i> Transaction Items
        </h5>
        <div class="mr-auto ml-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                </div>
                <input type="text" id="barcode-input" class="form-control" placeholder="Scan Barcode (Enter)" autofocus autocomplete="off">
            </div>
        </div>
        <button type="button" class="customer-toggle" id="customer-toggle">
            <i class="fas fa-user"></i> Customer Info
        </button>
    </div>
    
    <!-- Customer Panel (Hidden by default) -->
    <div class="customer-panel" id="customer-panel">
        <div class="form-group mb-2">
            <select id="customer-select" class="form-control" style="width: 100%;">
                <option></option>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="customer-input w-100 mb-2" id="customer-name" placeholder="Customer Name (Walk-in)">
            </div>
            <div class="col-md-6">
                <input type="text" class="customer-input w-100 mb-2" id="customer-phone" placeholder="Phone Number">
            </div>
        </div>
        <textarea id="notes" class="form-control" rows="2" placeholder="Transaction Notes"></textarea>
    </div>

    <div class="transaction-body">
        <!-- Header Row -->
        <div class="row mb-2 font-weight-bold text-muted d-none d-md-flex align-items-center">
            <div class="col-number">#</div>
            <div class="col">Product</div>
            <div class="col-md-1 text-center">Qty</div>
            <div class="col-md-2">Price</div>
            <div class="col-md-1 text-center">Disc %</div>
            <div class="col-md-2 text-right">Subtotal</div>
            <div class="col-md-1 text-center">Action</div>
        </div>

        <!-- Item Rows Container -->
        <div id="items-container">
            <!-- First empty row will be added by JS -->
        </div>

        <!-- Add Row Button -->
        <button type="button" class="add-row-btn" id="add-row-btn">
            <i class="fas fa-plus-circle"></i> Add Another Product
        </button>
    </div>
</div>

<!-- Fixed Bottom Bar -->
<div class="fixed-bottom-bar">
    <div class="bottom-bar-content">
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-item">
                <div class="label">Items</div>
                <div class="value" id="items-count">0</div>
            </div>
            <div class="summary-item">
                <div class="label">Subtotal</div>
                <div class="value" id="summary-subtotal">0</div>
            </div>
            <div class="summary-item">
                <div class="label">Discount</div>
                <div class="value" id="summary-discount" style="color: #ef473a;">0</div>
            </div>
            <div class="summary-item total">
                <div class="label">Total</div>
                <div class="value" id="summary-total">0</div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="payment-section">
            <div class="payment-methods">
                <div class="payment-method active" data-method="cash">
                    <i class="fas fa-money-bill-wave"></i> Cash
                </div>
                <div class="payment-method" data-method="card">
                    <i class="fas fa-credit-card"></i> Card
                </div>
                <div class="payment-method" data-method="transfer">
                    <i class="fas fa-university"></i> Transfer
                </div>
            </div>
            
            <input type="text" class="amount-input" id="amount-paid" placeholder="Amount Paid">
            
            <div class="change-display positive" id="change-display" style="display: none;">
                <small>Change</small><br>
                <span id="change-amount">0</span>
            </div>
            
            <button class="checkout-btn" id="submit-btn" disabled>
                <i class="fas fa-check-circle"></i> Complete
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> Transaction Complete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="success-content">
                <!-- Success content will be loaded here -->
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> View All Transactions
                </a>
                <a href="#" class="btn btn-info" id="print-btn" target="_blank">
                    <i class="fas fa-print"></i> Print Receipt
                </a>
                <button type="button" class="btn btn-primary" id="new-transaction-btn">
                    <i class="fas fa-plus"></i> New Transaction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Item Row Template -->
<template id="item-row-template">
    <div class="item-row" data-row-id="">
        <div class="row align-items-center">
            <div class="col-number">
                <span class="row-number"></span>
            </div>
            <div class="col">
                <select class="form-control variant-select" style="width: 100%;">
                    <option value="">Select product...</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control qty-input text-center" value="1" min="1" placeholder="Qty">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control price-display" readonly placeholder="Price">
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control discount-input text-center" value="0" min="0" max="100" placeholder="%">
            </div>
            <div class="col-md-2 text-right">
                <div class="item-subtotal">0</div>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>
@stop

@section('js')
<script>
$(function() {
    let rowCounter = 0;
    let selectedPaymentMethod = 'cash';
    let selectedCustomerId = null;

    // Barcode Scanner Logic
    $('#barcode-input').on('keypress', function(e) {
        if (e.which == 13) { // Enter key
            e.preventDefault();
            const sku = $(this).val().trim();
            if (!sku) return;

            // Clear input immediately for next scan
            $(this).val('');

            $.ajax({
                url: '{{ route("admin.pos.find-by-sku") }}',
                method: 'GET',
                data: { sku: sku },
                success: function(response) {
                    if (response.success) {
                        addProductToCart(response.variant);
                        toastr.success('Added: ' + response.variant.full_name ?? response.variant.product_name);
                    }
                },
                error: function(xhr) {
                    let message = 'Product not found';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                    
                    // Specific sound or visual cue could be added here
                }
            });
        }
    });

    // Keep focus on barcode input
    // $(document).on('click', function(e) {
    //     if (!$(e.target).closest('input, select, button, .select2').length) {
    //         $('#barcode-input').focus();
    //     }
    // });
    
    // Function to add product to cart (or increment if exists)
    function addProductToCart(variant) {
        let foundRow = null;
        
        // Check if item exists
        $('.item-row').each(function() {
            if ($(this).data('variant-id') == variant.id) {
                foundRow = $(this);
                return false;
            }
        });

        if (foundRow) {
            // Increment quantity
            const qtyInput = foundRow.find('.qty-input');
            let newQty = parseInt(qtyInput.val()) + 1;
            const stock = foundRow.data('stock');
            
            if (newQty <= stock) {
                qtyInput.val(newQty);
                updateRowSubtotal(foundRow);
            } else {
                toastr.warning('Maximum stock reached for ' + variant.product_name);
            }
        } else {
            // Add new row if current row is empty and it's the only row, use it.
            // Otherwise append new.
            let targetRow = null;
            const $validRows = $('.item-row').filter(function() { return $(this).data('variant-id'); });
            const $allRows = $('.item-row');

            if ($validRows.length < $allRows.length) {
                 // Try to find an empty row
                 $('.item-row').each(function() {
                     if (!$(this).data('variant-id')) {
                         targetRow = $(this);
                         return false;
                     }
                 });
            }

            if (!targetRow) {
                addNewRow();
                targetRow = $('.item-row').last();
            }
            
            // Populate row
            populateRow(targetRow, variant);
        }
        updateSummary();
    }

    function populateRow($row, variant) {
         $row.data('variant-id', variant.id);
         $row.data('product-name', variant.product_name);
         $row.data('variant-name', variant.variant_name);
         $row.data('price', variant.price);
         $row.data('stock', variant.stock);
         
         // Update Select2 data manually
         const $select = $row.find('.variant-select');
         const option = new Option(variant.product_name + ' - ' + variant.variant_name, variant.id, true, true);
         $select.append(option).trigger('change');
         
         $row.find('.price-display').val(formatNumber(variant.price));
         $row.find('.qty-input').attr('max', variant.stock).val(1);
         updateRowSubtotal($row);
    }

    // Initialize AutoNumeric for Amount Paid input
    const amountPaidAN = new AutoNumeric('#amount-paid', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        minimumValue: '0',
        modifyValueOnWheel: false
    });
    
    // Initialize Customer Select2
    $('#customer-select').select2({
        theme: 'bootstrap4',
        placeholder: 'Search Customer (Name/Phone)',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.customers.index") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { 
                    search: { value: params.term }, // DataTables search format
                    start: 0,
                    length: 10,
                    columns: [
                        {data: 'name', name: 'name', searchable: true, orderable: true, search: {value: '', regex: false}},
                        {data: 'phone', name: 'phone', searchable: true, orderable: false, search: {value: '', regex: false}}
                    ]
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(item) {
                        return {
                            id: item.id,
                            text: item.name + (item.phone ? ' (' + item.phone + ')' : ''),
                            customer: item
                        }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    // Handle Customer Selection
    $('#customer-select').on('select2:select', function(e) {
        let customer = e.params.data.customer;
        selectedCustomerId = customer.id;
        $('#customer-name').val(customer.name).prop('readonly', true);
        $('#customer-phone').val(customer.phone || '').prop('readonly', true);
        
        // Future: specific customer discount logic here
    });

    $('#customer-select').on('select2:clear', function(e) {
        selectedCustomerId = null;
        $('#customer-name').val('').prop('readonly', false);
        $('#customer-phone').val('').prop('readonly', false);
    });

    // Add first empty row on page load
    addNewRow();

    // Toggle customer panel
    $('#customer-toggle').on('click', function() {
        $('#customer-panel').toggleClass('show');
    });

    // Add new row button
    $('#add-row-btn').on('click', function() {
        addNewRow();
    });

    // Add new row function
    function addNewRow() {
        rowCounter++;
        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        const $row = $(clone).find('.item-row');
        
        $row.attr('data-row-id', rowCounter);
        $row.find('.row-number').text(rowCounter);
        
        $('#items-container').append($row);

        // Initialize Select2 on the new row
        const $select = $row.find('.variant-select');
        $select.select2({
            theme: 'bootstrap4',
            placeholder: 'Search product by name or SKU...',
            allowClear: true,
            ajax: {
                url: '{{ route("admin.pos.search-products") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data.results };
                },
                cache: true
            },
            minimumInputLength: 1,
            templateResult: formatProduct,
            templateSelection: formatProductSelection
        });

        updateRowNumbers();
        updateSummary();
    }

    function formatProduct(product) {
        if (!product.id) return product.text;
        return $('<div>' +
            '<strong>' + product.product_name + '</strong>' +
            '<span class="text-muted"> - ' + product.variant_name + '</span>' +
            '<br><small class="text-muted">SKU: ' + product.sku + ' | Stock: ' + product.stock + '</small>' +
            '<span class="float-right text-success font-weight-bold">' + formatNumber(product.price) + '</span>' +
            '</div>');
    }

    function formatProductSelection(product) {
        if (!product.id) return product.text;
        return product.variant_name || product.text;
    }

    // Handle variant selection
    $(document).on('select2:select', '.variant-select', function(e) {
        const data = e.params.data;
        const $row = $(this).closest('.item-row');
        const currentRowId = $row.attr('data-row-id');
        
        // Check if this variant is already selected in another row
        let isDuplicate = false;
        $('.item-row').each(function() {
            const rowId = $(this).attr('data-row-id');
            const variantId = $(this).data('variant-id');
            if (rowId !== currentRowId && variantId == data.id) {
                isDuplicate = true;
                return false; // break the loop
            }
        });
        
        if (isDuplicate) {
            toastr.warning('This product variant is already added. Please adjust the quantity instead.');
            $(this).val(null).trigger('change');
            return;
        }
        
        $row.data('variant-id', data.id);
        $row.data('product-name', data.product_name);
        $row.data('variant-name', data.variant_name);
        $row.data('price', data.price);
        $row.data('stock', data.stock);
        
        $row.find('.price-display').val(formatNumber(data.price));
        $row.find('.qty-input').attr('max', data.stock).val(1);
        
        updateRowSubtotal($row);
        updateSummary();
    });

    // Handle variant clear
    $(document).on('select2:clear', '.variant-select', function() {
        const $row = $(this).closest('.item-row');
        $row.removeData('variant-id price stock');
        $row.find('.price-display').val('');
        $row.find('.item-subtotal').text('0');
        updateSummary();
    });

    // Handle quantity change
    $(document).on('input change', '.qty-input', function() {
        const $row = $(this).closest('.item-row');
        let qty = parseInt($(this).val()) || 1;
        const stock = $row.data('stock') || 999;
        
        if (qty > stock) {
            qty = stock;
            $(this).val(qty);
            toastr.warning('Maximum stock available: ' + stock);
        }
        if (qty < 1) {
            qty = 1;
            $(this).val(1);
        }
        
        updateRowSubtotal($row);
        updateSummary();
    });

    // Remove row
    $(document).on('click', '.remove-row-btn', function() {
        const $row = $(this).closest('.item-row');
        const rowCount = $('.item-row').length;
        
        if (rowCount <= 1) {
            // Don't remove last row, just clear it
            $row.find('.variant-select').val(null).trigger('change');
            $row.find('.qty-input').val(1);
            $row.find('.discount-input').val(0);
            $row.find('.price-display').val('');
            $row.find('.item-subtotal').text('0');
            $row.removeData('variant-id price stock');
        } else {
            $row.remove();
            updateRowNumbers();
        }
        
        updateSummary();
    });

    // Update row numbers
    function updateRowNumbers() {
        $('.item-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
        });
    }

    // Update row subtotal
    function updateRowSubtotal($row) {
        const price = parseFloat($row.data('price')) || 0;
        const qty = parseInt($row.find('.qty-input').val()) || 0;
        const discountPercent = parseFloat($row.find('.discount-input').val()) || 0;
        const lineTotal = price * qty;
        const discountAmount = lineTotal * (discountPercent / 100);
        const subtotal = lineTotal - discountAmount;
        $row.find('.item-subtotal').text(formatNumber(subtotal));
    }

    // Handle discount input change
    $(document).on('input change', '.discount-input', function() {
        const $row = $(this).closest('.item-row');
        updateRowSubtotal($row);
        updateSummary();
    });

    // Update summary
    function updateSummary() {
        let itemsCount = 0;
        let subtotal = 0;
        let totalDiscount = 0;

        $('.item-row').each(function() {
            const variantId = $(this).data('variant-id');
            if (variantId) {
                const price = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                const lineTotal = price * qty;
                const discountAmount = lineTotal * (discountPercent / 100);
                subtotal += lineTotal;
                totalDiscount += discountAmount;
                itemsCount += qty;
            }
        });

        const total = subtotal - totalDiscount;

        $('#items-count').text(itemsCount);
        $('#summary-subtotal').text(formatNumber(subtotal));
        $('#summary-discount').text(formatNumber(totalDiscount));
        $('#summary-total').text(formatNumber(total));

        // Enable/disable submit button
        const hasItems = $('.item-row').filter(function() {
            return $(this).data('variant-id');
        }).length > 0;
        
        $('#submit-btn').prop('disabled', !hasItems);

        updateChangeDisplay();
    }

    // Payment method
    $('.payment-method').on('click', function() {
        $('.payment-method').removeClass('active');
        $(this).addClass('active');
        selectedPaymentMethod = $(this).data('method');
    });

    // Amount paid
    $('#amount-paid').on('input', function() {
        updateChangeDisplay();
    });

    // Update change display
    function updateChangeDisplay() {
        let subtotal = 0;
        let totalDiscount = 0;
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                const price = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                const lineTotal = price * qty;
                const discountAmount = lineTotal * (discountPercent / 100);
                subtotal += lineTotal;
                totalDiscount += discountAmount;
            }
        });

        const total = subtotal - totalDiscount;
        const amountPaid = amountPaidAN.getNumber() || 0;
        const change = amountPaid - total;

        if (amountPaid > 0) {
            $('#change-display').show();
            if (change >= 0) {
                $('#change-display').removeClass('negative').addClass('positive');
                $('#change-amount').text(formatNumber(change));
            } else {
                $('#change-display').removeClass('positive').addClass('negative');
                $('#change-amount').text(formatNumber(Math.abs(change)) + ' short');
            }
        } else {
            $('#change-display').hide();
        }
    }

    // Submit transaction
    $('#submit-btn').on('click', function() {
        // Collect items
        const items = [];
        let valid = true;

        $('.item-row').each(function() {
            const variantId = $(this).data('variant-id');
            if (variantId) {
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const price = parseFloat($(this).data('price')) || 0;
                const discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                const lineTotal = price * qty;
                const discountAmount = lineTotal * (discountPercent / 100);
                
                if (qty <= 0) {
                    toastr.error('Quantity must be at least 1');
                    valid = false;
                    return false;
                }

                items.push({
                    variant_id: variantId,
                    quantity: qty,
                    price: price,
                    discount: discountAmount
                });
            }
        });

        if (!valid) return;

        if (items.length === 0) {
            toastr.error('Please add at least one product');
            return;
        }

        // Calculate totals
        let subtotal = 0;
        let totalDiscount = 0;
        items.forEach(item => {
            subtotal += item.price * item.quantity;
            totalDiscount += item.discount;
        });
        const total = subtotal - totalDiscount;
        const amountPaid = amountPaidAN.getNumber() || 0;

        if (amountPaid < total) {
            toastr.error('Amount paid is insufficient!');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '{{ route("admin.pos.checkout") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items,
                subtotal: subtotal,
                discount: totalDiscount,
                tax: 0,
                grand_total: total,
                payment_method: selectedPaymentMethod,
                amount_paid: amountPaid,
                customer_id: selectedCustomerId,
                customer_name: $('#customer-name').val(),
                customer_phone: $('#customer-phone').val(),
                notes: $('#notes').val()
            },
            success: function(response) {
                if (response.success) {
                    showSuccessModal(response.transaction);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to process transaction');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Complete');
            }
        });
    });

    // Show success modal
    function showSuccessModal(transaction) {
        $('#success-content').html(`
            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            <h3 class="mt-3">Transaction Successful!</h3>
            <p class="text-muted">Invoice Number</p>
            <h4 class="text-primary">${transaction.invoice_no}</h4>
            <hr>
            <div class="row">
                <div class="col-6 text-right"><strong>Total:</strong></div>
                <div class="col-6 text-left">${formatNumber(transaction.grand_total)}</div>
            </div>
            <div class="row">
                <div class="col-6 text-right"><strong>Change:</strong></div>
                <div class="col-6 text-left text-success font-weight-bold">${formatNumber(transaction.change)}</div>
            </div>
        `);
        
        $('#print-btn').attr('href', '{{ url("admin/transactions") }}/' + transaction.id + '/print');
        $('#successModal').modal('show');
    }

    // New transaction button
    $('#new-transaction-btn').on('click', function() {
        location.reload();
    });

    // Format number helper
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }
});
</script>
@stop
