<?php namespace Jacopo\Authentication\Tests;
use Jacopo\Authentication\Exceptions\AuthenticationErrorException;
use Mockery as m;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use App;

class SentryAuthenticatorTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

	public function testGetUserWorks()
	{
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andReturn(true)->getMock();
        App::instance('sentry', $mock_sentry);

        $authenticator = new SentryAuthenticator;
        $success = $authenticator->getUser("");
        $this->assertTrue($success);
	}

    /**
     * @test
     **/
    public function it_authenticate_user_succesfully()
    {
        $credentials = [];
        $remember = false;

        $this->mockSentryAuthenticateSuccess($credentials, $remember);
        $authenticator = new SentryAuthenticator;
        $authenticator->authenticate($credentials, $remember);
    }

    /**
     * @test
     * @expectedException Jacopo\Authentication\Exceptions\AuthenticationErrorException
     **/
    public function it_throw_authenticationErrorException_on_authenticate()
    {
        $credentials = [];
        $remember = false;

        $this->mockSentryAuthenticateError($credentials, $remember);
        $authenticator = new SentryAuthenticator;
        $authenticator->authenticate($credentials, $remember);
    }

    /**
     * @expectedException Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    public function testGetUserWorksThrowsException()
	{
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException)->getMock();
        App::instance('sentry', $mock_sentry);

        $auth = new SentryAuthenticator();
        $auth->getUser("");
	}

    public function testGetActivationTokenWorks()
    {
        $mock_user = m::mock('StdClass')->shouldReceive('getActivationCode')->andReturn(true)->getMock();
        $mock_auth = m::mock('Jacopo\Authentication\Classes\SentryAuthenticator')->makePartial();
        $mock_auth->shouldReceive('getUser')->andReturn($mock_user);

        $token = $mock_auth->getActivationToken("");
        $this->assertEquals(true, $token);
    }

    /**
     * @test
     **/
    public function it_get_user_by_id()
    {
        $user_stub = new \StdClass;
        $user_stub->name = 1;
        $this->mockSentryFindUserById($user_stub);
        $user = App::make('authenticator')->getUserById(1);

        $this->assertEquals($user, $user_stub);
    }

    /**
     * @test
     **/
    public function it_get_logged_user()
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
    public function it_gets_csrf_token()
    {
        $email = "test@mailtest.com";
        $mock_user     = $this->createMockUserForPasswordCode();
        $authenticator = $this->createMockGetUser($email, $mock_user);

        $authenticator->getToken($email);
    }
    
    /**
     * @param $user_stub
     */
    private function mockSentryFindUserById($user_stub)
    {
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserById')->once()->andReturn($user_stub)->getMock();
        App::instance('sentry', $mock_sentry);
    }

    /**
     * @param $credentials
     * @param $remember
     */
    private function mockSentryAuthenticateSuccess($credentials, $remember)
    {
        $mock_sentry_authenticate = m::mock('StdClass')->shouldReceive('authenticate')->once()->with($credentials, $remember)->getMock();
        App::instance('sentry', $mock_sentry_authenticate);
    }

    /**
     * @param $credentials
     * @param $remember
     */
    private function mockSentryAuthenticateError($credentials, $remember)
    {
        $mock_sentry_authenticate = m::mock('StdClass')->shouldReceive('authenticate')->once()->with($credentials, $remember)
            ->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException())
            ->getMock();
        App::instance('sentry', $mock_sentry_authenticate);
    }

    /**
     * @param $email
     * @param $mock_user
     * @return mixed
     */
    private function createMockGetUser($email, $mock_user)
    {
        $authenticator = m::mock('Jacopo\Authentication\Classes\SentryAuthenticator')->makePartial()->shouldReceive('getUser')->once()->with($email)->andReturn($mock_user)->getMock();

        return $authenticator;
    }

    /**
     * @return m\MockInterface
     */
    private function createMockUserForPasswordCode()
    {
        $mock_user = m::mock('StdClass')->shouldReceive('getResetPasswordCode')->once()->getMock();

        return $mock_user;
    }

}