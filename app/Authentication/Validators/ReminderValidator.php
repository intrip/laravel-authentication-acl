<?php namespace LaravelAcl\Authentication\Validators;

use LaravelAcl\Library\Validators\OverrideConnectionValidator;

class ReminderValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "password" => ["required", "min:6"],
    );
}