<?php  namespace Jacopo\Authentication\Tests; 
use Mockery as m;
/**
 * Test AuthenticatorInstallCommandTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class AuthenticatorInstallCommandTest extends TestCase {

  public function tearDown ()
  {
    m::close();
  }

  /**
     * @test
     **/
    public function it_calls_migration_and_publish_config()
    {
      $mock_command = m::mock('AuthenticatorInstallCommand')->makePartial()
        ->shouldReceive('call')
        ->once()
        ->with('config:publish', array('package' => 'jacopo/authentication' ) )
        ->getMock();

      $mock_command->execute([]);


    }
}
 