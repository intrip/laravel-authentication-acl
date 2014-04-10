<div class="row margin-bottom-12">
    <div class="col-md-12">
        <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@editGroup')}}" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Add New</a>
    </div>
</div>
@if( ! $groups->isEmpty() )
<table class="table table-hover">
    <thead>
        <tr>
            <th>Group name</th>
            <th>Operations</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groups as $group)
        <tr>
            <td style="width:90%">{{$group->name}}</td>
            <td style="width:10%">
            @if(! $group->protected)
                <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@editGroup', ['id' => $group->id])}}"><i class="fa fa-edit fa-2x"></i></a>
                <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@deleteGroup',['id' => $group->id, '_token' => csrf_token()])}}" class="margin-left-5 delete"><i class="fa fa-trash-o fa-2x"></i></a>
                <span class="clearfix"></span>
            @else
                <i class="fa fa-times fa-2x light-blue"></i>
                <i class="fa fa-times fa-2x margin-left-12 light-blue"></i>
            @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<span class="text-warning"><h5>No results found.</h5></span>
@endif
<div class="paginator">
    {{$groups->appends(Input::except(['page']) )->links()}}
</div>