@extends('adminlte::page')

@section('title', 'Edit Supplier')

@section('content_header')
    <h1>Edit Supplier</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Supplier Information</h3>
            </div>
            <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $supplier->name) }}" placeholder="Enter supplier name" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                                       value="{{ old('contact_person', $supplier->contact_person) }}" placeholder="Enter contact person name" required>
                                @error('contact_person')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $supplier->email) }}" placeholder="Enter email address" required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $supplier->phone) }}" placeholder="Enter phone number" required>
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" 
                                  rows="3" placeholder="Enter full address" required>{{ old('address', $supplier->address) }}</textarea>
                        @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website">Website</label>
                                <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" 
                                       value="{{ old('website', $supplier->website) }}" placeholder="https://example.com">
                                @error('website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_id">Tax ID</label>
                                <input type="text" name="tax_id" id="tax_id" class="form-control @error('tax_id') is-invalid @enderror" 
                                       value="{{ old('tax_id', $supplier->tax_id) }}" placeholder="Enter tax identification number">
                                @error('tax_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_terms">Payment Terms</label>
                                <input type="text" name="payment_terms" id="payment_terms" class="form-control @error('payment_terms') is-invalid @enderror" 
                                       value="{{ old('payment_terms', $supplier->payment_terms) }}" placeholder="e.g., Net 30, COD, Net 60">
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
                                           value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Supplier
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
