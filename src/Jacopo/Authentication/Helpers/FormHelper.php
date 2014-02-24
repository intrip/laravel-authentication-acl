<?php  namespace Jacopo\Authentication\Helpers;
/**
 * Class FormHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Repository\PermissionRepository;
use Jacopo\Authentication\Repository\SentryGroupRepository;

class FormHelper 
{
    /**
     * @var \Jacopo\Authentication\Repository\PermissionRepository
     */
    protected $rp;
    /**
     * @var \Jacopo\Authentication\Repository\SentryGroupRepository
     */
    protected $rg;

    public function __construct(PermissionRepository $rp = null, SentryGroupRepository $rg = null)
    {
        $this->rp = $rp ? $rp : new PermissionRepository();
        $this->rg = $rg ? $rg : new SentryGroupRepository();
    }

    public function getSelectValues($repo_name, $key_value, $value_value)
    {
        $all_values = $this->{$repo_name}->all();
        // returns empty array if doesn't find any permission
        if($all_values->isEmpty())
            return [];

        $array_values = [];
        $all_values->each(function($value) use(&$array_values, $value_value, $key_value){
            $array_values[$value->{$key_value}] = $value->{$value_value};
        });

        return $array_values;
    }

    public function getSelectValuesPermission()
    {
        return $this->getSelectValues("rp",'permission','description');
    }

    public function getSelectValuesGroups()
    {
        return $this->getSelectValues("rg",'id','name');

    }

    /**
     * Prepares permission for sentry given the input
     * @param array $input
     * @param $operation
     * @param $field_name
     * @return void
     */
    public function prepareSentryPermissionInput(array &$input, $operation, $field_name = "permissions")
    {
        $input[$field_name] = isset($input[$field_name]) ? [$input[$field_name] => $operation] : '';
    }
} 