@extends('adminlte::page')

@section('title', 'Supplier Details')

@section('content_header')
    <h1>Supplier Details</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Supplier Info: {{ $supplier->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">Name</th>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Contact Person</th>
                                <td>{{ $supplier->contact_person }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $supplier->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $supplier->phone }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($supplier->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                             <tr>
                                <th style="width: 200px">Address</th>
                                <td>{{ $supplier->address }}</td>
                            </tr>
                            <tr>
                                <th>Website</th>
                                <td>
                                    @if($supplier->website)
                                        <a href="{{ $supplier->website }}" target="_blank">{{ $supplier->website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tax ID</th>
                                <td>{{ $supplier->tax_id ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Terms</th>
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
                <h3 class="card-title">Purchase History</h3>
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
