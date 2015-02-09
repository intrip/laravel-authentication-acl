<?php
// mail validator
Validator::extend('mail_signup', 'Jacopo\Authentication\Validators\UserSignupEmailValidator@validateEmailUnique');
// captcha validator
use Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator;
$captcha_validator = App::make('captcha_validator');
Validator::extend('captcha', 'Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator@validateCaptcha', $captcha_validator->getErrorMessage() );