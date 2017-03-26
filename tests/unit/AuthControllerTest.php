<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use Illuminate\Support\Facades\Event;
use LaravelAcl\Authentication\Exceptions\AuthenticationErrorException;
use LaravelAcl\Authentication\Tests\Unit\Traits\Helper;
use LaravelAcl\Authentication\Tests\Unit\Traits\UserFactory;
use Mockery as m;
use Config;
use App;

/**
 * Test AuthControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class AuthControllerTest extends DbTestCase {
    use UserFactory, Traits\MailTracking;

    protected $current_email;

    public function setUp()
    {
        parent::setUp();
        $this->initializeUserHasher();
        $this->current_user = $this->make('LaravelAcl\Authentication\Models\User', $this->getUserStub())->first();
        $this->current_email = $this->current_user->email;
    }

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

        $response = $this->post('/login', [
                "email"    => $email,
                "password" => $password,
                "remember" => $remember
        ]);
        $response->assertRedirect(Config::get('acl_base.user_login_redirect_url'));
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

        $response = $this->post('user/login', [
                "email"    => $email,
                "password" => $password,
                "remember" => $remember
        ]);

        $response->assertRedirect('/admin/users/dashboard');
    }

    /**
     * @param $email
     * @param $password
     * @param $remember
     */
    private function mockAuthenticatorSuccess($email, $password, $remember)
    {
        $mock_authenticator_success = m::mock('StdClass')
                                       ->shouldReceive('authenticate')
                                       ->with([
                                                      "email"    => $email,
                                                      "password" => $password
                                              ], $remember)->getMock();
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

        $response = $this->post("/login", [
                "email"    => $email,
                "password" => $password,
                "remember" => $remember
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('errors');
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

        $response = $this->post('user/login', [
                "email"    => $email,
                "password" => $password,
                "remember" => $remember
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHas('errors');
    }

    /**
     * @param $email
     * @param $password
     * @param $remember
     */
    private function mockAuthenticationFails($email, $password, $remember)
    {
        $mock_authenticator_fail = m::mock('StdClass')->shouldReceive('authenticate')->with([
                                                                                                    "email"    => $email,
                                                                                                    "password" => $password
                                                                                            ], $remember)->once()
                                    ->andThrow(new AuthenticationErrorException())->shouldReceive('getErrors')->once()->andReturn([])->getMock();
        App::instance('authenticator', $mock_authenticator_fail);
    }

    /**
     * @test
     * @jtodoIMP TODO fix this when try to send mail
     **/
    public function it_process_recovery_data_and_redirect_with_success()
    {
        $response = $this->post('/user/reminder', ["email" => $this->current_email]);

        $response->assertRedirect('/user/reminder-success');
        $this->seeEmailWasSent();
        $this->seeEmailTo($this->current_email);
        $this->seeEmailSubject('Password recovery request');
        $this->seeEmailContains('We received a request to change your password, if you authorize it');
    }

    /**
     * @test
     **/
    public function it_process_recovery_and_show_errors()
    {
        $mock_reminder_service = m::mock('LaravelAcl\Authentication\Services\ReminderService')
                                  ->shouldReceive('send')
                                  ->once()
                                  ->andThrow(new AuthenticationErrorException)
                                  ->shouldReceive('getErrors')
                                  ->getMock();
        $this->app->instance('LaravelAcl\Authentication\Services\ReminderService', $mock_reminder_service);

        $response = $this->post('/user/reminder');

        $response->assertRedirect('/user/recovery-password');
        $response->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function it_change_password_with_success()
    {
        $mock_reminder_service = m::mock('LaravelAcl\Authentication\Services\ReminderService')
                                  ->shouldReceive('reset')
                                  ->once()
                                  ->getMock();

        $this->app->instance('LaravelAcl\Authentication\Services\ReminderService', $mock_reminder_service);

        $response = $this->post('/user/change-password', ["password" => "newpassword"]);
        $response->assertRedirect('/user/change-password-success');
    }

    /**
     * @test
     **/
    public function it_change_password_with_error()
    {
        $mock_reminder_service = m::mock('LaravelAcl\Authentication\Services\ReminderService')
                                  ->shouldReceive('reset')
                                  ->once()
                                  ->andThrow(new AuthenticationErrorException)
                                  ->shouldReceive('getErrors')
                                  ->getMock();
        $this->app->instance('LaravelAcl\Authentication\Services\ReminderService', $mock_reminder_service);

        $response = $this->post('/user/change-password', ["password" => "newpassword"]);

        $response->assertRedirect('/user/change-password');
        $response->assertSessionHas('errors');
    }

    /**
     * @test
     **/
    public function it_validate_password_on_change_password()
    {
        $response = $this->post('/user/change-password', ["password" => ""]);

        $response->assertRedirect('/user/change-password');
        $response->assertSessionHas('errors');
        $response->assertSessionHas("_old_input");
    }
}
 
