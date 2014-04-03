@extends('authentication::layouts.base-2cols')

@section('title')
Admin area: edit group
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
                <h3 class="panel-title"><i class="fa fa-users"></i> {{isset($group->id) ? 'Edit' : 'Create'}} group</h3>
        </div>
        <div class="panel-body">
            <div class="col-md-6">
                {{-- group base form --}}
                <h4>General data</h4>
                {{Form::model($group, [ 'url' => [URL::action('Jacopo\Authentication\Controllers\GroupController@postEditGroup'), $group->id], 'method' => 'post'] ) }}
                {{FormField::name(["label" => "Name: *"])}}
                <span class="text-danger">{{$errors->first('name')}}</span>
                {{Form::hidden('id')}}
                <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@deleteGroup',['id' => $group->id, '_token' => csrf_token()])}}" class="btn btn-danger pull-right margin-left-5 delete">Delete</a>
                {{Form::submit('Save', array("class"=>"btn btn-primary pull-right "))}}
                {{Form::close()}}
            </div>
            <div class="col-md-6">
            {{-- group permission form --}}
                <h4><i class="fa fa-lock"></i> Permissions</h4>
                {{-- permissions --}}
                @include('authentication::group.perm')
            </div>
       </div>
    </div>
</div>
@stop

@section('footer_scripts')
<script>
    $(".delete").click(function(){
        return confirm("Are you sure to delete this item?");
    });
</script>
@stop