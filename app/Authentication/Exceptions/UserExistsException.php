<?php namespace LaravelAcl\Authentication\Exceptions;
/**
 * Class UserExistsException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;

class UserExistsException extends Exception implements JacopoExceptionsInterface {}