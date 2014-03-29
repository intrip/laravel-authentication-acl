<?php namespace Jacopo\Authentication\Exceptions;
/**
 * Class UserNotFoundException
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use Exception;
use Palmabit\Library\Exceptions\PalmabitExceptionsInterface;

class LoginRequiredException extends Exception implements PalmabitExceptionsInterface {}