@extends('laravel-authentication-acl::admin.layouts.base-2cols')

@section('title')
Admin area: edit permission
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        {{-- model general errors from the form --}}
        @if($errors->has('model') )
        <div class="alert alert-danger">{{$errors->first('model')}}</div>
        @endif

        {{-- successful message --}}
        <?php $message = Session::get('message'); ?>
        @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
        @endif
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title bariol-thin">{{isset($permission->id) ? '<i class="fa fa-pencil"></i> Edit' : '<i class="fa fa-lock"></i> Create'}} permission</h3>
            </div>
            <div class="panel-body">
                {{Form::model($permission, [ 'url' => [URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission'), $permission->id], 'method' => 'post'] ) }}
                <!-- description text field -->
                <div class="form-group">
                    {{Form::label('description','Description: *')}}
                    {{Form::text('description', null, ['class' => 'form-control', 'placeholder' => 'permission description', 'id' => 'slugme'])}}
                </div>
                <span class="text-danger">{{$errors->first('description')}}</span>
                <!-- permission text field -->
                <div class="form-group">
                    {{Form::label('permission','Permission: *')}}
                    {{Form::text('permission', null, ['class' => 'form-control', 'placeholder' => 'permission description', 'id' => 'slug'])}}
                </div>
                <span class="text-danger">{{$errors->first('permission')}}</span>
                {{Form::hidden('id')}}
                <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Delete</a>
                {{Form::submit('Save', array("class"=>"btn btn-info pull-right "))}}
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>
@stop

@section('footer_scripts')
{{HTML::script('packages/jacopo/laravel-authentication-acl/js/vendor/slugit.js')}}
<script>
    $(".delete").click(function(){
        return confirm("Are you sure to delete this item?");
    });
    $(function(){
        $('#slugme').slugIt();
    });
</script>
@stop