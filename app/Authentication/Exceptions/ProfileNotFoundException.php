<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class ProfileNotFoundException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;

class ProfileNotFoundException extends Exception implements JacopoExceptionsInterface {}