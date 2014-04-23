<?php

use Jacopo\Authentication\Services\ReminderService as Reminder;
use Mockery as m;
use Jacopo\Authentication\Tests\TestCase;

class ReminderServiceTest extends TestCase {

    protected $token;

    public function setUp()
    {
        parent::setUp();

        $this->token = "randomtoken";
    }

    public function tearDown()
    {
        m::close();
    }

	public function testCanCreate()
	{
        new Reminder();
	}

    public function testSendWorks()
    {
        $return = true;
        $template = "";
        $to = "destination@mail.com";
        $this->mockMailerSendTo($return, $template, $to);
        $this->mockAuthGetTokenTrue();
        $reminder = new Reminder();
        $reminder->setTemplate($template);

        $success = $reminder->send($to);

        $this->assertEquals(null, $success);
    }

    /**
     * @expectedException Jacopo\Library\Exceptions\MailException
     */
    public function testSendThrowsException()
    {
        $return = false;
        $template = "";
        $to = "destination@mail.com";
        $this->mockMailerSendTo($return, $template, $to);
        $this->mockAuthGetTokenTrue();
        $reminder = new Reminder();
        $reminder->setTemplate($template);

        $reminder->send($to);
    }
  
    private function mockMailerSendTo($return, $template, $to)
    {
        $mock_mail = m::mock('StdClass')->shouldReceive('sendTo')
            ->once($to, m::any(), m::any(), $template)
            ->with()
            ->andReturn($return)
            ->getMock();
        App::instance('jmailer', $mock_mail);
    }

    /**
     * @return mixed
     */
    private function mockAuthGetTokenTrue()
    {
        $mock_auth = m::mock('StdClass')->shouldReceive()->getToken()->andReturn($this->token);
        App::instance('authenticator', $mock_auth);
    }

}