@extends('adminlte::page')

@section('title', __('menu.receivables'))

@section('content_header')
    <h1><i class="fas fa-money-bill-wave"></i> {{ __('menu.receivables') }}</h1>
@stop

@section('content')
<!-- Summary Cards -->
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($summary->total_outstanding ?? 0, 0, ',', '.') }}</h3>
                <p>{{ __('transaction.total_outstanding') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($summary->total_transaction ?? 0, 0, ',', '.') }}</h3>
                <p>{{ __('customer.total_transaction') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($summary->total_paid ?? 0, 0, ',', '.') }}</h3>
                <p>{{ __('customer.total_paid') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> {{ __('general.filter') ?? 'Filter' }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filter_group_id">{{ __('customer.group') ?? 'Customer Group' }}</label>
                    <select name="filter_group_id" id="filter_group_id" class="form-control select2">
                        <option value="">{{ __('customer.all_groups') ?? 'All Groups' }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4" style="margin-top: 30px;">
                <button id="btn-filter" class="btn btn-primary"><i class="fas fa-filter"></i> {{ __('general.filter') ?? 'Filter' }}</button>
                <button id="btn-reset" class="btn btn-warning"><i class="fas fa-undo"></i> {{ __('general.reset') ?? 'Reset' }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Receivables Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i> {{ __('menu.receivables') }}
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            {{ $dataTable->table(['class' => 'table table-striped table-bordered w-100'], true) }}
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
                $('#receivables-table').DataTable().draw();
            });

            // Reset button click event
            $('#btn-reset').click(function() {
                $('#filter_group_id').val('').trigger('change');
                $('#receivables-table').DataTable().draw();
            });

            // Pass parameters to DataTable
            $('#receivables-table').on('preXhr.dt', function (e, settings, data) {
                data.customer_group_id = $('#filter_group_id').val();
            });
        });

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@stop
