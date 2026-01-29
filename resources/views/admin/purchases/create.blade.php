@extends('adminlte::page')

@section('title', 'Create Purchase')

@section('content_header')
    <h1>Create Purchase</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchase-form">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required>
                                <option value="">Select Supplier</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="PO-{{ date('YmdHis') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Purchase Items</label>
                    <table class="table table-bordered" id="items-table">
                        <thead>
                            <tr>
                                <th width="40%">Product</th>
                                <th width="20%">Quantity</th>
                                <th width="20%">Unit Cost</th>
                                <th width="15%">Subtotal</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Total Amount:</td>
                                <td colspan="2" class="font-weight-bold" id="total_amount">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="button" class="btn btn-info btn-sm" id="add-item"><i class="fas fa-plus"></i> Add Item</button>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Purchase</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        @section('plugins.AutoNumeric', true)
        @section('plugins.Select2', true)

        $(document).ready(function() {
            // Initialize Select2 for Supplier
            $('#supplier_id').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Supplier',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.suppliers.search") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            let itemIndex = 0;
            const autoNumericOptions = {
                currencySymbol: '', // Removed as per request
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                decimalPlaces: 0, // Assuming no decimals for IDR usually, or match product page
                unformatOnSubmit: true,
                minimumValue: 0
            };

            function initAutoNumeric(element) {
                if (AutoNumeric.getAutoNumericElement(element)) {
                    return;
                }
                new AutoNumeric(element, autoNumericOptions);
            }
            
            // Helper to format numbers for display text (subtotal, total)
            function formatMoney(amount) {
                // Format: 1.000.000 (Indonesian standard)
                return parseFloat(amount).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function addItemRow() {
                const row = `
                    <tr id="row-${itemIndex}">
                        <td>
                            <select name="items[${itemIndex}][variant_id]" class="form-control product-select" required>
                                <option value="">Search Product...</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="items[${itemIndex}][cost]" class="form-control cost-input" required>
                        </td>
                        <td class="subtotal-display align-middle font-weight-bold"> 0.00</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-id="${itemIndex}"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#items-table tbody').append(row);
                
                // Initialize Select2
                initializeSelect2($(`#row-${itemIndex} .product-select`));
                
                // Initialize AutoNumeric
                initAutoNumeric($(`#row-${itemIndex} .cost-input`)[0]);
                
                itemIndex++;
            }

            function initializeSelect2(element) {
                element.select2({
                    theme: 'bootstrap4',
                    ajax: {
                        url: '{{ route("admin.purchases.search-products") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                term: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Search for a product...',
                    minimumInputLength: 1
                });

                element.on('select2:select', function (e) {
                    const data = e.params.data;
                    const row = $(this).closest('tr');
                    const currentId = data.id;
                    
                    // Check for duplicates
                    let isDuplicate = false;
                    $('#items-table .product-select').not($(this)).each(function() {
                        if ($(this).val() == currentId) {
                            isDuplicate = true;
                            return false; // break loop
                        }
                    });

                    if (isDuplicate) {
                        toastr.warning('This product has already been added.');
                        $(this).val(null).trigger('change');
                        return;
                    }
                    
                    // Set AutoNumeric value
                    const costInput = row.find('.cost-input')[0];
                    if (AutoNumeric.getAutoNumericElement(costInput)) {
                        AutoNumeric.getAutoNumericElement(costInput).set(data.cost);
                    } else {
                        // Fallback
                        $(costInput).val(data.cost);
                    }
                    
                    calculateRowTotal(row);
                });
            }

            function calculateRowTotal(row) {
                const qty = parseFloat(row.find('.quantity-input').val()) || 0;
                
                // Get raw value from AutoNumeric
                let cost = 0;
                const costElement = row.find('.cost-input')[0];
                if (AutoNumeric.getAutoNumericElement(costElement)) {
                    cost = AutoNumeric.getAutoNumericElement(costElement).getNumber();
                } else {
                    cost = parseFloat($(costElement).val()) || 0;
                }

                const subtotal = qty * cost;
                row.find('.subtotal-display').text(formatMoney(subtotal));
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                $('#items-table tbody tr').each(function() {
                    const qty = parseFloat($(this).find('.quantity-input').val()) || 0;
                    
                    let cost = 0;
                    const costInput = $(this).find('.cost-input')[0];
                    if (AutoNumeric.getAutoNumericElement(costInput)) {
                        cost = AutoNumeric.getAutoNumericElement(costInput).getNumber();
                    } else {
                        cost = parseFloat($(costInput).val()) || 0;
                    }
                    
                    total += qty * cost;
                });
                $('#total_amount').text(formatMoney(total));
            }

            // Events
            $('#add-item').click(addItemRow);

            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            $(document).on('input', '.quantity-input', function() {
                calculateRowTotal($(this).closest('tr'));
            });
            
            // Listen to AutoNumeric events
            $(document).on('keyup', '.cost-input', function() {
                 calculateRowTotal($(this).closest('tr'));
            });

            // Add initial row
            addItemRow();

            // AJAX Form Submission
            $('#purchase-form').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                // Create a FormData object properly handling AutoNumeric values
                let formData = new FormData(this);
                
                // Manually fix the cost values in FormData
                // This is a bit tricky with FormData, so simpler approach is to use serializeArray and modify
                let serializedData = $(this).serializeArray();
                
                // Iterate through serialized data and fix costs
                serializedData.forEach(function(item) {
                   if (item.name.endsWith('[cost]')) {
                       // Find the input element to get the raw value
                       const input = $(`[name="${item.name}"]`)[0];
                       if (input && AutoNumeric.getAutoNumericElement(input)) {
                           item.value = AutoNumeric.getAutoNumericElement(input).getNumber();
                       }
                   } 
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $.param(serializedData), // Convert back to string
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text(originalText);
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors) {
                                Object.keys(errors).forEach(function(key) {
                                    // Handle array keys like items.0.quantity
                                    let inputName = key;
                                    if (key.includes('.')) {
                                        const parts = key.split('.');
                                        // items.0.quantity -> items[0][quantity]
                                        inputName = `${parts[0]}[${parts[1]}][${parts[2]}]`;
                                    }
                                    
                                    const input = $(`[name="${inputName}"]`);
                                    input.addClass('is-invalid');
                                    input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                                    
                                    // Also show toastr for general error
                                    if (key === 'items') {
                                        toastr.error(errors[key][0]);
                                    }
                                });
                            } else {
                                toastr.error(xhr.responseJSON.message || 'Validation error occurred');
                            }
                        } else {
                            toastr.error('An error occurred. Please try again.');
                            console.error(xhr);
                        }
                    }
                });
            });
        });
    </script>
@stop
