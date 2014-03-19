<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;
use Event;


class UserValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "email" => ["required", "email"],
        "password" => ["confirmed"]
    );

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            // check if the input comes form the correct form
            if(!isset($input['form_name']) || $input['form_name']!='user')
                return true;

            static::$rules["email"][] = "unique:users,email,{$input['id']}";

            if(empty($input["id"]))
            {
                static::$rules["password"][] = "required";
            }
        });
    }
} 