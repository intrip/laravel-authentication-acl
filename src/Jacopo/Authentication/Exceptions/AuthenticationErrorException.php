<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class AuthenticationErrorException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;

class AuthenticationErrorException extends Exception implements JacopoExceptionsInterface {}