@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <h1>Customers</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter_group_id">Customer Group</label>
                                <select name="filter_group_id" id="filter_group_id" class="form-control select2">
                                    <option value="">All Groups</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 30px;">
                            <button id="btn-filter" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                            <button id="btn-reset" class="btn btn-warning"><i class="fas fa-undo"></i> Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer List</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Customer
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
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Filter button click event
            $('#btn-filter').click(function() {
                $('#customers-table').DataTable().draw();
            });

            // Reset button click event
            $('#btn-reset').click(function() {
                $('#filter_group_id').val('').trigger('change');
                $('#customers-table').DataTable().draw();
            });
            
             // Pass parameters to DataTable
            $('#customers-table').on('preXhr.dt', function (e, settings, data) {
                 data.customer_group_id = $('#filter_group_id').val();
            });
        });
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            
            if (confirm('Are you sure you want to delete this customer?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.success);
                        $('#customers-table').DataTable().ajax.reload();
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
