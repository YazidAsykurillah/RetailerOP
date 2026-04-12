@extends('adminlte::page')

@section('title', __('supplier.details') ?? 'Supplier Details')

@section('content_header')
    <h1>{{ __('supplier.details') ?? 'Supplier Details' }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('supplier.information') ?? 'Supplier Info' }}: {{ $supplier->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                    </a>
                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> {{ __('general.edit') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">{{ __('supplier.name') ?? 'Name' }}</th>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.contact_person') ?? 'Contact Person' }}</th>
                                <td>{{ $supplier->contact_person }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.email') ?? 'Email' }}</th>
                                <td>{{ $supplier->email }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.phone') ?? 'Phone' }}</th>
                                <td>{{ $supplier->phone }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('general.status') ?? 'Status' }}</th>
                                <td>
                                    @if ($supplier->is_active)
                                        <span class="badge badge-success">{{ __('general.active') }}</span>
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
                                <th style="width: 200px">{{ __('supplier.address') ?? 'Address' }}</th>
                                <td>{{ $supplier->address }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.website') ?? 'Website' }}</th>
                                <td>
                                    @if($supplier->website)
                                        <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.tax_id') ?? 'Tax ID' }}</th>
                                <td>{{ $supplier->tax_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('supplier.payment_terms') ?? 'Payment Terms' }}</th>
                                <td>{{ $supplier->payment_terms ?? '-' }}</td>
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
                <h3 class="card-title">{{ __('supplier.purchase_history') ?? 'Purchase History' }}</h3>
            </div>
            <div class="card-body">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@stop
