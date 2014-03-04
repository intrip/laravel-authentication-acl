<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;
use Event;


class UserValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "email" => ["required", "email"],
    );

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            static::$rules["email"][] = "unique:users,email,{$input['id']}";

            if(empty($input["id"]))
            {
                static::$rules["password"][] = "required";
            }
        });
    }
} 