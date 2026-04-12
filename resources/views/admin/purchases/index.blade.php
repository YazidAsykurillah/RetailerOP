@extends('adminlte::page')

@section('title', __('purchase.plural') ?? 'Purchases')

@section('content_header')
    <h1>{{ __('purchase.plural') ?? 'Purchases' }}</h1>
@stop

@section('content')
    <!-- Filters -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> {{ __('general.filter') ?? 'Filters' }}
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('general.date_from') ?? 'Date From' }}</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('general.date_to') ?? 'Date To' }}</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>
                                {{ __('supplier.singular') ?? 'Supplier' }} 
                                <a href="javascript:void(0)" id="clear-supplier" class="text-danger ml-2" style="display:none;" title="Clear Selection">
                                    <i class="fas fa-times"></i>
                                </a>
                            </label>
                            <select class="form-control" id="supplier_id" name="supplier_id">
                                <option value="">{{ __('supplier.all') ?? 'All Suppliers' }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('general.status') ?? 'Status' }}</label>
                            <select class="form-control" name="status">
                                <option value="">{{ __('general.all_status') ?? 'All Status' }}</option>
                                <option value="pending">{{ __('purchase.status_pending') ?? 'Pending' }}</option>
                                <option value="partial">{{ __('purchase.status_partial') ?? 'Partial' }}</option>
                                <option value="completed">{{ __('purchase.status_completed') ?? 'Completed' }}</option>
                                <option value="cancelled">{{ __('purchase.status_cancelled') ?? 'Cancelled' }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> {{ __('general.filter') ?? 'Filter' }}
                                </button>
                                <button type="button" class="btn btn-secondary" id="reset-filter">
                                    <i class="fas fa-times"></i> {{ __('general.reset') ?? 'Reset' }}
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
            <h3 class="card-title">{{ __('purchase.list') ?? 'List of Purchases' }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> {{ __('purchase.create') ?? 'Create Purchase' }}
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                {{ $dataTable->table([], true) }}
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
    {{ $dataTable->scripts() }}
    <script>
    @section('plugins.Select2', true)

    $(function() {
        // Initialize Select2
        $('#supplier_id').select2({
            theme: 'bootstrap4',
            placeholder: "{{ __('supplier.all') ?? 'All Suppliers' }}",
            allowClear: false,
            ajax: {
                url: '{{ route("admin.suppliers.search") }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // Handle Clear Button Visibility
        function toggleClearButton() {
            if ($('#supplier_id').val()) {
                $('#clear-supplier').show();
            } else {
                $('#clear-supplier').hide();
            }
        }

        $('#supplier_id').on('change', toggleClearButton);
        $('#clear-supplier').on('click', function() {
            $('#supplier_id').val('').trigger('change');
        });
        
        // Initial check
        toggleClearButton();

        // Apply filters
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            
            var table = window.LaravelDataTables['purchases-table'];
            
            // Add filter parameters to the table's ajax request
            table.ajax.url('{{ route("admin.purchases.index") }}?' + $(this).serialize()).load();
        });

        // Reset filters
        $('#reset-filter').on('click', function() {
            $('#filter-form')[0].reset();
            $('#supplier_id').val('').trigger('change'); // Reset Select2 to empty
            var table = window.LaravelDataTables['purchases-table'];
            table.ajax.url('{{ route("admin.purchases.index") }}').load();
        });
    });
    </script>
@stop
