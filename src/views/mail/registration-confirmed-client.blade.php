<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Welcome to {{Config::get('authentication::app_name')}}</h2>
<div>
    <h3>Dear: {{$body['email']}}</h3>
    <strong>You email has been confirmed succesfully.</strong>
    You can go to our website using the <a href="{{URL::to('/')}}" target="_blank">Following link</a>
</div>
</body>
</html>