<?php  namespace Jacopo\Authentication\Interfaces;
/**
 * Interface AuthenticationHelperInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
interface AuthenticationHelperInterface 
{
    /**
     * Check if the current user is logged and has the
     * permission name
     * @param $permissions
     * @return boolean
     */
    public static function hasPermission(array $permissions);

} 