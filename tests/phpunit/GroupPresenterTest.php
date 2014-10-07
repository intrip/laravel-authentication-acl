<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test GroupPresenterTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Presenters\GroupPresenter;
use Mockery as m;

class GroupPresenterTest extends DbTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_get_permission_names()
    {
        $perm = new PermissionStub();
        $resource_stub = new \StdClass;
        $resource_stub->permissions = ["perm"];
        $presenter = new GroupPresenter($resource_stub);

        $names = $presenter->permissions_obj($perm);
        $this->assertEquals("name", $names[0]);
    }
}

class PermissionStub
{
    protected static $a = 1;

    public static function wherePermission()
    {
        $mock_first = m::mock('StdClass')->shouldReceive('first')->andReturn('name')->getMock();
        $mock_empty = m::mock('StdClass')->shouldReceive('isEmpty')->andReturn(false)->getMock();
        $mock_get = m::mock('StdClass')->shouldReceive('get')->andReturn($mock_empty)->getMock();

        if(static::$a == 1)
        {
            static::$a = 2;
            return $mock_get;
        }
        return $mock_first;
    }
}