@extends('authentication::layouts.base-2cols')

@section('title')
Admin area: Edit user profile
@stop

@section('content')

<div class="row">
    {{-- successful message --}}
    <?php $message = Session::get('message'); ?>
    @if( isset($message) )
    <div class="alert alert-success">{{$message}}</div>
    @endif
    @if( $errors->has('model') )
        <div class="alert alert-danger">{{$errors->first('model')}}</div>
    @endif

    <h3><i class="fa fa-user"></i> User profile</h3>
    <hr/>
    {{Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post'])}}
        {{FormField::code()}}
        {{FormField::new_password(["label" => "change password:"])}}
        <span class="text-danger">{{$errors->first('password')}}</span>
        {{FormField::first_name(["label" => "first name:"])}}
        <span class="text-danger">{{$errors->first('first_name')}}</span>
        {{FormField::last_name(["label" => "last name:"])}}
        <span class="text-danger">{{$errors->first('last_name')}}</span>
        {{FormField::phone(["label" => "phone:"])}}
        <span class="text-danger">{{$errors->first('phone')}}</span>
        {{FormField::vat(["label" => "vat"])}}
        <span class="text-danger">{{$errors->first('vat')}}</span>
        {{Form::hidden('user_id', $user_profile->user_id)}}
        {{Form::hidden('id', $user_profile->id)}}
        {{Form::submit('Salva',['class' =>'btn btn-primary pull-right margin-bottom-30'])}}
    {{Form::close()}}
</div>
@stop
