@extends('adminlte::page')

@section('title', __('pos.new_transaction') ?? 'New Transaction')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cash-register"></i> {{ __('pos.new_transaction') ?? 'New Transaction' }}</h1>
        <a href="{{ route('admin.pos.dedicated') }}" class="btn btn-outline-primary" target="_blank">
            <i class="fas fa-window-maximize"></i> {{ __('pos.open_dedicated_mode') ?? 'Open Dedicated Mode' }}
        </a>
    </div>
@stop

@section('css')
    @include('admin.pos.partials.styles')
@stop

@section('content')
    @include('admin.pos.partials.content')
@stop

@section('js')
    @include('admin.pos.partials.scripts')
@stop
