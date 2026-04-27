@extends('adminlte::page')

@section('title', __('stock.overview') ?? 'Stock Overview')

@section('content_header')
    <h1>{{ __('stock.overview') ?? 'Stock Overview' }}</h1>
@stop

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalItems) }}</h3>
                <p>{{ __('stock.total_variants') ?? 'Total Product Variants' }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($lowStockCount) }}</h3>
                <p>{{ __('stock.low_stock_items') ?? 'Low Stock Items' }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('admin.stock.index', ['low_stock' => 1]) }}" class="small-box-footer">
                {{ __('stock.view_low_stock') ?? 'View Low Stock' }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($outOfStockCount) }}</h3>
                <p>{{ __('stock.out_of_stock') ?? 'Out of Stock' }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

</div>
<!-- Filters -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> {{ __('general.filter') ?? 'Filters' }}
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('general.status') ?? 'Status' }}</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">{{ __('general.all_statuses') ?? 'All Statuses' }}</option>
                            <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>{{ __('stock.in_stock') ?? 'In Stock' }}</option>
                            <option value="low_stock" {{ request('low_stock') == '1' || request('status') == 'low_stock' ? 'selected' : '' }}>{{ __('stock.low_stock') ?? 'Low Stock' }}</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('stock.out_of_stock') ?? 'Out of Stock' }}</option>
                        </select>
                    </div>
                </div>
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('stock.list') ?? 'Stock List' }}</h3>
                
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
    $(function() {
        // Apply filters
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            
            var table = window.LaravelDataTables['stock-table'];
            
            // Add filter parameters to the table's ajax request
            table.ajax.url('{{ route("admin.stock.index") }}?' + $(this).serialize()).load();
        });

        // Reset filters
        $('#reset-filter').on('click', function() {
            $('#filter-form')[0].reset();
            // Also clear select manually if needed
            $('#status').val('');
            
            var table = window.LaravelDataTables['stock-table'];
            // Reset to base URL without params
            table.ajax.url('{{ route("admin.stock.index") }}').load();
        });
    });
    </script>
@stop
