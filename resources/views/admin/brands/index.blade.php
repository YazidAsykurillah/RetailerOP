@extends('adminlte::page')

@section('title', 'Brand Management')

@section('content_header')
    <h1>Brand Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Brand List</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.brands.create') }}">
                        <i class="fas fa-plus"></i> Create New Brand
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
                if(confirm("Are you sure you want to delete this brand?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/brands') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Brand deleted successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Error deleting brand.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
