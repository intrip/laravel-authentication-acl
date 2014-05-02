<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-search"></i> Group search</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Jacopo\Authentication\Controllers\GroupController@getList','method' => 'get'])}}
        {{FormField::name(['label' => 'Name:'])}}
        {{Form::submit('Search', ["class" => "btn btn-info pull-right
        "])}}
        {{Form::close()}}
    </div>
</div><?php
 