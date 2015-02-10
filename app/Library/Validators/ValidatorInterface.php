<?php namespace Jacopo\Library\Validators;

interface ValidatorInterface
{
    /**
     * Validate the input
     * @param $input
     * @return boolean
     */
    public function validate($input);

    /**
     * Obtain all errors list
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors();
}