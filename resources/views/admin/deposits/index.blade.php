@extends('adminlte::page')

@section('title', 'Deposit Management')

@section('content_header')
    <h1><i class="fas fa-wallet"></i> Deposit Management</h1>
@stop

@section('content')
{{-- Summary Cards --}}
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($totalTopUps, 0, ',', '.') }}</h3>
                <p>Total Top-ups</p>
            </div>
            <div class="icon"><i class="fas fa-arrow-circle-up"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>Rp {{ number_format($totalUsages, 0, ',', '.') }}</h3>
                <p>Total Used</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                <p>Outstanding Deposit Balance</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" id="filter_type" name="type">
                            <option value="">All Types</option>
                            <option value="top_up">Top Up</option>
                            <option value="usage">Usage</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="reset-filter">
                                <i class="fas fa-times"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DataTable --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> All Deposit Records</h3>
    </div>
    <div class="card-body">
        {{ $dataTable->table(['class' => 'table table-striped table-bordered w-100'], true) }}
    </div>
</div>

{{-- Edit Top-up Modal --}}
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
                        <input type="number" step="1" class="form-control" id="edit_amount" name="amount" required min="1">
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
@stop

@section('js')
{{ $dataTable->scripts() }}
<script>
$(function() {
    // Apply filters
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        window.LaravelDataTables['deposits-table'].draw();
    });

    // Reset filters
    $('#reset-filter').on('click', function() {
        $('#filter_type').val('');
        $('#date_from').val('');
        $('#date_to').val('');
        window.LaravelDataTables['deposits-table'].draw();
    });

    // Open edit modal
    $(document).on('click', '.btn-edit-deposit', function() {
        const id     = $(this).data('id');
        const amount = $(this).data('amount');
        const method = $(this).data('method');
        const notes  = $(this).data('notes');

        $('#edit_deposit_id').val(id);
        $('#edit_amount').val(amount);
        $('#edit_payment_method').val(method);
        $('#edit_notes').val(notes);
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
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    $('#editDepositModal').modal('hide');
                    window.LaravelDataTables['deposits-table'].draw();
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
        if (!confirm('Delete this top-up record? The customer balance will be recalculated.')) return;

        $.ajax({
            url:  '{{ url("admin/deposits") }}/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    window.LaravelDataTables['deposits-table'].draw();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong');
            }
        });
    });
});
</script>
@stop
