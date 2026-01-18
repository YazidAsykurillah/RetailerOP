@extends('adminlte::page')

@section('title', 'Stock In')

@section('content_header')
    <h1>Stock In</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-arrow-down"></i> Add Stock
                </h3>
            </div>
            <form id="stock-in-form">
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
                                <input type="text" class="form-control" id="current_stock" readonly value="{{ $selectedVariant ? $selectedVariant->stock : '-' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantity to Add <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>New Stock After</label>
                        <input type="text" class="form-control bg-success text-white font-weight-bold" id="new_stock" readonly value="-">
                    </div>

                    <div class="form-group">
                        <label for="reference">Reference (Invoice/PO Number)</label>
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g., PO-2024-001">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success float-right" id="submit-btn">
                        <i class="fas fa-check"></i> Confirm Stock In
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
            $('#current_stock').val(selected.stock);
            updateNewStock();
        } else {
            $('#current_stock').val('-');
            $('#new_stock').val('-');
        }
    });

    // Update new stock calculation
    $('#quantity').on('input', function() {
        updateNewStock();
    });

    function updateNewStock() {
        var currentStock = parseInt($('#current_stock').val()) || 0;
        var quantity = parseInt($('#quantity').val()) || 0;
        if ($('#current_stock').val() !== '-' && quantity > 0) {
            $('#new_stock').val(currentStock + quantity);
        } else {
            $('#new_stock').val('-');
        }
    }

    // AJAX form submission
    $('#stock-in-form').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#submit-btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: '{{ route("admin.stock.in.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                toastr.success(response.message);
                setTimeout(function() {
                    window.location.href = '{{ route("admin.stock.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Confirm Stock In');
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
