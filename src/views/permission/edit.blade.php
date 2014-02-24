@extends('authentication::layouts.base-2cols')

@section('title')
Admin area: modifica permesso
@stop

@section('content')

<div class="row">
    {{-- model general errors from the form --}}
    @if($errors->has('model') )
    <div class="alert alert-danger">{{$errors->first('model')}}</div>
    @endif

    {{-- successful message --}}
    <?php $message = Session::get('message'); ?>
    @if( isset($message) )
    <div class="alert alert-success">{{$message}}</div>
    @endif
    <h3><i class="glyphicon glyphicon-lock"></i> Modifica permesso</h3>

    {{Form::model($permission, [ 'url' => [URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission'), $permission->id], 'method' => 'post'] ) }}
    {{FormField::description(["label" => "Descrizione:"])}}
    <span class="text-danger">{{$errors->first('description')}}</span>
    {{FormField::permission(["label" => "Permesso:"])}}
    <span class="text-danger">{{$errors->first('permission')}}</span>
    {{Form::hidden('id')}}
    <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Cancella</a>
    {{Form::submit('Salva', array("class"=>"btn btn-primary pull-right "))}}
    {{Form::close()}}
@stop

@section('footer_scripts')
<script>
    $(".delete").click(function(){
        return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
    });
</script>
@stop