<?php  namespace LaravelAcl\Authentication\Tests\Unit\Stubs;
use LaravelAcl\Library\Validators\AbstractValidator;

class VoidValidator extends AbstractValidator{
    protected static $rules = [];
}