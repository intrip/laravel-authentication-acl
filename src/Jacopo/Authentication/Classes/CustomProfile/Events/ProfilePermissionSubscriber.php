<?php  namespace Jacopo\Authentication\Classes\CustomProfile\Events;
use App;
use Jacopo\Authentication\Exceptions\PermissionException;

/**
 * Class ProfilePermissionSubscriber
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProfilePermissionSubscriber 
{
    protected $permission_error_message = "You don't have the permission to edit custom user profiles.";

    /**
     * Check if the object is editable
     */
    public function checkProfileTypePermission()
    {
        $auth_helper = App::make('authentication_helper');
        if (! $auth_helper->checkCustomProfileEditPermission() ) throw new PermissionException($this->permission_error_message);
    }

    /**
     * Register the various event to the subscriber
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('customprofile.creating', 'Jacopo\Authentication\Classes\CustomProfile\Events\ProfilePermissionSubscriber@checkProfileTypePermission');
        $events->listen('customprofile.deleting', 'Jacopo\Authentication\Classes\CustomProfile\Events\ProfilePermissionSubscriber@checkProfileTypePermission');
    }

} 