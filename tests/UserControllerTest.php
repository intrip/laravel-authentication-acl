<?php  namespace Jacopo\Authentication\Tests; 
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Exceptions\ValidationException;
use Mockery as m;
use App;
/**
 * Test UserControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserControllerTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_run_register_and_return_success_on_post_register()
    {
        $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->getMock();
        App::instance('register_service', $mock_register);
        $this->action('POST','Jacopo\Authentication\Controllers\UserController@postSignup');

    $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\UserController@signup');

        $this->assertSessionHas('message');
    }
    
    /**
     * @test
     **/
    public function it_run_register_and_return_errors_on_post_register()
    {
        $mock_register = m::mock('StdClass')->shouldReceive('register')
            ->once()
            ->andThrow(new ValidationException())
            ->shouldReceive('getErrors')
            ->once()
            ->getMock();
        App::instance('register_service', $mock_register);
        $this->action('POST','Jacopo\Authentication\Controllers\UserController@postSignup');

        $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\UserController@signup');

        $this->assertSessionHasErrors();
    }

    /**
     * @test
     **/
    public function it_show_the_signup_view_on_signup()
    {
        $response = $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@signup');

        $this->assertResponseOk();
    }

    /**
     * @test
     **/
    public function it_show_view_with_success_if_token_is_valid()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,$token)->getMock();
        App::instance('register_service', $mock_service);

        $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '', ["email" => $email, "token" => $token]);

        $this->assertResponseOk();
    }

    /**
     * @test
     **/
    public function it_show_view_with_error_if_token_is_invalid()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')
            ->once()
            ->with($email,$token)
            ->andThrow( new \Jacopo\Authentication\Exceptions\TokenMismatchException)
            ->shouldReceive('getErrors')
            ->once()
            ->andReturn("")
            ->getMock();
        App::instance('register_service', $mock_service);

        $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '', ["email" => $email, "token" => $token]);

        $this->assertResponseOk();
        $this->assertViewHas('errors');
    }

    /**
     * @test
     **/
    public function it_show_view_errors_if_user_is_not_found()
    {
        $email = "mail";
        $token = "_token";
        $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')
            ->once()
            ->with($email,$token)
            ->andThrow( new \Jacopo\Authentication\Exceptions\UserNotFoundException())
            ->shouldReceive('getErrors')
            ->once()
            ->andReturn("")
            ->getMock();
        App::instance('register_service', $mock_service);

        $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '', ["email" => $email, "token" => $token]);

        $this->assertResponseOk();
        $this->assertViewHas('errors');
    }
}
 