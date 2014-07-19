<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test FormHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\Collection;
use Mockery as m;
use Jacopo\Authentication\Helpers\FormHelper;
use Jacopo\Authentication\Models\Permission;

class FormHelperTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_create_permissions_array_values_and_add_underscore_prefix_if_not_present()
    {
        $objs = $this->createArrayOfPermissions();
        $mock_permission = $this->mockPermissionFetch($objs);
        $helper = new FormHelper($mock_permission);

        $values = $helper->getSelectValuesPermission();

        $this->assertEquals("desc1", $values["_perm1"]);
    }

    /**
     * @test
     **/
    public function it_prepare_sentry_permission()
    {
        $data = ["permissions" => "permission1"];
        $operation = 1;

        $helper = new FormHelper();
        $helper->prepareSentryPermissionInput($data, $operation);

        $this->assertEquals(["permission1" => 1], $data["permissions"]);
    }

    /**
     * @return array
     */
    protected function createArrayOfPermissions()
    {
        $obj1   = new Permission(["description" => "desc1", "permission" => "perm1"]);
        $obj2   = new Permission(["description" => "desc2", "permission" => "perm2"]);
        $obj3   = new Permission(["description" => "desc3", "permission" => "perm3"]);
        return [$obj1, $obj2, $obj3];
    }

    /**
     * @param $objs
     * @return m\MockInterface|\Yay_MockObject
     */
    protected function mockPermissionFetch($objs)
    {
        $mock_permission = m::mock('Jacopo\Authentication\Repository\EloquentPermissionRepository');
        $mock_permission->shouldReceive('all')->andReturn(new Collection($objs));
        return $mock_permission;
    }

}
 