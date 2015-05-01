<?php namespace LaravelAcl\Authentication\Tests\Unit;

use Illuminate\Support\Facades\App;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException;
use LaravelAcl\Authentication\Services\ReminderService as Reminder;
use Mockery as m;

class ReminderServiceTest extends DbTestCase {

    protected $token;

    protected $to = "destination@mail.com";
    protected $template = "template_path";
    protected $reminder;

    public function setUp()
    {
        parent::setUp();

        $this->token = "pseudorandom_token";
        $this->reminder = new Reminder;
    }

    public function tearDown()
    {
        m::close();
    }

	public function testCanCreate()
	{
        new Reminder();
	}

    /**
     * @test
     */
    public function itCanSendEmail()
    {
        $success = true;
        $this->mockMailerSendTo($success, $this->template, $this->to);
        $this->mockGetTokenSuccesfully();
        $reminder = new Reminder();
        $reminder->setTemplate($this->template);

        $success = $reminder->send($this->to);

        $this->assertEquals(null, $success);
    }

    /**
     * @test
     * @expectedException \LaravelAcl\Library\Exceptions\MailException
     */
    public function itThrowsExceptionOnSendEmailErrors()
    {
        $success = false;
        $this->mockMailerSendTo($success, $this->template, $this->to);
        $this->mockGetTokenSuccesfully();
        $reminder = new Reminder();
        $reminder->setTemplate($this->template);

        $reminder->send($this->to);
    }

    /**
     * @test
     * @expectedException \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     **/
    public function itTrowsExceptionIfcannotFindTheUser()
    {
        $this->mockGetTokenWithError();

        $reminder = new Reminder();
        $reminder->send($this->to);
    }

    private function mockMailerSendTo($return, $template, $to)
    {
        $mock_mail = m::mock('LaravelAcl\Library\Email\MailerInterface')->shouldReceive('sendTo')
            ->once()
            ->with($to, m::any(), m::any(), $template)
            ->andReturn($return)
            ->getMock();
        App::instance('jmailer', $mock_mail);
    }

    /**
     * @return mixed
     */
    private function mockGetTokenSuccesfully()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive('getToken')->once()->andReturn($this->token)->getMock();
        App::instance('authenticator', $mock_auth);
    }

    private function mockGetTokenWithError()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive('getToken')->once()->andThrow(new UserNotFoundException)->getMock();
        App::instance('authenticator', $mock_auth);
    }
    
    /**
     * @test
     **/
    public function canResetPasswordSuccesfully()
    {
        $users = $this->make('LaravelAcl\Authentication\Models\User');
        $user = $users[0];
        $token = App::make('authenticator')->getToken($user->email);
        $new_pass = "new_password";

        $this->reminder->reset($user->email, $token, $new_pass);

        $user_saved = App::make('user_repository')->find($user->id);
        $this->assertTrue($user_saved->checkPassword($new_pass));
    }

    /**
     * @test
     * @expectedException \LaravelAcl\Library\Exceptions\InvalidException
     **/
    public function canHandleWrongTokenErrors()
    {
        $users = $this->make('LaravelAcl\Authentication\Models\User');
        $user = $users[0];
        $new_pass = "new_password";
        $token = "wrong token";

        $this->reminder->reset($user->email, $token, $new_pass);
    }

    protected function getModelStub()
    {
        return [
                "email"     => $this->faker->email(),
                "password" => $this->faker->text(20),
                "activated" => false,
                "banned" => false
        ];
    }

}