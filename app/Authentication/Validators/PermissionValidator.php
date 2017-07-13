<?php namespace LaravelAcl\Authentication\Validators;

use Event;
use LaravelAcl\Library\Validators\AbstractValidator;

class PermissionValidator extends AbstractValidator
{
    protected static $table_name = 'permission';

    protected static $rules = array(
        "description" => ["required", "max:255"],
        "permission" => ["required", "max:255"],
    );

    public function __construct()
    {
        Event::listen('validating', function ($input) {
            $table_name = isset($input['_table_name']) ? $input['_table_name'] : $this::$table_name;

            static::$rules["permission"][] = "unique:{$table_name},permission,{$input['id']}";
        });
    }
} 