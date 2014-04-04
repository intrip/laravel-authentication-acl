<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-user"></i> {{Input::all() ? 'Search results:' : 'Users'}}</h3>
    </div>
    <div class="panel-body">

        <div class="row margin-bottom-12">
            <div class="col-md-11">
                {{Form::open(['method' => 'get', 'class' => 'form-inline'])}}
                    {{Form::select('order_by', ["" => "select column", "first_name" => "First name", "last_name" => "Last name", "email" => "Email", "last_login" => "Last login", "active" => "Active"], Input::get('order_by',''), ['class' => 'form-control'])}}
                    {{Form::select('ordering', ["asc" => "Ascending", "desc" => "descending"], Input::get('ordering','asc'), ['class' =>'form-control'])}}
                    {{Form::submit('Order', ['class' => 'btn btn-primary'])}}
                {{Form::close()}}
            </div>
            <div class="col-md-1 ">
                    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                @if(! $users->isEmpty() )
                <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>First name</th>
                                <th>Last name</th>
                                <th>Active</th>
                                <th>Last login</th>
                                <th>Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{$user->email}}</td>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->activated ? '<i class="fa fa-circle green"></i>' : '<i class="fa fa-circle-o red"></i>'}}</td>
                                <td>{{$user->last_login ? $user->last_login : 'not logged yet.'}}</td>
                                <td>
                                    @if(! $user->protected)
                                        <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser', ['id' => $user->id])}}" class="margin-left-5"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                                        <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" class="margin-left-5 delete"><i class="fa fa-trash-o fa-2x"></i></a>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        @endforeach
                </table>
                @else
                    <span class="text-warning"><h5>No results found.</h5></span>
                @endif
            </div>
            <div class="paginator">
                {{$users->appends(Input::except(['page']) )->links()}}
            </div>
        </div>
    </div>
</div>