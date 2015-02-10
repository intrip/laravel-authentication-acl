<?php namespace Jacopo\Library\Email;

/**
 * Swift mailer implementation of MailerInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Mail;
use Log;

class SwiftMailer implements MailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendTo($to, $body, $subject, $template)
    {
        try
        {
            Mail::queue($template, ["body" => $body], function($message) use($to, $subject){
                $message->to($to)->subject($subject);
            });
        }
        catch( \Swift_TransportException $e)
        {
            Log::error('impossibile inviare l\'email:'.$e->getMessage());
            return false;
        }
        catch( \Swift_RfcComplianceException $e)
        {
            Log::error('impossibile inviare l\'email:'.$e->getMessage());
            return false;
        }

        return true;
    }
}