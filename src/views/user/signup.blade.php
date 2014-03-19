<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

{{ HTML::style('packages/jacopo/authentication/css/bootstrap.min.css') }}
{{ HTML::style('packages/jacopo/authentication/css/style.css') }}
{{ HTML::style('packages/jacopo/authentication/css/baselayout.css') }}
{{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="container">
    <div class="row centered-form">
        <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please sign up for {{Config::get('authentication::app_name')}}</h3>
                </div>
                @if( isset($message) )
                <div class="alert alert-success">{{$message}}</div>
                @endif
                <div class="panel-body">
                    {{Form::open(["action" => 'Jacopo\Authentication\Controllers\UserController@postSignup', "method" => "POST"])}}
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    {{Form::text('first_name', '', ['id' => 'first_name', 'class' => 'form-control', 'placeholder' => 'First Name', 'required'])}}
                                    <span class="text-danger">{{$errors->first('first_name')}}</span>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    {{Form::text('last_name', '', ['id' => 'last_name', 'class' => 'form-control', 'placeholder' => 'Last Name', 'required'])}}
                                    <span class="text-danger">{{$errors->first('last_name')}}</span>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {{Form::email('email', '', ['id' => 'email', 'class' => 'form-control', 'placeholder' => 'Email address', 'required'])}}
                            <span class="text-danger">{{$errors->first('email')}}</span>

                        </div>

                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    {{Form::password('password', ['id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'required'])}}
                                    <span class="text-danger">{{$errors->first('password')}}</span>

                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <div class="form-group">
                                    {{Form::password('password_confirmation', ['id' => 'password_confirmation', 'class' => 'form-control', 'placeholder' => 'Confirm Password', 'required'])}}

                                </div>
                            </div>
                        </div>

                        <input type="submit" value="Register" class="btn btn-info btn-block">

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>