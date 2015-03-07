<?php namespace LaravelAcl\Library\Validators;

use Illuminate\Validation\DatabasePresenceVerifier;
use LaravelAcl\Library\Traits\OverrideConnectionTrait;
use LaravelAcl\Library\Validators\AbstractValidator;

class OverrideConnectionValidator extends AbstractValidator
{
    use OverrideConnectionTrait;
    /**
     * @param $input
     * @return mixed
     * @override
     */
    public function instanceValidator($input)
    {
        $validator = V::make($input, static::$rules);
        // update presence verifier
        $validator->getPresenceVerifier()->setConnection($this->getConnection());
        return $validator;
    }
} 