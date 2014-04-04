<?php  namespace Jacopo\Authentication\Services;
use Illuminate\Support\Facades\App;
use Config, Redirect, DB, Event;
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Validators\UserSignupValidator;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Library\Exceptions\ValidationException;
use Jacopo\Authentication\Exceptions\TokenMismatchException;
/**
 * Class UserRegisterService
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserRegisterService 
{
    /**
     * @var \Jacopo\Authentication\Repository\Interfaces\UserRepositoryInterface
     */
    protected $u_r;
    /**
     * @var \Jacopo\Authentication\Repository\Interfaces\UserProfileRepositoryInterface
     */
    protected $p_r;
    /**
     * @var \Jacopo\Authentication\Validators\UserSignupValidator
     */
    protected $v;
    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;
    /**
     * If email activation is enabled
     * @var boolean
     */
    protected $activation_enabled;

    public function __construct(UserSignupValidator $v = null)
    {
        $this->u_r = App::make('user_repository');
        $this->p_r = App::make('profile_repository');
        $this->v = $v ? $v : new UserSignupValidator;
        $this->activation_enabled = Config::get('authentication::email_confirmation');
        Event::listen('service.activated', 'Jacopo\Authentication\Services\UserRegisterService@sendActivationEmailToClient');
    }


    /**
     * @param array $input
     * @return mixed
     */
    public function register(array $input)
    {
        $this->validateInput($input);
        $input['activated'] = $this->getActiveInputState();

        $user = $this->saveDbData($input);

        $this->sendRegistrationMailToClient($input);

        return $user;
    }

    /**
     * @param array $input
     * @throws \Jacopo\Library\Exceptions\ValidationException
     */
    protected function validateInput(array $input)
    {
        if (!$this->v->validate($input))
        {
            $this->errors = $this->v->getErrors();
            throw new ValidationException;
        }
    }

    /**
     * @param array $input
     * @return mixed $user
     */
    protected function saveDbData(array $input)
    {
        if(App::environment() != 'testing')
        {
            // temporary disable reference integrity check
            DB::connection('authentication')->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0;');
            DB::connection('authentication')->getPdo()->beginTransaction();
        }

        try
        {
            // user
            $user    = $this->u_r->create($input);

            // profile
            $this->p_r->create(array_merge(["user_id" => $user->id], $input) );
        }
        catch(UserExistsException $e)
        {
            if(App::environment() != 'testing')
            {
                DB::connection()->getPdo()->rollback();
            }
            $this->errors = new MessageBag(["model" => "User already exists."]);
            throw new UserExistsException;
        }

        if(App::environment() != 'testing')
        {
            DB::connection('authentication')->getPdo()->commit();
            // reactivate integrity check
            DB::connection('authentication')->getPdo()->exec('SET FOREIGN_KEY_CHECKS=1;');
        }
        return $user;
    }

    protected function getActiveInputState()
    {
        return Config::get('authentication::email_confirmation') ? false : true;
    }

    /**
     * @param $mailer
     * @param $user
     */
    public function sendRegistrationMailToClient($input)
    {
        $view_file = $this->activation_enabled ? "authentication::admin.mail.registration-waiting-client" : "authentication::admin.mail.registration-confirmed-client";

        $mailer = App::make('jmailer');

        // send email to client
        $mailer->sendTo( $input['email'], [ "email" => $input["email"], "password" => $input["password"], "first_name" => $input["first_name"], "token" => $this->activation_enabled ? App::make('authenticator')->getActivationToken($input["email"]) :'' ], "Register request to: " . \Config::get('authentication::app_name'), $view_file);
    }


    /**
     * Send activation email to the client if it's getting activated
     * @param $obj
     */
    public function sendActivationEmailToClient($user)
    {
        $mailer = App::make('jmailer');
        // if i activate a deactivated user
        $mailer->sendTo($user->email, [ "email" => $user->email ], "Your user is activated on: ".Config::get('authentication::app_name'), "authentication::admin.mail.registration-confirmed-client");
    }

    /**
     * @param $email
     * @param $token
     * @throws Jacopo\Authentication\Exceptions\UserNotFoundException
     * @throws Jacopo\Authentication\Exceptions\TokenMismatchException
     */
    public function checkUserActivationCode($email, $token)
    {
        $token_msg = "The given token/email are invalid.";

        try
        {
            $user = $this->u_r->findByLogin($email);
        }
        catch(UserNotFoundException $e)
        {
            $this->errors = new MessageBag(["token" => $token_msg]);
            throw new UserNotFoundException;
        }
        if( $user->activation_code != $token)
        {
            $this->errors = new MessageBag(["token" => $token_msg]);
            throw new TokenMismatchException;
        }

        $this->u_r->activate($email);
        Event::fire('service.activated', $user);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    protected function getToken()
    {
        return csrf_token();
    }
} 