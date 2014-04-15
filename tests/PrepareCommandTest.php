<?php  namespace Jacopo\Authentication\Tests;

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
            ->with('config:publish', ['package' => 'jacopo/authentication' ])
            ->andReturn(true)
            ->getMock();

    $command = new CommandTester(new PrepareCommand($mock_call));
    $command->execute([]);

    $this->assertEquals("## Authentication prepared successfully ##\n", $command->getDisplay());

  }
}
 