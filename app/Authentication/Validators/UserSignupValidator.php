<?php namespace Jacopo\Authentication\Validators;

use Config;
use Jacopo\Library\Validators\OverrideConnectionValidator;

class UserSignupValidator extends OverrideConnectionValidator
{
  protected static $messages = [
      "mail_signup" => "an user with that email already exists."
  ];

  protected static $rules = [
        "email" => ["required", "email", "mail_signup"],
        "password" => ["required", "min:6", "confirmed"],
        "first_name" => "max:255",
        "last_name" => "max:255",
    ];

    public function __construct()
    {
        $enable_captcha = Config::get('laravel-authentication-acl::captcha_signup');
        if($enable_captcha) $this->addCaptchaRule();
    }

    protected function addCaptchaRule()
    {
        static::$rules["captcha_text"] = "captcha";
    }
} 