<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test PermissionRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Authentication\Repository\PermissionRepository;

class PermissionRepositoryTest extends TestCase {

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function it_check_for_groups_and_throws_exception()
    {
        $data_obj = new \StdClass;
        $data_obj->permissions = ["_perm" => "1"];
        $data_stub = [$data_obj];
        $mock_repo_grp = m::mock('Jacopo\Authentication\Repository\GroupRepository')->shouldReceive('all')->andReturn($data_stub)->getMock();
        $perm_repo = new PermissionRepository($mock_repo_grp);
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm"]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }

    /**
     * @test
     **/
    public function it_check_for_groups_and_does_nothing()
    {
        $data_obj = new \StdClass;
        $data_obj->permissions = ["_perm" => "1"];
        $data_stub = [$data_obj];
        $mock_repo_grp = m::mock('Jacopo\Authentication\Repository\GroupRepository')->shouldReceive('all')->andReturn($data_stub)->getMock();
        $perm_repo = new PermissionRepository($mock_repo_grp);
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm_false"]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }
}
