<?php namespace LaravelAcl\Authentication\Validators;

use Event;
use LaravelAcl\Library\Validators\AbstractValidator;

class UserValidator extends AbstractValidator
{
    protected static $table_name = 'users';

    protected static $rules = [
        "email" => ["required", "email"],
        "password" => ["confirmed"]
    ];

    public function __construct()
    {
        Event::listen('validating', function ($input) {
            // check if the input comes form the correct form
            if (!isset($input['form_name']) || $input['form_name'] != 'user')
                return true;
            $table_name = isset($input['_table_name']) ? $input['_table_name'] : $this::$table_name;
            if (empty($input["id"])) {
                static::$rules["password"][] = "required";
                static::$rules["email"][] = "unique:{$table_name},email";
            } else {
                static::$rules["email"][] = "unique:{$table_name},email,{$input['id']}";
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