<?php

Validator::extend('mail_signup', 'Jacopo\Authentication\Validators\UserSignupEmailValidator@validateEmailUnique');
Validator::extend('captcha', 'Jacopo\Authentication\Classes\Captcha\GregWarCaptchaValidator@validateCaptcha');