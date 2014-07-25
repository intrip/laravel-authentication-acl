<div class="row">
    <div class="col-md-6">
        <h4><i class="fa fa-picture-o"></i> Avatar</h4>
        <div class="profile-avatar">
            <img src="{{$user_profile->presenter()->avatar}}">
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