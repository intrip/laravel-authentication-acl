<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Welcome to {{Config::get('authentication::app_name')}}</h2>
<div>
    Hello, {{ $body['email'] }}
    <strong>Your user has been activated.</strong>
    <br/>
    <strong>Now you can login to the website using the email {{ $body['email']}} and the password you insert in the registration form.</strong>
    <a href="{{URL::to('/')}}" target="_blank">Open website</a>
</div>
</body>
</html>