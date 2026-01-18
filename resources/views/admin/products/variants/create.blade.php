@extends('adminlte::page')

@section('title', 'Add Variant - ' . $product->name)

@section('content_header')
    <h1>Add Variant</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Product Info Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 50px;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-box"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h6 class="mb-0">{{ $product->name }}</h6>
                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variant Form Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Variant Information</h3>
            </div>
            <form id="variant-form" action="{{ route('admin.products.variants.store', $product->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku', $product->sku . '-') }}" placeholder="Enter variant SKU" required>
                                @error('sku')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Variant Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="e.g. Red - Large">
                                <small class="text-muted">Leave empty to auto-generate from attributes</small>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="price_display" id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $product->base_price) }}" required>
                                    <input type="hidden" name="price" id="price_hidden">
                                </div>
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cost">Cost Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="cost_display" id="cost" 
                                           class="form-control @error('cost') is-invalid @enderror" 
                                           value="{{ old('cost', $product->base_cost) }}">
                                    <input type="hidden" name="cost" id="cost_hidden">
                                </div>
                                @error('cost')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Initial Stock <span class="text-danger">*</span></label>
                                <input type="text" name="stock_display" id="stock" 
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       value="{{ old('stock', 0) }}" required>
                                <input type="hidden" name="stock" id="stock_hidden">
                                @error('stock')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_stock">Minimum Stock Alert</label>
                                <input type="text" name="min_stock_display" id="min_stock" 
                                       class="form-control @error('min_stock') is-invalid @enderror" 
                                       value="{{ old('min_stock', 5) }}">
                                <input type="hidden" name="min_stock" id="min_stock_hidden">
                                @error('min_stock')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Variant Attributes -->
                    @if($variantTypes->count() > 0)
                    <hr>
                    <h5>Variant Attributes</h5>
                    <div id="variant-attributes-error"></div>
                    <div class="row" id="variant-attributes-row">
                        @foreach($variantTypes as $type)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $type->name }}</label>
                                <select name="variant_values[]" class="form-control">
                                    <option value="">-- Select {{ $type->name }} --</option>
                                    @foreach($type->values as $value)
                                        <option value="{{ $value->id }}" {{ in_array($value->id, old('variant_values', [])) ? 'selected' : '' }}>
                                            {{ $value->value }}
                                            @if($value->color_code)
                                                <span style="background-color: {{ $value->color_code }};">■</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Variant
                    </button>
                    <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function() {
        // Initialize AutoNumeric for price inputs (Indonesian Rupiah format)
        var autoNumericOptions = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
        };
        
        // Integer format for stock fields
        var integerOptions = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
        };
        
        var priceAN = new AutoNumeric('#price', autoNumericOptions);
        var costAN = new AutoNumeric('#cost', autoNumericOptions);
        var stockAN = new AutoNumeric('#stock', integerOptions);
        var minStockAN = new AutoNumeric('#min_stock', integerOptions);
        
        // AJAX Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Form submission via AJAX
        $('#variant-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnHtml = submitBtn.html();
            
            // Clear previous errors
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback, .text-danger').not('.persistent-error').remove();
            $('#variant-attributes-error').empty();
            
            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            // Set hidden fields with raw numeric values before submission
            $('#price_hidden').val(priceAN.getNumber());
            $('#cost_hidden').val(costAN.getNumber() || 0);
            $('#stock_hidden').val(stockAN.getNumber() || 0);
            $('#min_stock_hidden').val(minStockAN.getNumber() || 5);
            
            // Use FormData for submission
            var formData = new FormData(this);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Redirect after short delay for toastr to show
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    
                    if (xhr.status === 422) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            var input;
                            
                            // Handle non-indexed variant_values error (duplicate combination)
                            if (field === 'variant_values') {
                                $('#variant-attributes-error').html('<div class="alert alert-danger">' + messages[0] + '</div>');
                                return true; // continue to next iteration
                            }
                            
                            // Check if this is an indexed array field (e.g., variant_values.0)
                            var arrayMatch = field.match(/^(.+)\.(\d+)$/);
                            if (arrayMatch) {
                                var baseField = arrayMatch[1];
                                var index = parseInt(arrayMatch[2]);
                                // Find the specific element by index
                                input = form.find('[name="' + baseField + '[]"]').eq(index);
                            } else {
                                input = form.find('[name="' + field + '"]');
                                if (input.length === 0) {
                                    // Try display field name
                                    input = form.find('[name="' + field + '_display"]');
                                }
                            }
                            
                            if (input.length > 0) {
                                input.addClass('is-invalid');
                                
                                // Add error message
                                if (input.closest('.input-group').length) {
                                    input.closest('.input-group').after('<span class="text-danger">' + messages[0] + '</span>');
                                } else {
                                    input.after('<span class="invalid-feedback d-block">' + messages[0] + '</span>');
                                }
                            }
                        });
                        toastr.error('Please fix the validation errors.');
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@stop
