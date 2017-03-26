<?php namespace LaravelAcl\Library\Email;

/**
 * Swift mailer implementation of MailerInterface
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Swift_RfcComplianceException;
use Swift_TransportException;

class SwiftMailer implements MailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendTo($to, $body, $subject, $template)
    {
        try
        {
            App::make('mailer')->send($template, ["body" => $body], function($message) use($to, $subject){
                $message->to($to)->subject($subject);
            });
        }
        catch( Swift_TransportException $e)
        {
            Log::error('Cannot send the email:'.$e->getMessage());
            return false;
        }
        catch( Swift_RfcComplianceException $e)
        {
            Log::error('Cannot send the email:'.$e->getMessage());
            return false;
        }

        return true;
    }
}
