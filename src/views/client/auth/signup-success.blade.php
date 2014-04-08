@extends('authentication::client.layouts.base-fullscreen')
@section ('title')
Registration complete
@stop
@section('content')
<div class="row">
    <div class="col-lg-12 text-center v-center">

        <h1><i class="fa fa-thumbs-up"></i> Congratulations, you successfully registered to {{Config::get('authentication::app_name')}}</h1>
        <p class="lead">Your user has been registered succesfully. Now you can login to the website. {{link_to('/','Go to homepage')}}</p>
    </div>
</div>
@stop