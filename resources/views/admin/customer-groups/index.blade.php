@extends('adminlte::page')

@section('title', 'Customer Groups')

@section('content_header')
    <h1>Customer Groups</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Groups List</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.customer-groups.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Group
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

@section('css')
@stop

@section('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    
    <script>
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            
            if (confirm('Are you sure you want to delete this customer group?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.success);
                        $('#customergroups-table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.error || 'Something went wrong!');
                    }
                });
            }
        });

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@stop
