@extends('adminlte::page')

@section('title', __('general.create') . ' ' . (__('supplier.singular') ?? 'Supplier'))

@section('content_header')
    <h1>{{ __('supplier.create') ?? 'Create New Supplier' }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('supplier.information') ?? 'Supplier Information' }}</h3>
            </div>
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('supplier.name') ?? 'Supplier Name' }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" placeholder="{{ __('supplier.name') ?? 'Supplier Name' }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">{{ __('supplier.contact_person') ?? 'Contact Person' }} <span class="text-danger">*</span></label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                       value="{{ old('contact_person') }}" placeholder="{{ __('supplier.contact_person') ?? 'Contact Person' }}" required>
                                @error('contact_person')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">{{ __('supplier.email') ?? 'Email' }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" placeholder="{{ __('supplier.email') ?? 'Email' }}" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">{{ __('supplier.phone') ?? 'Phone' }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone') }}" placeholder="{{ __('supplier.phone') ?? 'Phone' }}" required>
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">{{ __('supplier.address') ?? 'Address' }} <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" 
                                  rows="3" placeholder="{{ __('supplier.address') ?? 'Address' }}" required>{{ old('address') }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website">{{ __('supplier.website') ?? 'Website' }}</label>
                                <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" 
                                       value="{{ old('website') }}" placeholder="https://example.com">
                                @error('website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_id">{{ __('supplier.tax_id') ?? 'Tax ID' }}</label>
                                <input type="text" name="tax_id" id="tax_id" class="form-control @error('tax_id') is-invalid @enderror" 
                                       value="{{ old('tax_id') }}" placeholder="{{ __('supplier.tax_id') ?? 'Tax ID' }}">
                                @error('tax_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_terms">{{ __('supplier.payment_terms') ?? 'Payment Terms' }}</label>
                                <input type="text" name="payment_terms" id="payment_terms" class="form-control @error('payment_terms') is-invalid @enderror" 
                                       value="{{ old('payment_terms') }}" placeholder="e.g., Net 30, COD, Net 60">
                                @error('payment_terms')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" 
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">{{ __('general.active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('general.save') ?? 'Save Supplier' }}
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
