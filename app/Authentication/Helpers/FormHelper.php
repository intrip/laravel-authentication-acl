<?php  namespace Jacopo\Authentication\Helpers;

/**
 * Class FormHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Repository\EloquentPermissionRepository as PermissionRepository;
use Jacopo\Authentication\Repository\SentryGroupRepository;

class FormHelper
{
    /**
     * @var \Jacopo\Authentication\Repository\EloquentPermissionRepository
     */
    protected $repository_permission;
    /**
     * @var \Jacopo\Authentication\Repository\SentryGroupRepository
     */
    protected $repository_groups;

    public function __construct(PermissionRepository $rp = null, SentryGroupRepository $rg = null)
    {
        $this->repository_permission = $rp ? $rp : new PermissionRepository();
        $this->repository_groups = $rg ? $rg : new SentryGroupRepository();
    }

    public function getSelectValues($repo_name, $key_value, $value_value)
    {
        $all_objects = $this->{$repo_name}->all();

        if($all_objects->isEmpty()) return [];

        foreach($all_objects as $object) $array_values[$object->{$key_value}] = $object->{$value_value};

        return $array_values;
    }

    public function getSelectValuesPermission()
    {
        return $this->getSelectValues("repository_permission", 'permission', 'description');
    }

    public function getSelectValuesGroups()
    {
        return $this->getSelectValues("repository_groups", 'id', 'name');
    }

    /**
     * Prepares permission for sentry given the input
     *
     * @param array $input
     * @param       $operation
     * @param       $field_name
     * @return void
     */
    public function prepareSentryPermissionInput(array &$input, $operation, $field_name = "permissions")
    {
        $input[$field_name] = isset($input[$field_name]) ? [$input[$field_name] => $operation] : '';
    }
} 