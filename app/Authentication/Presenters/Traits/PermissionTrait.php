<?php  namespace LaravelAcl\Authentication\Presenters\Traits;
use LaravelAcl\Authentication\Models\Permission;

/**
 * Trait PermissionTrait
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
trait PermissionTrait
{
    /**
     * Obtains the permission obj associated to the model
     * @param null $model
     * @return array
     */
    public function permissions_obj($model = null)
    {
        $model = $model ? $model : $this->getPermissionModel();
        $objs = [];
        $permissions = $this->resource->permissions;
        if(! empty($permissions) ) foreach ($permissions as $permission => $status)
        {
            $objs[] = (! $model::wherePermission($permission)->get()->isEmpty()) ? $model::wherePermission($permission)->first() : null;
        }
        return $objs;
    }

    public function getPermissionModel(){
        $config = config('cartalyst.sentry');
        if (isset($config['permission']) && isset($config['permission']['model'])) {
            return new $config['permission']['model'];
        }

        return new Permission;
    }
}