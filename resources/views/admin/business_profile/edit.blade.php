@extends('adminlte::page')

@section('title', 'Business Profile')

@section('content_header')
    <h1>Business Profile Settings</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Business Profile</h3>
                </div>
                <form action="{{ route('admin.business-profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="business_name">Business Name</label>
                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', $businessProfile->business_name) }}" required>
                            @error('business_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="business_address">Business Address</label>
                            <textarea class="form-control @error('business_address') is-invalid @enderror" id="business_address" name="business_address" rows="3">{{ old('business_address', $businessProfile->business_address) }}</textarea>
                            @error('business_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="business_email">Business Email</label>
                            <input type="email" class="form-control @error('business_email') is-invalid @enderror" id="business_email" name="business_email" value="{{ old('business_email', $businessProfile->business_email) }}">
                            @error('business_email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="business_website">Business Website</label>
                            <input type="url" class="form-control @error('business_website') is-invalid @enderror" id="business_website" name="business_website" value="{{ old('business_website', $businessProfile->business_website) }}">
                            @error('business_website')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="business_phone">Business Phone</label>
                            <input type="text" class="form-control @error('business_phone') is-invalid @enderror" id="business_phone" name="business_phone" value="{{ old('business_phone', $businessProfile->business_phone) }}">
                            @error('business_phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">Choose file</label>
                                </div>
                            </div>
                            @if($businessProfile->logo_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $businessProfile->logo_path) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            @error('logo')
                                <span class="text-danger d-block mt-1">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="footer_text">Footer Text (for Receipts)</label>
                            <textarea class="form-control @error('footer_text') is-invalid @enderror" id="footer_text" name="footer_text" rows="2">{{ old('footer_text', $businessProfile->footer_text) }}</textarea>
                            @error('footer_text')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@stop
