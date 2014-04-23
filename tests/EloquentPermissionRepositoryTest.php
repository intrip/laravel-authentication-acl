<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test PermissionRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use App, Event;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Authentication\Repository\EloquentPermissionRepository as PermissionRepository;

class EloquentPermissionRepositoryTest extends TestCase {

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function it_check_if_is_associated_to_a_group_and_throws_exception()
    {
        $this->mockGroupRepositoryWithPerm1();
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm"]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }

    /**
     * @test
     **/
    public function it_check_if_is_associated_to_a_group()
    {
        $this->mockGroupRepositoryWithPerm1();
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => []]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }

    /**
     * @test
     **/
    public function it_check_if_is_associated_to_a_user()
    {
        $this->mockUserRepositoryWithPerm1();
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => []]);
        $perm_repo->checkIsNotAssociatedToAnyUser($permission_obj);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function it_check_if_is_associated_to_a_user_and_throws_exception()
    {
        $this->mockUserRepositoryWithPerm1();
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm"]);
        $perm_repo->checkIsNotAssociatedToAnyUser($permission_obj);
    }

    /**
     * @test
     **/
    public function it_calls_is_not_associated_to_any_group_and_is_not_associated_to_any_user_on_repositoryupdate()
    {
        $true_stub = new FalseGetter;
        Event::fire('repository.updating', [$true_stub]);
        $this->mockGroupRepositoryWithPerm1();
        $this->mockUserRepositoryWithPerm1();
    }

    /**
     * @return m\MockInterface
     */
    private function mockGroupRepositoryWithPerm1()
    {
        $data_obj              = new \StdClass;
        $data_obj->permissions = ["_perm" => "1"];
        $data_stub             = [$data_obj];
        $mock_repo_grp         = m::mock('Jacopo\Authentication\Repository\SentryGroupRepository')->shouldReceive('all')->andReturn($data_stub)->getMock();
        App::instance('group_repository', $mock_repo_grp);

        return $mock_repo_grp;
    }

    private function mockUserRepositoryWithPerm1()
    {
        $data_obj              = new \StdClass;
        $data_obj->permissions = ["_perm" => "1"];
        $data_stub             = [$data_obj];
        $mock_repo_user         = m::mock('Jacopo\Authentication\Repository\SentryUserRepository')->shouldReceive('all')->andReturn($data_stub)->getMock();
        App::instance('user_repository', $mock_repo_user);

        return $mock_repo_user;
    }
}

class FalseGetter
{
    public function __get($key)
    {
        return false;
    }
}