<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-user"></i> {{Input::all() ? 'Search results:' : 'Users'}}</h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-10 col-md-9 col-sm-9">
                {{Form::open(['method' => 'get', 'class' => 'form-inline'])}}
                    <div class="form-group">
                        {{Form::select('order_by', ["" => "select column", "first_name" => "First name", "last_name" => "Last name", "email" => "Email", "last_login" => "Last login", "active" => "Active"], Input::get('order_by',''), ['class' => 'form-control'])}}
                    </div>
                    <div class="form-group">
                        {{Form::select('ordering', ["asc" => "Ascending", "desc" => "descending"], Input::get('ordering','asc'), ['class' =>'form-control'])}}
                    </div>
                    <div class="form-group">
                        {{Form::submit('Order', ['class' => 'btn btn-default'])}}
                    </div>
                {{Form::close()}}
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3">
                    <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser')}}" class="btn btn-info"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>
      <div class="row">
          <div class="col-md-12">
              @if(! $users->isEmpty() )
              <table class="table table-hover">
                      <thead>
                          <tr>
                              <th>Email</th>
                              <th class="hidden-xs">First name</th>
                              <th class="hidden-xs">Last name</th>
                              <th>Active</th>
                              <th class="hidden-xs">Last login</th>
                              <th>Operations</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach($users as $user)
                          <tr>
                              <td>{{$user->email}}</td>
                              <td class="hidden-xs">{{$user->first_name}}</td>
                              <td class="hidden-xs">{{$user->last_name}}</td>
                              <td>{{$user->activated ? '<i class="fa fa-circle green"></i>' : '<i class="fa fa-circle-o red"></i>'}}</td>
                              <td class="hidden-xs">{{$user->last_login ? $user->last_login : 'not logged yet.'}}</td>
                              <td>
                                  @if(! $user->protected)
                                      <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser', ['id' => $user->id])}}"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                                      <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@deleteUser',['id' => $user->id, '_token' => csrf_token()])}}" class="margin-left-5 delete"><i class="fa fa-trash-o fa-2x"></i></a>
                                  @else
                                      <i class="fa fa-times fa-2x light-blue"></i>
                                      <i class="fa fa-times fa-2x margin-left-12 light-blue"></i>
                                  @endif
                              </td>
                          </tr>
                      </tbody>
                      @endforeach
              </table>
              <div class="paginator">
                  {{$users->appends(Input::except(['page']) )->links()}}
              </div>
              @else
                  <span class="text-warning"><h5>No results found.</h5></span>
              @endif
          </div>
      </div>
    </div>
</div>
