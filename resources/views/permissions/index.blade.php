@extends('adminlte::page')

@section('title', 'Permission Management')

@section('content_header')
    <h1>Permission Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Permission Management</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('permissions.create') }}"> Create New Permission</a>
                </div>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                <p>{{ $message }}</p>
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

            var table = window.LaravelDataTables["permissions-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm("Are You sure want to delete this permission?")){
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('permissions') }}/"+id,
                        success: function (data) {
                            table.draw();
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                alert(xhr.responseJSON.error);
                            } else {
                                console.log('Error:', xhr);
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
