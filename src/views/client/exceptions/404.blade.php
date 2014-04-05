@extends('authentication::client.layout.base')

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel middle-panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">Ooops....</h3>
                </div>
                <div class="panel-body">
                    <h1>404!</h1>
                    <p class="error-para">Sorry, this is not the page you were looking for.
                    </p>
                    <br>
                    <a href="{{URL::to('/')}}" class="btn btn-lg btn-warning btn-block"><i class="fa fa-home"></i> Go to home</a>
                </div>
            </div>
        </div>
    </div>
@stop