<?php namespace LaravelAcl\Authentication\Validators;

use LaravelAcl\Library\Validators\OverrideConnectionValidator;
use Event;

class GroupValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "name" => ["required"],
    );

    public function __construct()
    {
        Event::listen('validating', function($input)
        {
            static::$rules["name"][] = "unique:groups,name,{$input['id']}";
        });
    }
} 