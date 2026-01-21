@extends('adminlte::page')

@section('title', 'Edit Customer Group')

@section('content_header')
    <h1>Edit Customer Group</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customer-groups.update', $customerGroup->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customerGroup->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $customerGroup->code) }}" required>
                    @error('code')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="percentage_discount">Discount Percentage (%)</label>
                    <input type="number" step="0.01" name="percentage_discount" class="form-control @error('percentage_discount') is-invalid @enderror" value="{{ old('percentage_discount', $customerGroup->percentage_discount) }}" required>
                    @error('percentage_discount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" {{ old('is_default', $customerGroup->is_default) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_default">Set as Default Group</label>
                    </div>
                    <small class="form-text text-muted">If checked, this will be the default group for walk-in customers.</small>
                </div>

                <button type="submit" class="btn btn-primary">Update Group</button>
                <a href="{{ route('admin.customer-groups.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@stop
