@extends('adminlte::page')

@section('title', 'Create Variant Type')

@section('content_header')
    <h1>Create Variant Type</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Variant Type Information</h3>
            </div>
            <form id="variant-type-form" action="{{ route('admin.variant-types.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               placeholder="e.g. Color, Size, Material" required>
                        <small class="text-muted">A unique name for this variant type</small>
                    </div>

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
                    <a href="{{ route('admin.variant-types.index') }}" class="btn btn-secondary">
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

        $('#variant-type-form').on('submit', function(e) {
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
