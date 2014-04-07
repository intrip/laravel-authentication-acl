@extends('authentication::admin.layouts.baseauth')
@section('container')
    <h1>Recupero password</h1>
    <?php $message = Session::get('message'); ?>
    @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
    @endif
    @if($errors && ! $errors->isEmpty() )
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{$error}}</div>
        @endforeach
    @endif
    {{Form::open(array('url' => URL::action("Jacopo\Authentication\Controllers\AuthController@postReminder"), 'method' => 'post') )}}
    {{FormField::email(array('label' => "email") )}}
    {{Form::submit('Invia', array("class"=>"btn btn-large btn-info"))}}
    {{Form::close()}}
@stop