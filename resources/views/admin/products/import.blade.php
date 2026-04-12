@extends('adminlte::page')

@section('title', __('general.import') . ' ' . __('product.plural'))

@section('content_header')
    <h1>{{ __('general.import') }} {{ __('product.plural') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('general.import') }} {{ __('product.plural') }}</h3>
            </div>
            <form id="importForm" action="{{ route('admin.products.process-import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">{{ __('general.select') ?? 'Excel File' }}</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">{{ __('general.select') ?? 'Choose file' }}</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('product.import_supported_formats') ?? 'Supported formats: .xlsx, .xls, .csv.' }}
                            <a href="{{ route('admin.products.download-template') }}">{{ __('product.import_download_template') ?? 'Download Template' }}</a>
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1">
                            <label class="custom-control-label" for="update_existing">{{ __('product.import_update_existing') ?? 'Update existing products if SKU matches' }}</label>
                        </div>
                        <small class="form-text text-muted">{{ __('product.import_update_help') ?? 'Original values will be overwritten if checked. Default is to skip existing SKUs.' }}</small>
                    </div>

                    <div class="callout callout-info">
                        <h5>{{ __('general.information') ?? 'Instructions' }}</h5>
                        <ul>
                            <li><strong>{{ __('product.sku') }}</strong>, <strong>{{ __('product.name') }}</strong>, {{ __('general.and') ?? 'and' }} <strong>{{ __('product.price') }}</strong> {{ __('general.are_required') ?? 'are required.' }}</li>
                            <li><strong>{{ __('category.singular') }}</strong> {{ __('general.and') ?? 'and' }} <strong>{{ __('brand.singular') }}</strong> {{ __('product.import_category_brand_help') ?? 'names can be selected from the dropdown list in the template. If you import new names, they will be created automatically.' }}</li>
                            <li>{{ __('product.import_optional_fields_prefix') ?? 'Other fields like' }} <strong>{{ __('product.description') }}</strong> {{ __('general.and') ?? 'and' }} <strong>{{ __('product.cost') }}</strong> {{ __('product.import_optional_fields_suffix') ?? 'are optional.' }}</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">{{ __('general.import') }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-default float-right">{{ __('general.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
    <script>
        // Custom file input label update
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        // Prevent multiple submissions
        $('#importForm').on('submit', function() {
            var btn = $(this).find('button[type="submit"]');
            if (btn.prop('disabled')) {
                return false;
            }
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> ' + ("{{ __('general.importing') ?? 'Importing...' }}"));
        });
    </script>
@stop
