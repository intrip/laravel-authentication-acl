<?php namespace Jacopo\Authentication\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App;
/**
 * Class DbSeeder
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class DbSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();

        $this->call('Jacopo\Authentication\Seeds\PermissionSeeder');
        $this->call('Jacopo\Authentication\Seeds\GroupsSeeder');
        $this->call('Jacopo\Authentication\Seeds\UserSeeder');

        Eloquent::reguard();
    }
}

class PermissionSeeder
{
    public function run ()
    {
        $permission_repository = App::make('permission_repository');
        $permission1           = [
                "description" => "superadmin",
                "permission"  => "_superadmin"
        ];
        $permission_repository->create($permission1);
        $permission2 = [
                "description" => "user editor",
                "permission"  => "_user-editor"
        ];
        $permission_repository->create($permission2);
        $permission3 = [
                "description" => "group editor",
                "permission"  => "_group-editor"
        ];
        $permission_repository->create($permission3);
        $permission4 = [
                "description" => "permission editor",
                "permission"  => "_permission-editor"
        ];
        $permission_repository->create($permission4);
        $permission5 = [
                "description" => "profile type editor",
                "permission"  => "_profile-editor"
        ];
        $permission_repository->create($permission5);
    }
}

/**
 * @property mixed group_repository
 */
class GroupsSeeder
{

    public function run ()
    {
        $group_repository = App::make('group_repository');

        $group1 = [
                "name" => "superadmin",
                "permissions" => ["_superadmin" => 1]
        ];

        $group_repository->create($group1);

        $group2 = [
                "name" => "editor",
                "permissions" => ["_user-editor" => 1, "_group-editor" => 1]
        ];

        $group_repository->create($group2);

        $group3 = [
                "name" => "base admin",
                "permissions" => ["_user-editor" => 1]
        ];

        $group_repository->create($group3);

    }
}

class UserSeeder
{
    protected $admin_email = "admin@admin.com";
    protected $admin_password = "password";

    public function run ()
    {
        $user_repository = App::make('user_repository');
        $group_repository = App::make('group_repository');
        $profile_repository = App::make('profile_repository');

        $user_data = [
                "email" => $this->admin_email,
                "password" => $this->admin_password,
                "activated" => 1
        ];

        $user = $user_repository->create($user_data);

        $profile_repository->attachEmptyProfile($user);

        $superadmin_group = $this->getSuperadminGroup($group_repository);
        $user_repository->addGroup($user->id, $superadmin_group->id);
    }

    /**
     * @param $group_repository
     * @return mixed
     */
    private function getSuperadminGroup ($group_repository)
    {
        $superadmin_group = $group_repository->all(["name" => "superadmin"])->first();
        return $superadmin_group;
    }
}