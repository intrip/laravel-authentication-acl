<?php  namespace Jacopo\Authentication\Tests;

use App;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Seeds\DbSeeder;

/**
 * Test UserSeederTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class DbSeederTest extends DbTestCase {

  protected $user_repository;

  public function setUp () {
    parent::setUp();

    $this->user_repository = App::make('user_repository');
  }

  /**
   * @test
   **/
  public function it_clear_table_and_create_admin_user () {
    $seeder = new DbSeeder();

    $seeder->run();

    $users = $this->user_repository->all();

    $this->assertCount(1, $users);
    $default_user   = $this->getDefaultUser();
    $this->assertEquals($default_user->email, $users->first()->email);
  }

  private function getDefaultUser () {
    return new User([
                       "email" => "admin@admin.com",
                    ]);
  }

}
 