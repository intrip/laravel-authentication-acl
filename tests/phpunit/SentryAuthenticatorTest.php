<?php namespace Jacopo\Authentication\Tests\Unit;

use App;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Tests\Unit\Traits\UserFactory;
use Mockery as m;
use Event;

class SentryAuthenticatorTest extends DbTestCase
{
    use UserFactory;

    protected $authenticator;

    public function setUp()
    {
        parent::setUp();
        $this->authenticator = new SentryAuthenticator;
        $this->initializeUserHasher();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function canAuthenticateUser()
    {
        $credentials = [];
        $remember = false;

        $this->mockSentryAuthenticateSuccess($credentials, $remember, new User);
        $authenticator = new SentryAuthenticator;
        $authenticator->authenticate($credentials, $remember);
    }

    /**
     * @test
     **/
    public function firesEventBeforeAndAfterAuthenticate()
    {
        $this->mockSentryAuthenticateSuccess([], false, new User);
        $authenticator = new SentryAuthenticator;

        $fired_authenticating = false;
        Event::listen('service.authenticating', function () use (&$fired_authenticating)
        {
            $fired_authenticating = true;
        });
        $fired_authenticated = false;
        Event::listen('service.authenticated', function () use (&$fired_authenticated)
        {
            $fired_authenticated = true;
        });

        $authenticator->authenticate([], false);
        $this->assertTrue($fired_authenticating, 'Event service.authenticating has not been fired.');
        $this->assertTrue($fired_authenticated, 'Event service.authenticating has not been fired.');
    }

    /**
     * @test
     **/
    public function firesEventBeforeAndAfterLogout()
    {
        $fired_delogging = false;
        Event::listen('service.delogging', function () use (&$fired_delogging)
        {
            $fired_delogging = true;
        });
        $fired_delogged = false;
        Event::listen('service.delogged', function () use (&$fired_delogged)
        {
            $fired_delogged = true;
        });

        $authenticator = new SentryAuthenticator();

        $authenticator->logout();
        $this->assertTrue($fired_delogging, 'Event service.delogging has not been fired.');
        $this->assertTrue($fired_delogged, 'Event service.delogged has not been fired.');
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\AuthenticationErrorException
     **/
    public function itDoesntAuthenticateBannedUser()
    {
        $credentials = [];
        $remember = false;

        $banned_user = new User(["banned" => true]);
        $this->mockSentrySuccessReturnUserAndLogout($credentials, $remember, $banned_user);
        $authenticator = new SentryAuthenticator;

        $authenticator->authenticate($credentials, $remember);
    }

    private function mockSentryAuthenticateSuccess($credentials, $remember, $user_stub)
    {

        $mock_sentry_authenticate =
                m::mock('StdClass')->shouldReceive('authenticate')->once()->with($credentials, $remember)->andReturn($user_stub)->getMock();
        App::instance('sentry', $mock_sentry_authenticate);
    }

    /**
     * @test
     */
    public function canGetUser()
    {
        $mock_sentry = m::mock('Cartalyst\Sentry\Sentry')
                        ->shouldReceive('findUserByLogin')
                        ->andReturn(true)
                        ->getMock();
        App::instance('sentry', $mock_sentry);

        $authenticator = new SentryAuthenticator;
        $success = $authenticator->getUser("");
        $this->assertTrue($success);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function canHandleUserNotFoundInGetUser()
    {
        $mock_sentry = m::mock('Cartalyst\Sentry\Sentry')
                        ->shouldReceive('findUserByLogin')
                        ->andThrow(new UserNotFoundException)->getMock();
        App::instance('sentry', $mock_sentry);

        $authenticator = new SentryAuthenticator;
        $authenticator->getUser("");
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\AuthenticationErrorException
     **/
    public function canHandleAuthenticationErrors()
    {
        $credentials = [];
        $remember = false;

        $this->mockSentryAuthenticateError($credentials, $remember);
        $authenticator = new SentryAuthenticator;
        $authenticator->authenticate($credentials, $remember);
    }

    /**
     * @param $credentials
     * @param $remember
     */
    private function mockSentryAuthenticateError($credentials, $remember)
    {
        $mock_sentry_authenticate = m::mock('Cartalyst\Sentry\Sentry')
                                     ->shouldReceive('authenticate')
                                     ->once()
                                     ->with($credentials, $remember)
                                     ->andThrow(new UserNotFoundException())
                                     ->getMock();
        App::instance('sentry', $mock_sentry_authenticate);
    }

    /**
     * @test
     * @group login
     **/
    public function itLoginUserById()
    {
        $user_id = 1;
        $remember = false;
        $this->mockLoginSuccess($user_id);

        $authenticator = new SentryAuthenticator();
        $authenticator->loginById($user_id, $remember);
    }

    /**
     * @test
     **/
    public function itCheckIfUserIsBannedOnLogin()
    {
        $user_id = 1;
        $remember = false;
        $this->mockLoginBannedUser($user_id);

        $authenticator = new SentryAuthenticator();
        $authenticator->loginById($user_id, $remember);
        $this->assertFalse($authenticator->getErrors()->isEmpty());
    }


    /**
     * @param $user_id
     */
    private function mockLoginBannedUser($user_id)
    {
        $user_stub = new User(["banned" => 1]);

        $mock_sentry =
                m::mock('Cartalyst\Sentry\Sentry')
                 ->shouldReceive('findUserById')
                 ->once()
                 ->with($user_id)
                 ->andReturn($user_stub)
                 ->shouldReceive('login')
                 ->once()
                 ->shouldReceive('logout')
                 ->once()
                 ->getMock();
        App::instance('sentry', $mock_sentry);
    }

    /**
     * @test
     **/
    public function itCheckIfUserExistsOnLogin()
    {
        $user_id = 1;
        $remember = false;
        $this->mockSentryCannotFindUser();

        $authenticator = new SentryAuthenticator();
        $authenticator->loginById($user_id, $remember);
    }

    /**
     * @test
     **/
    public function itCheckIfUserIsActiveOnLogin()
    {
        $user_id = 1;
        $remember = false;
        $user_stub = new User;
        $this->mockSentryFindUserNotActive($user_stub, $remember);

        $authenticator = new SentryAuthenticator();
        $authenticator->loginById($user_id, $remember);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\AuthenticationErrorException
     **/
    public function itLogoutBannedUsersOnAuthenticate()
    {
        $user = new User(["banned" => 1]);
        $credentials = [];
        $remember = false;

        $this->mockSentrySuccessReturnUserAndLogout($credentials, $remember, $user);
        $authenticator = new SentryAuthenticator;
        $authenticator->authenticate($credentials, $remember);

        $this->assertFalse($authenticator->getErrors()->isEmpty());
    }

    protected function mockSentrySuccessReturnUserAndLogout($credentials, $remember, $user)
    {
        $mock_sentry_authenticate = m::mock('StdClass')
                                     ->shouldReceive('authenticate')
                                     ->once()
                                     ->with($credentials, $remember)
                                     ->andReturn($user)
                                     ->shouldReceive('logout')
                                     ->once()
                                     ->getMock();
        App::instance('sentry', $mock_sentry_authenticate);
    }

    /**
     * @test
     * @expectedException Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    public function itHanleErrorsOnGetUser()
    {
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException)->getMock();
        App::instance('sentry', $mock_sentry);

        $auth = new SentryAuthenticator();
        $auth->getUser("");
    }

    /**
     * @test
     */
    public function itCanGetActivationToken()
    {
        $mock_user = m::mock('StdClass')
                      ->shouldReceive('getActivationCode')
                      ->andReturn(true)
                      ->getMock();
        $mock_auth = m::mock('Jacopo\Authentication\Classes\SentryAuthenticator')
                      ->makePartial()
                      ->shouldReceive('getUser')
                      ->andReturn($mock_user)
                      ->getMock();

        $token = $mock_auth->getActivationToken("");
        $this->assertEquals(true, $token);
    }

    /**
     * @test
     **/
    public function canGetUserById()
    {
        $user_stub = new \StdClass;
        $user_stub->name = 1;
        $this->mockSentryFindUserById($user_stub);
        $user = App::make('authenticator')->getUserById(1);

        $this->assertEquals($user, $user_stub);
    }

    /**
     * @param $user_stub
     */
    private function mockSentryFindUserById($user_stub)
    {
        $mock_sentry = m::mock('StdClass')
                        ->shouldReceive('findUserById')
                        ->once()
                        ->andReturn($user_stub)
                        ->getMock();
        App::instance('sentry', $mock_sentry);
    }

    /**
     * @test
     **/
    public function itGetLoggedUser()
    {
        $sentry_mock = m::mock('StdClass')
                        ->shouldReceive('getUser')
                        ->once()
                        ->andReturn(true)
                        ->getMock();
        App::instance('sentry', $sentry_mock);

        $authenticator = new SentryAuthenticator;
        $authenticator->getLoggedUser();
    }

    /**
     * @test
     **/
    public function canGetCsrfTiken()
    {
        $email = "test@mailtest.com";
        $mock_user = $this->mockUserResetPasswordCode();
        $authenticator = $this->createMockGetUser($email, $mock_user);

        $authenticator->getToken($email);
    }

    /**
     * @return m\MockInterface
     */
    private function mockUserResetPasswordCode()
    {
        $mock_user = m::mock('StdClass')
                      ->shouldReceive('getResetPasswordCode')
                      ->once()
                      ->getMock();

        return $mock_user;
    }

    /**
     * @param $email
     * @param $mock_user
     * @return mixed
     */
    private function createMockGetUser($email, $mock_user)
    {
        $authenticator = m::mock('Jacopo\Authentication\Classes\SentryAuthenticator')
                          ->makePartial()
                          ->shouldReceive('getUser')
                          ->once()
                          ->with($email)
                          ->andReturn($mock_user)
                          ->getMock();

        return $authenticator;
    }

    /**
     * @param $user_id
     */
    private function mockLoginSuccess($user_id)
    {
        $user_stub = new User;

        $mock_sentry = m::mock('Cartalyst\Sentry\Sentry')
                        ->shouldReceive('findUserById')
                        ->once()
                        ->with($user_id)
                        ->andReturn($user_stub)
                        ->shouldReceive('login')
                        ->once()
                        ->getMock();
        App::instance('sentry', $mock_sentry);
    }

    private function mockSentryCannotFindUser()
    {
        $mock_sentry_no_user = m::mock('Cartalyst\Sentry\Sentry')
                                ->shouldReceive('findUserById')
                                ->once()
                                ->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException())
                                ->getMock();
        App::instance('sentry', $mock_sentry_no_user);
    }

    /**
     * @param $user_stub
     * @param $remember
     */
    private function mockSentryFindUserNotActive($user_stub, $remember)
    {
        $mock_sentry_no_user = m::mock('Cartalyst\Sentry\Sentry')
                                ->shouldReceive('findUserById')
                                ->once()
                                ->andReturn($user_stub)
                                ->shouldReceive('login')
                                ->once()
                                ->with($user_stub, $remember)
                                ->andThrow(new UserNotActivatedException)
                                ->getMock();
        App::instance('sentry', $mock_sentry_no_user);
    }

    /**
     * @test
     **/
    public function canCheckForLoggedUserUnbanned()
    {
        $unbanned_user = $this->make('Jacopo\Authentication\Models\User', array_merge($this->getUserStub(), ["banned" => 0]))->first();
        $this->authenticator->loginById($unbanned_user->id);

        $this->assertTrue($this->authenticator->check());
    }

    /**
     * @test
     * @group err
     **/
    public function failCheckWithBannedUser()
    {
        $unbanned_user = $this->make('Jacopo\Authentication\Models\User', array_merge($this->getUserStub(), ["banned" => 0]))->first();
        $authenticator = new SentryAuthenticatorStubLogout();
        $authenticator->loginById($unbanned_user->id);
        $this->banUser($unbanned_user);
        App::make('sentry')->setUser($unbanned_user);

        $this->assertFalse($authenticator->check());
        $this->assertTrue($authenticator->logged_out);
    }

    /**
     * @param $unbanned_user
     */
    protected function banUser($unbanned_user)
    {
        $unbanned_user->update(["banned" => 1]);
    }
}

class SentryAuthenticatorStubLogout extends SentryAuthenticator
{
    public $logged_out = false;

    public function logout()
    {
        $this->logged_out = true;
    }
}