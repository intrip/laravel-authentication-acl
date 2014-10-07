<?php  namespace Jacopo\Authentication\Tests\Unit;

use PrepareCommand;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test AuthenticatorInstallCommandTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class PrepareCommandTest extends TestCase {

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
            ->with('config:publish', ['package' => 'jacopo/laravel-authentication-acl' ])
            ->andReturn(true)
            ->getMock();

    $command = new CommandTester(new PrepareCommand($mock_call));
    $command->execute([]);

    $this->assertEquals("## Laravel Authentication ACL prepared successfully ##\n", $command->getDisplay());

  }
}
 