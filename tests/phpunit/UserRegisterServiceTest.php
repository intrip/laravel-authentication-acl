<?php  namespace Jacopo\Authentication\Tests\Unit;

use App, Config, Event;
use Illuminate\Database\QueryException;
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Services\UserRegisterService;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Library\Exceptions\NotFoundException;
use Mockery as m;

/**
 * Test UserRegisterServiceTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserRegisterServiceTest extends DbTestCase
{

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
    public function createAUserAndHisProfile()
    {
        $input = $this->createFakeRegisterInput();
        $mock_validator = $this->getValidatorSuccess();
        $service = new UserRegisterServiceNoMails($mock_validator);

        $user = $service->register($input);

        $this->assertTrue($user->exists);
        $this->assertNotEmpty($this->getFirstUserProfile());
    }

    /**
     * @test
     **/
    public function firesRegisteringAndRegisteredEvent()
    {
        $input = $this->createFakeRegisterInput();
        $mock_validator = $this->getValidatorSuccess();
        $service = new UserRegisterServiceNoMails($mock_validator);

        $has_registering = false;
        Event::listen('service.registering', function() use(&$has_registering){
            $has_registering = true;
        });
        $has_registered = false;
        Event::listen('service.registered', function() use(&$has_registered){
            $has_registered = true;
        });

        $service->register($input);

        $this->assertTrue($has_registering, 'service.registering event has not been fired.');
        $this->assertTrue($has_registered, 'service.registered event has not been fired.');
    }

    /**
     * @return array
     */
    private function createFakeRegisterInput()
    {
        $input = [
                "email"      => "test@test.com", "password" => "password@test.com", "activated" => 0,
                "first_name" => "first_name"
        ];

        return $input;
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
     * @return mixed
     */
    protected function getFirstUserProfile()
    {
        $user = $this->u_r->find(1);
        $profile = App::make('profile_repository')->getFromUserId($user->id);
        return $profile;
    }

    /**
     * @test
     **/
    public function it_sends_activation_email_to_the_client_on_activation()
    {
        $service = new UserRegisterService;
        $user = new \StdClass;
        $user->email = "user@user.com";

        StateKeeper::set('expected_to', $user->email);
        StateKeeper::set('expected_subject', Config::get('laravel-authentication-acl::messages.email.user_registraction_activation_subject') );
        StateKeeper::set('expected_body', 'Your email has been confirmed succesfully.');

        Event::listen('mailer.sending', 'Jacopo\Authentication\Tests\Unit\AuthControllerTest@checkForSingleMailData');

        $service->sendActivationEmailToClient($user);
    }

    /**
     * @return m\MockInterface
     */
    protected function getValidatorFails()
    {
        return m::mock('Jacopo\Authentication\Validators\UserSignupValidator')
                ->shouldReceive('validate')
                ->once()
                ->andReturn(false)
                ->getMock();
    }

    /**
     * @test
     **/
    public function handleInputValidationErrorsAndRollbackTransaction()
    {
        $mock_validator = $this->getValidatorFails();
        $errors = new MessageBag(["model" => "error"]);
        $mock_validator->shouldReceive('getErrors')->andReturn($errors);
        $service = new UserRegisterService($mock_validator);

        $throws_exception = false;
        try
        {
            $service->register([]);
        } catch(JacopoExceptionsInterface $e)
        {
            $throws_exception = true;
        }

        $this->assertTrue($throws_exception);
        $this->assertEmptyUsers();
        $this->assertHasErrors($service);
    }


    protected function assertEmptyUsers()
    {
        $this->assertNull(User::first());
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserExistsException
     **/
    public function throwsException_IfUserExists()
    {
        $mock_validator = $this->getValidatorSuccess();
        $mock_repo = m::mock('StdClass')->shouldReceive('create')->andThrow(new UserExistsException)->getMock();
        App::instance('user_repository', $mock_repo);

        $service = new UserRegisterServiceNoMails($mock_validator);

        $service->register([]);
    }

    /**
     * @test
     **/
    public function sendsConfirmationEmail_WhenEnabled()
    {
        $this->enableEmailConfirmation();

        $user_email = "email@email.com";
        StateKeeper::set('expected_to', $user_email);
        StateKeeper::set('expected_subject', Config::get('laravel-authentication-acl::messages.email.user_registration_request_subject') );
        StateKeeper::set('expected_body', 'You account has been created. However, before you can use it you need to confirm your email address first by clicking the');

        Event::listen('mailer.sending', 'Jacopo\Authentication\Tests\Unit\AuthControllerTest@checkForSingleMailData');

        $mock_validator = $this->getValidatorSuccess();
        $mock_user_repository = $this->mockUserRepositoryToCreateARandomUser();
        App::instance('user_repository', $mock_user_repository);
        $mock_profile_repository = $this->mockProfileRepositoryCreation();
        App::instance('profile_repository', $mock_profile_repository);
        $mock_auth = $this->mockAuthActiveToken();
        App::instance('authenticator', $mock_auth);
        $service = new UserRegisterService($mock_validator);

        $service->register([
                                   "email"      => $user_email,
                                   "password"   => "password",
                                   "activated"  => 1,
                                   "first_name" => "first_name"
                           ]);
    }

    /**
     * @return m\MockInterface
     */
    protected function mockUserRepositoryToCreateARandomUser()
    {
        $user_stub = new User();
        $user_stub->id = 1;
        $user_stub->email = "";
        $mock_u_r = m::mock('StdClass')->shouldReceive('create')->once()->andReturn($user_stub)->getMock();
        return $mock_u_r;
    }

    /**
     * @return m\MockInterface
     */
    protected function mockProfileRepositoryCreation()
    {
        $mock_p_r = m::mock('StdClass')->shouldReceive('create')->once()->andReturn(true)->getMock();
        return $mock_p_r;
    }

    private function enableEmailConfirmation()
    {
        Config::set('laravel-authentication-acl::email_confirmation', true);
    }

    /**
     * @test
     **/
    public function sendsActivationEmail()
    {
        $this->disableEmailConfirmation();

        $user_email = "email@email.com";
        StateKeeper::set('expected_to', $user_email);
        StateKeeper::set('expected_subject', Config::get('laravel-authentication-acl::messages.email.user_registration_request_subject') );
        StateKeeper::set('expected_body', 'You can now login to the website using the');

        Event::listen('mailer.sending', 'Jacopo\Authentication\Tests\Unit\AuthControllerTest@checkForSingleMailData');

        $mock_validator = $this->getValidatorSuccess();
        $mock_user_repository = $this->mockUserRepositoryToCreateARandomUser();
        App::instance('user_repository', $mock_user_repository);
        $mock_profile_repository = $this->mockProfileRepositoryCreation();
        App::instance('profile_repository', $mock_profile_repository);
        $mock_auth = $this->mockAuthActiveToken();
        App::instance('authenticator', $mock_auth);
        $service = new UserRegisterService($mock_validator);

        $service->register([
                                   "email"      => $user_email,
                                   "password"   => "p",
                                   "activated"  => 1,
                                   "first_name" => "first_name"
                           ]);
    }

    /**
     * @return m\MockInterface
     */
    protected function mockAuthActiveToken()
    {
        return m::mock('Jacopo\Authentication\Interfaces\AuthenticateInterface')
                ->shouldReceive('getActivationToken')
                ->andReturn(true)
                ->shouldReceive('getLoggedUser')
                ->getMock();
    }

    private function disableEmailConfirmation()
    {
        Config::set('laravel-authentication-acl::email_confirmation', false);
    }

    /**
     * @test
     **/
    public function setupActiveStateOfUser()
    {
        $this->disableEmailConfirmation();
        Config::set('laravel-authentication-acl::email_confirmation',false);
        $service = m::mock('Jacopo\Authentication\Services\UserRegisterService');

        $this->assertTrue($service->getDefaultActivatedState([]));

        Config::set('laravel-authentication-acl::email_confirmation',true);
        $this->assertFalse($service->getDefaultActivatedState([]));
    }

    /**
     * @test
     **/
    public function checksUserActivationCode()
    {
        $user_stub = new \StdClass;
        $user_stub->activation_code = "12345_";
        $user_stub->email = "";
        $mock_repo = m::mock('StdClass')
                      ->shouldReceive('findByLogin')
                      ->andReturn($user_stub)
                      ->shouldReceive('activate')
                      ->once()
                      ->andReturn(true)
                      ->getMock();
        App::instance('user_repository', $mock_repo);
        $email = "mail@mail.com";
        $token = $user_stub->activation_code;
        $this->stopEventPropagation();
        $service = new UserRegisterServiceNoMails;

        $service->checkUserActivationCode($email, $token);
    }

    protected function stopEventPropagation()
    {
        Event::listen('service.activated', function ()
        {
            return false;
        });
    }

    /**
     * @test
     **/
    public function checksForActivationAndThrowsUserNotFoundException()
    {
        $service = new UserRegisterService;
        $email = "mail@mail.com";
        $token = "12345_";
        $gotcha = false;

        try
        {
            $service->checkUserActivationCode($email, $token);
        } catch(\Jacopo\Authentication\Exceptions\UserNotFoundException $e)
        {
            $gotcha = true;
        }

        $this->assertTrue($gotcha);
        $this->assertNotEmpty($service->getErrors());
    }

    /**
     * @test
     **/
    public function checksForActivationCodeAndThrowTokenMismatchExceptionWithErrors()
    {
        $user_stub =
                m::mock('Jacopo\Authentication\Modles\User')->makePartial()->shouldReceive('checkResetPasswordCode')->andReturn(false)->getMock();
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
        } catch(\Jacopo\Authentication\Exceptions\TokenMismatchException $e)
        {
            $gotcha = true;
        }

        $this->assertTrue($gotcha);
        $this->assertNotEmpty($service->getErrors());
    }

    /**
     * @test
     **/
    public function fireEvent_OnCheckUserActivationCode()
    {
        $user_stub = m::mock('Jacopo\Authentication\Modles\User');
        $user_stub->activation_code = "12345_";
        $mock_repo = m::mock('StdClass')->shouldReceive('findByLogin')->andReturn($user_stub)->shouldReceive('activate')->once()->andReturn(true)
                      ->getMock();
        App::instance('user_repository', $mock_repo);
        $email = "mail@mail.com";
        $token = "12345_";
        $service = new UserRegisterService;

        $event_fired = false;

        Event::listen('service.activated', function () use (&$event_fired)
        {
            $event_fired = true;
            return false;
        }, 1000);
        $service->checkUserActivationCode($email, $token);

        $this->assertTrue($event_fired);
    }
}

class UserRegisterServiceNoMails extends UserRegisterService
{
    public function sendRegistrationMailToClient($input)
    {
        // do nothing...
    }
}