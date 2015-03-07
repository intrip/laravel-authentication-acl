<?php  namespace LaravelAcl\Authentication\Events;
/**
 * Class EbitableSubscriber
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

use LaravelAcl\Authentication\Exceptions\PermissionException;

class EditableSubscriber
{
    protected $editable_field = "protected";
    /**
     * Check if the object is editable
     */
    public function isEditable($object)
    {
        if($object->{$this->editable_field} == true) throw new PermissionException;
    }

    /**
     * Register the various event to the subscriber
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('repository.deleting', 'LaravelAcl\Authentication\Events\EditableSubscriber@isEditable',10);
        $events->listen('repository.updating', 'LaravelAcl\Authentication\Events\EditableSubscriber@isEditable',10);
    }

} 