<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;

class UserSignupValidator extends OverrideConnectionValidator
{
  protected static $messages = ["mail_signup" => "an user with that email already exists."];

  protected static $rules = array(
        "email" => ["required", "email", "mail_signup"],
        "password" => ["required", "min:6", "confirmed"],
        "first_name" => "max:255",
        "last_name" => "max:255",
    );
} 