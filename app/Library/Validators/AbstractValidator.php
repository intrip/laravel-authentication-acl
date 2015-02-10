<?php namespace Jacopo\Library\Validators;

use Validator as V;
use Event;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $errors;
    /**
     * Validation rules
     * @var array
     */
    protected static $rules;
    /**
     * Custom validation messages
     * @var array
     */
    protected static $messages;

    public function validate($input)
    {
        Event::fire('validating', [$input]);
        static::$messages = static::$messages ? static::$messages : [];
        $validator = V::make($input, static::$rules, static::$messages);

        if($validator->fails())
        {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public static function getRules()
    {
        return static::$rules;
    }

}