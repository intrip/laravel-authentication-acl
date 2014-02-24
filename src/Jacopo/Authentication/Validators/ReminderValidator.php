<?php namespace Jacopo\Authentication\Validators;

use Jacopo\Library\Validators\AbstractValidator;

class ReminderValidator extends AbstractValidator
{

    protected static $rules = array(
        "email" => "required",
    );
} 