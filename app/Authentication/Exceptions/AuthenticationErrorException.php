<?php namespace LaravelAcl\Authentication\Exceptions;
/**
 * Class AuthenticationErrorException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;

class AuthenticationErrorException extends Exception implements JacopoExceptionsInterface {}