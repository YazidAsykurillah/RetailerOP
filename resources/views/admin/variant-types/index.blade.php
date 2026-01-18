@extends('adminlte::page')

@section('title', 'Variant Types')

@section('content_header')
    <h1>Variant Types</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Variant Types</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.variant-types.create') }}">
                        <i class="fas fa-plus"></i> Add Variant Type
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
                if(confirm("Are you sure you want to delete this variant type?")) {
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
                                toastr.error('Error deleting variant type.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
