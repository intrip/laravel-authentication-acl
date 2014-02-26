<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test SentryAuthenticationHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;

class SentryAuthenticationHelperTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_check_has_permissions()
    {
        $mock_sentry = m::mock('StdClass')->shouldReceive('hasAnyAccess')->andReturn(true,false)->getMock();
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn($mock_sentry)->getMock();
        \App::instance('sentry', $mock_current);

        $success = SentryAuthenticationHelper::hasPermission(["_admin"]);
        $this->assertTrue($success);

        $success = SentryAuthenticationHelper::hasPermission(["_admin"]);
        $this->assertFalse($success);

        // ceck also if no permission is given
        $success = SentryAuthenticationHelper::hasPermission([]);
        $this->assertTrue($success);
    }
}
 