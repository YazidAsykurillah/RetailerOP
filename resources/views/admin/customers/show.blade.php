@extends('adminlte::page')

@section('title', __('customer.details') ?? 'Customer Details')

@section('content_header')
    <h1>{{ __('customer.details') ?? 'Customer Details' }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('customer.information') ?? 'Customer Info' }}: {{ $customer->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('general.back') ?? 'Back' }}
                    </a>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> {{ __('general.edit') ?? 'Edit' }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">{{ __('customer.name') ?? 'Name' }}</th>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('customer.email') ?? 'Email' }}</th>
                                <td>{{ $customer->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('customer.phone') ?? 'Phone' }}</th>
                                <td>{{ $customer->phone }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('general.status') ?? 'Status' }}</th>
                                <td>
                                    @if($customer->is_active)
                                        <span class="badge badge-success">{{ __('general.active') ?? 'Active' }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('general.inactive') ?? 'Inactive' }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">{{ __('customer.address') ?? 'Address' }}</th>
                                <td>{{ $customer->address }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('customer.group') ?? 'Group' }}</th>
                                <td>{{ $customer->customerGroup->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('customer.joined_date') ?? 'Joined Date' }}</th>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('customer.transaction_history') ?? 'Transaction History' }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>{{ __('general.date_from') ?? 'Date From' }}</label>
                        <input type="date" name="date_from" id="date_from" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('general.date_to') ?? 'Date To' }}</label>
                        <input type="date" name="date_to" id="date_to" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button id="btn-filter" class="btn btn-primary btn-block">{{ __('general.filter') ?? 'Filter' }}</button>
                    </div>
                     <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button id="btn-reset" class="btn btn-warning btn-block">{{ __('general.reset') ?? 'Reset' }}</button>
                    </div>
                </div>
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
        $(document).ready(function(){
            $('#btn-filter').click(function(){
                $('#customer-transactions-table').DataTable().draw();
            });
            $('#btn-reset').click(function(){
                $('#date_from').val('');
                $('#date_to').val('');
                $('#customer-transactions-table').DataTable().draw();
            });
        });
    </script>
@stop
