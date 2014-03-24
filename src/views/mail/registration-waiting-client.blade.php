<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Registration request on: {{Config::get('authentication::app_name')}}</h2>
<div>
    <h3>Dear: {{$body['first_name']}}</h3>
    <strong>You account has been created. However, before you can use it you need to confirm your email address first by clicking <a href="{{URL::action('Jacopo\Authentication\Controllers\UserController@emailConfirmation', ['token' => $body['token'], 'email' => $body['email'] ] )}}">the following link</a></strong>
    <br/>
    <strong>Please find your account details below: </strong>
    <ul>
        <li>Username: {{$body['email']}}</li>
        <li>Password: {{$body['password']}}</li>
    </ul>
</div>
</body>
</html>