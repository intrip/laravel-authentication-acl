@extends('laravel-authentication-acl::client.layouts.base-fullscreen')
@section ('title')
401
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-shield"></i> 401</h1>
        <p class="lead">
            Sorry, you don't have permission to see this page
            <a href="{{URL::to('/')}}"><i class="fa fa-home"></i> Go to homepage</a>
        </p>
    </div>
</div>
@stop