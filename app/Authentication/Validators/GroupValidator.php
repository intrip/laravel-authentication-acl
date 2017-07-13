<?php namespace LaravelAcl\Authentication\Validators;

use Event;
use LaravelAcl\Library\Validators\AbstractValidator;

class GroupValidator extends AbstractValidator
{
    protected static $table_name = 'groups';

    protected static $rules = array(
        "name" => ["required"],
    );

    public function __construct()
    {
        Event::listen('validating', function ($input) {
            $table_name = isset($input['_table_name']) ? $input['_table_name'] : $this::$table_name;
            static::$rules["name"][] = "unique:{$table_name},name,{$input['id']}";
        });
    }
} 