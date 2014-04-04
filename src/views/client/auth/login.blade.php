@extends('authentication::admin.layouts.baseauth')
@section('container')
    <h1>Login amministratore</h1>
    @if($errors && ! $errors->isEmpty() )
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">{{$error}}</div>
            @endforeach
    @endif
    {{Form::open(array('url' => URL::action("Jacopo\Authentication\Controllers\AuthController@postLogin"), 'method' => 'post') )}}
            {{FormField::email(array('label' => "email") )}}
            {{FormField::password(array('label' => 'password' ))}}
            <div class='form-group'>
            {{Form::label('checkbox','Ricordami')}}
            {{Form::checkbox('checkbox',null, null)}}
            </div>
            {{Form::submit('Login', array("class"=>"btn btn-large btn-primary"))}}
    {{Form::close()}}
    {{link_to_action('Jacopo\Authentication\Controllers\AuthController@getReminder','dimenticato la password?')}}
@stop