@extends('authentication::layouts.base-2cols')

@section('title')
    Admin area: lista permessi
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
    <h3>Lista permessi</h3>
    @if( ! $permissions->isEmpty() )
        <ul class="list-group">
        @foreach($permissions as $permission)
            <li class="list-group-item">
                <i class="fa fa-lock
 fa-2x"></i> {{$permission->description}}
                @if(! $permission->protected)
                <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" class="pull-right margin-left-5"><i class="fa fa-trash-o delete fa-2x"></i>cancella </a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission', ['id' => $permission->id])}}" class="pull-right"><i class="fa fa-pencil-square-o fa-2x"></i> modifica </a>
                @endif
                <span class="clearfix"></span>
            </li>
            @endforeach
        </ul>
    @else
        <h5>Non ci sono permessi presenti nel sistema.</h5>
    @endif
    <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission')}}" class="btn btn-primary pull-right">Aggiungi</a>
</div>
@stop

@section('footer_scripts')
    <script>
        $(".delete").click(function(){
            return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
        });
    </script>
@stop