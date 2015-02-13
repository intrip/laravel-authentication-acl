<?php  namespace Jacopo\Authentication\Tests\Unit\Stubs; 
use Jacopo\Library\Validators\AbstractValidator;

class VoidValidator extends AbstractValidator{
    protected static $rules = [];
}