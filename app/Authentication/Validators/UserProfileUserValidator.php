<?php  namespace Jacopo\Authentication\Validators; 

use Jacopo\Library\Validators\AbstractValidator;

class UserProfileUserValidator extends AbstractValidator{
    protected static $rules = array(
            "password" => ["confirmed", "min:6"],
    );
} 