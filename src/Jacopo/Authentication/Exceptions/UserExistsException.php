<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class UserExistsException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;

class UserExistsException extends Exception implements JacopoExceptionsInterface {}