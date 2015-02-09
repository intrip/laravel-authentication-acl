<div class="row">
    <div class="col-md-12 margin-bottom-12">
        <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission')}}" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Add New</a>
    </div>
</div>
@if( ! $permissions->isEmpty() )
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Permission description</th>
            <th>Permission name</th>
            <th>Operations</th>
        </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td style="width:45%">{{$permission->description}}</td>
                <td style="width:45%">{{$permission->permission}}</td>
                <td style="witdh:10%">
                    @if(! $permission->protected)
                    <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@editPermission', ['id' => $permission->id])}}"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                    <a href="{{URL::action('Jacopo\Authentication\Controllers\PermissionController@deletePermission',['id' => $permission->id, '_token' => csrf_token()])}}" class="margin-left-5"><i class="fa fa-trash-o delete fa-2x"></i></a>
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
<span class="text-warning"><h5>No permissions found.</h5></span>
@endif