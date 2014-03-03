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

        $helper = new SentryAuthenticationHelper;
        $success = $helper->hasPermission(["_admin"]);
        $this->assertTrue($success);

        $success = $helper->hasPermission(["_admin"]);
        $this->assertFalse($success);

        $success = $helper->hasPermission([]);
        $this->assertTrue($success);
    }

    /**
     * @test
     **/
    public function it_check_current_user_can_edit_his_profile()
    {
        $helper = new SentryAuthenticationHelper;
        $user = new \StdClass;
        $user->id = 1;
        $mock_sentry = m::mock('StdClass')->shouldReceive('getUser')->andReturn($user)->getMock();
        \App::instance('sentry', $mock_sentry);

        $can = $helper->checkProfileEditPermission(1);

        $this->assertTrue($can);
    }

    /**
     * @test
     **/
    public function it_check_for_permission_to_edit_other_profiles()
    {
        $helper = m::mock('Jacopo\Authentication\Helpers\SentryAuthenticationHelper')->makePartial()->shouldReceive('hasPermission')->andReturn(true)->getMock();
        $user = new \StdClass;
        $user->id = 1;
        $mock_sentry = m::mock('StdClass')->shouldReceive('getUser')->andReturn($user)->getMock();
        \App::instance('sentry', $mock_sentry);

        $can = $helper->checkProfileEditPermission(2);

        $this->assertTrue($can);
    }

    /**
     * @test
     **/
    public function it_gets_user_emails_that_need_to_be_notificated_on_user_subscription()
    {
        $helper = new SentryAuthenticationHelper;
        $mock_users = m::mock('StdClass')->shouldReceive('lists')->with('email')->andReturn(["admin@admin.com"])->getMock();
        $mock_user_repo = m::mock('StdClass')->shouldReceive('findFromGroupName')->andReturn($mock_users)->getMock();
        \App::instance('user_repository', $mock_user_repo);

        $mail = $helper->getNotificationRegistrationUsersEmail();

        $this->assertEquals(["admin@admin.com"], $mail);
    }
}
 