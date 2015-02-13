<?php
// user signup data
$first_name = "jacopo";
$last_name = "beschi";
$email = "fake@email.com";
$password = $password_confirmation = "password";

Config::override('laravel-authentication-acl::captcha_signup',false);
Config::override('mail.pretend',true);

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

//@todo assert mail data
//@todo make it work with captcha
