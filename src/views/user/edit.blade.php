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
    {{-- print errors --}}
    @if($errors && ! $errors->isEmpty() )
    @foreach($errors->all() as $error)
    <div class="alert alert-danger">{{$error}}</div>
    @endforeach
    @endif
    <h3><i class="glyphicon glyphicon-user"></i> Modifica utente</h3>
    <div class="col-md-6">
    <h3>Dati generali</h3>
    {{Form::model($user, [ 'url' => URL::action('Jacopo\Authentication\Controllers\UserController@postEditUser')] ) }}
    {{FormField::email(["autocomplete" => "off"])}}
    <span class="text-danger">{{$errors->first('email')}}</span>
    {{FormField::password(["autocomplete" => "off", "label" => isset($user->id) ? "modifica password" : "password"])}}
    <span class="text-danger">{{$errors->first('password')}}</span>
    {{FormField::last_name( ["label" => "Nome"] ) }}
    <span class="text-danger">{{$errors->first('last_name')}}</span>
    {{FormField::first_name( ["label" => "Cognome"] ) }}
    <span class="text-danger">{{$errors->first('first_name')}}</span>
    <div class="form-group">
        {{Form::label("activated","Utente attivo")}}
        {{Form::select('activated', ["1" => "Sì", "0" => "No"], (isset($user->activated) && $user->activated) ? $user->activated : "0", ["class"=> "form-control"] )}}
    </div>
    {{Form::hidden('id')}}
    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Cancella</a>
    {{Form::submit('Salva', array("class"=>"btn btn-primary pull-right "))}}
    {{Form::close()}}
    </div>
    <div class="col-md-6">
        <h3>Gruppi</h3>
        @include('authentication::user.groups')
    </div>
@stop

@section('footer_scripts')
<script>
    $(".delete").click(function(){
        return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
    });
</script>
@stop