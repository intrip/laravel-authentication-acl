<?php namespace Jacopo\Authentication\Tests;

use App;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Jacopo\Authentication\Models\User;
use Mockery as m;

class SentryAuthenticatorTest extends DbTestCase
{

  public function tearDown()
  {
    m::close();
  }

  public function testGetUserWorks()
  {
    $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andReturn(true)->getMock();
    App::instance('sentry', $mock_sentry);

    $authenticator = new SentryAuthenticator;
    $success       = $authenticator->getUser("");
    $this->assertTrue($success);
  }

  /**
   * @test
   **/
  public function it_authenticate_user_succesfully()
  {
    $credentials = [];
    $remember    = false;

    $this->mockSentryAuthenticateSuccess($credentials, $remember);
    $authenticator = new SentryAuthenticator;
    $authenticator->authenticate($credentials, $remember);
  }

  /**
   * @param $credentials
   * @param $remember
   */
  private function mockSentryAuthenticateSuccess($credentials, $remember)
  {
    $user_stub = new User();
    $mock_sentry_authenticate = m::mock('StdClass')->shouldReceive('authenticate')->once()->with($credentials, $remember)->andReturn($user_stub)->getMock();
    App::instance('sentry', $mock_sentry_authenticate);
  }

  /**
   * @test
   * @expectedException Jacopo\Authentication\Exceptions\AuthenticationErrorException
   **/
  public function it_throw_authenticationErrorException_on_authenticate()
  {
    $credentials = [];
    $remember    = false;

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
    $mock_sentry_authenticate = m::mock('StdClass')->shouldReceive('authenticate')->once()->with($credentials, $remember)->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException())->getMock();
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

      $authenticator =  new SentryAuthenticator();
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

      $authenticator =  new SentryAuthenticator();
      $authenticator->loginById($user_id, $remember);
      $this->assertFalse($authenticator->getErrors()->isEmpty());
  }
    
  /**
   * @test
   **/
  public function itCheckIfUserExistsOnLogin()
  {
      $user_id = 1;
      $remember = false;
      $this->mockSentryCannotFindUser();

      $authenticator =  new SentryAuthenticator();
      $authenticator->loginById($user_id, $remember);
  }

  /**
   * @test
   **/
  public function itCheckIfUserIsActiveOnLogin()
  {
      $user_id = 1;
      $remember = false;
      $user_stub           = new User;
      $this->mockSentryFindUserNotActive($user_stub, $remember);

      $authenticator =  new SentryAuthenticator();
      $authenticator->loginById($user_id, $remember);
  }

  /**
   * @test
   * @expectedException \Jacopo\Authentication\Exceptions\AuthenticationErrorException
   **/
  public function itLogoutBannedUsersOnAuthenticate()
  {
    $user        = new User(["banned" => 1]);
    $credentials = [];
    $remember    = false;

    $this->mockSentrySuccessReturnUserAndLogout($credentials, $remember, $user);
    $authenticator = new SentryAuthenticator;
    $authenticator->authenticate($credentials, $remember);

    $this->assertFalse($authenticator->getErrors()->isEmpty());
  }

  /**
   * @param $credentials
   * @param $remember
   * @param $user
   */
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
    $user_stub       = new \StdClass;
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
    $mock_sentry = m::mock('StdClass')->shouldReceive('findUserById')->once()->andReturn($user_stub)->getMock();
    App::instance('sentry', $mock_sentry);
  }

  /**
   * @test
   **/
  public function it_get_logged_user()
  {
    $sentry_mock = m::mock('StdClass')->shouldReceive('getUser')->once()->andReturn(true)->getMock();
    App::instance('sentry', $sentry_mock);

    $authenticator = new SentryAuthenticator;
    $authenticator->getLoggedUser();
  }

  /**
   * @test
   **/
  public function it_gets_csrf_token()
  {
    $email         = "test@mailtest.com";
    $mock_user     = $this->createMockUserForPasswordCode();
    $authenticator = $this->createMockGetUser($email, $mock_user);

    $authenticator->getToken($email);
  }

  /**
   * @return m\MockInterface
   */
  private function createMockUserForPasswordCode()
  {
    $mock_user = m::mock('StdClass')->shouldReceive('getResetPasswordCode')->once()->getMock();

    return $mock_user;
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
     * @param $user_id
     */
    private function mockLoginSuccess($user_id)
    {
        $user_stub = new User;

        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserById')->once()->with($user_id)->andReturn($user_stub)->shouldReceive('login')->once()->getMock();
        App::instance('sentry', $mock_sentry);
    }

    /**
     * @param $user_id
     */
    private function mockLoginBannedUser($user_id)
    {
        $user_stub = new User(["banned" => 1]);

        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserById')->once()->with($user_id)->andReturn($user_stub)->shouldReceive('login')->once()
                ->shouldReceive('logout')->once()->getMock();
        App::instance('sentry', $mock_sentry);
    }

    private function mockSentryCannotFindUser()
    {
        $mock_sentry_no_user = m::mock('StdClass')->shouldReceive('findUserById')->once()->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException())->getMock();
        App::instance('sentry', $mock_sentry_no_user);
    }

    /**
     * @param $user_stub
     * @param $remember
     */
    private function mockSentryFindUserNotActive($user_stub, $remember)
    {
        $mock_sentry_no_user = m::mock('StdClass')->shouldReceive('findUserById')->once()->andReturn($user_stub)->shouldReceive('login')->once()->with($user_stub, $remember)->andThrow(new UserNotActivatedException)->getMock();
        App::instance('sentry', $mock_sentry_no_user);
    }

}