<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{ HTML::style('packages/jacopo/laravel-authentication-acl/css/mail-base.css') }}
    {{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}
</head>
<body>
   <h2>Invitation to join {{Config::get('laravel-authentication-acl::app_name')}}</h2>
<div>
    <strong>{{ $body['admin_name'] }} has created an account for you. Before you can use it you'll need to create a password using the link below.</strong>
	<br/>
	<br/>
    <strong>Your new account details: </strong>
    <ul>
        <li>Username: {{$body['email']}}</li>
		<li>Password: <a href="{{URL::action('Jacopo\Authentication\Controllers\AuthController@getChangePassword', ['token' => $body['token'], 'email' => $body['email'] ] )}}">Create Password</a></li>
    </ul>
	<p>The password link is only valid for 24 hours. If you miss this opportunity use the <a href="{{ URL::to('user/recover-password') }}">"Forgot Password"</a> link on the login page.</p>
</div>
</body>
</html>
