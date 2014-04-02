@extends('authentication::layouts.base-2cols')

@section('title')
    Admin area: users list
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="col-md-9">
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
            @include('authentication::user.all')

            {{--
            @if(! $users->isEmpty() )
                <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item">
                        <i class="
        fa fa-envelope fa-2x"></i> {{$user->email}}
                        {{$user->first_name || $user->last_name ? '<i class="fa fa-user fa-2x
        "></i>' :''}} {{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}
                        Activated: <i class="fa {{$user->activated ? 'fa-unlock' : 'fa-lock'}} fa-2x margin-left-5"></i>

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
                <h5 class="text-warning">No results found.</h5>
                <div class="paginator">
                    {{$users->appends(Input::except(['page']) )->links()}}
                </div>
            @endif
            --}}
        </div>
        <div class="col-md-3">
            @include('authentication::user.search')
        </div>
    </div>
</div>
@stop

@section('footer_scripts')
    <script>
        $(".delete").click(function(){
            return confirm("Are you sure to delete the item?");
        });
    </script>
@stop