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
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id">
                            <option value="">All Customers</option>
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
        {{ $dataTable->table(['class' => 'table table-striped table-bordered w-100'], true) }}
    </div>
</div>
@stop

@section('js')
{{ $dataTable->scripts() }}
<script>
$(function() {
    // Initialize Select2
    $('#customer_id').select2({
        theme: 'bootstrap4',
        placeholder: 'All Customers',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.customers.search") }}',
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

    // Apply filters
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        window.LaravelDataTables['transactions-table'].draw();
    });

    // Reset filters
    $('#reset-filter').on('click', function() {
        $('#date_from').val('');
        $('#date_to').val('');
        $('#payment_method').val('');
        $('#customer_id').val('').trigger('change');
        window.LaravelDataTables['transactions-table'].draw();
    });

    // Handle Delete Button
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var url = '{{ route("admin.transactions.destroy", ":id") }}';
        url = url.replace(':id', id);

        if (confirm('Are you sure you want to delete this transaction? This will RESTORE the stock quantities for all items in this transaction.')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.LaravelDataTables['transactions-table'].draw();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred'));
                }
            });
        }
    });
});
</script>
@stop
