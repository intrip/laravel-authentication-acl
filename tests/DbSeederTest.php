<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Seeds\DbSeeder;
use App;

/**
 * Test UserSeederTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class DbSeederTest extends DbTestCase {

    /**
     * @test
     **/
    public function it_clear_table_and_create_admin_user()
    {
      $admin_email = "admin@admin.com";
      $user_repository = App::make('user_repository');
      $seeder = new DbSeeder();

      $seeder->run();

      $users = $user_repository->all();
      $this->assertCount(1, $users);
      $this->assertEquals($admin_email, $users->first()->email);
    }
  
    /**
     * @test
     **/
    public function it_clear_table_and_create_base_groups()
    {
        
    }

}
 