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
            <div class="navbar-form navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="{{URL::to('/admin/users/list')}}"><i class="fa fa-lock fa-user"></i> Edit profile</a>
                        </li>
                        <li class="divider"></li>
                            <a href="{{URL::to('/user/logout')}}" class="btn btn-warning">Logout</a>
                        </li>
                    </ul>
                </il>
            </div>
        </div><!--/.nav-collapse -->
    </div>
</div>