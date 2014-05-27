@extends('laravel-authentication-acl::client.layouts.base-fullscreen')
@section ('title')
500
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-exclamation-triangle"></i> 500</h1>
        <p class="lead">
            Sorry, there was an error.
            <a href="{{URL::to('/')}}"><i class="fa fa-home"></i> Go to homepage</a>
        </p>
    </div>
</div>
@stop