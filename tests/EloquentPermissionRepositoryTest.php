<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test PermissionRepositoryTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Models\User;
use Mockery as m;
use App, Event, DB;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Authentication\Repository\EloquentPermissionRepository as PermissionRepository;

class EloquentPermissionRepositoryTest extends DbTestCase {

    protected $faker;

    public function setUp()
    {
        parent::setUp();
        $this->faker = \Faker\Factory::create();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\PermissionException
     **/
    public function it_check_if_is_associated_to_a_group_and_throws_exception()
    {
        $this->make('Jacopo\Authentication\Models\Group');
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm"]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }

    /**
     * @test
     **/
    public function it_check_if_is_associated_to_a_group()
    {
        $this->make('Jacopo\Authentication\Models\Group');
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => []]);
        $perm_repo->checkIsNotAssociatedToAnyGroup($permission_obj);
    }

    /**
     * @test
     **/
    public function it_check_if_is_associated_to_a_user()
    {
        $this->make('Jacopo\Authentication\Models\Group');
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
        $this->createUserWithPerm1();
        $perm_repo = new PermissionRepository();
        $permission_obj = new Permission(["description" => "desc", "permission" => "_perm" ]);
        $perm_repo->checkIsNotAssociatedToAnyUser($permission_obj);
    }

    /**
     * @test
     **/
    public function it_calls_is_not_associated_to_any_group_and_is_not_associated_to_any_user_on_repositoryupdate()
    {
        $true_stub = new FalseGetter;
        Event::fire('repository.updating', [$true_stub]);
        $this->make('Jacopo\Authentication\Models\Group');
        $this->createUserWithPerm1();
    }

    protected function getModelStub()
    {
        return [
            "name" => $this->faker->name(),
            "permissions" => ["_perm" => "1"]
        ];
    }

    private function createUserWithPerm1()
    {
        DB::table('users')->insert([
                                   "email"      => $this->faker->email(),
                                   "password"   => $this->faker->text(10), "activated" => 1,
                                   "permissions" => json_encode(["_perm" => "1"]),
                                   "created_at" => 0, "updated_at" => 0
                                   ]);
        return User::first();
    }
}

class FalseGetter
{
    public function __get($key)
    {
        return false;
    }
}