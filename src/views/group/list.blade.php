@extends('authentication::layouts.base-2cols')

@section('title')
    Admin area: Groups list
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="col-md-8">
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
            <h3>{{Input::all() ? 'Search results:' : 'Groups list'}}</h3>
            @if( ! $groups->isEmpty() )
                <ul class="list-group">
                @foreach($groups as $group)
                    <li class="list-group-item">
                    <i class="fa fa-group fa-2x"></i>{{$group->name}}
                        @if(! $group->protected)
                            <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@deleteGroup',['id' => $group->id, '_token' => csrf_token()])}}" class="pull-right margin-left-5 delete"><i class="fa fa-trash-o fa-2x"></i> delete</a>
                            <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@editGroup', ['id' => $group->id])}}" class="pull-right"><i class="fa fa-edit fa-2x"></i>edit </a>
                            <span class="clearfix"></span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @else
                <h5>No results found.</h5>
            @endif
            <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@editGroup')}}" class="btn btn-primary pull-right">Add New</a>
            <div class="paginator">
                {{$groups->appends(Input::except(['page']) )->links()}}
            </div>
        </div>
        <div class="col-md-4">
            @include('authentication::group.search')
        </div>
    </div>
</div>
@stop

@section('footer_scripts')
    <script>
        $(".delete").click(function(){
            return confirm("Sei sicuro di volere eliminare l'elemento selezionato?");
        });
    </script>
@stop