@extends('adminlte::page')

@section('title', 'Category Management')

@section('content_header')
    <h1>Category Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Category List</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.categories.create') }}">
                        <i class="fas fa-plus"></i> Create New Category
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

@section('css')
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

            var table = window.LaravelDataTables["categories-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm("Are you sure you want to delete this category?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/categories') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Category deleted successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Error deleting category.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
