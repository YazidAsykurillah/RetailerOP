@extends('adminlte::page')

@section('title', 'Manage Variants - ' . $product->name)

@section('content_header')
    <h1>Manage Variants</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Product Info Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($product->primary_image_url)
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 60px;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h5 class="mb-0">{{ $product->name }}</h5>
                        <small class="text-muted">SKU: {{ $product->sku }} | Base Price: {{ number_format($product->base_price, 0, ',', '.') }}</small>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants Table Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Variants</h3>
                <div class="card-tools">
                    <a class="btn btn-info mr-2" href="{{ route('admin.products.variants.import', $product->id) }}">
                        <i class="fas fa-file-import"></i> Import Variants
                    </a>
                    <a class="btn btn-success" href="{{ route('admin.products.variants.create', $product->id) }}">
                        <i class="fas fa-plus"></i> Add Variant
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check"></i> {{ $message }}
                </div>
                @endif

                <div class="table-responsive">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = window.LaravelDataTables["product-variants-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm("Are you sure you want to delete this variant?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/products/' . $product->id . '/variants') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Variant deleted successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Error deleting variant.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
