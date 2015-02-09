<?php  namespace Jacopo\Authentication\Interfaces;
/**
 * Interface AuthenticationRoutesInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface AuthenticationRoutesInterface
{
    /**
     * Obtain the permissions from a given url
     *
*@param $route_name
     * @return mixed
     */
    public function getPermFromRoute($route_name);
    /**
     * Obtain the permissions from the current url
     * @return mixed
     */
    public function getPermFromCurrentRoute();

} 