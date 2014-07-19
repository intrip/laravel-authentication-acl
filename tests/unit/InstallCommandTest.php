<?php  namespace Jacopo\Authentication\Tests\Unit;

use InstallCommand;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test AuthenticatorInstallCommandTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class InstallCommandTest extends TestCase {

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
              ->with('migrate', ['--package' => 'jacopo/laravel-authentication-acl', '--database' => "authentication" ])
              ->andReturn(true)
              ->shouldReceive('call')
              ->once()
              ->with('asset:publish')
              ->andReturn(true)
              ->getMock();

      $mock_seeder = m::mock('DbSeeder')
              ->shouldReceive('run')
              ->once()
              ->getMock();

      $command = new CommandTester(new InstallCommand($mock_call, $mock_seeder));
      $command->execute([]);

      $this->assertEquals("## Laravel Authentication ACL Installed successfully ##\n", $command->getDisplay());

    }
}
 