<?php  namespace Jacopo\Authentication\Tests;

use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Services\UserRegisterService;
use App, Config;
use Mockery as m;
use Jacopo\Authentication\Models\User;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Illuminate\Database\QueryException;
use Event;

/**
 * Test UserRegisterServiceTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserRegisterServiceTest extends DbTestCase {

    public function setUp()
    {
        parent::setUp();

        $this->u_r = App::make('user_repository');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     **/
    public function it_can_be_created()
    {
        new UserRegisterService();
    }

    /**
     * @test
     **/
    public function it_register_a_user()
    {
        $input          = $this->createFakeRegisterInput();
        $mock_validator = $this->getValidatorSuccess();
        $service = new UserRegisterServiceNoMails($mock_validator);

        $user= $service->register($input);

        $this->assertTrue($user->exists);
    }

    /**
     * @test
     **/
    public function it_create_a_profile()
    {
        $input          = $this->createFakeRegisterInput();
        $mock_validator = $this->getValidatorSuccess();
        $service = new UserRegisterServiceNoMails($mock_validator);

        $service->register($input);

        $user = $this->u_r->find(1);
        $profile = App::make('profile_repository')->getFromUserId($user->id);
        $this->assertNotEmpty($profile);
    }
    
    /**
     * @test
     **/
    public function it_sends_activation_email_to_the_client_on_activation()
    {
        $service = new UserRegisterService;
        $user = new \StdClass;
        $user->email = "user@user.com";

        $mock_mailer = m::mock('StdClass')->shouldReceive('sendTo')->once()->with("user@user.com", m::any(), m::any(), "authentication::admin.mail.registration-confirmed-client")->andReturn(true)->getMock();
        App::instance('jmailer', $mock_mailer);

        $service->sendActivationEmailToClient($user);
    }
    
    /**
     * @test
     **/
    public function it_validates_user_input()
    {
        $mock_validator = $this->getValidatorSuccess();
        $input          = $this->createFakeRegisterInput();

        $service = new UserRegisterServiceNoMails($mock_validator);

        $service->register($input);
    }

    /**
     * @test
     * @expectedException \Jacopo\Library\Exceptions\ValidationException
     **/
    public function it_throw_validation_exception_if_validation_fails()
    {
        $mock_validator = $this->getValidatorFails();
        $errors = new MessageBag(["model"=> "error"]);
        $mock_validator->shouldReceive('getErrors')->andReturn($errors);

        $service = new UserRegisterService($mock_validator);

        $service->register([]);
    }

    /**
     * @test
     **/
    public function it_sets_error_if_input_validation_fails()
    {
        $mock_validator = $this->getValidatorFails();
        $errors = new MessageBag(["model"=> "error"]);
        $mock_validator->shouldReceive('getErrors')->andReturn($errors);
        $service = new UserRegisterService($mock_validator);

        try
        {
            $service->register([]);
        }
        catch(JacopoExceptionsInterface $e)
        {}

        $errors = $service->getErrors();
        $this->assertFalse($errors->isEmpty());
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserExistsException
     **/
    public function it_throws_user_exists_exception_if_user_exists()
    {
        $mock_validator = $this->getValidatorSuccess();
        $mock_repo = m::mock('StdClass')->shouldReceive('create')->andThrow(new UserExistsException)->getMock();
        App::instance('user_repository', $mock_repo);

        $service = new UserRegisterServiceNoMails($mock_validator);

        $service->register([]);
    }

    /**
     * @deprecated test
     * @expectedException \Jacopo\Library\Exceptions\NotFoundException
     **/
    public function it_throws_not_found_exception_if_cannot_find_the_user()
    {
        $mock_validator = $this->getValidatorSuccess();
        $user_stub = new \StdClass;
        $user_stub->id = 1;
        $mock_repo = m::mock('StdClass')->shouldReceive('create')->andReturn($user_stub)
            ->shouldReceive('addGroup')->andThrow(new NotFoundException)
            ->getMock();
        App::instance('user_repository', $mock_repo);

        $service = new UserRegisterService($mock_validator);

        $service->register(["group_id" => 1]);
    }

    /**
     * @test
     * @group 1
     **/
    public function it_sent_confirmation_email_if_is_enabled()
    {
        Config::shouldReceive('get')->andReturn(true);
        $mock_mailer = m::mock('StdClass')->shouldReceive('sendTo')->once()->with('email@email.com', m::any(), m::any(), "authentication::admin.mail.registration-waiting-client")->andReturn(true)->getMock();
        App::instance('jmailer', $mock_mailer);
        $mock_validator = $this->getValidatorSuccess();
        $user_stub = new User();
        $user_stub->id = 1;
        $user_stub->email = "";
        $mock_u_r = m::mock('StdClass')
            ->shouldReceive('create')
            ->once()
            ->andReturn($user_stub)
            ->getMock();
        App::instance('user_repository', $mock_u_r);
        $mock_p_r = m::mock('StdClass')
            ->shouldReceive('create')
            ->once()
            ->andReturn(true)
            ->getMock();
        App::instance('profile_repository', $mock_p_r);
        $mock_auth = m::mock('StdClass')->shouldReceive('getActivationToken')->andReturn(true)->getMock();
        App::instance('authenticator', $mock_auth);
        $service = new UserRegisterService($mock_validator);

        $service->register(["email" => "email@email.com", "password" => "p", "activated" => 1, "first_name" => "first_name"]);
    }

    /**
     * @test
     **/
    public function it_send_activation_mail_to_client()
    {
        Config::shouldReceive('get')->andReturn(false);
        $mock_mailer = m::mock('StdClass')->shouldReceive('sendTo')->once()->with('email@email.com', m::any(), m::any(), "authentication::admin.mail.registration-confirmed-client")->andReturn(true)->getMock();
        App::instance('jmailer', $mock_mailer);
        $mock_validator = $this->getValidatorSuccess();
        $user_stub = new User();
        $user_stub->id = 1;
        $user_stub->email = "";
        $mock_u_r = m::mock('StdClass')
            ->shouldReceive('create')
            ->once()
            ->andReturn($user_stub)
            ->getMock();
        App::instance('user_repository', $mock_u_r);
        $mock_p_r = m::mock('StdClass')
            ->shouldReceive('create')
            ->once()
            ->andReturn(true)
            ->getMock();
        App::instance('profile_repository', $mock_p_r);
        $mock_auth = m::mock('StdClass')->shouldReceive('getActivationToken')->andReturn(true)->getMock();
        App::instance('authenticator', $mock_auth);
        $service = new UserRegisterService($mock_validator);

        $service->register(["email" => "email@email.com", "password" => "p", "activated" => 1, "first_name" => "first_name"]);
    }

    /**
     * @test
     **/
    public function it_setup_active_state_of_user()
    {
        Config::shouldReceive('get')->andReturn(false)->once();
        $service = m::mock('Jacopo\Authentication\Services\UserRegisterService');
        $input = [];

        $state = $service->getActiveInputState($input);

        $this->assertTrue($state);

        Config::shouldReceive('get')->andReturn(true)->once();
        $service = m::mock('Jacopo\Authentication\Services\UserRegisterService');
        $input = [];

        $state = $service->getActiveInputState($input);

        $this->assertFalse($state);
    }

    /**
     * @test
     **/
    public function it_check_user_activaction_code()
    {
        $user_stub = new \StdClass;
        $user_stub->activation_code = "12345_";
        $user_stub->email = "";
        $mock_repo = m::mock('StdClass')->shouldReceive('findByLogin')->andReturn($user_stub)
            ->shouldReceive('activate')
            ->once()
            ->andReturn(true)
            ->getMock();
        App::instance('user_repository', $mock_repo);
        $email = "mail@mail.com";
        $token = "12345_";
        $this->stopEventPropagation();
        $service = new UserRegisterServiceNoMails;

        $service->checkUserActivationCode($email, $token);
    }
    
    /**
     * @test
     **/
    public function it_check_for_activation_and_throw_not_found_exception()
    {
        $service = new UserRegisterService;
        $email = "mail@mail.com";
        $token = "12345_";
        $gotcha = false;

        try
        {
            $service->checkUserActivationCode($email, $token);
        }catch(\Jacopo\Authentication\Exceptions\UserNotFoundException $e)
        {
            $gotcha = true;
        }

        $this->assertTrue($gotcha);
        $this->assertNotEmpty($service->getErrors());
    }

    /**
     * @test
     **/
    public function it_check_for_activaction_code_and_throw_token_mismatch_exception_and_set_errors()
    {
        $user_stub = m::mock('Jacopo\Authentication\Modles\User')->makePartial()->shouldReceive('checkResetPasswordCode')->andReturn(false)->getMock();
        $user_stub->activation_code = "";
        $mock_repo = m::mock('StdClass')->shouldReceive('findByLogin')->andReturn($user_stub)->getMock();
        App::instance('user_repository', $mock_repo);
        $email = "mail@mail.com";
        $token = "12345_";
        $service = new UserRegisterService;
        $gotcha = false;

        try
        {
            $service->checkUserActivationCode($email, $token);
        }catch(\Jacopo\Authentication\Exceptions\TokenMismatchException $e)
        {
            $gotcha = true;
        }

        $this->assertTrue($gotcha);
        $this->assertNotEmpty($service->getErrors());
    }
    
    /**
     * @test
     **/
    public function it_fire_an_event_when_checking_for_user_mail_confirmation()
    {
        $user_stub = m::mock('Jacopo\Authentication\Modles\User');
        $user_stub->activation_code = "12345_";
        $mock_repo = m::mock('StdClass')->shouldReceive('findByLogin')->andReturn($user_stub)
            ->shouldReceive('activate')
            ->once()
            ->andReturn(true)
            ->getMock();
        App::instance('user_repository', $mock_repo);
        $email = "mail@mail.com";
        $token = "12345_";
        $service = new UserRegisterService;

        $event_fired = false;

        Event::listen('service.activated', function() use(&$event_fired){
            $event_fired = true;
            return false;
        },1000);
        $service->checkUserActivationCode($email, $token);

        $this->assertTrue($event_fired);
    }

    /**
     * @return m\MockInterface
     */
    protected function getValidatorSuccess()
    {
        $mock_validator = m::mock('Jacopo\Authentication\Validators\UserSignupValidator')->shouldReceive('validate')->andReturn(true)->getMock();

        return $mock_validator;
    }

    /**
     * @return m\MockInterface
     */
    protected function getValidatorFails()
    {
        $mock_validator = m::mock('Jacopo\Authentication\Validators\UserSignupValidator')->shouldReceive('validate')->once()->andReturn(false)->getMock();

        return $mock_validator;
    }

    protected function stopEventPropagation()
    {
        Event::listen('service.activated', function(){return false;});
    }

    /**
     * @return array
     */
    private function createFakeRegisterInput()
    {
        $input = [
            "email" => "test@test.com", "password" => "password@test.com", "activated" => 0, "first_name" => "first_name"];

        return $input;
    }
}

class UserRegisterServiceNoMails extends UserRegisterService
{
    public function sendRegistrationMailToClient($input)
    {
        // silence is golen...
    }
}