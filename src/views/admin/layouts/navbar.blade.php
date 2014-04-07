<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">{{$app_name}}</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                @if(isset($menu_items))
                    @foreach($menu_items as $item)
                        <li class="{{Jacopo\Library\Views\Helper::get_active_route_name($item->getRoute())}}"> <a href="{{$item->getLink()}}">{{$item->getName()}}</a></li>
                    @endforeach
                @endif
            </ul>
            <div class="navbar-nav nav navbar-right">
                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> {{isset($logged_user) ? $logged_user->email : 'User'}} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editProfile',['user_id' => isset($logged_user->id) ? $logged_user->id : ''])}}"><i class="fa fa-user"></i> Your profile</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{URL::action('Jacopo\Authentication\Controllers\AuthController@getLogout')}}"><i class="fa fa-sign-out"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </div><!-- nav-right -->
        </div><!--/.nav-collapse -->
    </div>
</div>