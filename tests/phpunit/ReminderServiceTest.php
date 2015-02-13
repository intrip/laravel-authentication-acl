<?php namespace Jacopo\Authentication\Tests\Unit;

use Illuminate\Support\Facades\App;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Services\ReminderService as Reminder;
use Mockery as m;

class ReminderServiceTest extends DbTestCase {

    protected $token;

    protected $to = "destination@mail.com";
    protected $reminder;

    public function setUp()
    {
        parent::setUp();

        $this->token = "pseudorandom token";
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
        $return = true;
        $template = "";
        $this->mockMailerSendTo($return, $template, $this->to);
        $this->mockGetTokenSuccesfully();
        $reminder = new Reminder();
        $reminder->setTemplate($template);

        $success = $reminder->send($this->to);

        $this->assertEquals(null, $success);
    }

    /**
     * @test
     * @expectedException \Jacopo\Library\Exceptions\MailException
     */
    public function itThrowsExceptionOnSendEmailErrors()
    {
        $return = false;
        $template = "";
        $to = "destination@mail.com";
        $this->mockMailerSendTo($return, $template, $this->to);
        $this->mockGetTokenSuccesfully();
        $reminder = new Reminder();
        $reminder->setTemplate($template);

        $reminder->send($this->to);
    }

    /**
     * @test
     * @expectedException \Jacopo\Authentication\Exceptions\UserNotFoundException
     **/
    public function itTrowsExceptionIfcannotFindTheUser()
    {
        $this->mockGetTokenWithError();

        $reminder = new Reminder();
        $reminder->send($this->to);
    }

    private function mockMailerSendTo($return, $template, $to)
    {
        $mock_mail = m::mock('Jacopo\Library\Email\MailerInterface')->shouldReceive('sendTo')
            ->once($to, m::any(), m::any(), $template)
            ->with()
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
        $users = $this->make('Jacopo\Authentication\Models\User');
        $user = $users[0];
        $token = App::make('authenticator')->getToken($user->email);
        $new_pass = "new_password";

        $this->reminder->reset($user->email, $token, $new_pass);

        $user_saved = App::make('user_repository')->find($user->id);
        $this->assertTrue($user_saved->checkPassword($new_pass));
    }

    /**
     * @test
     * @expectedException \Jacopo\Library\Exceptions\InvalidException
     **/
    public function canHandleWrongTokenErrors()
    {
        $users = $this->make('Jacopo\Authentication\Models\User');
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