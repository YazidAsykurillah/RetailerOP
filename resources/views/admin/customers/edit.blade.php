@extends('adminlte::page')

@section('title', __('general.edit') . ' ' . (__('customer.singular') ?? 'Customer'))

@section('content_header')
    <h1>{{ __('general.edit') }} {{ __('customer.singular') ?? 'Customer' }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('customer.name') ?? 'Name' }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_group_id">{{ __('customer.group') ?? 'Customer Group' }}</label>
                            <select name="customer_group_id" class="form-control @error('customer_group_id') is-invalid @enderror" required>
                                <option value="">{{ __('customer.select_group') ?? 'Select Group' }}</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('customer_group_id', $customer->customer_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $group->percentage_discount }}%) @if($group->is_default) - {{ __('general.default') ?? 'Default' }} @endif</option>
                                @endforeach
                            </select>
                            @error('customer_group_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">{{ __('customer.email') ?? 'Email' }}</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">{{ __('customer.phone') ?? 'Phone' }}</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">{{ __('customer.address') ?? 'Address' }}</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('general.active') ?? 'Active' }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('general.update') }} {{ __('customer.singular') ?? 'Customer' }}</button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">{{ __('general.cancel') ?? 'Cancel' }}</a>
            </form>
        </div>
    </div>
@stop
