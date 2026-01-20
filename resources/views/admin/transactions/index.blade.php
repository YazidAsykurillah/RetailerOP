@extends('adminlte::page')

@section('title', 'Transaction History')

@section('content_header')
    <h1><i class="fas fa-receipt"></i> Transaction History</h1>
@stop

@section('content')
<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <p>Today's Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($todayTransactions) }}</h3>
                <p>Today's Transactions</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Rp {{ number_format($monthSales, 0, ',', '.') }}</h3>
                <p>This Month's Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($totalTransactions) }}</h3>
                <p>Total Transactions</p>
            </div>
            <div class="icon">
                <i class="fas fa-database"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> Filters
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
                        <label>Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="reset-filter">
                                <i class="fas fa-times"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transaction Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> All Transactions
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.pos.index') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> New Transaction
            </a>
        </div>
    </div>
    <div class="card-body">
        {{ $dataTable->table(['class' => 'table table-striped table-bordered w-100']) }}
    </div>
</div>
@stop

@section('js')
{{ $dataTable->scripts() }}
<script>
$(function() {
    // Apply filters
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        
        var table = window.LaravelDataTables['transactions-table'];
        
        // Add filter parameters to the table's ajax request
        table.ajax.url('{{ route("admin.transactions.index") }}?' + $(this).serialize()).load();
    });

    // Reset filters
    $('#reset-filter').on('click', function() {
        $('#filter-form')[0].reset();
        var table = window.LaravelDataTables['transactions-table'];
        table.ajax.url('{{ route("admin.transactions.index") }}').load();
    });
});
</script>
@stop
