@extends('adminlte::page')

@section('title', 'Stock Movement History')

@section('content_header')
    <h1>Stock Movement History</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Variant Info Card -->
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Product:</strong>
                        <p class="text-muted">{{ $variant->product->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Variant:</strong>
                        <p class="text-muted">{{ $variant->name ?: 'Default' }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>SKU:</strong>
                        <p class="text-muted">{{ $variant->sku }}</p>
                    </div>
                    <div class="col-md-2">
                        <strong>Current Stock:</strong>
                        <p class="text-muted">
                            <span class="badge {{ $variant->is_low_stock ? 'badge-warning' : 'badge-success' }} font-weight-bold" style="font-size: 1rem;">
                                {{ number_format($variant->stock) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <strong>Min Stock:</strong>
                        <p class="text-muted">{{ number_format($variant->min_stock) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movement History Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Movement History</h3>
                <div class="card-tools">
                    <a class="btn btn-success btn-sm" href="{{ route('admin.stock.in', ['variant' => $variant->id]) }}">
                        <i class="fas fa-arrow-down"></i> Stock In
                    </a>
                    <a class="btn btn-warning btn-sm" href="{{ route('admin.stock.out', ['variant' => $variant->id]) }}">
                        <i class="fas fa-arrow-up"></i> Stock Out
                    </a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('admin.stock.index') }}">
                        <i class="fas fa-arrow-left"></i> Back to Overview
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter by Type -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-control" id="type-filter">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
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
    $(function() {
        // Filter by type
        $('#type-filter').on('change', function() {
            var table = window.LaravelDataTables["stock-movements-table"];
            var url = new URL(table.ajax.url(), window.location.origin);
            url.searchParams.set('type', $(this).val());
            url.searchParams.set('variant_id', '{{ $variant->id }}');
            table.ajax.url(url.toString()).load();
        });
    });
    </script>
@stop
