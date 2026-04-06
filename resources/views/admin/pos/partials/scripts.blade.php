<script>
$(function() {
    let rowCounter = 0;
    let selectedPaymentMethod = 'cash';
    let selectedPaymentMode = 'full';
    let selectedCustomerId = null;
    let currentCustomerDiscount = 0;

    // Check if partial payment is allowed (only for registered customers)
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
         

         // Apply current customer discount if any
         if (currentCustomerDiscount > 0) {
             $row.find('.discount-input').val(currentCustomerDiscount);
         }
         
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
    const initialPaymentAmountAN = new AutoNumeric('#initial-payment-amount', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        minimumValue: '0',
        modifyValueOnWheel: false
    });
    
    // Initialize Customer Select2
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
                        let groupName = item.group_name || (item.customer_group ? item.customer_group.name : '');
                        let text = item.name + (item.phone ? ' (' + item.phone + ')' : '');
                        if (groupName && groupName !== '-') {
                             text += ' [' + groupName + ']';
                        }
                        return {
                            id: item.id,
                            text: text,
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
        
        // Apply customer group discount if available
        if (customer.customer_group && customer.customer_group.percentage_discount > 0) {
            currentCustomerDiscount = parseFloat(customer.customer_group.percentage_discount);
            toastr.info(`Applied ${currentCustomerDiscount}% discount from ${customer.customer_group.name} membership.`);
        } else {
            currentCustomerDiscount = 0;
        }

        // Update all existing items with the new discount
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                $(this).find('.discount-input').val(currentCustomerDiscount);
                updateRowSubtotal($(this));
            }
        });
        updateSummary();
        updatePaymentModeAbility();
    });

    $('#customer-select').on('select2:clear', function(e) {
        selectedCustomerId = null;
        currentCustomerDiscount = 0;
        $('#customer-name').val('').prop('readonly', false);
        $('#customer-phone').val('').prop('readonly', false);
        
        // Reset discount on all items
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                $(this).find('.discount-input').val(0);
                updateRowSubtotal($(this));
            }
        });
        updateSummary();
        updatePaymentModeAbility();
        toastr.info('Customer removed. Discounts reset.');
    });

    // Add first empty row on page load
    addNewRow();
    updatePaymentModeAbility();

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

        // Init AutoNumeric for cut input if row created empty (though populateRow does it too, but for manual add it's safe to do here or check)
        // Actually populateRow is called ONLY when adding product. Empty row doesn't have it initialized?
        // Wait, empty row added via addNewRow has the input, needs AutoNumeric init.
        new AutoNumeric($row.find('.cut-input').get(0), {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
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
        
        // Apply current customer discount if any
        if (currentCustomerDiscount > 0) {
            $row.find('.discount-input').val(currentCustomerDiscount);
        }
        
        updateRowSubtotal($row);
        updateSummary();
    });

    // Handle variant clear
    $(document).on('select2:clear', '.variant-select', function() {
        const $row = $(this).closest('.item-row');
        $row.removeData('variant-id price stock');
        $row.find('.price-display').val('');
        $row.find('.item-subtotal').text('0');
        // Clear cut and discount inputs
        $row.find('.discount-input').val(0);
        AutoNumeric.getAutoNumericElement($row.find('.cut-input').get(0)).set(0);

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
            AutoNumeric.getAutoNumericElement($row.find('.cut-input').get(0)).set(0);
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
        
        // Get cut amount (parse from AutoNumeric format: 1.000 -> 1000)
        let cutAmountStr = $row.find('.cut-input').val() || '0';
        let cutAmount = parseFloat(cutAmountStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

        const lineTotal = price * qty;
        const discountAmount = lineTotal * (discountPercent / 100);
        const subtotal = lineTotal - discountAmount - cutAmount;
        
        // Prevent negative subtotal
        const finalSubtotal = subtotal < 0 ? 0 : subtotal;
        
        $row.find('.item-subtotal').text(formatNumber(finalSubtotal));
    }

    // Handle discount input change
    $(document).on('input change', '.discount-input', function() {
        const $row = $(this).closest('.item-row');
        updateRowSubtotal($row);
        updateSummary();
    });

    // Handle cut input change
    $(document).on('input change keyup', '.cut-input', function() {
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
                
                let cutAmountStr = $(this).find('.cut-input').val() || '0';
                let cutAmount = parseFloat(cutAmountStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

                const lineTotal = price * qty;
                const discountAmount = lineTotal * (discountPercent / 100);
                
                // Calculate item subtotal (gross - discount - cut)
                // But realistically, cut shouldn't exceed subtotal? 
                // Let's assume valid inputs or clamp:
                let actualDeduction = discountAmount + cutAmount;
                if (actualDeduction > lineTotal) actualDeduction = lineTotal;

                // We want summary subtotal to be GROSS total? Or total after line discounts?
                // Usually Subtotal is Gross, Discount is total deductions, Total is Net.
                // let's stick to: Subtotal = sum(price * qty)
                // Total Discount = sum(percent_disc + cut)
                
                subtotal += lineTotal;
                totalDiscount += actualDeduction;
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
        
        let canSubmit = hasItems;
        if (selectedPaymentMode === 'partial') {
            const balance = getGrandTotal() - getPartialTotal();
            if (balance < 0) {
                canSubmit = false;
            }
        }

        $('#submit-btn').prop('disabled', !canSubmit);

        updateChangeDisplay();
        updateRemainingBalance();
    }

    function getPartialTotal() {
        return initialPaymentAmountAN.getNumber() || 0;
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

    // Payment Mode Toggle
    $('input[name="payment_mode"]').on('change', function() {
        selectedPaymentMode = $(this).val();
        if (selectedPaymentMode === 'partial') {
            $('#full-payment-section').hide();
            $('#partial-payment-section').show();
        } else {
            $('#partial-payment-section').hide();
            $('#full-payment-section').show();
        }
        updateSummary();
    });

    // Update Remaining Balance on input
    $('#initial-payment-amount').on('input', function() {
        updateRemainingBalance();
        updateSummary();
    });

    function updateRemainingBalance() {
        let totalAllocated = getPartialTotal();
        const grandTotal = getGrandTotal();
        const balance = grandTotal - totalAllocated;
        
        $('#remaining-balance').text(formatNumber(balance));
        
        if (balance < 0) {
            $('#remaining-balance').removeClass('text-danger').addClass('text-danger');
            $('#remaining-balance').parent().addClass('bg-danger text-white').removeClass('bg-white text-dark');
        } else {
            $('#remaining-balance').removeClass('text-danger').addClass('text-danger');
            $('#remaining-balance').parent().addClass('bg-white text-dark').removeClass('bg-danger text-white');
        }
    }

    function getGrandTotal() {
        let subtotal = 0;
        let totalDiscount = 0;
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                const price = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                let cutAmountStr = $(this).find('.cut-input').val() || '0';
                let cutAmount = parseFloat(cutAmountStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

                const lineTotal = price * qty;
                let actualDeduction = (lineTotal * (discountPercent / 100)) + cutAmount;
                if (actualDeduction > lineTotal) actualDeduction = lineTotal;
                
                subtotal += lineTotal;
                totalDiscount += actualDeduction;
            }
        });
        return subtotal - totalDiscount;
    }

    // Update change display
    function updateChangeDisplay() {
        // Recalculate total here or reuse from global/DOM? 
        // Safer to recalculate to match updateSummary logic
        let subtotal = 0;
        let totalDiscount = 0;
        $('.item-row').each(function() {
            if ($(this).data('variant-id')) {
                const price = parseFloat($(this).data('price')) || 0;
                const qty = parseInt($(this).find('.qty-input').val()) || 0;
                const discountPercent = parseFloat($(this).find('.discount-input').val()) || 0;
                let cutAmountStr = $(this).find('.cut-input').val() || '0';
                let cutAmount = parseFloat(cutAmountStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

                const lineTotal = price * qty;
                let actualDeduction = (lineTotal * (discountPercent / 100)) + cutAmount;
                if (actualDeduction > lineTotal) actualDeduction = lineTotal;
                
                subtotal += lineTotal;
                totalDiscount += actualDeduction;
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
                
                let cutAmountStr = $(this).find('.cut-input').val() || '0';
                let cutAmount = parseFloat(cutAmountStr.replace(/\./g, '').replace(/,/g, '.')) || 0;

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
                    discount: discountAmount,
                    cut_amount: cutAmount
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
            const lineTotal = item.price * item.quantity;
            subtotal += lineTotal;
            // logic same as updateSummary
            let deduction = item.discount + item.cut_amount;
            if (deduction > lineTotal) deduction = lineTotal;
            totalDiscount += deduction;
        });
        const total = subtotal - totalDiscount;
        const amountPaid = amountPaidAN.getNumber() || 0;

        if (selectedPaymentMode === 'full' && amountPaid < total) {
            toastr.error('Amount paid is insufficient!');
            return;
        }

        // Collect payments
        const payments = [];
        if (selectedPaymentMode === 'full') {
            payments.push({
                amount: amountPaid,
                payment_method: selectedPaymentMethod,
                payment_date: '{{ date("Y-m-d") }}',
                status: 'paid',
                notes: ''
            });
        } else {
            const initialAmount = initialPaymentAmountAN.getNumber() || 0;
            if (initialAmount <= 0) {
                toastr.error('Please enter an initial payment amount!');
                return;
            }
            if (initialAmount > total) {
                toastr.error('Initial payment cannot exceed the total amount!');
                return;
            }

            payments.push({
                amount: initialAmount,
                payment_method: $('#initial-payment-method').val(),
                payment_date: $('#initial-payment-date').val(),
                notes: $('#initial-payment-note').val(),
                status: 'paid'
            });
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
                payment_mode: selectedPaymentMode,
                payment_method: selectedPaymentMethod, // Fallback for full payment
                amount_paid: amountPaid, // Fallback for full payment
                payments: payments,
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
                // Keep disabled until successful reset or manual re-enable
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

    // Reset Transaction Form
    function resetTransactionForm() {
        // Clear items
        $('#items-container').empty();
        rowCounter = 0;
        addNewRow();

        // Clear customer
        selectedCustomerId = null;
        $('#customer-select').val(null).trigger('change');
        $('#customer-name').val('').prop('readonly', false);
        $('#customer-phone').val('').prop('readonly', false);
        $('#customer-panel').addClass('show');

        // Clear notes
        $('#notes').val('');

        // Reset payment
        $('.payment-method').removeClass('active');
        $('.payment-method[data-method="cash"]').addClass('active');
        selectedPaymentMethod = 'cash';
        
        // Clear amount paid
        amountPaidAN.set(0);
        $('#amount-paid').val('');

        // Reset Partial Payments
        selectedPaymentMode = 'full';
        $('input[name="payment_mode"][value="full"]').parent().addClass('active').siblings().removeClass('active');
        $('input[name="payment_mode"][value="full"]').prop('checked', true);
        $('#partial-payment-section').hide();
        $('#full-payment-section').show();
        
        initialPaymentAmountAN.set(0);
        $('#initial-payment-method').val('cash');
        $('#initial-payment-date').val('{{ date("Y-m-d") }}');
        $('#initial-payment-note').val('');
        
        // Reset summary and change display
        updateSummary();
        
        // Focus barcode
        setTimeout(() => {
            $('#barcode-input').focus();
        }, 500);
        
        toastr.info('Ready for new transaction');
    }

    // Handle modal close event (triggers reset)
    $('#successModal').on('hidden.bs.modal', function () {
        resetTransactionForm();
    });

    // New transaction button (just closes modal now)
    $('#new-transaction-btn').on('click', function() {
        $('#successModal').modal('hide');
    });

    // Format number helper
    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    }
});
</script>
