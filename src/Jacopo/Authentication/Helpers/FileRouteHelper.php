<?php  namespace Jacopo\Authentication\Helpers;
/**
 * Class FileRouteHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Config, Route, App;
use Jacopo\Authentication\Interfaces\AuthenticationRoutesInterface;
use Jacopo\Library\Views\Helper as ViewHelper;

class FileRouteHelper implements AuthenticationRoutesInterface
{
    /**
     * @var string
     */
    protected $config_path = "laravel-authentication-acl::menu.list";
    /**
     * @var string
     */
    protected $route_variable_index = "route";
    /**
     * @var string
     */
    protected $pemissions_variable_index = "permissions";
    /**
     * @var string
     */
    protected $skip_permissions_variable_index = "skip_permissions";

    protected $authentication_helper;

    public function __construct($config_path = null)
    {
        $this->config_path = $config_path ? $config_path : $this->config_path;
        $this->authentication_helper = App::make('authentication_helper');
    }

    /**
     * Obtain the permissions from a given url
     *
     * @param $url
     * @return mixed
     */
    public function getPermFromRoute($route_name)
    {
        $menu_info = Config::get($this->config_path);

        foreach ($menu_info as $menu)
        {
            if(isset($menu[$this->skip_permissions_variable_index]) && in_array($route_name, $menu[$this->skip_permissions_variable_index]))
                return [];

            if($menu[$this->route_variable_index] == ViewHelper::get_base_route_name($route_name))
                return $menu[$this->pemissions_variable_index];
        }
    }

    /**
     * Obtain the permissions from the current url
     *
     * @return mixed
     */
    public function getPermFromCurrentRoute()
    {
        $route_base = ViewHelper::get_base_route_name( Route::currentRouteName() );

        return $this->getPermFromRoute($route_base);
    }


    /**
     * Check if the logged user has permission for the given route
     *
     * @param $route_name
     */
    public function hasPermForRoute($route_name)
    {
        $permissions = $this->getPermFromRoute($route_name);
        if( empty($permissions)) return true;

        return $this->authentication_helper->hasPermission($permissions);
    }

}