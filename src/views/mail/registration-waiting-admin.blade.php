<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>User registration request on: {{Config::get('authentication::app_name')}}</h2>
<div>
    <strong>The user: {{$body['email']}}</strong>
    <br/>
    Sent a registration request to your website. Check the profile and proceed with the activation.
    <br/>
    @if(! empty($body['comments']) )
        Comments : {{$body['comments']}}<br/>
    @endif
    <a href="{{URL::action('Palmabit\Authentication\Controllers\UserController@editUser', [ 'id' => $body['id'] ] )}}" target="_blank">Check the user</a>
</div>
</body>
</html>