@extends('adminlte::page')

@section('title', 'Edit Transaction ' . $transaction->invoice_no)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit"></i> Edit Transaction: {{ $transaction->invoice_no }}</h1>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to History
        </a>
    </div>
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
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); /* Different color for edit mode */
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
        border-color: #f5576c;
    }
    .item-row .row-number {
        font-weight: bold;
        color: #f5576c;
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
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
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
        box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
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
        background: rgba(0,0,0,0.05); /* Slight dark bg for edit mode visibility on white */
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
    }
    .customer-panel.show {
        display: block;
    }
    .col-number {
        flex: 0 0 45px;
        max-width: 45px;
        text-align: center;
        padding-left: 5px;
        padding-right: 5px;
    }
    /* Fix for Select2 Bootstrap 4 Theme */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 25px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 99;
        font-weight: bold;
        font-size: 1.2rem;
        color: #dc3545;
        cursor: pointer;
        display: block !important;
    }
</style>
@stop

@section('content')
<!-- Transaction Items Panel - Full Width -->
<div class="transaction-panel">
    <div class="transaction-header d-flex justify-content-between align-items-center">
        <h5 class="m-0">
            <i class="fas fa-edit"></i> Edit Transaction Items
        </h5>
        <div class="mr-auto ml-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                </div>
                <input type="text" id="barcode-input" class="form-control" placeholder="Scan Barcode (Enter)" autofocus autocomplete="off">
            </div>
        </div>
        <button type="button" class="customer-toggle" id="customer-toggle" style="color: #333; border-color: #ddd;">
            <i class="fas fa-user"></i> Customer Info
        </button>
    </div>
    
    <!-- Customer Panel (Visible by default) -->
    <div class="customer-panel show" id="customer-panel">
        <div class="form-group mb-2">
            <select id="customer-select" class="form-control" style="width: 100%;">
                <option></option>
                @if($transaction->customer)
                    <option value="{{ $transaction->customer_id }}" selected>{{ $transaction->customer->name }} ({{ $transaction->customer->phone }})</option>
                @endif
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control w-100 mb-2" id="customer-name" value="{{ $transaction->customer_name }}" placeholder="Customer Name (Walk-in)" {{ $transaction->customer_id ? 'readonly' : '' }}>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control w-100 mb-2" id="customer-phone" value="{{ $transaction->customer_phone }}" placeholder="Phone Number" {{ $transaction->customer_id ? 'readonly' : '' }}>
            </div>
        </div>
        <textarea id="notes" class="form-control" rows="2" placeholder="Transaction Notes">{{ $transaction->notes }}</textarea>
    </div>

    <div class="transaction-body">
        <!-- Header Row -->
        <div class="row mb-2 font-weight-bold text-muted d-none d-md-flex align-items-center">
            <div class="col-number">#</div>
            <div class="col" style="min-width: 0;">Product</div>
            <div class="col-md-1 text-center">Qty</div>
            <div class="col-md-2">Price</div>
            <div class="col-md-1 text-center">Disc %</div>
            <div class="col-md-1">Potongan Harga</div>
            <div class="col-md-2 text-right">Subtotal</div>
            <div class="col-md-1 text-center">Action</div>
        </div>

        <!-- Item Rows Container -->
        <div id="items-container">
            <!-- Rows will be populated by JS -->
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
            <div class="payment-mode-toggle mb-3">
                <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                    <label class="btn btn-outline-info {{ $transaction->payment_mode === 'full' ? 'active' : '' }} flex-fill">
                        <input type="radio" name="payment_mode" value="full" {{ $transaction->payment_mode === 'full' ? 'checked' : '' }} autocomplete="off"> Full Payment
                    </label>
                    <label class="btn btn-outline-info {{ $transaction->payment_mode === 'partial' ? 'active' : '' }} flex-fill">
                        <input type="radio" name="payment_mode" value="partial" {{ $transaction->payment_mode === 'partial' ? 'checked' : '' }} autocomplete="off"> Partial Payment
                    </label>
                </div>
            </div>

            <div id="full-payment-section" style="{{ $transaction->payment_mode === 'partial' ? 'display: none;' : '' }}">
                <div class="payment-methods">
                    <div class="payment-method {{ $transaction->payment_method == 'cash' || $transaction->payment_mode === 'partial' ? 'active' : '' }}" data-method="cash">
                        <i class="fas fa-money-bill-wave"></i> Cash
                    </div>
                    <div class="payment-method {{ $transaction->payment_method == 'card' ? 'active' : '' }}" data-method="card">
                        <i class="fas fa-credit-card"></i> Card
                    </div>
                    <div class="payment-method {{ $transaction->payment_method == 'transfer' ? 'active' : '' }}" data-method="transfer">
                        <i class="fas fa-university"></i> Transfer
                    </div>
                </div>
                
                <input type="text" class="amount-input" id="amount-paid" placeholder="Amount Paid" value="{{ $transaction->payment_mode === 'full' ? (float)$transaction->amount_paid : 0 }}">
                
                <div class="change-display {{ $transaction->change >= 0 ? 'positive' : 'negative' }}" id="change-display" style="{{ $transaction->payment_mode === 'partial' ? 'display: none;' : '' }}">
                    <small>Change</small><br>
                    <span id="change-amount">{{ number_format(abs($transaction->change), 0, ',', '.') }}</span>
                </div>
            </div>

            <div id="partial-payment-section" style="{{ $transaction->payment_mode === 'full' ? 'display: none;' : '' }}">
                <div class="partial-header d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-calendar-alt"></i> Payment Schedule</h6>
                    <button type="button" class="btn btn-xs btn-primary font-weight-bold" id="add-payment-row">
                        <i class="fas fa-plus"></i> ADD PAYMENT
                    </button>
                </div>
                <div id="payment-schedule-container" class="mb-2" style="max-height: 150px; overflow-y: auto; overflow-x: hidden; width: 100%;">
                    <!-- Existing payments will be populated here by JS -->
                </div>
                <div class="remaining-balance-box p-2 bg-light border rounded mb-2 d-flex justify-content-between align-items-center text-dark">
                    <span>Remaining Balance:</span>
                    <span id="remaining-balance" class="font-weight-bold text-primary">0</span>
                </div>
            </div>
            
            <button class="checkout-btn" id="submit-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>
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
            <div class="col" style="min-width: 0;">
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
            <div class="col-md-1">
                <input type="text" class="form-control cut-input text-right" placeholder="0">
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

<!-- Payment Row Template -->
<template id="payment-row-template">
    <div class="payment-row mb-2 p-2 bg-dark rounded border border-secondary">
        <div class="row no-gutters align-items-center">
            <div class="col-5 pr-1">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text p-1"><i class="fas fa-calendar-day"></i></span>
                    </div>
                    <input type="date" class="form-control payment-date-input" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="col-4 px-1">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text p-1"><i class="fas fa-money-bill"></i></span>
                    </div>
                    <input type="text" class="form-control payment-amount-input text-right" placeholder="0">
                </div>
            </div>
            <div class="col-2 px-1">
                <select class="form-control form-control-sm payment-method-select">
                    <option value="cash">CASH</option>
                    <option value="card">CARD</option>
                    <option value="transfer">BANK</option>
                </select>
            </div>
            <div class="col-1 text-right pl-1">
                <button type="button" class="btn btn-xs btn-outline-danger remove-payment-row">
                    <i class="fas fa-times"></i>
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
    let selectedPaymentMode = '{{ $transaction->payment_mode ?? "full" }}';
    let selectedPaymentMethod = '{{ $transaction->payment_method }}';
    let selectedCustomerId = {{ $transaction->customer_id ?? 'null' }};
    const initialCart = @json($initialCart);
    const initialPayments = @json($initialPayments ?? []);

    // Initialize AutoNumeric for Amount Paid
    const amountPaidAN = new AutoNumeric('#amount-paid', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        minimumValue: '0',
        modifyValueOnWheel: false
    });
    
    @if($transaction->payment_mode === 'full')
        amountPaidAN.set({{ (float)$transaction->amount_paid }});
    @else
        amountPaidAN.set(0);
    @endif

    // Initialize logic function
    function init() {
        if (initialCart && initialCart.length > 0) {
            initialCart.forEach(item => {
                addProductToCart_Init(item);
            });
        } else {
            addNewRow();
        }

        // Initialize Existing Payments
        if (selectedPaymentMode === 'partial' && initialPayments.length > 0) {
            initialPayments.forEach(p => {
                addPaymentRow(p.amount, p.payment_method, p.payment_date, p.notes);
            });
        } else if (selectedPaymentMode === 'partial') {
            addPaymentRow(); // Add at least one empty row if partial but no payments
        }

        updateSummary();
        updatePaymentModeAbility();
    }

    // --- Core Logic Copied & Adapted from POS ---

    // Constants & Initialization
    // Barcode Scanner
    $('#barcode-input').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            const sku = $(this).val().trim();
            if (!sku) return;
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
                    if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                    toastr.error(message);
                }
            });
        }
    });

    // Helper to add product (new vs init)
    function addProductToCart(variant) {
        // ... (Same reuse logic as POS, check existence)
        let foundRow = null;
        $('.item-row').each(function() {
            if ($(this).data('variant-id') == variant.id) {
                foundRow = $(this);
                return false;
            }
        });

        if (foundRow) {
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
            let targetRow = null;
            const $validRows = $('.item-row').filter(function() { return $(this).data('variant-id'); });
            const $allRows = $('.item-row');

            if ($validRows.length < $allRows.length) {
                 $('.item-row').each(function() {
                     if (!$(this).data('variant-id')) { targetRow = $(this); return false; }
                 });
            }

            if (!targetRow) {
                addNewRow();
                targetRow = $('.item-row').last();
            }
            populateRow(targetRow, variant);
        }
        updateSummary();
    }

    // Special init for existing items (stock is pre-calculated to include current qty)
    function addProductToCart_Init(item) {
        addNewRow();
        const $row = $('.item-row').last();
        
        $row.data('variant-id', item.variant_id);
        $row.data('product-name', item.product_name);
        $row.data('variant-name', item.variant_name);
        $row.data('price', item.price);
        $row.data('stock', item.stock); // This includes current quantity

        // Update Select2
        const $select = $row.find('.variant-select');
        const option = new Option(item.product_name + ' - ' + item.variant_name, item.variant_id, true, true);
        $select.append(option).trigger('change');

        $row.find('.price-display').val(formatNumber(item.price));
        $row.find('.qty-input').attr('max', item.stock).val(item.quantity);
        $row.find('.qty-input').attr('max', item.stock).val(item.quantity);
        $row.find('.discount-input').val(item.discount_percent || 0);
        
        // Init cut amount
        AutoNumeric.getAutoNumericElement($row.find('.cut-input').get(0)).set(item.cut_amount || 0);
        
        updateRowSubtotal($row);
    }

    function populateRow($row, variant) {
         $row.data('variant-id', variant.id);
         $row.data('product-name', variant.product_name);
         $row.data('variant-name', variant.variant_name);
         $row.data('price', variant.price);
         $row.data('stock', variant.stock);
         
         const $select = $row.find('.variant-select');
         const option = new Option(variant.product_name + ' - ' + variant.variant_name, variant.id, true, true);
         $select.append(option).trigger('change');
         
         $row.find('.price-display').val(formatNumber(variant.price));
         $row.find('.qty-input').attr('max', variant.stock).val(1);
         updateRowSubtotal($row);
    }

    // --- Shared Functions ---
    // Customer Select2
    $('#customer-select').select2({
        theme: 'bootstrap4',
        placeholder: 'Search Customer Member',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.customers.index") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { 
                    search: { value: params.term },
                    start: 0, length: 10,
                    columns: [{data: 'name', name: 'name', searchable: true}, {data: 'phone', name: 'phone', searchable: true}]
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(item) {
                        return { id: item.id, text: item.name + (item.phone ? ' (' + item.phone + ')' : ''), customer: item }
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#customer-select').on('select2:select', function(e) {
        let customer = e.params.data.customer;
        selectedCustomerId = customer.id;
        $('#customer-name').val(customer.name).prop('readonly', true);
        $('#customer-phone').val(customer.phone || '').prop('readonly', true);
        updatePaymentModeAbility();
    });

    $('#customer-select').on('select2:clear', function(e) {
        selectedCustomerId = null;
        $('#customer-name').val('').prop('readonly', false);
        $('#customer-phone').val('').prop('readonly', false);
        updatePaymentModeAbility();
    });

    // Row Management
    $('#add-row-btn').on('click', function() { addNewRow(); });

    function addNewRow() {
        rowCounter++;
        const template = document.getElementById('item-row-template');
        const clone = template.content.cloneNode(true);
        const $row = $(clone).find('.item-row');
        
        $row.attr('data-row-id', rowCounter);
        $row.find('.row-number').text(rowCounter);
        $('#items-container').append($row);

        const $select = $row.find('.variant-select');
        $select.select2({
            theme: 'bootstrap4',
            placeholder: 'Search product...',
            allowClear: true,
            ajax: {
                url: '{{ route("admin.pos.search-products") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data.results }; },
                cache: true
            },
            minimumInputLength: 1,
            templateResult: formatProduct,
            templateSelection: formatProductSelection
        });

        new AutoNumeric($row.find('.cut-input').get(0), {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
        });

        updateRowNumbers();
    }

    // --- Payment Mode & Schedule Logic ---
    function updatePaymentModeAbility() {
        const $partialLabel = $('input[name="payment_mode"][value="partial"]').parent();
        const $partialInput = $('input[name="payment_mode"][value="partial"]');
        
        if (!selectedCustomerId) {
            $partialInput.prop('disabled', true);
            $partialLabel.addClass('disabled');
            
            if (selectedPaymentMode === 'partial') {
                $('input[name="payment_mode"][value="full"]').prop('checked', true).parent().click();
                toastr.warning('Partial payment is only available for registered customers.');
            }
        } else {
            $partialInput.prop('disabled', false);
            $partialLabel.removeClass('disabled');
        }
    }

    $('input[name="payment_mode"]').on('change', function() {
        selectedPaymentMode = $(this).val();
        if (selectedPaymentMode === 'full') {
            $('#full-payment-section').show();
            $('#partial-payment-section').hide();
        } else {
            $('#full-payment-section').hide();
            $('#partial-payment-section').show();
            if ($('.payment-row').length === 0) {
                addPaymentRow();
            }
        }
        updateSummary();
    });

    $('#add-payment-row').on('click', function() {
        addPaymentRow();
    });

    function addPaymentRow(amount = 0, method = 'cash', date = '{{ date("Y-m-d") }}', notes = '') {
        const template = document.getElementById('payment-row-template');
        const clone = template.content.cloneNode(true);
        const $row = $(clone).find('.payment-row');
        
        $row.find('.payment-date-input').val(date);
        $row.find('.payment-method-select').val(method);
        
        const $amountInput = $row.find('.payment-amount-input');
        const an = new AutoNumeric($amountInput.get(0), {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
        });
        if (amount > 0) an.set(amount);

        $('#payment-schedule-container').append($row);
        updateRemainingBalance();
    }

    $(document).on('click', '.remove-payment-row', function() {
        $(this).closest('.payment-row').remove();
        updateRemainingBalance();
    });

    $(document).on('input', '.payment-amount-input', function() {
        updateRemainingBalance();
    });

    function updateRemainingBalance() {
        let total = parseFloat($('#summary-total').text().replace(/\./g, '')) || 0;
        let totalPaid = 0;
        
        $('.payment-amount-input').each(function() {
            totalPaid += AutoNumeric.getNumber(this) || 0;
        });

        const remaining = total - totalPaid;
        $('#remaining-balance').text(formatNumber(remaining));
        
        if (remaining < 0) {
            $('#remaining-balance').removeClass('text-primary').addClass('text-danger');
        } else {
            $('#remaining-balance').removeClass('text-danger').addClass('text-primary');
        }
    }

    function formatProduct(product) {
        if (!product.id) return product.text;
        return $('<div><strong>' + product.product_name + '</strong><span class="text-muted"> - ' + product.variant_name + '</span><br><small class="text-muted">SKU: ' + product.sku + ' | Stock: ' + product.stock + '</small><span class="float-right text-success font-weight-bold">' + formatNumber(product.price) + '</span></div>');
    }
    function formatProductSelection(product) { return product.id ? (product.variant_name || product.text) : product.text; }

    $(document).on('select2:select', '.variant-select', function(e) {
        // Prevent recursive trigger when manual append
        if(e.params.data.element) return; 

        const data = e.params.data;
        const $row = $(this).closest('.item-row');
        const currentRowId = $row.attr('data-row-id');
        let isDuplicate = false;
        $('.item-row').each(function() {
            if ($(this).attr('data-row-id') !== currentRowId && $(this).data('variant-id') == data.id) {
                isDuplicate = true; return false; 
            }
        });
        
        if (isDuplicate) {
            toastr.warning('Product already added. Adjust quantity instead.');
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

    $(document).on('select2:clear', '.variant-select', function() {
        const $row = $(this).closest('.item-row');
        $row.removeData('variant-id price stock');
        $row.find('.price-display').val('');
        $row.find('.item-subtotal').text('0');
        $row.find('.discount-input').val(0);
        AutoNumeric.getAutoNumericElement($row.find('.cut-input').get(0)).set(0);
        updateSummary();
    });

    $(document).on('input change', '.qty-input', function() {
        const $row = $(this).closest('.item-row');
        let qty = parseInt($(this).val()) || 1;
        const stock = $row.data('stock') || 999;
        if (qty > stock) {
            qty = stock;
            $(this).val(qty);
            toastr.warning('Maximum stock available: ' + stock);
        }
        if (qty < 1) { qty = 1; $(this).val(1); }
        updateRowSubtotal($row);
        updateSummary();
    });

    $(document).on('input change', '.discount-input', function() {
        updateRowSubtotal($(this).closest('.item-row'));
        updateSummary();
    });

    $(document).on('input change keyup', '.cut-input', function() {
        updateRowSubtotal($(this).closest('.item-row'));
        updateSummary();
    });

    $(document).on('click', '.remove-row-btn', function() {
        const $row = $(this).closest('.item-row');
        if ($('.item-row').length <= 1) {
            $row.find('.variant-select').val(null).trigger('change');
            $row.find('.qty-input').val(1);
        } else {
            $row.remove();
            updateRowNumbers();
        }
        updateSummary();
    });

    function updateRowNumbers() {
        $('.item-row').each(function(index) { $(this).find('.row-number').text(index + 1); });
    }

    function updateRowSubtotal($row) {
        const price = parseFloat($row.data('price')) || 0;
        const qty = parseInt($row.find('.qty-input').val()) || 0;
        const disc = parseFloat($row.find('.discount-input').val()) || 0;
        let cutStr = $row.find('.cut-input').val() || '0';
        let cut = parseFloat(cutStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

        const lineTotal = price * qty;
        let sub = lineTotal - (lineTotal * (disc / 100)) - cut;
        if (sub < 0) sub = 0;
        $row.find('.item-subtotal').text(formatNumber(sub));
    }

    function updateSummary() {
        let itemsCount = 0;
        let subtotal = 0;
        let totalDiscount = 0;
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                const price = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const disc = parseFloat($(this).find('.discount-input').val()) || 0;
                let cutStr = $(this).find('.cut-input').val() || '0';
                let cut = parseFloat(cutStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

                const lineTotal = price * qty;
                const discAmt = lineTotal * (disc / 100);
                
                let deduction = discAmt + cut;
                if (deduction > lineTotal) deduction = lineTotal;

                subtotal += lineTotal;
                totalDiscount += deduction;
                itemsCount += qty;
            }
        });

        const total = subtotal - totalDiscount;
        $('#items-count').text(itemsCount);
        $('#summary-subtotal').text(formatNumber(subtotal));
        $('#summary-discount').text(formatNumber(totalDiscount));
        $('#summary-total').text(formatNumber(total));

        if (selectedPaymentMode === 'full') {
            updateChangeDisplay();
        } else {
            updateRemainingBalance();
        }
    }

    function updateChangeDisplay() {
        // ... (reuse logic)
        let total = parseFloat($('#summary-total').text().replace(/\./g, '')) || 0;
        const paid = amountPaidAN.getNumber() || 0;
        const change = paid - total;
        
        if (paid > 0) {
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

    // Payment method switch
    $('.payment-method').on('click', function() {
        $('.payment-method').removeClass('active');
        $(this).addClass('active');
        selectedPaymentMethod = $(this).data('method');
    });

    $('#amount-paid').on('input', function() { updateChangeDisplay(); });

    // Submit / Update
    $('#submit-btn').on('click', function() {
        const items = [];
        let valid = true;
        $('.item-row').each(function() {
            const vid = $(this).data('variant-id');
            if (vid) {
                const qty = parseInt($(this).find('.qty-input').val());
                const price = parseFloat($(this).data('price'));
                const disc = parseFloat($(this).find('.discount-input').val());
                let cutStr = $(this).find('.cut-input').val() || '0';
                let cut = parseFloat(cutStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

                if (qty <= 0) { toastr.error('Qty must be > 0'); valid=false; return false; }
                const lineTotal = price * qty;
                items.push({
                    variant_id: vid,
                    quantity: qty,
                    price: price,
                    discount: lineTotal * (disc/100),
                    cut_amount: cut
                });
            }
        });

        if (!valid) return;
        if (items.length === 0) { toastr.error('Add at least one product'); return; }

        const subtotal = parseFloat($('#summary-subtotal').text().replace(/\./g, ''));
        const totalDiscount = parseFloat($('#summary-discount').text().replace(/\./g, ''));
        const total = parseFloat($('#summary-total').text().replace(/\./g, ''));
        const amountPaid = amountPaidAN.getNumber();

        const payments = [];
        if (selectedPaymentMode === 'partial') {
            let totalInstallments = 0;
            $('.payment-row').each(function() {
                const amt = AutoNumeric.getAutoNumericElement($(this).find('.payment-amount-input').get(0)).getNumber();
                if (amt > 0) {
                    payments.push({
                        amount: amt,
                        payment_method: $(this).find('.payment-method-select').val(),
                        payment_date: $(this).find('.payment-date-input').val(),
                        notes: ''
                    });
                    totalInstallments += amt;
                }
            });

            if (payments.length === 0) {
                toastr.error('Please add at least one payment installment.');
                return;
            }

            if (totalInstallments > total) {
                toastr.error('Total payments exceed the transaction total!');
                return;
            }
        } else {
            if (amountPaid < total) {
                toastr.error('Insufficient payment!');
                return;
            }
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: '{{ route("admin.transactions.update", $transaction->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                items: items,
                subtotal: subtotal,
                discount: totalDiscount,
                tax: 0,
                grand_total: total,
                payment_mode: selectedPaymentMode,
                payment_method: selectedPaymentMethod,
                amount_paid: amountPaid,
                payments: payments,
                customer_id: selectedCustomerId,
                customer_name: $('#customer-name').val(),
                customer_phone: $('#customer-phone').val(),
                notes: $('#notes').val()
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Transaction Updated!');
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.transactions.index") }}';
                    }, 1000);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Update failed');
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
            }
        });
    });

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }

    // Toggle customer panel
    $('#customer-toggle').on('click', function() { $('#customer-panel').toggleClass('show'); });

    // Initialize
    init();
});
</script>
@stop
