<?php  namespace Jacopo\Authentication\Tests; 
use Jacopo\Authentication\Exceptions\AuthenticationErrorException;
use Mockery as m;
use App;
/**
 * Test AuthControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class AuthControllerTest extends TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_login_client_with_success()
    {
        $email = "mail@mail.com";
        $password = "pass";
        $remember = "1";
        $this->mockAuthenticatorSuccess($email, $password, $remember);

        $this->action('POST','Jacopo\Authentication\Controllers\AuthController@postClientLogin', ["email" => $email, "password" => $password, "remember" => $remember]);

        $this->assertRedirectedTo('/');
    }

    /**
     * @test
     **/
    public function it_login_admin_with_success()
    {
        $email = "mail@mail.com";
        $password = "pass";
        $remember = "1";
        $this->mockAuthenticatorSuccess($email, $password, $remember);

        $this->action('POST','Jacopo\Authentication\Controllers\AuthController@postAdminLogin', ["email" => $email, "password" => $password, "remember" => $remember]);

        $this->assertRedirectedTo('/admin/users/list');
    }

    /**
     * @param $email
     * @param $password
     * @param $remember
     */
    private function mockAuthenticatorSuccess($email, $password, $remember)
    {
        $mock_authenticator_success = m::mock('StdClass')->shouldReceive('authenticate')->with([
                                                                                               "email" => $email, "password" => $password], $remember)->getMock();
        App::instance('authenticator', $mock_authenticator_success);
    }
    
    /**
     * @test
     **/
    public function it_login_client_with_error()
    {
        $email = "mail@mail.com";
        $password = "pass";
        $remember = "1";

        $this->mockAuthenticationFails($email, $password, $remember);

        $this->action('POST','Jacopo\Authentication\Controllers\AuthController@postClientLogin', ["email" => $email, "password" => $password, "remember" => $remember]);

        $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\AuthController@getClientLogin');
        $this->assertSessionHasErrors();
    }

    /**
     * @test
     **/
    public function it_login_admin_with_error()
    {
        $email = "mail@mail.com";
        $password = "pass";
        $remember = "1";

        $this->mockAuthenticationFails($email, $password, $remember);

        $this->action('POST',g'Jacopo\Authentication\Controllers\AuthController@postAdminLogin', ["email" => $email, "password" => $password, "remember" => $remember]);

        $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\AuthController@getAdminLogin');
        $this->assertSessionHasErrors();
    }

    /**
     * @param $email
     * @param $password
     * @param $remember
     */
    private function mockAuthenticationFails($email, $password, $remember)
    {
        $mock_authenticator_fail = m::mock('StdClass')->shouldReceive('authenticate')->with([
                                                                                            "email" => $email, "password" => $password], $remember)->once()->andThrow(new AuthenticationErrorException())->shouldReceive('getErrors')->once()->andReturn([])->getMock();
        App::instance('authenticator', $mock_authenticator_fail);
    }

}
 