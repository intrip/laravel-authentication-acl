<?php

use Jacopo\Authentication\Classes\ReminderService as Reminder;
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
        $mock_mail = m::mock('StdClass')->shouldReceive('sendTo')->andReturn(true)->getMock();
        App::instance('jmailer', $mock_mail);
        $mock_auth = m::mock('StdClass')->shouldReceive()->getToken()->andReturn($this->token);
        App::instance('authenticator', $mock_auth);
        $reminder = new Reminder();

        $success = $reminder->send("mock@mock.com");
        $this->assertEquals(null, $success);
    }

    /**
     * @expectedException Jacopo\Library\Exceptions\MailException
     */
    public function testSendThrowsException()
    {
        $mock_mail = m::mock('StdClass')->shouldReceive('sendTo')->andReturn(false)->getMock();
        App::instance('jmailer', $mock_mail);
        $mock_auth = m::mock('StdClass')->shouldReceive()->getToken()->andReturn($this->token);
        App::instance('authenticator', $mock_auth);
        $reminder = new Reminder();
        $reminder->send("");
    }

}