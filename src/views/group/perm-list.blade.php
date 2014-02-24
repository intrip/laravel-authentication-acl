<h3>Permessi attivi:</h3>
@if( $presenter->permissions )
<ul class="list-group">
    @foreach($presenter->permissions_obj as $permission)
    <li class="list-group-item">
                <span class="glyphicon glyphicon-lock"></span> {{$permission->description}}
        <a href="{{URL::action('Jacopo\Authentication\Controllers\GroupController@editPermission', ['id' => $group->id, '_token' => csrf_token(), 'operation' => '0', 'permissions' => $permission->permission ])}}" ><span class="glyphicon glyphicon-trash pull-right margin-left-5 delete">cancella </span></a>
        <span class="clearfix"></span>
    </li>
    @endforeach
</ul>
@else
<h5>Non ci sono permessi associati al gruppo.</h5>
@endif