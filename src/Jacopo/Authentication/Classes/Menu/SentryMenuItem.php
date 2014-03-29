<?php  namespace Jacopo\Authentication\Classes\Menu;
/**
 * Class MenuItem
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;
use Jacopo\Authentication\Interfaces\MenuInterface;
use App;

class SentryMenuItem implements MenuInterface
{
    /**
     * @var String
     */
    protected $link;
    /**
     * @var String
     */
    protected $name;
    /**
     * The permission needed to see the menu
     * @var String
     */
    protected $permissions;
    /**
     * The route name
     * @var String
     */
    protected $route;
    /**
     * Sentry user instance
     */
    protected $sentry;

    function __construct($link, $name, $permissions, $route)
    {
        $this->link = $link;
        $this->name = $name;
        $this->permissions = $permissions;
        $this->route = $route;
        $this->sentry = App::make('sentry');
    }

    /**
     * Check if the current user have access to the menu item
     *
     * @return boolean
     */
    public function havePermission()
    {
        return App::make('authentication_helper')->hasPermission($this->permissions);
    }

    /**
     * Obtain the menu link
     *
     * @return String
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Obtain the menu name
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Obtain the permission to see the menu
     * @return String
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Obtain the route name
     * @return String
     */
    public function getRoute()
    {
        return $this->route;
    }

}