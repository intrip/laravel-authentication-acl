<?php namespace Jacopo\Library\Validators;

use Illuminate\Validation\DatabasePresenceVerifier;
use Jacopo\Library\Traits\OverrideConnectionTrait;
use Jacopo\Library\Validators\AbstractValidator;

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