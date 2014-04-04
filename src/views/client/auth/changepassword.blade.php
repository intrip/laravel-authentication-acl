@extends('authentication::admin.layouts.baseauth')
@section('container')
    <h1>Modifica password</h1>
    <?php $message = Session::get('message'); ?>
    @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
    @endif
    @if($errors && ! $errors->isEmpty() )
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
        @endforeach
    @endif
    {{Form::open(array('url' => URL::action("Jacopo\Authentication\Controllers\AuthController@postChangePassword"), 'method' => 'post') )}}
            {{FormField::password(array('label' => 'password' ))}}
            {{Form::hidden('email',$email)}}
            {{Form::hidden('token',$token)}}
            {{Form::submit('Cambia', array("class"=>"btn btn-large btn-primary"))}}
    {{Form::close()}}
@stop