<?php  namespace Jacopo\Authentication\Tests;

/**
 * Test SentryAuthenticationHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mockery as m;
use Config;
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;

class SentryAuthenticationHelperTest extends TestCase {

    protected $sentry_auth_helper;

    public function setUp()
    {
        parent::setUp();
        $this->sentry_auth_helper = new SentryAuthenticationHelper;
    }

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

        $success = $this->sentry_auth_helper->hasPermission(["_admin"]);
        $this->assertTrue($success);

        $success = $this->sentry_auth_helper->hasPermission(["_admin"]);
        $this->assertFalse($success);

        $success = $this->sentry_auth_helper->hasPermission([]);
        $this->assertTrue($success);
    }

    /**
     * @test
     **/
    public function it_check_current_user_can_edit_his_profile()
    {
        $user = new \StdClass;
        $user->id = 1;
        $mock_sentry = m::mock('StdClass')->shouldReceive('getUser')->andReturn($user)->getMock();
        \App::instance('sentry', $mock_sentry);

        $can = $this->sentry_auth_helper->checkProfileEditPermission(1);

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
    public function itCheckCustomProfileEditPermission()
    {
        $custom_profile_edit_permission = ["_profile-editor"];
        Config::set('laravel-authentication-acl::permissions.edit_custom_profile',
                    $custom_profile_edit_permission);

        $mock_sentry = m::mock('StdClass')->shouldReceive('hasAnyAccess')->with($custom_profile_edit_permission)->andReturn(true)->getMock();
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn($mock_sentry)->getMock();
        \App::instance('sentry', $mock_current);

        $can = $this->sentry_auth_helper->checkCustomProfileEditPermission();

        $this->assertTrue($can);
    }

    /**
     * @test
     **/
    public function it_gets_user_emails_that_need_to_be_notificated_on_user_subscription()
    {
        $mock_users = m::mock('StdClass')->shouldReceive('lists')->with('email')->andReturn(["admin@admin.com"])->getMock();
        $mock_user_repo = m::mock('StdClass')->shouldReceive('findFromGroupName')->andReturn($mock_users)->getMock();
        \App::instance('user_repository', $mock_user_repo);

        $mail = $this->sentry_auth_helper->getNotificationRegistrationUsersEmail();

        $this->assertEquals(["admin@admin.com"], $mail);
    }
}
 