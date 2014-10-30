<?php
// user signup data
$first_name = "jacopo";
$last_name = "beschi";
$email = "fake@email.com";
$password = $password_confirmation = "password";

//@todo fix the error that make use the wrong connection
// then make an api to get session data that works only for testing-acceptance env and check the captcha
Config::override('laravel-authentication-acl::captcha_signup',false);

$I = new AcceptanceTester($scenario);
$I->wantTo('signup a new user');
$I->amOnPage('/user/signup');
$I->see('Please sign up for Authenticator');

$I->fillField('first_name',$first_name);
$I->fillField('last_name',$last_name);
$I->fillField('email',$email);
$I->fillField('password',$password);
$I->fillField('password_confirmation',$password_confirmation);

$I->click('Register');

$I->see('Request received');

//@todo make it work with captcha and without and without and with email confirmation