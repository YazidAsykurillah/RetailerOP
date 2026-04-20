@extends('adminlte::page')

@section('title', __('menu.payment_list'))

@section('content_header')
    <h1><i class="fas fa-money-bill-wave"></i> {{ __('menu.payment_list') }}</h1>
@stop

@section('content')
<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp {{ number_format($todayIncome, 0, ',', '.') }}</h3>
                <p>Today's Net Income</p>
            </div>
            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($monthIncome, 0, ',', '.') }}</h3>
                <p>This Month's Net Income</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                <p>Total Net Income</p>
            </div>
            <div class="icon">
                <i class="fas fa-wallet"></i>
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
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
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

<!-- Payment Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> All Payment Records
        </h3>
    </div>
    <div class="card-body">
        {{ $dataTable->table(['class' => 'table table-striped table-bordered w-100'], true) }}
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
        window.LaravelDataTables['payments-table'].draw();
    });

    // Reset filters
    $('#reset-filter').on('click', function() {
        $('#date_from').val('');
        $('#date_to').val('');
        $('#payment_method').val('');
        window.LaravelDataTables['payments-table'].draw();
    });
});
</script>
@stop
