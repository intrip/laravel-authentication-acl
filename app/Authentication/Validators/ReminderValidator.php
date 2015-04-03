<?php namespace LaravelAcl\Authentication\Validators;

use LaravelAcl\Library\Validators\AbstractValidator;

class ReminderValidator extends AbstractValidator
{
    protected static $rules = array(
        "password" => ["required", "min:6"],
    );
}