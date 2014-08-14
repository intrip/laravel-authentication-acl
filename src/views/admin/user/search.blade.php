<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-search"></i> User search</h3>
    </div>
    <div class="panel-body">
        {{Form::open(['action' => 'Jacopo\Authentication\Controllers\UserController@getList','method' => 'get'])}}
        <!-- email text field -->
        <div class="form-group">
            {{Form::label('email','Email: ')}}
            {{Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'user email'])}}
        </div>
        <span class="text-danger">{{$errors->first('email')}}</span>
        <!-- first_name text field -->
        <div class="form-group">
            {{Form::label('first_name','First name: ')}}
            {{Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'first name'])}}
        </div>
        <span class="text-danger">{{$errors->first('first_name')}}</span>
        <!-- last_name text field -->
        <div class="form-group">
            {{Form::label('last_name','Last name:')}}
            {{Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'last name'])}}
        </div>
        <span class="text-danger">{{$errors->first('last_name')}}</span>
        <!-- zip text field -->
        <div class="form-group">
            {{Form::label('zip','Zip:')}}
            {{Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'zip'])}}
        </div>
        <span class="text-danger">{{$errors->first('zip')}}</span>
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
        @include('laravel-authentication-acl::admin.user.partials.sorting')
        <div class="form-group">
            <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@getList')}}" class="btn btn-default search-reset">Reset</a>
            {{Form::submit('Search', ["class" => "btn btn-info", "id" => "search-submit"])}}
        </div>
        {{Form::close()}}
    </div>
</div>