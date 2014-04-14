<?php  namespace Jacopo\Authentication\Tests;

use AuthenticatorInstallCommand;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

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
      $mock_call = m::mock('StdClass')
              ->shouldReceive('call')
              ->once()
              ->with('config:publish', ['package' => 'jacopo/authentication' ])
              ->andReturn(true)
              ->getMock();

      $mock_seeder = m::mock('DbSeeder')
              ->shouldReceive('run')
              ->once()
              ->getMock();

      $command = new CommandTester(new AuthenticatorInstallCommand($mock_call, $mock_seeder));
      $command->execute([]);

      $this->assertEquals("## Authenticator Installed successfully ##\n", $command->getDisplay());

    }
}
 