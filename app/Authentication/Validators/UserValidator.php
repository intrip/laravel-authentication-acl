<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;
use Event;


class UserValidator extends OverrideConnectionValidator
{
    protected static $rules = [
        "email" => ["required", "email"],
        "password" => ["confirmed"]
    ];

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            // check if the input comes form the correct form
            if(!isset($input['form_name']) || $input['form_name']!='user')
                return true;

            if(empty($input["id"]))
            {
                static::$rules["password"][] = "required";
                static::$rules["email"][] = "unique:users,email";
            }
            else
            {
                static::$rules["email"][] = "unique:users,email,{$input['id']}";
            }
        });

        // make unique keys for email and password
        static::$rules["email"] = array_unique(static::$rules["email"]);
        static::$rules["password"] = array_unique(static::$rules["password"]);
    }

    /**
     * User to reset static rules to default values
     */
    public static function resetStatic()
    {
        static::$rules = [
                "email" => ["required", "email"],
                "password" => ["confirmed"]
        ];
    }
} 