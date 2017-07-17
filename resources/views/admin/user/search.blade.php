<div class="panel panel-info visible-sm visible-md visible-lg">
    <div class="panel-heading">
        <h3 class="panel-title bariol-thin"><i class="fa fa-search"></i> User search</h3>
    </div>
    <div class="panel-body small sm">
        {!! Form::open(['route' => 'users.list','method' => 'get', 'class'=>'form-inline form-horizontal']) !!}
        <!-- email text field -->
        <div class="form-group">
            {!! Form::label('email','Email: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'user email']) !!}
            </div>
        </div>
        <span class="text-danger">{!! $errors->first('email') !!}</span>
        <!-- first_name text field -->
        <div class="form-group">
            {!! Form::label('first_name','First name: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'first name']) !!}
            </div>
        </div>
        <span class="text-danger">{!! $errors->first('first_name') !!}</span>
        <!-- last_name text field -->
        <div class="form-group">
            {!! Form::label('last_name','Last name:', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'last name']) !!}
            </div>
        </div>
        <span class="text-danger">{!! $errors->first('last_name') !!}</span>
        <!-- zip text field -->
        <div class="form-group">
            {!! Form::label('zip','Zip: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => 'zip']) !!}
            </div>
        </div>
        <span class="text-danger">{!! $errors->first('zip') !!}</span>
        <div class="form-group">
            {!! Form::label('activated', 'Active: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::select('activated', ['' => 'Any', 1 => 'Yes', 0 => 'No'], $request->get('activated',''), ["class" => "form-control"]) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('banned', 'Banned: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <div class="col-sm-2">
            {!! Form::select('banned', ['' => 'Any', 1 => 'Yes', 0 => 'No'], $request->get('banned',''), ["class" => "form-control"]) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('group_id', 'Group: ', ['class'=>'col-sm-3 control-label inline-label']) !!}
            <?php $group_values[""] = "Any"; ?>
            <div class="col-sm-2">
            {!! Form::select('group_id', $group_values, $request->get('group_id',''), ["class" => "form-control"]) !!}
            </div>
        </div>
        <div class="form-group">
<!--            <div class="col-sm-3">
            <a href="{!! URL::route('users.list') !!}" class="btn btn-default search-reset col-sm-3">Reset</a>
            </div>-->
            <div class="col-sm-2">
            {!! Form::submit('Search', ["class" => "btn btn-info", "id" => "search-submit"]) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>