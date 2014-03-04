<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\OverrideConnectionValidator;

class ReminderValidator extends OverrideConnectionValidator
{

    protected static $rules = array(
        "email" => "required",
    );
}