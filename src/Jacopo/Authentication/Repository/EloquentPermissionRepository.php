<?php namespace Jacopo\Authentication\Repository;
/**
 * Class EloquentPermissionRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Exceptions\PermissionException;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Library\Repository\EloquentBaseRepository;
use Event, App;

class EloquentPermissionRepository extends EloquentBaseRepository
{
    protected $group_repo;
    protected $user_repo;

    public function __construct()
    {
        $this->group_repo = App::make('group_repository');
        $this->user_repo = App::make('user_repository');

        Event::listen('repository.deleting', '\Jacopo\Authentication\Repository\EloquentPermissionRepository@checkIsNotAssociatedToAnyGroup');
        Event::listen('repository.deleting', '\Jacopo\Authentication\Repository\EloquentPermissionRepository@checkIsNotAssociatedToAnyUser');

        return parent::__construct(new Permission);
    }

    /**
     * @param $obj
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    public function checkIsNotAssociatedToAnyGroup($permission_obj)
    {
        $all_groups = $this->group_repo->all();
        $this->validateIfPermissionIsInCollection($permission_obj, $all_groups);
    }

    /**
     * @param $obj
     * @param $all_groups
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    private function validateIfPermissionIsInCollection($obj, $all_groups)
    {
        foreach ($all_groups as $group)
        {
            $perm = $group->permissions;
            if (! empty($perm) && array_key_exists($obj->permission, $perm)) throw new PermissionException;
        }
    }

    /**
     * @param $permission_obj
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    public function checkIsNotAssociatedToAnyUser($permission_obj)
    {
        $all_users = $this->user_repo->all();
        $this->validateIfPermissionIsInCollection($permission_obj, $all_users);
    }
}