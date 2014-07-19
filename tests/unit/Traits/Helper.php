<?php  namespace Jacopo\Authentication\Tests\Unit\Traits;

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
} 