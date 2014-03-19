<?php  namespace Jacopo\Authentication\Services;
use Illuminate\Support\Facades\App;
use Config, Redirect, DB;
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Validators\UserSignupValidator;
use Jacopo\Library\Exceptions\NotFoundException;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Library\Exceptions\ValidationException;
use Event;
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

    public function __construct(UserSignupValidator $v = null)
    {
        $this->u_r = App::make('user_repository');
        $this->p_r = App::make('profile_repository');
        $this->v = $v ? $v : new UserSignupValidator;
        Event::listen('service.registering', 'Jacopo\Authentication\Services\UserRegisterService@sendRegistrationMailToClient');
    }

    public function register(array $input)
    {
        $this->validateInput($input);
        $input['activated'] = $this->getActiveInputState($input);

        Event::fire('service.registering', [$input]);

        $user = $this->saveDbData($input);

        return $user;
    }

    /**
     * @param $mailer
     * @param $user
     */
    public function sendRegistrationMailToClient($input)
    {
        if (! Config::get('email_confirmation')) return;

        $mailer = App::make('jmailer');
        // send email to client
        $mailer->sendTo( $input['email'], [ "email" => $input["email"], "password" => $input["password"] ], "Register request to: " . \Config::get('authentication::app_name'), "authentication::mail.registration-waiting-client");
    }

    protected function getActiveInputState($input)
    {
        return Config::get('email_confirmation') ? false : true;
    }

    /**
     * Send activation email to the client if it's getting activated
     * @param $obj
     * @todo
     */
    public function sendActivationEmailToClient($obj, array $input)
    {
        $mailer = App::make('jmailer');
        // if i activate a deactivated user
        if(isset($input["activated"]) && $input["activated"] && (! $obj->activated) ) $mailer->sendTo($obj->email, [ "email" => $input["email"] ], "Your user is activated on: ".Config::get('authentication::app_name'), "authentication::mail.registration-activated-client");
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

    public function getErrors()
    {
        return $this->errors;
    }
} 