@extends('adminlte::page')

@section('title', __('customer.details') ?? 'Customer Details')

@section('content_header')
    <h1>{{ __('customer.details') ?? 'Customer Details' }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
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

    {{-- Deposit Balance Card --}}
    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-wallet"></i> Deposit Balance</h3>
                @can('Manage Deposits')
                <div class="card-tools">
                    <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#topUpModal">
                        <i class="fas fa-plus"></i> Top Up
                    </button>
                </div>
                @endcan
            </div>
            <div class="card-body text-center">
                <h2 class="text-success font-weight-bold">
                    Rp {{ number_format($customer->deposit_balance, 0, ',', '.') }}
                </h2>
                <small class="text-muted">Current usable balance</small>
            </div>
        </div>
    </div>
</div>

{{-- Deposit History --}}
@can('Manage Deposits')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Deposit History</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-center">Type</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Before</th>
                            <th class="text-right">After</th>
                            <th>Method</th>
                            <th>Notes</th>
                            <th>Processed By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->deposits as $deposit)
                        <tr>
                            <td>{{ $deposit->created_at->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $deposit->type === 'top_up' ? 'success' : 'info' }}">
                                    {{ $deposit->type_label }}
                                </span>
                            </td>
                            <td class="text-right font-weight-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
                            <td class="text-right text-muted">Rp {{ number_format($deposit->balance_before, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($deposit->balance_after, 0, ',', '.') }}</td>
                            <td>{{ $deposit->payment_method ? ucfirst($deposit->payment_method) : '-' }}</td>
                            <td>{{ $deposit->notes ?: '-' }}</td>
                            <td>{{ $deposit->processedBy->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($deposit->type === 'top_up')
                                <button class="btn btn-xs btn-warning btn-edit-deposit"
                                    data-id="{{ $deposit->id }}"
                                    data-amount="{{ $deposit->amount }}"
                                    data-method="{{ $deposit->payment_method }}"
                                    data-notes="{{ $deposit->notes }}"
                                    title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-xs btn-danger btn-delete-deposit"
                                    data-id="{{ $deposit->id }}"
                                    title="Delete"><i class="fas fa-trash"></i></button>
                                @else
                                    @if($deposit->transaction_id)
                                    <a href="{{ route('admin.transactions.show', $deposit->transaction_id) }}"
                                       class="btn btn-xs btn-info" title="View Transaction">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">No deposit records yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endcan

{{-- Transaction History --}}
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

{{-- Top Up Modal --}}
@can('Manage Deposits')
<div class="modal fade" id="topUpModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-wallet"></i> Top Up Deposit — {{ $customer->name }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="topUpForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Balance</label>
                        <div class="h5 text-success font-weight-bold">
                            Rp {{ number_format($customer->deposit_balance, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="topup_amount">Top-up Amount (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="topup_amount" name="amount" required placeholder="e.g. 100.000">
                    </div>
                    <div class="form-group">
                        <label for="topup_payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_method" id="topup_payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="topup_notes">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="topup_notes" rows="2" placeholder="e.g. Customer advance payment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="saveTopUpBtn">
                        <i class="fas fa-save"></i> Save Top Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Deposit Modal --}}
<div class="modal fade" id="editDepositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Top-up Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDepositForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_deposit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_amount">Amount (Rp)</label>
                        <input type="text" class="form-control" id="edit_amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_payment_method">Payment Method</label>
                        <select class="form-control" name="payment_method" id="edit_payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="saveEditDepositBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@stop

@section('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
    <script>
        $(document).ready(function(){
            @can('Manage Deposits')
            const topUpAmountAN = new AutoNumeric('#topup_amount', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                minimumValue: '0',
                unformatOnSubmit: true
            });

            const editAmountAN = new AutoNumeric('#edit_amount', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 0,
                minimumValue: '0',
                unformatOnSubmit: true
            });
            @endcan

            // Transaction table filters
            $('#btn-filter').click(function(){
                $('#customer-transactions-table').DataTable().draw();
            });
            $('#btn-reset').click(function(){
                $('#date_from').val('');
                $('#date_to').val('');
                $('#customer-transactions-table').DataTable().draw();
            });

            @can('Manage Deposits')
            // Top Up form submit
            $('#topUpForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#saveTopUpBtn');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url:  '{{ route("admin.deposits.store", $customer->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: topUpAmountAN.getNumber(),
                        payment_method: $('#topup_payment_method').val(),
                        notes: $('#topup_notes').val()
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $('#topUpModal').modal('hide');
                            $('#topUpForm')[0].reset();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(res.message);
                            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Top Up');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Top Up');
                    }
                });
            });

            // Open edit modal
            $(document).on('click', '.btn-edit-deposit', function() {
                $('#edit_deposit_id').val($(this).data('id'));
                editAmountAN.set($(this).data('amount'));
                $('#edit_payment_method').val($(this).data('method'));
                $('#edit_notes').val($(this).data('notes'));
                $('#editDepositModal').modal('show');
            });

            // Submit edit
            $('#editDepositForm').on('submit', function(e) {
                e.preventDefault();
                const id   = $('#edit_deposit_id').val();
                const $btn = $('#saveEditDepositBtn');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url:  '{{ url("admin/deposits") }}/' + id,
                    type: 'POST',
                    data: {
                        _method: 'PUT',
                        _token: '{{ csrf_token() }}',
                        amount: editAmountAN.getNumber(),
                        payment_method: $('#edit_payment_method').val(),
                        notes: $('#edit_notes').val()
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $('#editDepositModal').modal('hide');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(res.message);
                            $btn.prop('disabled', false).text('Save Changes');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                        $btn.prop('disabled', false).text('Save Changes');
                    }
                });
            });

            // Delete top-up
            $(document).on('click', '.btn-delete-deposit', function() {
                const id = $(this).data('id');
                if (!confirm('Delete this top-up record? The deposit balance will be recalculated.')) return;

                $.ajax({
                    url:  '{{ url("admin/deposits") }}/' + id,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                    }
                });
            });
            @endcan
        });
    </script>
@stop
