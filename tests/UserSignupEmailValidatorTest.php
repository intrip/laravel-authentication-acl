<?php  namespace Jacopo\Authentication\Tests; 

/**
 * Test UserSignupEmailValidatorTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use App, Input, Session, Config;
use Jacopo\Authentication\Validators\UserSignupEmailValidator;
use Mockery as m;
class UserSignupEmailValidatorTest extends DbTestCase {

    public function tearDown()
    {
        m::close();
    }
    /**
     * @test
     **/
    public function it_check_if_email_already_exists_and_user_is_active()
    {
        $user_repo = App::make('user_repository');
        $fake_mail = "email@email.com";
        $input = [
            "email" => $fake_mail,
            "password" => "pass",
            "activated" => 1
        ];
        $user_repo->create($input);
        $validator = new UserSignupEmailValidator();
        $this->assertFalse($validator->validateEmailUnique("email", $fake_mail, $input));

        $fake_mail = "email2@email.com";
        $input = [
            "email" => $fake_mail,
            "password" => "pass",
            "activated" => 0
        ];
        $validator = new UserSignupEmailValidator();
        $this->assertTrue($validator->validateEmailUnique("email", $fake_mail, $input));
    }
    
    /**
     * @test
     **/
    public function it_send_email_if_user_exists_and_is_not_active_and_set_message()
    {
        $user_repo = App::make('user_repository');
        $this->enableEmailConfirmation();
        $fake_mail = "email@email.com";
        $input = [
            "email" => $fake_mail,
            "password" => "pass",
            "activated" => 0,
            "first_name" => "",
            "last_name" => ""
        ];
        $user_repo->create($input);
        $validator = new UserSignupEmailValidator();
        $mock_mail = m::mock('StdClass')->shouldReceive('sendTo')
            ->once()
            ->with($fake_mail, m::any(), m::any(), m::any())
            ->getMock();
        App::instance('jmailer', $mock_mail);
        Input::shouldReceive('all')->once()->andReturn($input);

        $this->assertFalse($validator->validateEmailUnique("email", $fake_mail, $input));
        $this->assertTrue(Session::has('message'));
    }

    private function enableEmailConfirmation()
    {
        Config::set('laravel-authentication-acl::email_confirmation', true);
    }

}
 