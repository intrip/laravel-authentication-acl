<?php namespace LaravelAcl\Authentication\Exceptions;
/**
 * Class UseTokenMismatchExceptionrExistsException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;

class TokenMismatchException extends Exception implements JacopoExceptionsInterface {}