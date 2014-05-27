<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test SentryMenuFactoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
use Jacopo\Authentication\Tests\TestCase;
use Config;
use Mockery as m;

class SentryMenuFactoryTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_creates_a_collection()
    {
        $this->mockConfigWithNameAndRoutes("name1", ["route1", "route2"]);

        \App::instance('sentry', '');

        $collection = SentryMenuFactory::create();
        $this->assertInstanceOf('Jacopo\Authentication\Classes\Menu\MenuItemCollection', $collection);
        $items = $collection->getItemList();
        $this->assertEquals(2, count($items));
        $this->assertEquals("name1", $items[0]->getName());
        $this->assertEquals("route2", $items[1]->getRoute());
    }

    /**
     * @test
     **/
    public function it_create_collection_checking_permissions()
    {
        $this->mockConfigWithNameAndRoutes("name1", ["route1", "route2"]);

        $this->mockSentryHasAccessFirstYesSecondNo();

        $collection = SentryMenuFactory::create();
        $this->assertInstanceOf('Jacopo\Authentication\Classes\Menu\MenuItemCollection', $collection);
        $items = $collection->getItemListAvailable();
        $this->assertEquals(1, count($items));
        $this->assertEquals("name1", $items[0]->getName());
        $this->assertEquals("route1", $items[0]->getRoute());
    }

    private function mockConfigWithNameAndRoutes($name, array $routes)
    {
        $config_arr = [
            [
                "name" => $name, "link" => "link1", "permissions" => ["permission1"], "route" => $routes[0]], [
                "name" => $name, "link" => "link1", "permissions" => ["permission1"], "route" => $routes[1]]];
        Config::set('laravel-authentication-acl::menu.list', $config_arr);
    }

    private function mockSentryHasAccessFirstYesSecondNo()
    {
        $mock_sentry  = m::mock('StdClass')->shouldReceive('hasAnyAccess')->andReturn(true, false)->getMock();
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn($mock_sentry)->getMock();
        \App::instance('sentry', $mock_current);
    }
}
 