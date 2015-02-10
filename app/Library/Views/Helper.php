<?php namespace Jacopo\Library\Views;
/**
 * Class View Helper
 *
 * @author jacopo beschi j.beschi@jacopo.com
 */
use Request;
use Route;

class Helper 
{
    /**
     * Check if url is the same of the current url and return active state
     * @param String $url
     * @param String $active
     * @return $active
     */
    public static function get_active($url, $active = 'active')
    {
        return (Request::url() == $url) ? $active : '';
    }

    /**
     * Check if route name is the same as the current url and returns active state
     * @param $match
     * @param $active
     */
    public static function get_active_route_name($match, $active = 'active')
    {
        $route_name = Route::currentRouteName();
        return (strcasecmp(static::get_base_route_name($route_name), $match) == 0) ? $active : '';
    }

    /**
     * Get the first part of the route name before the dot
     * @param $route_name
     * @return mixed
     */
    public static function get_base_route_name($route_name)
    {
        return array_values(explode(".", $route_name))[0];
    }
} 