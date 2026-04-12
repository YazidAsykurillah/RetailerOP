@extends('adminlte::page')

@section('title', __('general.create') . ' ' . __('brand.singular'))

@section('content_header')
    <h1>{{ __('brand.create') }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('brand.information') }}</h3>
            </div>
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('brand.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="{{ __('brand.name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('general.description') }}</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="{{ __('general.description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="logo">{{ __('brand.logo') }}</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="logo" id="logo" class="custom-file-input @error('logo') is-invalid @enderror" 
                                       accept="image/*">
                                <label class="custom-file-label" for="logo">{{ __('general.choose_file') ?? 'Choose file' }}</label>
                            </div>
                        </div>
                        @error('logo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">{{ __('general.active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('brand.save') ?? 'Save Brand' }}
                    </button>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('general.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var label = e.target.nextElementSibling;
        label.innerText = fileName;
    });
</script>
@stop
