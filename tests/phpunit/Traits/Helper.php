<?php  namespace Jacopo\Authentication\Tests\Unit\Traits;

use Jacopo\Authentication\Tests\Unit\StateKeeper;

trait Helper
{
    public function checkForSingleMailData($message)
    {
        if(StateKeeper::get('expected_body'))
        {
            $this->assertMessageContains($message, StateKeeper::get('expected_body'));
        }
        if(StateKeeper::get('expected_to'))
        {
            $this->assertMessageSentTo($message, StateKeeper::get('expected_to'));
        }
        if(StateKeeper::get('expected_subject'))
        {
            $this->assertMessagSubjectEquals($message, StateKeeper::get('expected_subject'));
        }
    }

    /**
     * @param $message
     * @param $this
     * @return mixed
     */
    protected  function assertMessageContains($message, $text) {
        return $this->assertEquals(true, strpos($message->getBody(), $text), 'The message does not contain: '. $text. 'but: '. $message);
    }

    /**
     * @param $message
     * @param $expected_to
     */
    protected function assertMessageSentTo($message, $expected_to) {
        return $this->assertEquals(array_keys($message->getTo())[0], $expected_to, 'The message is not sent to: '. $expected_to. 'but to:'. $message);
    }

    /**
     * @param $message
     * @param $expected_subject
     */
    protected function assertMessagSubjectEquals($message, $expected_subject) {
        return $this->assertEquals($message->getSubject(), $expected_subject, 'Message has not the subject: '. $expected_subject. 'but: '. $message);
    }
} 