@extends('adminlte::page')

@section('title', 'Import Variants - ' . $product->name)

@section('content_header')
    <h1>Import Variants: {{ $product->name }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Upload Excel File</h3>
            </div>
            <form id="importForm" action="{{ route('admin.products.variants.process-import', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">Excel File</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Choose file</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Supported formats: .xlsx, .xls, .csv. 
                            <a href="{{ route('admin.products.variants.download-template') }}">Download Template</a>
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="update_existing" name="update_existing" value="1">
                            <label class="custom-control-label" for="update_existing">Update existing variants if SKU matches</label>
                        </div>
                        <small class="form-text text-muted">Original values will be overwritten if checked. Default is to skip existing SKUs.</small>
                    </div>

                    <div class="callout callout-info">
                        <h5>Instructions</h5>
                        <ul>
                            <li><strong>SKU</strong>, <strong>Name</strong>, <strong>Price</strong>, and <strong>Stock</strong> are required.</li>
                            <li><strong>Cost</strong> and <strong>Min Stock</strong> are optional.</li>
                            <li>To add attributes (like Color, Size), use columns starting with <code>Attribute:</code> (e.g., <code>Attribute: Color</code>, <code>Attribute: Size</code>).</li>
                            <li>Attribute values are optional and can be left empty.</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Import Variants</button>
                    <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-default float-right">Cancel</a>
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
            btn.html('<i class="fas fa-spinner fa-spin"></i> Importing...');
        });
    </script>
@stop
