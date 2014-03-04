<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;

class UserSignupValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "email" => ["required", "email", "unique:users,email"],
        "password" => ["required", "min:6"],
        "first_name" => "required|max:255",
        "last_name" => "required|max:255",
        "vat" => "numeric",
        "agree" => "accepted"
    );
} 