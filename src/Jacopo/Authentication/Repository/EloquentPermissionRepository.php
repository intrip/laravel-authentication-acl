<?php namespace Jacopo\Authentication\Repository;
/**
 * Class EloquentPermissionRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Exceptions\PermissionException;
use Jacopo\Library\Repository\EloquentBaseRepository;
use Event;
use Jacopo\Authentication\Repository\SentryGroupRepository as GroupRepo;

class EloquentPermissionRepository extends EloquentBaseRepository
{
    protected $model_name = '\Jacopo\Authentication\Models\Permission';
    /**
     * @var \Jacopo\Authentication\Repository\SentryGroupRepository
     */
    protected $group_repo;

    public function __construct($group_repo = null)
    {
        $this->group_repo = $group_repo ? $group_repo : new GroupRepo;

        Event::listen('repository.deleting', '\Jacopo\Authentication\Repository\EloquentPermissionRepository@checkIsNotAssociatedToAnyGroup');
    }

    /**
     * @param $obj
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    public function checkIsNotAssociatedToAnyGroup($obj)
    {
        // obtain all groups
        $all_groups = $this->group_repo->all();
        // spin trough groups to check if any of em has the permission
        foreach ($all_groups as $group) {
            $perm = $group->permissions;
            if(array_key_exists($obj->permission, $perm )) throw new PermissionException;
        }
    }
}