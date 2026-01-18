@extends('adminlte::page')

@section('title', 'Add Value - ' . $variantType->name)

@section('content_header')
    <h1>Add Value</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-6">
        <!-- Parent Type Info Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">{{ $variantType->name }}</h6>
                        <small class="text-muted">Adding new value to this type</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Value Information</h3>
            </div>
            <form id="value-form" action="{{ route('admin.variant-types.values.store', $variantType->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="value">Value <span class="text-danger">*</span></label>
                        <input type="text" name="value" id="value" class="form-control" 
                               placeholder="e.g. Red, XL, Cotton" required>
                    </div>

                    @if(strtolower($variantType->name) === 'color' || strtolower($variantType->slug) === 'color')
                    <div class="form-group">
                        <label for="color_code">Color Code</label>
                        <div class="input-group">
                            <input type="color" id="color_picker" class="form-control" style="width: 50px; padding: 2px;">
                            <input type="text" name="color_code" id="color_code" class="form-control" 
                                   placeholder="#FF0000">
                        </div>
                        <small class="text-muted">Select or enter a color code for color swatches</small>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="color_code">Color Code (Optional)</label>
                        <input type="text" name="color_code" id="color_code" class="form-control" 
                               placeholder="e.g. #FF0000">
                        <small class="text-muted">Optional color code for visual representation</small>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control" 
                               value="0" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <a href="{{ route('admin.variant-types.values.index', $variantType->id) }}" class="btn btn-secondary">
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sync color picker with text input
        $('#color_picker').on('input', function() {
            $('#color_code').val($(this).val());
        });
        $('#color_code').on('input', function() {
            var val = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                $('#color_picker').val(val);
            }
        });

        $('#value-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnHtml = submitBtn.html();
            
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback, .text-danger').remove();
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            var input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<span class="invalid-feedback d-block">' + messages[0] + '</span>');
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
