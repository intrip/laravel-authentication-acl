@extends('laravel-authentication-acl::client.layouts.base-fullscreen')
@section ('title')
404
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-exclamation-triangle"></i> 404</h1>
        <p class="lead">
            Sorry, this is not the page you were looking for.
            <a href="{{URL::to('/')}}"><i class="fa fa-home"></i> Go to homepage</a>
        </p>
    </div>
</div>
@stop