<?php namespace LaravelAcl\Authentication\Exceptions;
/**
 * Class ProfileNotFoundException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;

class ProfileNotFoundException extends Exception implements JacopoExceptionsInterface {}