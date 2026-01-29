@extends('adminlte::page')

@section('title', 'Product Management')

@section('content_header')
    <h1>Product Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Brand</label>
                                <select class="form-control" id="brand_id" name="brand_id">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="reset-filter">
                                        <i class="fas fa-times"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product List</h3>
                <div class="card-tools">
                    <a class="btn btn-info mr-2" href="{{ route('admin.products.import') }}">
                        <i class="fas fa-file-import"></i> Import Products
                    </a>
                    <a class="btn btn-success" href="{{ route('admin.products.create') }}">
                        <i class="fas fa-plus"></i> Create New Product
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
            <!-- Loading Overlay -->
            <div class="overlay" id="loading-overlay" style="display: none;">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Ensure DataTable takes full width */
    .dataTables_wrapper {
        width: 100%;
    }
    #products-table {
        width: 100% !important;
    }
    #products-table_wrapper {
        width: 100%;
    }
</style>
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

            var table = window.LaravelDataTables["products-table"];
            var overlay = $('#loading-overlay');

            // Apply filters
            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                overlay.show();
                table.draw();
            });

            // Reset filters
            $('#reset-filter').on('click', function() {
                $('#filter-form')[0].reset();
                overlay.show();
                table.draw();
            });

            // Add filter parameters to the table's request
            table.on('preXhr.dt', function (e, settings, data) {
                data.category_id = $('#category_id').val();
                data.brand_id = $('#brand_id').val();
            });

            // Hide overlay when table is drawn
            table.on('draw.dt', function () {
                overlay.hide();
            });

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm("Are you sure you want to delete this product?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/products') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Product deleted successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Error deleting product.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
