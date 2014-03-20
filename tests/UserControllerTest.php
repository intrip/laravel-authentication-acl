<?php  namespace Jacopo\Authentication\Tests; 
use Jacopo\Library\Exceptions\ValidationException;
use Mockery as m;
use App;
/**
 * Test UserControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserControllerTest extends TestCase {

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
        $mock_auth = m::mock('StdClass')->shouldReceive('getToken')->andReturn("1")->getMock();
        App::instance('authenticator', $mock_auth);

        $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '', ["email" => "email", "token" => "1"]);
    }
}
 