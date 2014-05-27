@extends('laravel-authentication-acl::client.layouts.base')
@section('title')
User login
@stop
@section('content')
<div class="row centered-form">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title bariol-thin">Login to {{Config::get('laravel-authentication-acl::app_name')}}</h3>
            </div>
            <?php $message = Session::get('message'); ?>
            @if( isset($message) )
            <div class="alert alert-success">{{$message}}</div>
            @endif
            @if($errors && ! $errors->isEmpty() )
            @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
            @endforeach
            @endif
            <div class="panel-body">
                {{Form::open(array('url' => URL::action("Jacopo\Authentication\Controllers\AuthController@postClientLogin"), 'method' => 'post') )}}
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                {{Form::email('email', '', ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email address', 'required', 'autocomplete' => 'off'])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                {{Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'required', 'autocomplete' => 'off'])}}
                            </div>
                        </div>
                    </div>
                </div>
                {{Form::label('remember','Remember me')}}
                {{Form::checkbox('remember')}}
                <input type="submit" value="Login" class="btn btn-info btn-block">
                {{Form::close()}}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 margin-top-10">
        {{link_to_action('Jacopo\Authentication\Controllers\AuthController@getReminder','Forgot password?')}}
        or <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@signup')}}"><i class="fa fa-sign-in"></i> Signup here</a>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
@stop