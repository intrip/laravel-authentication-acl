<?php  namespace LaravelAcl\Authentication\Helpers;
/**
 * Class SentryAuthenticationHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\Facades\Config;
use LaravelAcl\Authentication\Interfaces\AuthenticationHelperInterface;
use LaravelAcl\Authentication\Interfaces\PermissionProfileHelperInterface;
use Session, App;

class SentryAuthenticationHelper implements AuthenticationHelperInterface, PermissionProfileHelperInterface
{
    /**
     * Check if the current user is logged and has access
     * to all the permissions
     *
     * @param $permissions
     * @return boolean
     */
    public function hasPermission(array $permissions)
    {
        $current_user = $this->currentUser();
        if(! $current_user) return false;
        if($permissions && (! $current_user->hasAnyAccess($permissions)) ) return false;

        return true;
    }

    /**
     * Check if the current user has permission to edit the profile
     *
     * @return boolean
     */
    public function checkProfileEditPermission($user_id)
    {
        $current_user_id = $this->currentUser()->id;

        // edit his profile
        if($user_id == $current_user_id) return true;
        // has special permission to edit other user profiles
        $edit_perm = Config::get('acl_permissions.edit_profile');
        if($this->hasPermission($edit_perm) ) return true;

        return false;
    }

    /**
     * Check if the current user has permission to edit the custom profile types
     *
     * @return boolean
     */
    public function checkCustomProfileEditPermission()
    {
        $edit_perm = Config::get('acl_permissions.edit_custom_profile');

        return $this->hasPermission($edit_perm) ? true : false;
    }

    /**
     * Obtain the user that needs to be notificated on registration
     *
     * @return array
     */
    public function getNotificationRegistrationUsersEmail()
    {
        $group_name = Config::get('acl_permissions.profile_notification_group');
        $user_r = App::make('user_repository');
        $users = $user_r->findFromGroupName($group_name)->lists('email');

        return $users;
    }

    /**
     * @return mixed
     */
    protected function currentUser()
    {
        $sentry = App::make('sentry');
        $current_user = $sentry->getUser();
        return $current_user;
    }
}