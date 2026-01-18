@extends('adminlte::page')

@section('title', 'Stock Overview')

@section('content_header')
    <h1>Stock Overview</h1>
@stop

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalItems) }}</h3>
                <p>Total Product Variants</p>
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
                <p>Low Stock Items</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('admin.stock.index', ['low_stock' => 1]) }}" class="small-box-footer">
                View Low Stock <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($outOfStockCount) }}</h3>
                <p>Out of Stock</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stock List</h3>
                <div class="card-tools">
                    <a class="btn btn-success btn-sm" href="{{ route('admin.stock.in') }}">
                        <i class="fas fa-arrow-down"></i> Stock In
                    </a>
                    <a class="btn btn-warning btn-sm" href="{{ route('admin.stock.out') }}">
                        <i class="fas fa-arrow-up"></i> Stock Out
                    </a>
                </div>
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
@stop
