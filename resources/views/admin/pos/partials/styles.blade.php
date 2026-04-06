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
    /* Adjust for no-sidebar layout */
    body:not(.sidebar-mini-md) .fixed-bottom-bar,
    body.layout-top-nav .fixed-bottom-bar {
        left: 0;
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

    /* Disabled style for radio button group labels */
    .btn-group-toggle label.disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
        pointer-events: none;
    }
</style>
