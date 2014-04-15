<?php  namespace Jacopo\Authentication\Tests;
use Jacopo\Authentication\Helpers\FileRouteHelper;
use Config, Route;
/**
 * Test FileRouteHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class FileRouteHelperTest extends TestCase {

    /**
     * @test
     **/
    public function it_gets_perm_from_route()
    {
        $config_arr = $this->mockConfig();
        $helper = new FileRouteHelper();
        $perm = $helper->getPermFromRoute("route2");
        $this->assertEquals($config_arr[1]["permissions"],$perm);
    }

    /**
     * @test
     **/
    public function it_gets_perm_from_current_route()
    {
        $config_arr = $this->mockConfig();
        Route::shouldReceive('currentRouteName')->andReturn("route2");
        $helper = new FileRouteHelper();
        $perm = $helper->getPermFromCurrentRoute();
        $this->assertEquals($config_arr[1]["permissions"],$perm);
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
}
 