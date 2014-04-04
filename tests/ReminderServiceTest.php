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
        $reminder = new Reminder();
	}

    public function testSendWorks()
    {
        $return = true;
        $this->mockMailerSendTo($return);
        $this->mockAuthGetTokenTrue();
        $reminder = new Reminder();

        $success = $reminder->send("mock@mock.com");
        $this->assertEquals(null, $success);
    }

    /**
     * @expectedException Jacopo\Library\Exceptions\MailException
     */
    public function testSendThrowsException()
    {
        $return = false;
        $this->mockMailerSendTo($return);
        $this->mockAuthGetTokenTrue();
        $reminder = new Reminder();
        $reminder->send("");
    }

    private function mockMailerSendTo($return)
    {
        $mock_mail = m::mock('StdClass')->shouldReceive('sendTo')->andReturn($return)->getMock();
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