<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-search"></i> User search</h3>
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
            {{Form::select('activated', ['' => 'Any', 1 => 'Yes', 0 => 'No'], Input::get('activated',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('banned', 'Banned: ')}}
            {{Form::select('banned', ['' => 'Any', 1 => 'Yes', 0 => 'No'], Input::get('banned',''), ["class" => "form-control"])}}
        </div>
        <div class="form-group">
            {{Form::label('group_id', 'Group: ')}}
            <?php $group_values[""] = "Any"; ?>
            {{Form::select('group_id', $group_values, Input::get('group_id',''), ["class" => "form-control"])}}
        </div>
        <div class="row form-group">
            <div class="col-md-12">
                {{Form::label('Sorting: ')}}
            </div>
            <div class="col-md-12">
                {{Form::select('order_by', ["" => "select column", "first_name" => "First name", "last_name" => "Last name", "email" => "Email", "last_login" => "Last login", "active" => "Active"], Input::get('order_by',''), ['class' => 'form-control'])}}
            </div>
            <div class="col-md-12 margin-top-10">
                {{Form::select('ordering', ["asc" => "Ascending", "desc" => "descending"], Input::get('ordering','asc'), ['class' =>'form-control'])}}
            </div>
        </div>
        <div class="form-group">
            <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@getList')}}" class="btn btn-default">Reset</a>
            {{Form::submit('Search', ["class" => "btn btn-info"])}}
        </div>
        {{Form::close()}}
    </div>
</div>