@extends('authentication::admin.layouts.baseauth')
@section ('title')
Password recovery success
@stop
@section('container')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-thumbs-up"></i>  Password changed successfully</h1>
        <p class="lead">
            Your password has been changed succesfully. Now you can
            {{link_to('/','Go to homepage')}}
        </p>
    </div>
</div>
@stop