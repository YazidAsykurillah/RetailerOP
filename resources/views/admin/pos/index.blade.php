@extends('adminlte::page')

@section('title', 'New Transaction')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-cash-register"></i> New Transaction</h1>
        <a href="{{ route('admin.pos.dedicated') }}" class="btn btn-outline-primary" target="_blank">
            <i class="fas fa-window-maximize"></i> Open Dedicated Mode
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
