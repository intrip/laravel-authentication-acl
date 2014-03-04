@extends('authentication::layouts.base-2cols')

@section('title')
Admin area: modifica utenti
@stop

@section('content')

<div class="row">
    {{-- successful message --}}
    <?php $message = Session::get('message'); ?>
    @if( isset($message) )
    <div class="alert alert-success">{{$message}}</div>
    @endif
    <h3><i class="fa fa-user"></i> Edit user</h3>
    <hr/>
    <div class="col-md-6">
    <h3><i class="fa fa-desktop"></i> Login data</h3>
    {{Form::model($user, [ 'url' => URL::action('Jacopo\Authentication\Controllers\UserController@postEditUser')] ) }}
    {{FormField::email(["autocomplete" => "off"])}}
    <span class="text-danger">{{$errors->first('email')}}</span>
    {{FormField::password(["autocomplete" => "off", "label" => isset($user->id) ? "Change password" : "password"])}}
    <span class="text-danger">{{$errors->first('password')}}</span>
    <div class="form-group">
        {{Form::label("activated","Utente attivo")}}
        {{Form::select('activated', ["1" => "Sì", "0" => "No"], (isset($user->activated) && $user->activated) ? $user->activated : "0", ["class"=> "form-control"] )}}
    </div>
    {{Form::hidden('id')}}
    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Delete</a>
    {{Form::submit('Save', array("class"=>"btn btn-primary pull-right "))}}
    {{Form::close()}}
    </div>
    <div class="col-md-6">
        <h3><i class="fa fa-users"></i> Grupps</h3>
        @include('authentication::user.groups')

        {{-- group permission form --}}
        <h3><i class="fa fa-lock"></i> Permission</h3>
        {{-- permissions --}}
        @include('authentication::user.perm')
    </div>
@stop

@section('footer_scripts')
<script>
    $(".delete").click(function(){
        return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
    });
</script>
@stop