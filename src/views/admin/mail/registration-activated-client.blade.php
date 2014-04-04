<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Registration request on: {{Config::get('authentication::app_name')}}</h2>
<div>
    <h3>Dear: {{$body['first_name']}}</h3>
    <strong>You account has been created. You can now login to our website using the <a href="{{URL::to('/user/login')}}">Following link</a>.
    <br/>
    <strong>Please find your account details below: </strong>
    <ul>
        <li>Username: {{$body['email']}}</li>
        <li>Password: {{$body['password']}}</li>
    </ul>
</div>
</body>
</html>