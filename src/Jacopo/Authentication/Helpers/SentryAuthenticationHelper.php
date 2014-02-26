<?php  namespace Jacopo\Authentication\Helpers;
/**
 * Class SentryAuthenticationHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Interfaces\AuthenticationHelperInterface;
use Session;

class SentryAuthenticationHelper implements AuthenticationHelperInterface
{
    /**
     * Check if the current user is logged and has access
     * to all the permissions $permissions
     *
     * @param $permissions
     * @return boolean
     */
    public static function hasPermission(array $permissions)
    {
        $sentry = \App::make('sentry');
        $current_user = $sentry->getUser();
        if(! $current_user)
            return false;
        if($permissions && (! $current_user->hasAnyAccess($permissions)) )
            return false;

        return true;
    }
}