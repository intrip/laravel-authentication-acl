<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
        {{ HTML::style('packages/jacopo/laravel-authentication-acl/css/mail-base.css') }}
        {{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}
	</head>
	<body>
		<h2>Password recovery for {{Config::get('laravel-authentication-acl::app_name')}}</h2>
		<div>
            We received a request to change your password, if you authorize it {{$body}}<br/>
            Otherwise ignore this email.
		</div>
	</body>
</html>