@extends('adminlte::page')

@section('title', 'Transaction History')

@section('content_header')
    <h1><i class="fas fa-receipt"></i> Transaction History</h1>
@stop

@section('content')
<!-- Summary Cards -->
<div class="row">
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
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($todayIncomes, 0, ',', '.') }}</h3>
                <p>{{ __('dashboard.todays_income') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-cash-register"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($monthIncomes, 0, ',', '.') }}</h3>
                <p>{{ __('dashboard.months_income') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($totalSoldVariants) }}</h3>
                <p>{{ __('general.total_sold_product') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
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
                        <label>Customer</label>
                        <select class="form-control" id="customer_id" name="customer_id">
                            <option value="">All Customers</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Payment Mode</label>
                        <select class="form-control" id="payment_mode" name="payment_mode">
                            <option value="">All Modes</option>
                            <option value="full" {{ request('payment_mode') === 'full' ? 'selected' : '' }}>Full</option>
                            <option value="partial" {{ request('payment_mode') === 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select class="form-control" id="payment_status" name="payment_status">
                            <option value="">All Statuses</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Completed</option>
                            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Not Completed</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
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
        $('#payment_mode').val('');
        $('#payment_status').val('');
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
