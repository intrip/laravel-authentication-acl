<?php namespace Jacopo\Authentication\Validators;

class UserSignupValidator extends AbstractValidator
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