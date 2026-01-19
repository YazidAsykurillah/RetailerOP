@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> Welcome!</h5>
            Welcome to the Siskha Store Management System. Here's an overview of your store.
        </div>
    </div>
</div>

{{-- Product Overview --}}
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-box"></i> Product Overview</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalProducts) }}</h3>
                <p>Total Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                View Products <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($activeProducts) }}</h3>
                <p>Active Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                View Products <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($totalCategories) }}</h3>
                <p>Categories</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="small-box-footer">
                View Categories <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($totalBrands) }}</h3>
                <p>Brands</p>
            </div>
            <div class="icon">
                <i class="fas fa-copyright"></i>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="small-box-footer">
                View Brands <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Stock Overview --}}
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-warehouse"></i> Stock Overview</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalVariants) }}</h3>
                <p>Product Variants</p>
            </div>
            <div class="icon">
                <i class="fas fa-cubes"></i>
            </div>
            <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                View Stock <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
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
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($outOfStockCount) }}</h3>
                <p>Out of Stock</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                View Stock <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                <p>Stock Value</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                View Stock <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Transaction Overview --}}
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-cash-register"></i> Transaction Overview</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ number_format($totalTransactions) }}</h3>
                <p>Total Transactions</p>
            </div>
            <div class="icon">
                <i class="fas fa-receipt"></i>
            </div>
            <span class="small-box-footer">
                View Transactions <i class="fas fa-arrow-circle-right"></i>
            </span>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($todayTransactions) }}</h3>
                <p>Today's Transactions</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <span class="small-box-footer">
                View Transactions <i class="fas fa-arrow-circle-right"></i>
            </span>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <p>Total Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <span class="small-box-footer">
                View Transactions <i class="fas fa-arrow-circle-right"></i>
            </span>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-olive">
            <div class="inner">
                <h3>Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                <p>Today's Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-coins"></i>
            </div>
            <span class="small-box-footer">
                View Transactions <i class="fas fa-arrow-circle-right"></i>
            </span>
        </div>
    </div>
</div>

{{-- Additional Information --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Stock Movement Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="border-right">
                            <h4 class="text-success"><i class="fas fa-arrow-down"></i> {{ number_format($recentStockIn) }}</h4>
                            <p class="text-muted">Recent Stock In</p>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <h4 class="text-danger"><i class="fas fa-arrow-up"></i> {{ number_format($recentStockOut) }}</h4>
                        <p class="text-muted">Recent Stock Out</p>
                    </div>
                </div>
                <hr>
                <a href="{{ route('admin.stock.in') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Stock In
                </a>
                <a href="{{ route('admin.stock.out') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-minus"></i> Stock Out
                </a>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-list"></i> View All Stock
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> User Management</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h4>{{ number_format($totalUsers) }}</h4>
                    <p class="text-muted">Total Users</p>
                </div>
                <hr>
                @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
                @endcan
                @can('view users')
                <a href="{{ route('users.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-users"></i> View All Users
                </a>
                @endcan
                @can('view roles')
                <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-user-tag"></i> Manage Roles
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .small-box h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }
        .callout {
            margin-bottom: 20px;
        }
        h4 {
            margin-top: 20px;
            color: #6c757d;
            font-weight: 600;
        }
        .border-right {
            border-right: 1px solid #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        console.log('Dashboard loaded successfully!');
    </script>
@stop
