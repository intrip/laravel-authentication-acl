@extends('authentication::layouts.base-2cols')

@section('title')
    Admin area: users list
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
    <hr/>
    @if(! $users->isEmpty() )
        <ul class="list-group">
        @foreach($users as $user)
            <li class="list-group-item">
                <i class="
fa fa-envelope fa-2x"></i> {{$user->email}} <i class="fa fa-user fa-2x
"></i> {{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}
                <i class="fa fa-unlock fa-2x margin-left-5"></i> Active:{{$user->activated ? 'Sì' : 'No'}}
                @if(! $user->protected)
                <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" class="margin-left-5 pull-right delete"><i class="fa fa-trash-o fa-2x"></i>delete</a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser', ['id' => $user->id])}}" class="pull-right margin-left-5"><i class="fa fa-pencil-square-o fa-2x"></i>edit</a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editProfile', ['user_id' => $user->id])}}" class="pull-right"><i class="fa fa-user fa-2x"></i> profile </a>
                @endif
                <span class="clearfix"></span>
            </li>
            @endforeach
        </ul>
    @else
        <h5>Non ci sono utenti presenti nel sistema.</h5>
    @endif
    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser')}}" class="btn btn-primary pull-right">Add New</a>
</div>
@stop

@section('footer_scripts')
    <script>
        $(".delete").click(function(){
            return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
        });
    </script>
@stop