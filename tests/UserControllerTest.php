<?php  namespace Jacopo\Authentication\Tests;

use App;
use Config;
use Illuminate\Support\Facades\Facade;
use Jacopo\Authentication\Models\User;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Exceptions\ValidationException;
use Mockery as m;

/**
 * Test UserControllerTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserControllerTest extends DbTestCase
{

  public function tearDown()
  {
    m::close();
  }

  /**
   * @test
   **/
  public function it_run_signup_and_return_success_on_post_signup()
  {
    $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->getMock();
    App::instance('register_service', $mock_register);

    $this->action('POST', 'Jacopo\Authentication\Controllers\UserController@postSignup');

    $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\UserController@signupSuccess');

  }

  /**
   * @test
   **/
  public function it_run_signup_and_return_errors_on_post_signup()
  {
    $mock_register = m::mock('StdClass')->shouldReceive('register')->once()->andThrow(new ValidationException())->shouldReceive('getErrors')->once()->getMock();
    App::instance('register_service', $mock_register);

    $this->action('POST', 'Jacopo\Authentication\Controllers\UserController@postSignup');

    $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\UserController@signup');
    $this->assertSessionHasErrors();
  }

  /**
   * @test
   **/
  public function it_show_the_signup_view_on_signup()
  {
    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@signup');

    $this->assertResponseOk();
  }

  /**
   * @test
   **/
  public function it_showConfirmationEmailSuccessOnSignup_ifEmailConfirmationIsEnabled()
  {
    $active = true;
    $this->mockConfigGetEmailConfirmation($active);

    \View::shouldReceive('make')->once()->with('authentication::client.auth.signup-email-confirmation');

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@signupSuccess');
  }

  private function mockConfigGetEmailConfirmation($active)
  {
    Config::set('authentication::email_confirmation', $active);
  }

  /**
   * @test
   **/
  public function it_showSuccessSignup_ifEmailConfirmationIsDisabled()
  {
    $active = false;
    $this->mockConfigGetEmailConfirmation($active);

    \View::shouldReceive('make')->once()->with('authentication::client.auth.signup-success');

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@signupSuccess');
  }

  /**
   * @test
   **/
  public function it_show_view_with_success_if_token_is_valid()
  {
    $email        = "mail";
    $token        = "_token";
    $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
                                                                                                $token)->getMock();
    App::instance('register_service', $mock_service);

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '',
                  ["email" => $email, "token" => $token]);

    $this->assertResponseOk();
  }

  /**
   * @test
   **/
  public function it_show_view_with_error_if_token_is_invalid()
  {
    $email        = "mail";
    $token        = "_token";
    $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
                                                                                                $token)->andThrow(new \Jacopo\Authentication\Exceptions\TokenMismatchException)->shouldReceive('getErrors')->once()->andReturn("")->getMock();
    App::instance('register_service', $mock_service);

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '',
                  ["email" => $email, "token" => $token]);

    $this->assertResponseOk();
    $this->assertViewHas('errors');
  }

  /**
   * @test
   **/
  public function it_show_view_errors_if_user_is_not_found()
  {
    $email        = "mail";
    $token        = "_token";
    $mock_service = m::mock('StdClass')->shouldReceive('checkUserActivationCode')->once()->with($email,
                                                                                                $token)->andThrow(new \Jacopo\Authentication\Exceptions\UserNotFoundException())->shouldReceive('getErrors')->once()->andReturn("")->getMock();
    App::instance('register_service', $mock_service);

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@emailConfirmation', '',
                  ["email" => $email, "token" => $token]);

    $this->assertResponseOk();
    $this->assertViewHas('errors');
  }

  /**
   * @test
   **/
  public function it_show_user_lists_on_lists()
  {
    \Session::put('_old_input', ["intersect" => "old intersect", "old" => "old input"]);

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@getList',
                  ["new" => "new input", "intersect" => "new intersect"]);

    $this->assertResponseOk();
  }

  /**
   * @test
   **/
  public function it_edit_user_with_success_and_redirect_to_edit_page()
  {
    $user_stub       = new User();
    $user_stub->id   = 1;
    $form_model_mock = m::mock('StdClass')->shouldReceive('process')->once()->andReturn($user_stub)->getMock();
    App::instance('form_model', $form_model_mock);

    $this->action('POST', 'Jacopo\Authentication\Controllers\UserController@postEditUser');

    $this->assertRedirectedToAction('Jacopo\Authentication\Controllers\UserController@editUser',
                                    ['id' => $user_stub->id]);
  }

  /**
   * @test
   **/
  public function canShowDashboardPage()
  {
    $mock_authenticator = m::mock('StdClass');
    $mock_authenticator->shouldReceive('getLoggedUser')
      ->andReturn(new User());
    App::instance('authenticator', $mock_authenticator);

    $this->action('GET', 'Jacopo\Authentication\Controllers\UserController@dashboard');

    $this->assertResponseOk();
  }

}
 