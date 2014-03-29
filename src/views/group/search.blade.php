<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Group search</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Jacopo\Authentication\Controllers\GroupController@getList','method' => 'get'])}}
        {{FormField::name(['label' => 'Name:'])}}
        {{Form::submit('Search', ["class" => "btn btn-primary pull-right
        "])}}
        {{Form::close()}}
    </div>
</div><?php
 