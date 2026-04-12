@extends('adminlte::page')

@section('title', __('dashboard.title'))

@section('content_header')
    <h1>{{ __('dashboard.heading') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-info"></i> {{ __('general.welcome') }}</h5>
            {{ __('dashboard.welcome_message') }}
        </div>
    </div>
</div>

{{-- Transaction Overview --}}
@if(\Auth::user()->can('Access Pos'))
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-cash-register"></i> {{ __('dashboard.transaction_overview') }}</h4>
    </div>
    <div class="col-lg-6 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($todayTransactions) }}</h3>
                <p>{{ __('dashboard.todays_transactions') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <a href="{{ route('admin.transactions.index', ['date_from' => date('Y-m-d'), 'date_to' => date('Y-m-d')]) }}" class="small-box-footer">
                {{ __('dashboard.view_transactions') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-6 col-6">
        <div class="small-box bg-olive">
            <div class="inner">
                <h3>{{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                <p>{{ __('dashboard.todays_sales') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-coins"></i>
            </div>
            <a href="{{ route('admin.transactions.index', ['date_from' => date('Y-m-d'), 'date_to' => date('Y-m-d')]) }}" class="small-box-footer">
                {{ __('dashboard.view_transactions') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
</div>
@endif

{{-- Stock Overview --}}
@if(\Auth::user()->can('Access Inventory'))
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-warehouse"></i> {{ __('dashboard.stock_overview') }}</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($lowStockCount) }}</h3>
                <p>{{ __('dashboard.low_stock_items') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('admin.stock.index', ['low_stock' => 1]) }}" class="small-box-footer">
                {{ __('dashboard.view_low_stock') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($outOfStockCount) }}</h3>
                <p>{{ __('dashboard.out_of_stock') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('admin.stock.index', ['status' => 'out_of_stock']) }}" class="small-box-footer">
                {{ __('dashboard.view_stock') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalVariants) }}</h3>
                <p>{{ __('dashboard.product_variants') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-cubes"></i>
            </div>
            <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                {{ __('dashboard.view_stock') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-teal">
            <div class="inner">
                <h3>{{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                <p>{{ __('dashboard.stock_value') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('admin.stock.index') }}" class="small-box-footer">
                {{ __('dashboard.view_stock') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
@endif

{{-- Purchase Overview --}}
@if(\Auth::user()->can('Manage Purchases'))
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-shopping-cart"></i> {{ __('dashboard.purchase_overview') }}</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-indigo">
            <div class="inner">
                <h3>{{ number_format($totalPurchases) }}</h3>
                <p>{{ __('dashboard.total_purchases') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <a href="{{ route('admin.purchases.index') }}" class="small-box-footer">
                {{ __('dashboard.view_purchases') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($pendingPurchases) }}</h3>
                <p>{{ __('dashboard.pending_purchases') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('admin.purchases.index') }}" class="small-box-footer">
                {{ __('dashboard.view_pending') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($completedPurchases) }}</h3>
                <p>{{ __('dashboard.completed_purchases') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('admin.purchases.index') }}" class="small-box-footer">
                {{ __('dashboard.view_completed') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($totalPurchaseCost, 0, ',', '.') }}</h3>
                <p>{{ __('dashboard.total_cost') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route('admin.purchases.index') }}" class="small-box-footer">
                {{ __('dashboard.view_purchases') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
@endif


{{-- Product Overview --}}
@if(\Auth::user()->can("Manage Products"))
<div class="row">
    <div class="col-12">
        <h4 class="mb-2"><i class="fas fa-box"></i> {{ __('dashboard.product_overview') }}</h4>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalProducts) }}</h3>
                <p>{{ __('dashboard.total_products') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                {{ __('dashboard.view_products') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($activeProducts) }}</h3>
                <p>{{ __('dashboard.active_products') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                {{ __('dashboard.view_products') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($totalCategories) }}</h3>
                <p>{{ __('dashboard.categories') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="small-box-footer">
                {{ __('dashboard.view_categories') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($totalBrands) }}</h3>
                <p>{{ __('dashboard.brands') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-copyright"></i>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="small-box-footer">
                {{ __('dashboard.view_brands') }} <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
@endif




{{-- Additional Information --}}
<div class="row">
    @if(\Auth::user()->can('Access Inventory'))
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> {{ __('dashboard.stock_movement_summary') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <div class="border-right">
                            <h4 class="text-success"><i class="fas fa-arrow-down"></i> {{ number_format($recentStockIn) }}</h4>
                            <p class="text-muted">{{ __('dashboard.recent_stock_in') }}</p>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <h4 class="text-danger"><i class="fas fa-arrow-up"></i> {{ number_format($recentStockOut) }}</h4>
                        <p class="text-muted">{{ __('dashboard.recent_stock_out') }}</p>
                    </div>
                </div>
                <hr>
                <a href="{{ route('admin.stock.in') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> {{ __('dashboard.stock_in') }}
                </a>
                <a href="{{ route('admin.stock.out') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-minus"></i> {{ __('dashboard.stock_out') }}
                </a>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-list"></i> {{ __('dashboard.view_all_stock') }}
                </a>
            </div>
        </div>
    </div>
    @endif
    @if(\Auth::user()->can('Manage Users'))
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> {{ __('dashboard.user_management') }}</h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h4>{{ number_format($totalUsers) }}</h4>
                    <p class="text-muted">{{ __('dashboard.total_users') }}</p>
                </div>
                <hr>
                @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> {{ __('dashboard.add_user') }}
                </a>
                @endcan
                @can('view users')
                <a href="{{ route('users.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-users"></i> {{ __('dashboard.view_all_users') }}
                </a>
                @endcan
                @can('view roles')
                <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-user-tag"></i> {{ __('dashboard.manage_roles') }}
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endif
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
