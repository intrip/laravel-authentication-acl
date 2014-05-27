<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{ HTML::style('packages/jacopo/laravel-authentication-acl/css/mail-base.css') }}
    {{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}
</head>
<body>
<h2><i class="fa fa-pencil"></i> Registration request on: {{Config::get('laravel-authentication-acl::app_name')}}</h2>
<div>
    <h3>Dear: {{$body['first_name']}}</h3>
    <strong>You account has been created. However, before you can use it you need to confirm your email address first by clicking the
        <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@emailConfirmation', ['token' => $body['token'], 'email' => $body['email'] ] )}}">Following link</a></strong>
    <br/>
    <strong>Please find your account details below: </strong>
    <ul>
        <li>Username: {{$body['email']}}</li>
        <li>Password: {{$body['password']}}</li>
    </ul>
</div>
</body>
</html>