<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class UserNotFoundException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;

class UserNotFoundException extends Exception implements JacopoExceptionsInterface {}