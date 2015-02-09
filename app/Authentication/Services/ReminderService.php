<?php namespace Jacopo\Authentication\Services;

use Illuminate\Support\MessageBag;
use App;
use Config;
use Jacopo\Library\Exceptions\MailException;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Library\Exceptions\InvalidException;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Library\Email\MailerInterface;
use Jacopo\Authentication\Interfaces\AuthenticatorInterface;
/**
 * Class ReminderService
 *
 * Service to send email and error handling
 *
 * @package Auth
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ReminderService {

    /**
     * Class to send email
     *
     * @var MailerInterface
     */
    protected $mailer;
    /**
     * Email body
     *
     * @var string
     */
    protected $body;
    /**
     * Email subject
     */
    protected $subject;
    /**
     * Femplate mail file
     *
     * @var string
     */
    protected $template = "laravel-authentication-acl::admin.mail.reminder";
    /**
     * Errors
     *
     * @var \Illuminate\Support\\MessageBag
     */
    protected $errors ;
    /**
     * @var \Jacopo\Authentication\Interfaces\AuthenticatorInterface
     */
    protected $auth;

    protected static $INVALID_USER_MAIL = 'There is no user associated with this email.';

    public function __construct()
    {
        $this->auth = App::make('authenticator');
        $this->mailer = App::make('jmailer');
        $this->errors = new MessageBag();
        $this->subject = Config::get('laravel-authentication-acl::messages.email.user_password_recovery_subject');
    }

    public function send($to)
    {
        // gets reset pwd code
        try
        {
            $token = $this->auth->getToken($to);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $this->errors->add('mail', static::$INVALID_USER_MAIL);
            throw new UserNotFoundException;
        }

        $this->prepareResetPasswordLink($token, $to);

        // send email with change password link
        $success = $this->mailer->sendTo($to, $this->body, $this->subject, $this->template);

        if(! $success)
        {
            $this->errors->add('mail', 'There was an error sending the email');
            throw new MailException;
        }
    }

    private function prepareResetPasswordLink($token, $to)
    {
        $this->body = link_to_action("Jacopo\\Authentication\\Controllers\\AuthController@getChangePassword",
                 Config::get('laravel-authentication-acl::messages.links.change_password'),
                ["email"=> $to, "token"=> $token] );
    }

    public function reset($email, $token, $password)
    {
        try
        {
            $user = $this->auth->getUser($email);
        }
        catch(JacopoExceptionsInterface $e)
        {
            $this->errors->add('user', static::$INVALID_USER_MAIL);
            throw new UserNotFoundException;
        }

        // Check if the reset password code is valid
        if ($user->checkResetPasswordCode($token))
        {
            // Attempt to reset the user password
            if (! $user->attemptResetPassword($token, $password))
            {
                $this->errors->add('user', Config::get('laravel-authentication-acl::messages.flash.error.reset_password_error') );
                throw new InvalidException();
            }
        }
        else
        {
            $this->errors->add('user', Config::get('laravel-authentication-acl::messages.flash.error.captcha_error') );
            throw new InvalidException();
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

} 