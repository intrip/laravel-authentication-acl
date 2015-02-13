<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test SentryMenuFactoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
use \Config;
use Mockery as m;

class SentryMenuFactoryTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function itCreateACollectionOfMenItem()
    {
        $this->initializeConfig();

        $collection = SentryMenuFactory::create();

        $this->assertInstanceOf('Jacopo\Authentication\Classes\Menu\MenuItemCollection', $collection);
        $items = $collection->getItemList();
        $this->assertEquals(2, count($items));
        $this->assertEquals("name1", $items[0]->getName());
        $this->assertEquals("name2", $items[1]->getName());
        $this->assertEquals("route2", $items[1]->getRoute());
        $this->assertEquals("link2", $items[1]->getLink());
    }

    /**
     * @test
     **/
    public function itSkipItemsWithoutName()
    {
        $extra_data = [
                [
                        "name"        => "",
                        "link"        => "link3",
                        "permissions" => ["permission1"],
                        "route"       => "route3"
                ],
                [
                        "link"        => "link4",
                        "permissions" => ["permission1"],
                        "route"       => "route4"
                ]
        ];
        $this->initializeConfig($extra_data);

        $collection = SentryMenuFactory::create();
        $items = $collection->getItemList();
        $this->assertEquals(2, count($items));
    }

    /**
     * @test
     **/
    public function it_create_collection_checking_permissions()
    {
        $this->initializeConfig();
        $this->mockSentryHasAccessOnlyOnFirstItem();

        $collection = SentryMenuFactory::create();

        $this->assertInstanceOf('Jacopo\Authentication\Classes\Menu\MenuItemCollection', $collection);
        $items = $collection->getItemListAvailable();
        $this->assertEquals(1, count($items));
        $this->assertEquals("name1", $items[0]->getName());
        $this->assertEquals("route1", $items[0]->getRoute());
    }

    private function mockSentryHasAccessOnlyOnFirstItem()
    {
        $mock_sentry = m::mock('StdClass')->shouldReceive('hasAnyAccess')->andReturn(true, false)->getMock();
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn($mock_sentry)->getMock();
        \App::instance('sentry', $mock_current);
    }

    private function initializeConfig($extra_fields = [])
    {
        $config_arr = $this->getConfigData();
        $config_arr = array_merge($config_arr, $extra_fields);

        Config::set(SentryMenuFactory::$config_file, $config_arr);
    }

    /**
     * @return array
     */
    private function getConfigData()
    {
        $config_arr = [
                [
                        "name"        => "name1",
                        "link"        => "link1",
                        "permissions" => ["permission1"],
                        "route"       => "route1"
                ],
                [
                        "name"        => "name2",
                        "link"        => "link2",
                        "permissions" => ["permission1"],
                        "route"       => "route2"
                ],
        ];
        return $config_arr;
    }
}
 