<ul class="nav nav-pills nav-stacked">
@if(isset($sidebar_items) && $sidebar_items)
@foreach($sidebar_items as $name => $link)
    <li class="{{Jacopo\Library\Views\Helper::get_active($link)}}"><a href="{{$link}}">{{$name}}</a></li>
@endforeach
@endif
</ul>
