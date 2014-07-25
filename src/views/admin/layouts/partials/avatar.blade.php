@if(isset($logged_user) && $logged_user->user_profile()->count())
    <img src="{{$logged_user->user_profile()->first()->presenter()->avatar($size)}}" width="{{$size}}">
@else
    <img src="{{URL::asset('/packages/jacopo/laravel-authentication-acl/images/avatar.png')}}" width="{{$size}}">
@endif