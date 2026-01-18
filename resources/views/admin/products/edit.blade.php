@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
    <h1>Edit Product</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
            </div>
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" 
                                       value="{{ old('sku', $product->sku) }}" placeholder="Enter SKU" required>
                                @error('sku')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $product->name) }}" placeholder="Enter product name" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select name="category_id" id="category_id" class="form-control select2 @error('category_id') is-invalid @enderror" style="width: 100%;">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brand_id">Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control select2 @error('brand_id') is-invalid @enderror" style="width: 100%;">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="base_price">Selling Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="base_price_display" id="base_price" 
                                           class="form-control @error('base_price') is-invalid @enderror" 
                                           value="{{ old('base_price', $product->base_price) }}" required>
                                    <input type="hidden" name="base_price" id="base_price_hidden">
                                </div>
                                @error('base_price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="base_cost">Cost Price</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="base_cost_display" id="base_cost" 
                                           class="form-control @error('base_cost') is-invalid @enderror" 
                                           value="{{ old('base_cost', $product->base_cost) }}">
                                    <input type="hidden" name="base_cost" id="base_cost_hidden">
                                </div>
                                @error('base_cost')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Short Description</label>
                        <textarea name="short_description" id="short_description" class="form-control @error('short_description') is-invalid @enderror" 
                                  rows="2" placeholder="Brief product description (max 500 characters)">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Full Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" placeholder="Detailed product description">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Existing Images -->
                    @if($product->images->count() > 0)
                    <div class="form-group">
                        <label>Current Images</label>
                        <div class="row">
                            @foreach($product->images as $image)
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <div class="card">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" 
                                                   class="custom-control-input" id="delete_{{ $image->id }}">
                                            <label class="custom-control-label text-danger" for="delete_{{ $image->id }}">
                                                <small>Delete</small>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio mt-1">
                                            <input type="radio" name="primary_image" value="{{ $image->id }}" 
                                                   class="custom-control-input" id="primary_{{ $image->id }}"
                                                   {{ $image->is_primary ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="primary_{{ $image->id }}">
                                                <small>Primary</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="images">Add More Images</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="images[]" id="images" class="custom-file-input @error('images.*') is-invalid @enderror" 
                                       accept="image/*" multiple>
                                <label class="custom-file-label" for="images">Choose files</label>
                            </div>
                        </div>
                        <small class="text-muted">You can select multiple images to add.</small>
                        @error('images.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="image-preview" class="row mt-3"></div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" 
                                   value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
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
        // Initialize Select2 for category and brand dropdowns
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: function() {
                return $(this).data('placeholder') || $(this).find('option:first').text();
            },
            allowClear: true,
            width: '100%'
        });
        
        // Initialize AutoNumeric for price inputs
        var autoNumericOptions = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false
        };
        
        var basePriceAN = new AutoNumeric('#base_price', autoNumericOptions);
        var baseCostAN = new AutoNumeric('#base_cost', autoNumericOptions);
        
        // AJAX Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Form submission via AJAX
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnHtml = submitBtn.html();
            
            // Clear previous errors
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback, .text-danger').not('.persistent-error').remove();
            
            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            
            // Set hidden fields with raw numeric values before submission
            $('#base_price_hidden').val(basePriceAN.getNumber());
            $('#base_cost_hidden').val(baseCostAN.getNumber() || 0);
            
            // Use FormData for file uploads
            var formData = new FormData(this);
            // Override method for PUT with FormData
            formData.append('_method', 'PUT');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST', // Use POST with _method override for FormData
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
                            var input = form.find('[name="' + field + '"]');
                            if (input.length === 0) {
                                // Handle array fields like images.*
                                input = form.find('[name="' + field.replace(/\.\d+$/, '[]') + '"]');
                            }
                            input.addClass('is-invalid');
                            
                            // Add error message
                            if (input.closest('.input-group').length) {
                                input.closest('.input-group').after('<span class="text-danger">' + messages[0] + '</span>');
                            } else if (input.closest('.custom-file').length) {
                                input.closest('.custom-file').after('<span class="text-danger">' + messages[0] + '</span>');
                            } else {
                                input.after('<span class="invalid-feedback d-block">' + messages[0] + '</span>');
                            }
                        });
                        toastr.error('Please fix the validation errors.');
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Show selected filenames and preview
        document.getElementById('images').addEventListener('change', function(e) {
            var fileCount = e.target.files.length;
            var label = e.target.nextElementSibling;
            label.innerText = fileCount > 0 ? fileCount + ' file(s) selected' : 'Choose files';
            
            // Preview images
            var preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach(function(file, index) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6 mb-3';
                    col.innerHTML = '<div class="card"><img src="' + event.target.result + '" class="card-img-top" style="height: 150px; object-fit: cover;"><div class="card-body p-2 text-center"><small class="text-muted">New Image ' + (index + 1) + '</small></div></div>';
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@stop

