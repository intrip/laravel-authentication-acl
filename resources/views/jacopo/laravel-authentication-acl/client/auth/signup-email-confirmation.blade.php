@extends('laravel-authentication-acl::client.layouts.base-fullscreen')
@section ('title')
Registration request received
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-download"></i> Request received</h1>
        <p class="lead">You account has been created. However, before you can use it you need to confirm your email address.<br/>
            We sent you a confirmation email, please check your inbox.</p>
    </div>
</div>
@stop