@extends('adminlte::page')

@section('title', 'Variant Values - ' . $variantType->name)

@section('content_header')
    <h1>Variant Values</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Parent Type Info Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">{{ $variantType->name }}</h5>
                        <small class="text-muted">Slug: {{ $variantType->slug }} | Sort Order: {{ $variantType->sort_order }}</small>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.variant-types.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Types
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Table Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Values for "{{ $variantType->name }}"</h3>
                <div class="card-tools">
                    <a class="btn btn-success" href="{{ route('admin.variant-types.values.create', $variantType->id) }}">
                        <i class="fas fa-plus"></i> Add Value
                    </a>
                </div>
            </div>
            <div class="card-body">
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
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = window.LaravelDataTables["variant-values-table"];

            $('body').on('click', '.delete', function () {
                var id = $(this).data("id");
                if(confirm("Are you sure you want to delete this value?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/variant-types/' . $variantType->id . '/values') }}/" + id,
                        success: function (data) {
                            table.draw();
                            toastr.success('Value deleted successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Error deleting value.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
