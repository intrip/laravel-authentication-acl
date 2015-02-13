<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test MenuItemCollectionTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Classes\Menu\MenuItemCollection;
use Mockery as m;

class MenuItemCollectionTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_gets_items_available()
    {
        $item_with_permission = m::mock('StdClass')->shouldReceive('havePermission')->andReturn(true)->getMock();
        $item_without_permission = m::mock('StdClass')->shouldReceive('havePermission')->andReturn(false)->getMock();

        $collection = new MenuItemCollection([$item_with_permission, $item_without_permission]);
        $item_valid = $collection->getItemListAvailable();

        $this->assertCount(1, $item_valid);
    }
}
 