@extends('adminlte::page')

@section('title', __('brand.management'))

@section('content_header')
    <h1>{{ __('brand.management') }}</h1>
@stop

@section('content')
<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($totalBrands) }}</h3>
                <p>Total Brands</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($totalVariantsSold) }}</h3>
                <p>Total Variants Sold</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('brand.list') }}</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.brands.create') }}">
                        <i class="fas fa-plus"></i> {{ __('brand.create') }}
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

            var table = window.LaravelDataTables["brands-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm(window.Lang.confirm_delete || "Are you sure you want to delete this brand?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/brands') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success("{{ __('brand.deleted') ?? 'Brand deleted successfully!' }}");
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error("{{ __('brand.delete_error') ?? 'Error deleting brand.' }}");
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
