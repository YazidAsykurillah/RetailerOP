@extends('adminlte::page')

@section('title', __('purchase.details') ?? 'Purchase Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>{{ __('purchase.details') ?? 'Purchase Details' }}: {{ $purchase->reference_number }}</h1>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">{{ __('general.back') ?? 'Back to List' }}</a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Purchase Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('general.information') ?? 'Information' }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>{{ __('general.status') ?? 'Status' }}</th>
                            <td>
                                @if($purchase->status == 'pending')
                                    <span class="badge badge-warning">{{ __('purchase.status_pending') ?? 'Pending' }}</span>
                                @elseif($purchase->status == 'partial')
                                    <span class="badge badge-info">{{ __('purchase.status_partial') ?? 'Partial' }}</span>
                                @elseif($purchase->status == 'completed')
                                    <span class="badge badge-success">{{ __('purchase.status_completed') ?? 'Completed' }}</span>
                                @elseif($purchase->status == 'cancelled')
                                    <span class="badge badge-danger">{{ __('purchase.status_cancelled') ?? 'Cancelled' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('supplier.singular') ?? 'Supplier' }}</th>
                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('general.date') ?? 'Date' }}</th>
                            <td>{{ $purchase->date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('general.created_by') ?? 'Created By' }}</th>
                            <td>{{ $purchase->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('purchase.total_amount') ?? 'Total Amount' }}</th>
                            <td class="font-weight-bold ml-2">{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    </table>
                    @if($purchase->notes)
                        <div class="mt-3">
                            <strong>{{ __('purchase.notes') ?? 'Notes' }}:</strong>
                            <p class="text-muted">{{ $purchase->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items & Receive Action -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('purchase.items') ?? 'Items' }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.purchases.receive', $purchase->id) }}" method="POST">
                        @csrf
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('product.singular') ?? 'Product' }}</th>
                                    <th class="text-center">{{ __('purchase.ordered') ?? 'Ord' }}</th>
                                    <th class="text-center">{{ __('purchase.received') ?? 'Rec' }}</th>
                                    <th class="text-center">{{ __('purchase.remaining') ?? 'Rem' }}</th>
                                    <th class="text-right">{{ __('product.cost') ?? 'Cost' }}</th>
                                    <th class="text-right">{{ __('purchase.subtotal') ?? 'Subtotal' }}</th>
                                    @if($purchase->status !== 'completed' && $purchase->status !== 'cancelled')
                                        <th width="150" class="table-primary">{{ __('purchase.receive_now') ?? 'Receive Now' }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->productVariant->product->name ?? '-' }} <br>
                                            <small class="text-muted">{{ $detail->productVariant->name ?? '' }}</small>
                                        </td>
                                        <td class="text-center">{{ $detail->quantity }}</td>
                                        <td class="text-center text-success">{{ $detail->quantity_received }}</td>
                                        <td class="text-center text-danger font-weight-bold">{{ $detail->remaining_quantity }}</td>
                                        <td class="text-right">{{ number_format($detail->cost, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->subtotal, 2) }}</td>
                                        
                                        @if($purchase->status !== 'completed' && $purchase->status !== 'cancelled')
                                            <td class="table-primary">
                                                @if($detail->remaining_quantity > 0)
                                                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $detail->id }}">
                                                    <input type="number" 
                                                           name="items[{{ $loop->index }}][receive_qty]" 
                                                           class="form-control form-control-sm" 
                                                           min="0" 
                                                           max="{{ $detail->remaining_quantity }}"
                                                           value="0">
                                                @else
                                                    <span class="text-success small"><i class="fas fa-check"></i> {{ __('general.done') ?? 'Done' }}</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($purchase->status !== 'completed' && $purchase->status !== 'cancelled')
                            <div class="mt-3 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-box-open"></i> {{ __('purchase.process_receipt') ?? 'Process Receipt' }}
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                {{ __('purchase.cannot_process') ?? 'This purchase is ' . $purchase->status . '. No further actions can be taken.' }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function() {
                const btn = $(this).find('button[type="submit"]');
                if (btn.length > 0) {
                    btn.prop('disabled', true);
                    btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                }
            });
        });
    </script>
@stop
