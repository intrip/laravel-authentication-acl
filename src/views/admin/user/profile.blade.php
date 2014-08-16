@extends('laravel-authentication-acl::admin.layouts.base-2cols')

@section('title')
Admin area: Edit user profile
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        {{-- success message --}}
        <?php $message = Session::get('message'); ?>
        @if( isset($message) )
        <div class="alert alert-success">{{$message}}</div>
        @endif
        @if( $errors->has('model') )
        <div class="alert alert-danger">{{$errors->first('model')}}</div>
        @endif
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="panel-title bariol-thin"><i class="fa fa-user"></i> User profile</h3>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@editUser',['id' => $user_profile->user_id])}}" class="btn btn-info pull-right"><i class="fa fa-pencil-square-o"></i> Edit user</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        @if(! $use_gravatar)
                            @include('laravel-authentication-acl::admin.user.partials.avatar_upload')
                        @else
                            @include('laravel-authentication-acl::admin.user.partials.show_gravatar')
                        @endif
                        <h4><i class="fa fa-cubes"></i> User data</h4>
                        {{Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post'])}}
                        <!-- code text field -->
                        <div class="form-group">
                            {{Form::label('code','User code:')}}
                            {{Form::text('code', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('code')}}</span>
                        <!-- first_name text field -->
                        <div class="form-group">
                            {{Form::label('first_name','First name:')}}
                            {{Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('first_name')}}</span>
                        <!-- last_name text field -->
                        <div class="form-group">
                            {{Form::label('last_name','Last name: ')}}
                            {{Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('last_name')}}</span>
                        <!-- phone text field -->
                        <div class="form-group">
                            {{Form::label('phone','Phone: ')}}
                            {{Form::text('phone', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('phone')}}</span>
                        <!-- state text field -->
                        <div class="form-group">
                            {{Form::label('state','State: ')}}
                            {{Form::text('state', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('state')}}</span>
                        <!-- var text field -->
                        <div class="form-group">
                            {{Form::label('var','Vat: ')}}
                            {{Form::text('var', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('vat')}}</span>
                        <!-- city text field -->
                        <div class="form-group">
                            {{Form::label('city','City: ')}}
                            {{Form::text('city', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('city')}}</span>
                        <!-- country text field -->
                        <div class="form-group">
                            {{Form::label('country','Country: ')}}
                            {{Form::text('country', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('country')}}</span>
                        <!-- zip text field -->
                        <div class="form-group">
                            {{Form::label('zip','Zip: ')}}
                            {{Form::text('zip', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('zip')}}</span>
                        <!-- address text field -->
                        <div class="form-group">
                            {{Form::label('address','Address: ')}}
                            {{Form::text('address', null, ['class' => 'form-control', 'placeholder' => ''])}}
                        </div>
                        <span class="text-danger">{{$errors->first('address')}}</span>
                        {{-- custom profile fields --}}
                        @foreach($custom_profile->getAllTypesWithValues() as $profile_data)
                        <div class="form-group">
                            {{Form::label($profile_data->description)}}
                            {{Form::text("custom_profile_{$profile_data->id}", $profile_data->value, ["class" => "form-control"])}}
                            {{-- delete field --}}
                        </div>
                        @endforeach

                        {{Form::hidden('user_id', $user_profile->user_id)}}
                        {{Form::hidden('id', $user_profile->id)}}
                        {{Form::submit('Save',['class' =>'btn btn-info pull-right margin-bottom-30'])}}
                        {{Form::close()}}
                    </div>
                    <div class="col-md-6 col-xs-12">
                        @if($can_add_fields)
                        @include('laravel-authentication-acl::admin.user.custom-profile')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
