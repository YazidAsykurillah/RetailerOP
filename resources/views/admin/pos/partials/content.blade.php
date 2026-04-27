<!-- Transaction Items Panel - Full Width -->
<div class="transaction-panel">
    <div class="transaction-header d-flex justify-content-between align-items-center">
        <h5 class="m-0">
            <i class="fas fa-list"></i> {{ __('pos.transaction_items') ?? 'Transaction Items' }}
        </h5>
        <div class="mr-auto ml-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                </div>
                <input type="text" id="barcode-input" class="form-control" placeholder="{{ __('pos.scan_barcode') ?? 'Scan Barcode (Enter)' }}" autofocus autocomplete="off">
            </div>
        </div>
        <button type="button" class="customer-toggle" id="customer-toggle">
            <i class="fas fa-user"></i> {{ __('pos.customer_info') ?? 'Customer Info' }}
        </button>
    </div>
    
    <!-- Customer Panel (Visible by default) -->
    <div class="customer-panel show" id="customer-panel">
        <div class="form-group mb-2">
            <select id="customer-select" class="form-control" style="width: 100%;">
                <option></option>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control w-100 mb-2" id="customer-name" placeholder="{{ __('pos.customer_name') ?? 'Customer Name (Walk-in)' }}">
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control w-100 mb-2" id="customer-phone" placeholder="{{ __('customer.phone') ?? 'Phone Number' }}">
            </div>
        </div>
        <textarea id="notes" class="form-control" rows="2" placeholder="{{ __('pos.transaction_notes') ?? 'Transaction Notes' }}"></textarea>
    </div>

    <div class="transaction-body">
        <!-- Header Row -->
        <div class="row mb-2 font-weight-bold text-muted d-none d-md-flex align-items-center">
            <div class="col-number">#</div>
            <div class="col" style="min-width: 0;">{{ __('product.singular') ?? 'Product' }}</div>
            <div class="col-md-1 text-center">{{ __('general.qty') ?? 'Qty' }}</div>
            <div class="col-md-2">{{ __('product.price') ?? 'Price' }}</div>
            <div class="col-md-1 text-center">{{ __('pos.disc_percent') ?? 'Disc %' }}</div>
            <div class="col-md-1">{{ __('pos.price_deduction') ?? 'Potongan Harga' }}</div>
            <div class="col-md-2 text-right">{{ __('pos.subtotal') ?? 'Subtotal' }}</div>
            <div class="col-md-1 text-center">{{ __('general.action') ?? 'Action' }}</div>
        </div>

        <!-- Item Rows Container -->
        <div id="items-container">
            <!-- First empty row will be added by JS -->
        </div>

        <!-- Add Row Button -->
        <button type="button" class="add-row-btn" id="add-row-btn">
            <i class="fas fa-plus-circle"></i> {{ __('pos.add_product') ?? 'Add Another Product' }}
        </button>
    </div>
</div>

<!-- Fixed Bottom Bar -->
<div class="fixed-bottom-bar">
    <div class="bottom-bar-content">
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-item">
                <div class="label">{{ __('pos.items') ?? 'Items' }}</div>
                <div class="value" id="items-count">0</div>
            </div>
            <div class="summary-item">
                <div class="label">{{ __('pos.subtotal') ?? 'Subtotal' }}</div>
                <div class="value" id="summary-subtotal">0</div>
            </div>
            <div class="summary-item">
                <div class="label">{{ __('general.discount') ?? 'Discount' }}</div>
                <div class="value" id="summary-discount" style="color: #ef473a;">0</div>
            </div>
            <div class="summary-item total">
                <div class="label">{{ __('general.total') ?? 'Total' }}</div>
                <div class="value" id="summary-total">0</div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="payment-section">
            <div class="payment-mode-toggle mb-3">
                <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                    <label class="btn btn-outline-info active flex-fill">
                        <input type="radio" name="payment_mode" value="full" checked autocomplete="off"> {{ __('pos.full_payment') ?? 'Full Payment' }}
                    </label>
                    <label class="btn btn-outline-info flex-fill">
                        <input type="radio" name="payment_mode" value="partial" autocomplete="off"> {{ __('pos.partial_payment') ?? 'Partial Payment' }}
                    </label>
                </div>
            </div>

            <div id="full-payment-section">
                <div class="payment-methods" id="payment-methods-row">
                    <div class="payment-method active" data-method="cash">
                        <i class="fas fa-money-bill-wave"></i> {{ __('pos.cash') ?? 'Cash' }}
                    </div>
                    <div class="payment-method" data-method="card">
                        <i class="fas fa-credit-card"></i> {{ __('pos.card') ?? 'Card' }}
                    </div>
                    <div class="payment-method" data-method="transfer">
                        <i class="fas fa-university"></i> {{ __('pos.transfer') ?? 'Transfer' }}
                    </div>
                </div>

                @can('Use Deposit')
                {{-- Deposit section: only visible when a customer with balance is selected --}}
                <div id="deposit-section" style="display:none; margin-top: 8px;">
                    <div style="background: rgba(40,167,69,0.15); border: 1px solid #28a745; border-radius: 6px; padding: 8px 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="m-0" style="font-size: 12px; color: #c8ffc8; font-weight: 600;" for="use-deposit-toggle">
                                <i class="fas fa-wallet"></i> Use Deposit
                            </label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use-deposit-toggle">
                                <label class="custom-control-label" for="use-deposit-toggle"></label>
                            </div>
                        </div>
                        <div style="font-size: 11px; color: #a0e0a0;" class="mb-1">
                            Balance: <span id="deposit-balance-display" class="font-weight-bold">0</span>
                        </div>
                        <div id="deposit-insufficient-warning" style="display:none; font-size: 11px; color: #ffcccc; margin-top: 2px; background: rgba(220,53,69,0.2); padding: 4px; border-radius: 4px;">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('pos.deposit_insufficient_full') ?? 'Deposit balance is insufficient for full payment.' }}
                        </div>
                        <div id="deposit-input-row" style="display:none;">
                            <input type="text" class="amount-input" id="deposit-amount-input"
                                   placeholder="Deposit amount" style="margin-top: 4px; background: rgba(255,255,255,0.1);">
                            <div style="font-size: 11px; color: #a0e0a0; margin-top: 3px;">
                                Remaining to pay: <span id="after-deposit-remaining" class="font-weight-bold">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <input type="text" class="amount-input" id="amount-paid" placeholder="{{ __('pos.amount_paid') ?? 'Amount Paid' }}">
                
                <div class="change-display positive" id="change-display" style="display: none;">
                    <small>{{ __('pos.change') ?? 'Change' }}</small><br>
                    <span id="change-amount">0</span>
                </div>
            </div>

            <div id="partial-payment-section" style="display: none;">
                <div class="partial-header mb-2">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-check"></i> {{ __('pos.initial_payment') ?? 'Initial Payment' }}</h6>
                </div>
                
                <div class="initial-payment-box p-2 border rounded bg-light mb-2">
                    <div class="row no-gutters mb-2">
                        <div class="col-6 pr-1">
                            <label class="small font-weight-bold mb-1">{{ __('general.amount') ?? 'Amount' }}</label>
                            <input type="text" class="form-control form-control-sm" id="initial-payment-amount" placeholder="0">
                        </div>
                        <div class="col-6 pl-1">
                            <label class="small font-weight-bold mb-1">{{ __('pos.payment_method') ?? 'Method' }}</label>
                            <select class="form-control form-control-sm" id="initial-payment-method">
                                <option value="cash">{{ __('pos.cash') ?? 'Cash' }}</option>
                                <option value="card">{{ __('pos.card') ?? 'Card' }}</option>
                                <option value="transfer">{{ __('pos.transfer') ?? 'Transfer' }}</option>
                                @can('Use Deposit')
                                <option value="deposit">{{ __('pos.deposit') ?? 'Deposit' }}</option>
                                @endcan
                            </select>
                        </div>
                    </div>
                    <div class="row no-gutters mb-2">
                        <div class="col-12">
                            <label class="small font-weight-bold mb-1">{{ __('pos.payment_date') ?? 'Payment Date' }}</label>
                            <input type="date" class="form-control form-control-sm" id="initial-payment-date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-12">
                            <label class="small font-weight-bold mb-1">{{ __('pos.note') ?? 'Note' }}</label>
                            <textarea class="form-control form-control-sm" id="initial-payment-note" rows="1" placeholder="{{ __('pos.note') ?? 'Note' }}..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="remaining-balance-box p-2 bg-white border rounded mb-2 d-flex justify-content-between align-items-center">
                    <span class="small font-weight-bold">{{ __('pos.remaining_balance') ?? 'Remaining Balance:' }}</span>
                    <span id="remaining-balance" class="font-weight-bold text-danger">0</span>
                </div>
            </div>
            
            <button class="checkout-btn" id="submit-btn" disabled>
                <i class="fas fa-check-circle"></i> {{ __('pos.complete') ?? 'Complete' }}
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
                    <i class="fas fa-check-circle"></i> {{ __('pos.transaction_complete') ?? 'Transaction Complete' }}
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
                    <i class="fas fa-list"></i> {{ __('pos.view_all_transactions') ?? 'View All Transactions' }}
                </a>
                <a href="#" class="btn btn-info" id="print-btn" target="_blank">
                    <i class="fas fa-print"></i> {{ __('pos.print_receipt') ?? 'Print Receipt' }}
                </a>
                <button type="button" class="btn btn-primary" id="new-transaction-btn">
                    <i class="fas fa-plus"></i> {{ __('pos.new_transaction') ?? 'New Transaction' }}
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
            <div class="col" style="min-width: 0;">
                <select class="form-control variant-select" style="width: 100%;">
                    <option value="">{{ __('pos.select_product') ?? 'Select product...' }}</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control qty-input text-center" value="1" min="1" placeholder="{{ __('general.qty') ?? 'Qty' }}">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control price-display" readonly placeholder="{{ __('product.price') ?? 'Price' }}">
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

