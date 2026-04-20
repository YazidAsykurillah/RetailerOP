@extends('adminlte::page')

@section('title', __('product.management'))

@section('content_header')
    <h1>{{ __('product.management') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> {{ __('general.filter') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('category.singular') }}</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">{{ __('category.plural') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('brand.singular') }}</label>
                                <select class="form-control" id="brand_id" name="brand_id">
                                    <option value="">{{ __('brand.plural') }}</option>
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
                                        <i class="fas fa-search"></i> {{ __('general.filter') }}
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="reset-filter">
                                        <i class="fas fa-times"></i> {{ __('general.reset') }}
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
                <h3 class="card-title">{{ __('product.list') }}</h3>
                <div class="card-tools">
                    @can('delete-multiple-product')
                        <button class="btn btn-danger mr-2" id="bulk-delete" style="display: none;">
                            <i class="fas fa-trash"></i> {{ __('general.delete') }}
                        </button>
                    @endcan
                    <a class="btn btn-info mr-2" href="{{ route('admin.products.import') }}">
                        <i class="fas fa-file-import"></i> {{ __('general.import') ?? 'Import' }}
                    </a>
                    <button type="button" class="btn btn-dark mr-2" data-toggle="modal" data-target="#exportPdfModal">
                        <i class="fas fa-file-pdf"></i> {{ __('general.export_pdf') ?? 'Export PDF' }}
                    </button>
                    <a class="btn btn-success" href="{{ route('admin.products.create') }}">
                        <i class="fas fa-plus"></i> {{ __('product.create') }}
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

<!-- Export PDF Modal -->
<div class="modal fade" id="exportPdfModal" tabindex="-1" role="dialog" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportPdfModalLabel">{{ __('product.export_options') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="custom-control custom-radio mb-2">
                        <input class="custom-control-input" type="radio" id="exportAll" name="exportStockFilter" value="false" checked>
                        <label for="exportAll" class="custom-control-label">{{ __('product.export_all') }}</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="exportStockOnly" name="exportStockFilter" value="true">
                        <label for="exportStockOnly" class="custom-control-label">{{ __('product.export_stock_only') }}</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('general.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-export-pdf">
                    <i class="fas fa-file-pdf"></i> {{ __('general.export') }}
                </button>
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

            // Handle Confirm Export PDF
            $('#confirm-export-pdf').on('click', function() {
                var categoryId = $('#category_id').val();
                var brandId = $('#brand_id').val();
                var stockOnly = $('input[name="exportStockFilter"]:checked').val();
                
                var exportUrl = "{{ route('admin.products.export-pdf') }}";
                var params = $.param({
                    category_id: categoryId,
                    brand_id: brandId,
                    stock_only: stockOnly
                });
                
                window.location.href = exportUrl + '?' + params;
                $('#exportPdfModal').modal('hide');
            });

            // Hide overlay when table is drawn
            table.on('draw.dt', function () {
                overlay.hide();
                $('#select-all-products').prop('checked', false);
                toggleBulkDeleteButton();
            });

            // Handle Select All checkbox
            $('body').on('click', '#select-all-products', function() {
                $('.product-checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkDeleteButton();
            });

            // Handle individual checkbox changes
            $('body').on('change', '.product-checkbox', function() {
                var total = $('.product-checkbox').length;
                var checked = $('.product-checkbox:checked').length;
                
                $('#select-all-products').prop('checked', total > 0 && total === checked);
                toggleBulkDeleteButton();
            });

            function toggleBulkDeleteButton() {
                var checkedCount = $('.product-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#bulk-delete').show();
                } else {
                    $('#bulk-delete').hide();
                }
            }

            // Bulk Delete
            $('#bulk-delete').on('click', function() {
                var ids = [];
                $('.product-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length > 0) {
                    if (confirm(window.Lang.confirm_delete || "{{ __('general.confirm_delete') ?? 'Are you sure you want to delete the selected items?' }}")) {
                        overlay.show();
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('admin.products.bulk-destroy') }}",
                            data: { ids: ids },
                            success: function (data) {
                                table.draw();
                                toastr.success(data.message);
                            },
                            error: function (xhr) {
                                overlay.hide();
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    toastr.error(xhr.responseJSON.error);
                                } else {
                                    toastr.error(window.Lang.error || 'An error occurred.');
                                }
                            }
                        });
                    }
                }
            });

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm(window.Lang.confirm_delete || "{{ __('general.confirm_delete') ?? 'Are you sure you want to delete this item?' }}")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/products') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success("{{ __('product.delete_success') ?? 'Product deleted successfully!' }}");
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error(window.Lang.error || 'An error occurred.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
