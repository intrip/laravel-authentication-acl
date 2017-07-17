<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ErrandPlace | Admin</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/admin/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/bootstrap-progressbar.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
   <link rel="stylesheet" href="{{ asset('css/mystyles.css') }}">
  </head>

  <body class="">
    <div class="container">
        
        <div class="login-box">
            <div class="row centered-form">
                <div class="row text-center">
                    <a href="/" title="Go back to homepage"><i class="fa fa-home fa-3x"></i></a>
                </div>
                <div class="col-xs-10 col-md-8 col-xs-offset-1 col-md-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title bariol-thin">Login to {!!Config::get('acl_base.app_name')!!}</h3>
                        </div>
                        <?php $message = Session::get('message'); ?>
                        @if( isset($message) )
                        <div class="alert alert-success text-center">{{$message}}</div>
                        @endif
                        @if($errors && ! $errors->isEmpty() )
                        @foreach($errors->all() as $error)
                        <div class="alert alert-danger text-center">{{$error}}</div>
                        @endforeach
                        @endif
                        <div class="panel-body">
                            {!! Form::open(array('url' => URL::route("user.login.process"), 'method' => 'post') ) !!}
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            {!! Form::email('email', '', ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email address', 'required', 'autocomplete' => 'off']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                            {!! Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'required', 'autocomplete' => 'off']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="Login" class="btn btn-info btn-block">
                            {!! Form::close() !!}
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 margin-top-10">
                                    {!! link_to_route('user.recovery-password','Forgot password?') !!} 
                                    or <a href="{!! route('partner.register') !!}"><i class="fa fa-sign-in"></i> Signup here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>








