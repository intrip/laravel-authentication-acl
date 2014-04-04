@extends('authentication::admin.layouts.base-2cols')

@section('title')
Admin area: edit permission
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-lock"></i> {{isset($permission->id) ? 'Edit' : 'Create'}} permission</h3>
        </div>
        <div class="panel-body">
            {{Form::model($permission, [ 'url' => [URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission'), $permission->id], 'method' => 'post'] ) }}
            {{FormField::description(["label" => "Description: *", 'id' => 'slugme', "type" => "text"])}}
            <span class="text-danger">{{$errors->first('description')}}</span>
            {{FormField::permission(["label" => "Permission: *", 'id' => 'slug'])}}
            <span class="text-danger">{{$errors->first('permission')}}</span>
            {{Form::hidden('id')}}
            <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Delete</a>
            {{Form::submit('Save', array("class"=>"btn btn-primary pull-right "))}}
            {{Form::close()}}
        </div>
    </div>
</div>
@stop

@section('footer_scripts')
{{HTML::script('packages/jacopo/authentication/js/slugit.js')}}
<script>
    $(".delete").click(function(){
        return confirm("Are you sure to delete this item?");
    });
    $(function(){
        $('#slugme').slugIt();
    });
</script>
@stop