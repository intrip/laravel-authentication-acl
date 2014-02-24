@extends('authentication::layouts.base')

@section('container')
    <div class="col-md-2 nav bs-sidenav">
        @include('authentication::layouts.sidebar')
    </div>
    <div class="col-md-10">
        @yield('content')
    </div>
@stop