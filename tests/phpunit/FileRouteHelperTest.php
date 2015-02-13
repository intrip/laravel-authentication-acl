<?php  namespace Jacopo\Authentication\Tests\Unit;

use Jacopo\Authentication\Helpers\FileRouteHelper;
use Config, Route, App;
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;
use Mockery\Container;

/**
 * Test FileRouteHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class FileRouteHelperTest extends DbTestCase
{
    use UserFactory;

    protected $route_helper;
    protected $logged_user;

    public function setUp()
    {
        parent::setUp();
        $this->route_helper = new FileRouteHelper();

        $this->initializeUserHasher();
        $this->createAndLoginUserWithPermissions(["_perm" => 1]);
    }

    /**
     * @test
     **/
    public function getsPermFromRoute()
    {
        $config_arr = $this->mockConfig();
        $helper = new FileRouteHelper();
        $perm = $helper->getPermFromRoute("route2");
        $this->assertEquals($config_arr[1]["permissions"], $perm);
    }

    /**
     * @test
     **/
    public function GetsPermFromCurrentRoute()
    {
        $config_arr = $this->mockConfig();
        Route::shouldReceive('currentRouteName')->andReturn("route2");
        $helper = new FileRouteHelper();
        $perm = $helper->getPermFromCurrentRoute();
        $this->assertEquals($config_arr[1]["permissions"], $perm);
    }

    /**
     * @return array
     */
    protected function mockConfig()
    {
        $config_arr = [
                [
                        "name" => "name1", "link" => "link1", "permissions" => ["permission1"], "route" => "route1",], [
                        "name" => "name1", "link" => "link1", "permissions" => ["permission1"], "route" => "route2"]];
        Config::shouldReceive('get')->andReturn($config_arr);

        return $config_arr;
    }

    /**
     * @test
     **/
    public function checkPermissionForRoute_WithNoPermissionConstraints()
    {
        $route_name = "test";
        $permissions = [];
        $this->setCustomMenuConfig($route_name, $permissions);

        $this->assertTrue($this->route_helper->hasPermForRoute($route_name));
    }

    /**
     * @test
     **/
    public function canCheckPermissionForRoute_WhenInvalidNameGiven()
    {
        $route_name = "test";
        $permissions = [];
        $this->setCustomMenuConfig($route_name, $permissions);

        $this->assertTrue($this->route_helper->hasPermForRoute("invalid"));
    }

    /**
     * @test
     **/
    public function canCheckPermissionForRouteSuccesfully()
    {
        $route_name = "test";
        $permissions = ["_perm"];
        $this->setCustomMenuConfig($route_name, $permissions);

        $this->assertTrue($this->route_helper->hasPermForRoute("test"));
    }

    /**
     * @test
     **/
    public function canCheckPermissionForRouteWithError()
    {
        $route_name = "test";
        $permissions = ["_wrong_perm"];
        $this->setCustomMenuConfig($route_name, $permissions);

        $this->assertFalse($this->route_helper->hasPermForRoute("test"));
    }

    /**
     * @test
     * @group error
     **/
    public function skipSkipPermissionsRoutesFromCheckingPermissions()
    {
        $route_name = "test";
        $permissions = ["_wrong_perm"];
        $to_skip = ["test.toskip"];
        $this->setCustomMenuConfig($route_name, $permissions, $to_skip);

        $this->assertTrue($this->route_helper->hasPermForRoute("test.toskip"));
    }
    
    /**
     * @param $route_name
     * @param $permissions
     */
    protected function setCustomMenuConfig($route_name, $permissions, array $to_skip = [])
    {
        Config::set('laravel-authentication-acl::menu.list',
                    [[
                             "name"        => "Test",
                             "route"       => $route_name,
                             "link"        => '',
                             "permissions" => $permissions,
                             "skip_permissions" => $to_skip,

                     ]]);
    }

    protected function createAndLoginUserWithPermissions($permissions)
    {
        $users_created = $this->make('Jacopo\Authentication\Models\User', array_merge($this->getUserStub(), ["permissions" => $permissions]));
        $this->logged_user = ($users_created[0]);

        App::make('authenticator')->loginById($this->logged_user->id);
    }
}