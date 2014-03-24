<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class UseTokenMismatchExceptionrExistsException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;

class TokenMismatchException extends Exception implements JacopoExceptionsInterface {}