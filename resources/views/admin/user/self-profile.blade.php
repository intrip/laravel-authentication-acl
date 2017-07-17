@extends('laravel-authentication-acl::admin.layouts.base-2cols')

@section('title')
Admin area: Edit user settings
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        {{-- success message --}}
        <?php $message = Session::get('message'); ?>
        @if( isset($message) )
        <div class="alert alert-success text-center">{!! $message !!}</div>
        @endif
        @if( $errors->has('model') )
        <div class="alert alert-danger text-center">{!! $errors->first('model') !!}</div>
        @endif
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="panel-title bariol-thin"><i class="fa fa-user"></i> User settings</h3>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Update Details
                            </div>
                            <div class="panel-body">
                                {!! Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post']) !!}
                                <div class="form-group">
                                    {!! Form::label('first_name','First name:') !!}
                                    {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('first_name') !!}</span>
                                <!-- last_name text field -->
                                <div class="form-group">
                                    {!! Form::label('last_name','Last name: ') !!}
                                    {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('last_name') !!}</span>
                                <!-- phone text field -->
                                <div class="form-group">
                                    {!! Form::label('phone','Phone: ') !!}
                                    {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('phone') !!}</span>
                                <!-- state text field -->
                                <div class="form-group">
                                    {!! Form::label('state','State: ') !!}
                                    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('state') !!}</span>
                                <!-- city text field -->
                                <div class="form-group">
                                    {!! Form::label('city','City: ') !!}
                                    {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('city') !!}</span>
                                <!-- country text field -->
                                <div class="form-group">
                                    {!! Form::label('country','Country: ') !!}
                                    {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('country') !!}</span>
                                <!-- zip text field -->
                                <div class="form-group">
                                    {!! Form::label('zip','Zip: ') !!}
                                    {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('zip') !!}</span>
                                <!-- address text field -->
                                <div class="form-group">
                                    {!! Form::label('address','Address: ') !!}
                                    {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('address') !!}</span>
                                {!! Form::hidden('user_id', $user_profile->user_id) !!}
                                {!! Form::hidden('id', $user_profile->id) !!}
                                {!! Form::submit('Save',['class' =>'btn btn-info pull-right margin-bottom-30']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        @if(request()->get('user_group') != 'Vendors')
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Other Details
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="pull-right pr30 clearfix col-sm-12">

                                            @if($can_add_fields)
                                            @include('laravel-authentication-acl::admin.user.custom-profile')
                                            @endif

                                        </div>
                                    </div>
                                
                                    {!! Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post']) !!}
                                    {{-- custom profile fields --}}
                                    @php
                                        $custom_profile_data = $custom_profile->getAllTypesWithValues();
                                    @endphp
                                    @foreach($custom_profile_data as $profile_data)
                                    <div class="form-group">
                                        {!! Form::label($profile_data->description) !!}
                                        {!! Form::text("custom_profile_{$profile_data->id}", $profile_data->value, ["class" => "form-control", "required"=>"required"]) !!}
                                        {{-- delete field --}}
                                    </div>
                                    @endforeach
                                    @if(sizeof($custom_profile_data) > 0)
                                        {!! Form::hidden('user_id', $user_profile->user_id) !!}
                                        {!! Form::hidden('id', $user_profile->id) !!}
                                        {!! Form::submit('Save',['class' =>'btn btn-info pull-right margin-bottom-30']) !!}

                                    @endif
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        @endif
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                Reset password
                            </div>
                            <div class="panel-body">
                                {!! Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post']) !!}
                                <!-- password text field -->
                                <div class="form-group">
                                    {!! Form::label('password','new password:') !!}
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                </div>
                                <span class="text-danger">{!! $errors->first('password') !!}</span>
                                <!-- password_confirmation text field -->
                                <div class="form-group">
                                    {!! Form::label('password_confirmation','confirm password:') !!}
                                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                </div>
                                {!! Form::hidden('user_id', $user_profile->user_id) !!}
                                {!! Form::hidden('id', $user_profile->id) !!}
                                {!! Form::submit('Save',['class' =>'btn btn-info pull-right margin-bottom-30']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
