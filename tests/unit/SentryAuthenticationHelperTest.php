<?php  namespace Jacopo\Authentication\Tests\Unit;

/**
 * Test SentryAuthenticationHelperTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Models\User;
use Mockery as m;
use Config;
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper;

class SentryAuthenticationHelperTest extends TestCase
{

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
        $mock_sentry = m::mock('StdClass')->shouldReceive('hasAnyAccess')->andReturn(true, false)->getMock();
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn($mock_sentry)->getMock();
        \App::instance('sentry', $mock_current);

        $has_permission = $this->sentry_auth_helper->hasPermission(["_admin"]);
        $this->assertTrue($has_permission);

        $has_permission = $this->sentry_auth_helper->hasPermission(["_admin"]);
        $this->assertFalse($has_permission);
    }

    /**
     * @test
     **/
    public function itDoesntCheckForEmptyPermission()
    {
        $mock_current = m::mock('StdClass')->shouldReceive('getUser')->andReturn(new User)->getMock();
        \App::instance('sentry', $mock_current);

        $has_permission = $this->sentry_auth_helper->hasPermission([]);
        $this->assertTrue($has_permission);
    }

    /**
     * @test
     **/
    public function it_check_current_user_can_edit_his_profile()
    {
        $user = new \StdClass;
        $user_id = 1;
        $user->id = $user_id;
        $this->mockSentryReturnUser($user);

        $can_edit = $this->sentry_auth_helper->checkProfileEditPermission($user_id);

        $this->assertTrue($can_edit);
    }

    /**
     * @test
     **/
    public function it_check_for_permission_to_edit_other_profiles()
    {
        $helper = m::mock('Jacopo\Authentication\Helpers\SentryAuthenticationHelper')->makePartial()->shouldReceive('hasPermission')->andReturn(true)
                   ->getMock();
        $user = new \StdClass;
        $user_id = 1;
        $user->id = $user_id;
        $this->mockSentryReturnUser($user);

        $different_user_id = 2;
        $can = $helper->checkProfileEditPermission($different_user_id);

        $this->assertTrue($can);
    }

    /**
     * @test
     **/
    public function itCheckCustomProfileEditPermission()
    {
        $custom_profile_edit_permission = ["_profile-editor"];
        Config::set('laravel-authentication-acl::permissions.edit_custom_profile', $custom_profile_edit_permission);

        $sentry_helper = new SentryAuthenticatorHelperStub;

        $can = $sentry_helper->checkCustomProfileEditPermission();

        $this->assertTrue($can);
    }

    /**
     * @test
     **/
    public function it_gets_user_emails_that_need_to_be_notificated_on_user_subscription()
    {
        $users_email = ["admin@admin.com"];
        $this->mockGetUserFromGroup($users_email);

        $mail = $this->sentry_auth_helper->getNotificationRegistrationUsersEmail();

        $this->assertEquals($users_email, $mail);
    }

    /**
     * @param $user
     * @return m\MockInterface
     */
    protected function mockSentryReturnUser($user)
    {
        $mock_sentry = m::mock('StdClass')->shouldReceive('getUser')->andReturn($user)->getMock();
        \App::instance('sentry', $mock_sentry);
    }

    protected function mockGetUserFromGroup(array $users_email)
    {
        $mock_users = m::mock('StdClass')->shouldReceive('lists')->with('email')->andReturn($users_email)->getMock();
        $mock_user_repo = m::mock('StdClass')->shouldReceive('findFromGroupName')->andReturn($mock_users)->getMock();
        \App::instance('user_repository', $mock_user_repo);
    }
}

class SentryAuthenticatorHelperStub extends SentryAuthenticationHelper
{
    public function hasPermission(array $permissions)
    {
        return true;
    }
}