<?php namespace Jacopo\Authentication\Validators;

use Illuminate\Validation\DatabasePresenceVerifier;
use Jacopo\Authentication\Traits\OverrideConnectionTrait;
use Jacopo\Library\Validators\AbstractValidator as BaseValidator;

class AbstractValidator extends BaseValidator
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