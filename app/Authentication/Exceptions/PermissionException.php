<?php namespace LaravelAcl\Authentication\Exceptions;
/**
 * Class PermissionException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use LaravelAcl\Library\Exceptions\JacopoExceptionsInterface;

class PermissionException extends Exception implements JacopoExceptionsInterface {}