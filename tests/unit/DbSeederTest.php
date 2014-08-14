<?php  namespace Jacopo\Authentication\Tests\Unit;

use App;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Seeds\DbSeeder;

/**
 * Test UserSeederTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class DbSeederTest extends DbTestCase
{

    protected $user_repository;
    protected $permission_repository;
    protected $dbSeeder;
    protected $group_repository;

    public function setUp()
    {
        parent::setUp();

        $this->dbSeeder = $seeder = new DbSeeder();
        $this->user_repository = App::make('user_repository');
        $this->permission_repository = App::make('permission_repository');
        $this->group_repository = App::make('group_repository');
    }

    /**
     * @test
     **/
    public function it_createPermissions()
    {
        $this->dbSeeder->run();

        $actual_permissions = $this->permission_repository->all();
        $expected_permissions = $this->getDefaultPermissions();

        foreach($expected_permissions as $expected_permission)
        {
            $is_present = $this->permissionIsPresentInArray($actual_permissions, $expected_permission);
            $this->assertTrue($is_present);
        }
    }

    private function getDefaultPermissions()
    {
        $permission1 = new Permission([
                                              "description" => "superadmin",
                                              "permission"  => "_superadmin"
                                      ]);
        $permission2 = new Permission([
                                              "description" => "user editor",
                                              "permission"  => "_user-editor"
                                      ]);
        $permission3 = new Permission([
                                              "description" => "group editor",
                                              "permission"  => "_group-editor"
                                      ]);
        $permission4 = new Permission([
                                              "description" => "permission editor",
                                              "permission"  => "_permission-editor"
                                      ]);
        $permission5 = new Permission([
                                              "description" => "profile type editor",
                                              "permission"  => "_profile-editor"
                                      ]);
        return [
                $permission1,
                $permission2,
                $permission3,
                $permission4,
                $permission5
        ];
    }

    /**
     * @param $actual_permissions
     * @param $expected_permission
     * @return bool
     */
    private function permissionIsPresentInArray($actual_permissions, $expected_permission)
    {
        $is_present = false;

        foreach($actual_permissions as $actual_permission)
        {
            if($expected_permission->permission == $actual_permission->permission) $is_present = true;
        }
        return $is_present;
    }

    /**
     * @test
     **/
    public function it_createGroupsAndAssociatePermissions()
    {
        $this->dbSeeder->run();

        $group = $this->getGroupByName("superadmin");
        $this->assertNotEmpty($group);
        $this->assertTrue($group->hasAccess("_superadmin"));

        $group = $this->getGroupByName("editor");
        $this->assertNotEmpty($group);
        $this->assertTrue($group->hasAccess("_user-editor"));
        $this->assertTrue($group->hasAccess("_group-editor"));

        $group = $this->getGroupByName("base admin");
        $this->assertNotEmpty($group);
        $this->assertTrue($group->hasAccess("_user-editor"));
    }

    /**
     * @return mixed
     */
    private function getGroupByName($name)
    {
        return $this->group_repository->all(["name" => $name])->first();
    }

    /**
     * @test
     **/
    public function it_createAdminUser_AndAssociateSuperadminGroup_andProfile()
    {

        $this->dbSeeder->run();

        $users = $this->user_repository->all();

        $this->assertCount(1, $users);
        $default_user = $this->getDefaultUser();
        $user = $users->first();

        $this->assertEquals($default_user->email, $user->email);
        $user_superadmin = $this->getSuperadminUser();
        $this->assertEquals($user->email, $user_superadmin->email);
        $this->assertUserHasProfile($user);
    }

    /**
     * @param $user
     */
    protected function assertUserHasProfile($user)
    {
        $user_profile = User::find($user->id)->user_profile()->first();

        $this->assertEquals($user_profile->user_id, $user->id);
    }

    private function getDefaultUser()
    {
        return new User([
                                "email" => "admin@admin.com",
                        ]);
    }

    /**
     * @return mixed
     */
    private function getSuperadminUser()
    {
        return $this->user_repository->findFromGroupName("superadmin")->first();
    }
}
