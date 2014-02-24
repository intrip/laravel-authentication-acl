<?php  namespace Jacopo\Authentication\Helpers;
/**
 * Class FileRouteHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Config, Route;
use Jacopo\Authentication\Interfaces\AuthenticationRoutesInterface;
use Jacopo\Library\Views\Helper as ViewHelper;

class FileRouteHelper implements AuthenticationRoutesInterface
{
    /**
     * @var string
     */
    protected $config_path = "authentication::menu";
    /**
     * @var string
     */
    protected $route_variable_index = "route";
    /**
     * @var string
     */
    protected $pemissions_variable_index = "permissions";

    public function __construct($config_path = null)
    {
        $this->config_path = $config_path ? $config_path : $this->config_path;
    }

    /**
     * Obtain the permissions from a given url
     *
     * @param $url
     * @return mixed
     */
    public function getPermFromRoute($route)
    {
        $menu_info = Config::get($this->config_path);

        foreach ($menu_info as $menu)
        {
            if($menu[$this->route_variable_index] == $route)
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
}