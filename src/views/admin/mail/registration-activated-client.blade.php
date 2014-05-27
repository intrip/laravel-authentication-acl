
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{ HTML::style('packages/jacopo/laravel-authentication-acl/css/mail-base.css') }}
    {{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}
</head>
<body>
<h2>Welcome to {{Config::get('laravel-authentication-acl::app_name')}}</h2>
<div>
    <h3>Dear: {{$body['email']}}</h3>
    <strong>Your email has been confirmed succesfully.</strong>
    You can now login to the website using the
    <a href="{{URL::to('/login')}}">Following link</a>.
</div>
</body>
</html>