<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">User search</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Jacopo\Authentication\Controllers\UserController@getList','method' => 'get'])}}
        {{FormField::email()}}
        {{FormField::first_name(['label' => 'First name:'])}}
        {{FormField::last_name(['label' => 'Last name:'])}}
        {{FormField::zip(['label' => 'Zip:'])}}
        {{FormField::code(['label' => 'User code:'])}}
        <div class="form-group">
            {{Form::label('activated', 'Active: ')}}
            {{Form::select('activated', ['' => '', 1 => 'Yes', 0 => 'No'], Input::get('activated',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('group_id', 'Group: ')}}
            {{Form::select('group_id', array_merge([""=>""], $group_values), Input::get('group_id',''), ["class" => "form-control"])}}
        </div>
        {{Form::reset('Reset', ["class" => "btn btn-default
        "])}}
        {{Form::submit('Search', ["class" => "btn btn-primary
            "])}}
        {{Form::close()}}
    </div>
</div>