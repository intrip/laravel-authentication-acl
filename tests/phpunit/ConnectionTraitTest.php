<?php  namespace Jacopo\Library\Tests;

use Jacopo\Library\Traits\ConnectionTrait;

/**
 * Test ConnectionTraitTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ConnectionTraitTest extends TestCase {

  protected $connection_helper;

  public function setUp()
  {
    parent::setUp();
    $this->connection_helper = new ConnectionTraitStub();
  }

  /**
   * @test
   **/
  public function itReturnTestinConnectionNameOnTesting()
  {
    $this->assertEquals($this->connection_helper->getConnectionName(), 'testbench');
  }

  /**
   * @test
   **/
  public function itReturnAuthenticationOnOtherEnv()
  {
    $this->app['env'] = 'production';
    $this->assertEquals($this->connection_helper->getConnectionName(), 'authentication');
  }
}

class ConnectionTraitStub
{
  use ConnectionTrait;
}
 