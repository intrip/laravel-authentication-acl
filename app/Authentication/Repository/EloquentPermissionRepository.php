<?php namespace LaravelAcl\Authentication\Repository;
/**
 * Class EloquentPermissionRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use LaravelAcl\Authentication\Exceptions\PermissionException;
use LaravelAcl\Authentication\Models\Permission;
use LaravelAcl\Library\Repository\EloquentBaseRepository;
use Event, App;

class EloquentPermissionRepository extends EloquentBaseRepository
{
    protected $group_repo;
    protected $user_repo;

    protected $permissions_model = 'LaravelAcl\Authentication\Models\Permission';

    public function __construct()
    {
        $this->group_repo = App::make('group_repository');
        $this->user_repo = App::make('user_repository');

        Event::listen(['repository.deleting','repository.updating'], '\LaravelAcl\Authentication\Repository\EloquentPermissionRepository@checkIsNotAssociatedToAnyUser');
        Event::listen(['repository.deleting','repository.updating'], '\LaravelAcl\Authentication\Repository\EloquentPermissionRepository@checkIsNotAssociatedToAnyGroup');

        $config = config('cartalyst.sentry');
        if (isset($config['permission']) && isset($config['permission']['model'])) {
            $this->permissions_model = $config['permission']['model'];
        }
        return parent::__construct(new $this->permissions_model);
    }

    /**
     * @param $obj
     * @throws \LaravelAcl\Authentication\Exceptions\PermissionException
     */
    public function checkIsNotAssociatedToAnyGroup($permission_obj)
    {
        $all_groups = $this->group_repo->all();
        $this->validateIfPermissionIsInCollection($permission_obj, $all_groups);
    }

    /**
     * @param $permission_obj
     * @throws \LaravelAcl\Authentication\Exceptions\PermissionException
     */
    public function checkIsNotAssociatedToAnyUser($permission_obj)
    {
        $all_users = $this->user_repo->all();
        $this->validateIfPermissionIsInCollection($permission_obj, $all_users);
    }

    /**
     * @param $permission
     * @param $collection
     * @throws \LaravelAcl\Authentication\Exceptions\PermissionException
     */
    private function validateIfPermissionIsInCollection($permission, $collection)
    {
        foreach ($collection as $collection_item)
        {
            $perm = $this->permissionsToArray($collection_item->permissions);
            if (! empty($perm) && is_array($perm) && array_key_exists($permission->permission, $perm)) throw new PermissionException;
        }
    }

    private function permissionsToArray($permissions)
    {
        if ( ! $permissions)
        {
            return array();
        }

        if (is_array($permissions))
        {
            return $permissions;
        }

        if ( ! $_permissions = json_decode($permissions, true))
        {
            throw new \InvalidArgumentException("Cannot JSON decode permissions [$permissions].");
        }

        return $_permissions;
    }
}