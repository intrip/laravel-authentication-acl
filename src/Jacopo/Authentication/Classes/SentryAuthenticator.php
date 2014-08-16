<?php namespace Jacopo\Authentication\Classes;

/**
 * Class SentryAuthenticator
 * Sentry authenticate implementatione
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\AuthenticationErrorException;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Interfaces\AuthenticateInterface;
use App;
use Event;

class SentryAuthenticator implements AuthenticateInterface
{

    protected $errors;
    protected $sentry;

    public function __construct()
    {
        $this->sentry = App::make('sentry');
        $this->errors = new MessageBag();
    }

    public function check()
    {
        if( ! $this->sentry->check()) return false;

        if($this->sentry->getUser()->banned)
        {
            $this->logout();
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(array $credentials, $remember = false)
    {
        Event::fire('service.authenticating', [$credentials, $remember]);

        try
        {
            $user = $this->sentry->authenticate($credentials, $remember);
        } catch(\Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $this->errors->add('login', 'Login field is required.');
        } catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $this->errors->add('login', 'Login failed.');
        } catch(\Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $this->errors->add('login', 'Your user is not activated.');
        } catch(\Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $this->errors->add('login', 'Password field is required.');
        } catch(\Cartalyst\Sentry\Throttling\UserSuspendedException $e)
        {
            $this->errors->add('login', 'Too many login attempts, please try later.');
        }
        if($this->foundAnyErrors())
        {
            $this->checkForBannedUser($user);
        }

        if(!$this->errors->isEmpty()) throw new AuthenticationErrorException;

        Event::fire('service.authenticated', [$credentials, $remember, $user]);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function loginById($id, $remember = false)
    {
        try
        {
            $user = $this->sentry->findUserById($id);
        } catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $this->errors->add('login', 'Utente non trovato.');
        }

        if($this->foundAnyErrors())
        {
            try
            {
                $this->sentry->login($user, $remember);
            } catch(\Cartalyst\Sentry\Users\UserNotActivatedException $e)
            {
                $this->errors->add('login', 'Utente non attivo.');
            }

            $this->checkForBannedUser($user);
        }

        return $this->errors->isEmpty() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        Event::fire('service.delogging');
        $this->sentry->logout();
        Event::fire('service.delogged');
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($email)
    {
        try
        {
            $user = $this->sentry->findUserByLogin($email);
        } catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            throw new UserNotFoundException($e->getMessage());
        }
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivationToken($email)
    {
        $user = $this->getUser($email);

        return $user->getActivationCode();
    }

    public function getUserById($id)
    {
        return $this->sentry->findUserById($id);
    }

    public function getLoggedUser()
    {
        return $this->sentry->getUser();
    }

    /**
     * @param $user
     */
    private function checkForBannedUser($user)
    {
        if($user->banned)
        {
            $this->errors->add('login', 'This user is banned.');
            $this->sentry->logout();
        }
    }

    /**
     * {@inheritdoc}
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    public function getToken($email)
    {
        $user = $this->getUser($email);

        return $user->getResetPasswordCode();
    }

    /**
     * @return bool
     */
    private function foundAnyErrors()
    {
        return $this->errors->isEmpty();
    }
}
