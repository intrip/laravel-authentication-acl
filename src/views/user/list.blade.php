@extends('authentication::layouts.base-2cols')

@section('title')
    Admin area: lista utenti
@stop

@section('content')

<div class="row">
    {{-- print messages --}}
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
    <h3>Lista utenti</h3>
    @if(! $users->isEmpty() )
        <ul class="list-group">
        @foreach($users as $user)
            <li class="list-group-item">
                <span class="
glyphicon glyphicon-comment"></span> {{$user->email}} <span class="glyphicon glyphicon-user
"></span> {{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}
                <span class="glyphicon glyphicon-lock margin-left-5">Attivo:{{$user->activated ? 'Sì' : 'No'}}</span>
                @if(! $user->protected)
                <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" ><span class="glyphicon glyphicon-trash pull-right margin-left-5 delete">cancella </span></a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser', ['id' => $user->id])}}"><span class="glyphicon glyphicon-edit pull-right">modifica </span></a>
                @endif
                <span class="clearfix"></span>
            </li>
            @endforeach
        </ul>
    @else
        <h5>Non ci sono utenti presenti nel sistema.</h5>
    @endif
    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser')}}" class="btn btn-primary pull-right">Aggiungi</a>
</div>
@stop

@section('footer_scripts')
    <script>
        $(".delete").click(function(){
            return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
        });
    </script>
@stop