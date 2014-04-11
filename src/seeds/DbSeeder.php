<?php namespace Jacopo\Authentication\Seeds;

use Illuminate\Database\Seeder;
use Eloquent, App;
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
  }
}

class UserSeeder
{
  protected $admin_email = "admin@admin.com";
  protected $admin_password = "password";

  public function run ()
  {
    $user_repository = App::make('user_repository');
    $user_data = [
      "email" => $this->admin_email,
      "password" => $this->admin_password,
      "activated" => 1
    ];

    $user_repository->create($user_data);
  }
}

class PermissionSeeder
{
  public function run ()
  {

  }
}

class GroupsSeeder
{
  public function run ()
  {

  }
}
