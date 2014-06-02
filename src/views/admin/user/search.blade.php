<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-search"></i> User search</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Jacopo\Authentication\Controllers\UserController@getList','method' => 'get'])}}
<!--        {{FormField::email()}}-->
        <!-- email text field -->
        <div class="form-group">
            {{Form::label('email','Email: ')}}
            {{Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'user email'])}}
        </div>
        <span class="text-danger">{{$errors->first('email')}}</span>
<!--        {{FormField::first_name(['label' => 'First name:'])}}-->
        <!-- first_name text field -->
        <div class="form-group">
            {{Form::label('first_name','First name: ')}}
            {{Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'first name'])}}
        </div>
        <span class="text-danger">{{$errors->first('first_name')}}</span>
<!--        {{FormField::last_name(['label' => 'Last name:'])}}-->
        <!-- last_name text field -->
        <div class="form-group">
            {{Form::label('last_name','Last name:')}}
            {{Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'last name'])}}
        </div>
        <span class="text-danger">{{$errors->first('last_name')}}</span>
<!--        {{FormField::zip(['label' => 'Zip:'])}}-->
        <!-- zip text field -->
        <div class="form-group">
            {{Form::label('zip','Zip:')}}
            {{Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'zip'])}}
        </div>
        <span class="text-danger">{{$errors->first('zip')}}</span>
<!--        {{FormField::code(['label' => 'User code:'])}}-->
        <!-- code text field -->
        <div class="form-group">
            {{Form::label('code','User code:')}}
            {{Form::text('code', null, ['class' => 'form-control', 'placeholder' => 'user code'])}}
        </div>
        <span class="text-danger">{{$errors->first('code')}}</span>
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