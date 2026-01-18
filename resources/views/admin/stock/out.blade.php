@extends('adminlte::page')

@section('title', 'Stock Out')

@section('content_header')
    <h1>Stock Out</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-arrow-up"></i> Remove Stock
                </h3>
            </div>
            <form id="stock-out-form">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="product_variant_id">Product / Variant <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="product_variant_id" name="product_variant_id" style="width: 100%;" required>
                            @if($selectedVariant)
                                <option value="{{ $selectedVariant->id }}" selected>
                                    {{ $selectedVariant->product->name ?? '' }} - {{ $selectedVariant->name ?: 'Default' }} 
                                    (SKU: {{ $selectedVariant->sku }}) [Stock: {{ $selectedVariant->stock }}]
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Current Stock</label>
                                <input type="text" class="form-control font-weight-bold" id="current_stock" readonly value="{{ $selectedVariant ? $selectedVariant->stock : '-' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantity to Remove <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                <small class="text-muted">Maximum: <span id="max-qty">-</span></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remaining Stock After</label>
                        <input type="text" class="form-control font-weight-bold" id="remaining_stock" readonly value="-">
                    </div>

                    <div id="stock-warning" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="warning-message">Cannot remove more stock than available!</span>
                    </div>

                    <div class="form-group">
                        <label for="reference">Reference (Invoice/Reason)</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g., SO-2024-001 or Damaged">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Reason for stock removal..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning float-right" id="submit-btn">
                        <i class="fas fa-check"></i> Confirm Stock Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(function() {
    var currentStock = {{ $selectedVariant ? $selectedVariant->stock : 0 }};

    // Initialize Select2 with AJAX search
    $('#product_variant_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Search product or variant...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.stock.search-products") }}',
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
        minimumInputLength: 1
    });

    // Update current stock when variant is selected
    $('#product_variant_id').on('change', function() {
        var selected = $(this).select2('data')[0];
        if (selected && selected.stock !== undefined) {
            currentStock = selected.stock;
            $('#current_stock').val(selected.stock);
            $('#max-qty').text(selected.stock);
            $('#quantity').attr('max', selected.stock);
            updateRemainingStock();
        } else {
            currentStock = 0;
            $('#current_stock').val('-');
            $('#max-qty').text('-');
            $('#remaining_stock').val('-');
        }
    });

    // Update remaining stock calculation
    $('#quantity').on('input', function() {
        updateRemainingStock();
    });

    function updateRemainingStock() {
        var quantity = parseInt($('#quantity').val()) || 0;
        
        if ($('#current_stock').val() !== '-' && quantity > 0) {
            var remaining = currentStock - quantity;
            $('#remaining_stock').val(remaining);
            
            if (remaining < 0) {
                $('#remaining_stock').removeClass('bg-success text-white').addClass('bg-danger text-white');
                $('#stock-warning').show();
                $('#submit-btn').prop('disabled', true);
            } else {
                $('#remaining_stock').removeClass('bg-danger').addClass('bg-success text-white');
                $('#stock-warning').hide();
                $('#submit-btn').prop('disabled', false);
            }
        } else {
            $('#remaining_stock').val('-').removeClass('bg-success bg-danger text-white');
            $('#stock-warning').hide();
        }
    }

    // Set initial max-qty
    @if($selectedVariant)
        $('#max-qty').text({{ $selectedVariant->stock }});
        $('#quantity').attr('max', {{ $selectedVariant->stock }});
    @endif

    // AJAX form submission
    $('#stock-out-form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#submit-btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '{{ route("admin.stock.out.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.href = '{{ route("admin.stock.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Confirm Stock Out');
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>
@stop
