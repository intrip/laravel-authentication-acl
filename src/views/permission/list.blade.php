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
                <span class="glyphicon glyphicon-lock
"></span> {{$permission->description}}
                @if($permission->editable)
                <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" ><span class="glyphicon glyphicon-trash pull-right margin-left-5 delete">cancella </span></a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission', ['id' => $permission->id])}}"><span class="glyphicon glyphicon-edit pull-right">modifica </span></a>
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