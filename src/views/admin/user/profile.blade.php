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
                    <div class="row">
                        <div class="col-md-6">
                            <h4><i class="fa fa-picture-o"></i> Avatar</h4>
                            <div class="profile-avatar">
                                <img src="{{$user_profile->presenter()->avatar_src}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{Form::open(['action' => 'Jacopo\Authentication\Controllers\UserController@changeAvatar', 'method' => 'POST', 'files' => true])}}
                            {{Form::label('avatar',$user_profile->avatar ? 'Change avatar: ' : 'Upload avatar: ')}}
                            <div class="form-group">
                                {{Form::file('avatar', ['class' => 'form-control'])}}
                                <span class="text-danger">{{$errors->first('avatar')}}</span>
                            </div>
                            {{Form::hidden('user_id', $user_profile->user_id)}}
                            {{Form::hidden('user_profile_id', $user_profile->id)}}
                            <div class="form-group">
                                {{Form::submit('Update avatar', ['class' => 'btn btn-info'])}}
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>
                    <h4><i class="fa fa-cubes"></i> User data</h4>
                        {{Form::model($user_profile,['route'=>'users.profile.edit', 'method' => 'post'])}}
                            {{FormField::code(["label" => "User code:"])}}
                            <span class="text-danger">{{$errors->first('code')}}</span>
                            {{FormField::first_name(["label" => "First name:"])}}
                            <span class="text-danger">{{$errors->first('first_name')}}</span>
                            {{FormField::last_name(["label" => "Last name:"])}}
                            <span class="text-danger">{{$errors->first('last_name')}}</span>
                            {{FormField::phone(["label" => "Phone:"])}}
                            <span class="text-danger">{{$errors->first('phone')}}</span>
                            {{FormField::state(["label" => "State: "])}}
                            <span class="text-danger">{{$errors->first('state')}}</span>
                            {{FormField::vat(["label" => "Vat: "])}}
                            <span class="text-danger">{{$errors->first('vat')}}</span>
                            {{FormField::city(["label" => "City: "])}}
                            <span class="text-danger">{{$errors->first('city')}}</span>
                            {{FormField::country(["label" => "Country: "])}}
                            <span class="text-danger">{{$errors->first('country')}}</span>
                            {{FormField::zip(["label" => "Zip: "])}}
                            <span class="text-danger">{{$errors->first('zip')}}</span>
                            {{FormField::address(["label" => "Address: "])}}
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
