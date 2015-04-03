<?php  namespace LaravelAcl\Authentication\Validators;

use LaravelAcl\Library\Validators\AbstractValidator;

class UserProfileUserValidator extends AbstractValidator{

    protected static $rules = array(
            "password" => ["confirmed", "min:6"],
    );
} 