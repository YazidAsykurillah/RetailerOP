@extends('adminlte::page')

@section('title', __('variant.types'))

@section('content_header')
    <h1>{{ __('variant.types') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('variant.manage_types') ?? 'Manage Variant Types' }}</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.variant-types.create') }}">
                        <i class="fas fa-plus"></i> {{ __('variant.add_type') ?? 'Add Variant Type' }}
                    </a>
                </div>
            </div>
            <div class="card-body">
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

            var table = window.LaravelDataTables["variant-types-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm(window.Lang.confirm_delete || "Are you sure you want to delete this variant type?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/variant-types') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Variant type deleted successfully!');
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
