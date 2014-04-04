@extends('authentication::admin.layouts.base')

@section('container')
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            @include('authentication::admin.layouts.sidebar')
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            @yield('content')
        </div>
    </div>
@stop