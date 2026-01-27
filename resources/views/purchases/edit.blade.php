@extends('adminlte::page')

@section('title', 'Edit Purchase')

@section('content_header')
    <h1>Edit Purchase</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @php
                $hasReceipts = $purchase->details->where('quantity_received', '>', 0)->isNotEmpty();
                $isEditable = !$hasReceipts && $purchase->status !== 'completed' && $purchase->status !== 'cancelled';
            @endphp

            <form action="{{ route('admin.purchases.update', $purchase->id) }}" method="POST" id="purchase-form">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" {{ $isEditable ? '' : 'disabled' }}>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @if(!$isEditable)
                                <input type="hidden" name="supplier_id" value="{{ $purchase->supplier_id }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $purchase->date->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="{{ $purchase->reference_number }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Purchase Items</label>
                    
                    @if(!$isEditable)
                        <div class="alert alert-warning py-1 px-3 mb-2">
                            <small><i class="fas fa-info-circle"></i> Items cannot be edited because receipts have already been processed.</small>
                        </div>
                    @endif

                    <table class="table table-bordered" id="items-table">
                        <thead>
                            <tr>
                                <th width="40%">Product</th>
                                <th width="20%">Quantity</th>
                                <th width="20%">Unit Cost</th>
                                <th width="15%">Subtotal</th>
                                @if($isEditable)
                                    <th width="5%">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right font-weight-bold">Total Amount:</td>
                                <td colspan="{{ $isEditable ? 2 : 1 }}" class="font-weight-bold" id="total_amount">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    @if($isEditable)
                        <button type="button" class="btn btn-info btn-sm" id="add-item"><i class="fas fa-plus"></i> Add Item</button>
                    @endif
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $purchase->notes }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Purchase</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        @section('plugins.AutoNumeric', true)

        $(document).ready(function() {
            let itemIndex = 0;
            const isEditable = {{ $isEditable ? 'true' : 'false' }};
            const existingItems = @json($purchase->details);

            const autoNumericOptions = {
                currencySymbol: '', 
                decimalCharacter: ',',
                digitGroupSeparator: '.',
                decimalPlaces: 0,
                unformatOnSubmit: true,
                minimumValue: 0
            };

            function initAutoNumeric(element) {
                if (AutoNumeric.getAutoNumericElement(element)) {
                    return;
                }
                new AutoNumeric(element, autoNumericOptions);
            }
            
            function formatMoney(amount) {
                return parseFloat(amount).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function addItemRow(data = null) {
                let variantId = '';
                let variantText = 'Search Product...';
                let quantity = 1;
                let cost = '';
                let detailId = '';

                if (data) {
                    variantId = data.product_variant_id;
                    variantText = data.product_variant.full_name || data.product_variant.name; // Adjust based on model accessor
                    quantity = data.quantity;
                    cost = data.cost;
                    detailId = data.id;
                    
                    // Note: full_name might need to be constructed if not available in json
                    if(data.product_variant && data.product_variant.product) {
                         variantText = data.product_variant.product.name + ' - ' + data.product_variant.name;
                    }
                }

                let row = `
                    <tr id="row-${itemIndex}">
                        <td>
                            ${detailId ? `<input type="hidden" name="items[${itemIndex}][id]" value="${detailId}">` : ''}
                            <select name="items[${itemIndex}][variant_id]" class="form-control product-select" ${!isEditable ? 'disabled' : 'required'}>
                                ${variantId ? `<option value="${variantId}" selected>${variantText}</option>` : '<option value="">Search Product...</option>'}
                            </select>
                            ${!isEditable ? `<input type="hidden" name="items[${itemIndex}][variant_id]" value="${variantId}">` : ''}
                        </td>
                        <td>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="${quantity}" ${!isEditable ? 'readonly' : 'required'}>
                        </td>
                        <td>
                            <input type="text" name="items[${itemIndex}][cost]" class="form-control cost-input" value="${cost}" ${!isEditable ? 'readonly' : 'required'}>
                        </td>
                        <td class="subtotal-display align-middle font-weight-bold">0.00</td>
                        ${isEditable ? `
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-id="${itemIndex}"><i class="fas fa-trash"></i></button>
                        </td>` : ''}
                    </tr>
                `;
                $('#items-table tbody').append(row);
                
                // Initialize Select2 (only if editable, or to show selected value)
                initializeSelect2($(`#row-${itemIndex} .product-select`));
                
                // Initialize AutoNumeric
                const costEl = $(`#row-${itemIndex} .cost-input`)[0];
                initAutoNumeric(costEl);
                if (cost) {
                    AutoNumeric.getAutoNumericElement(costEl).set(cost);
                }
                
                calculateRowTotal($(`#row-${itemIndex}`));
                
                itemIndex++;
            }

            function initializeSelect2(element) {
                element.select2({
                    theme: 'bootstrap4',
                    disabled: !isEditable,
                    ajax: {
                        url: '{{ route("admin.purchases.search-products") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { term: params.term };
                        },
                        processResults: function (data) {
                            return { results: data };
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
                            return false;
                        }
                    });

                    if (isDuplicate) {
                        toastr.warning('This product has already been added.');
                        $(this).val(null).trigger('change');
                        return;
                    }
                    
                    const costInput = row.find('.cost-input')[0];
                    if (AutoNumeric.getAutoNumericElement(costInput)) {
                        AutoNumeric.getAutoNumericElement(costInput).set(data.cost);
                    } else {
                        $(costInput).val(data.cost);
                    }
                    
                    calculateRowTotal(row);
                });
            }

            function calculateRowTotal(row) {
                const qty = parseFloat(row.find('.quantity-input').val()) || 0;
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

            $('#add-item').click(function() {
                addItemRow();
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            $(document).on('input', '.quantity-input', function() {
                calculateRowTotal($(this).closest('tr'));
            });
            
            $(document).on('keyup', '.cost-input', function() {
                 calculateRowTotal($(this).closest('tr'));
            });

            // Populate existing items
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItemRow(item);
                });
            } else if (isEditable) {
                addItemRow();
            }

            // AJAX Form Submission
            $('#purchase-form').on('submit', function(e) {
                e.preventDefault();
                
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                let formData = new FormData(this);
                let serializedData = $(this).serializeArray();
                
                serializedData.forEach(function(item) {
                   if (item.name.endsWith('[cost]')) {
                       const input = $(`[name="${item.name}"]`)[0];
                       if (input && AutoNumeric.getAutoNumericElement(input)) {
                           item.value = AutoNumeric.getAutoNumericElement(input).getNumber();
                       }
                   } 
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST', // Form handles PUT via _method
                    data: $.param(serializedData),
                    headers: { 'Accept': 'application/json' },
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
                                    let inputName = key;
                                    if (key.includes('.')) {
                                        const parts = key.split('.');
                                        inputName = `${parts[0]}[${parts[1]}][${parts[2]}]`;
                                    }
                                    const input = $(`[name="${inputName}"]`);
                                    input.addClass('is-invalid');
                                    input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                                    if (key === 'items') toastr.error(errors[key][0]);
                                });
                            } else {
                                toastr.error(xhr.responseJSON.message || 'Validation error occurred');
                            }
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@stop
