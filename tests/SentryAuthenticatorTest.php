<?php
use Mockery as m;
use Jacopo\Authentication\Classes\SentryAuthenticator;
use Jacopo\Authentication\Tests\TestCase;

class SentryAuthenticatorTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

	public function testGetUserWorks()
	{
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andReturn(true)->getMock();
        App::instance('sentry', $mock_sentry);

        $auth = new SentryAuthenticator();
        $success = $auth->getUser("");
        $this->assertTrue($success);
	}

    /**
     * @expectedException Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    public function testGetUserWorksThrowsException()
	{
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserByLogin')->andThrow(new \Cartalyst\Sentry\Users\UserNotFoundException)->getMock();
        App::instance('sentry', $mock_sentry);

        $auth = new SentryAuthenticator();
        $success = $auth->getUser("");
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
        $mock_sentry = m::mock('StdClass')->shouldReceive('findUserById')
            ->once()
            ->andReturn($user_stub)
            ->getMock();
        App::instance('sentry', $mock_sentry);
        $user = App::make('authenticator')->getUserById(1);

        $this->assertEquals($user, $user_stub);
    }

}